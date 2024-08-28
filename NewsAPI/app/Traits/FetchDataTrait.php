<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

trait FetchDataTrait
{
    protected function fetchArticlesByFilter(string $filterType, array $filters, string $apiKeyName): array
    {
        $allArticles = [];

        $includeToDate = ($filterType == 'from-date');

        foreach ($filters as $filter) {
            try {
                $params = [
                    $filterType => str_replace(' ', '', $filter),
                    $apiKeyName => $this->apiKey,
                    'show-fields' => 'byline,trailText,thumbnail',
                ];

                if ($includeToDate) {
                    $params['to-date'] = str_replace(' ', '', $filter);
                }

                $data = $this->fetchFromApi(
                    $this->baseUrl,
                    $this->getSearchEndpoint(),
                    $params,
                    'articles'
                );

                if (empty($data)) {
                    throw new \Exception('No data returned from API');
                }

                $articles = $this->extractArticles($data);

                $allArticles = array_merge($allArticles, $articles);
            } catch (\Exception $e) {
                Log::error("Failed to fetch articles for {$filterType} '{$filter}': " . $e->getMessage());
            }
        }

        return $this->normalizeArticles($allArticles);
    }

    protected function fetchDataFromSources(string $methodName, string $dataType,array $params = []): array
    {
        Log::channel('api')->info("Fetching {$dataType} from all news sources.");

        $allData = [];
        $articleMethods =['fetchArticles','filterArticles'];

        foreach ($this->newsSources as $newsSource) {
            try {
                $data = $newsSource->{$methodName}($params);
                if(in_array($methodName,$articleMethods)){
                    $allData = $this->combineArrays($allData, $data);
                }
                else{
                    $allData = array_merge($allData, $data);
                }
            } catch (\Exception $e) {
                Log::channel('api')->warning("Failed to fetch {$dataType} from a news source.", [
                    'source' => get_class($newsSource),
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::channel('api')->info($dataType. " successfully fetched.");

        return $allData;
    }

    protected function combineArrays(array $array1, array $array2): array
    {
        $result = [];

        $keys = array_unique(array_merge(array_keys($array1), array_keys($array2)));

        foreach ($keys as $key) {
            $values1 = isset($array1[$key]) ? $array1[$key] : [];
            $values2 = isset($array2[$key]) ? $array2[$key] : [];

            $mergedValues = array_merge($values1, $values2);

            $result[$key] = $mergedValues;
        }

        return $result;
    }


    protected function fetchFromApi(string $baseUrl, string $endpoint, array $params, string $dataType): array
    {
        try {
            Log::channel('api')->info("Fetching News {$dataType} from {$this->baseUrl}");

            $response = Http::get("{$baseUrl}{$endpoint}", $params);

            if ($response->failed()) {
                Log::channel('api')->error("Failed to fetch {$dataType}", [
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
                return [];
            }

            $data = $response->json();

            return $data;
        } catch (\Exception $e) {
            Log::channel('api')->error("Exception while fetching {$dataType}", [
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }


    protected function mappingData(array $data, bool $isAuthorType = false, string ...$keys)
    {

        $mappedData = array_map(function ($item) use ($keys, $isAuthorType) {
            foreach ($keys as $key) {
                if (is_array($item) && array_key_exists($key, $item)) {
                    if (!$isAuthorType) {
                        $item = strtolower($item[$key]);
                    } else {
                        $item = $item[$key];
                    }
                } else {
                    return null;
                }
            }

            if (is_string($item)) {
                // Remove "By " only if it appears at the start of the string

                if (preg_match('/^By\s+/i', $item)) {
                    $item = preg_replace('/^By\s+/i', '', $item);
                }

                // Trim any leading/trailing whitespace
                $item = trim($item);
            } else {
                //setting non string item to null to remove it later
                $item = null;
            }

            // Explode multiple names into an array

            if ($isAuthorType) {
                $names = array_filter(array_map('trim', preg_split('/\s*,\s*|\s+and\s+/', $item)));
                return $names;
            } else {
                return $item;
            }
        }, $data);

        if ($isAuthorType) {
            $flattened = array_merge(...array_filter($mappedData, fn($value) => is_array($value)));
        } else {
            $flattened = $mappedData;
        }

        return array_filter($flattened, function ($value) {
            return $value !== '' && $value !== null;
        });
    }


    protected function paginateData(array $allData, string $dataType, int $page = 1, int $pageSize = 10): array
    {

        $uniqueData = array_unique($allData, SORT_REGULAR);

        $page = (int) request()->input('page', 1);

        $pageSize = (int) request()->input('pageSize', 10);
        $totalPages = (int) ceil(count($uniqueData) / $pageSize);

        $offset = ($page - 1) * $pageSize;
        $pagedData = array_slice($uniqueData, $offset, $pageSize);

        Log::channel('api')->info($dataType . " successfully filtered and merged.");

        return [
            $dataType => $pagedData,
            'totalPages' =>$totalPages
        ];
    }
}
