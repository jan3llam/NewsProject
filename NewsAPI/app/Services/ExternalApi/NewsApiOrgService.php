<?php

namespace App\Services\ExternalApi;

use Carbon\Carbon;
use App\Enums\SourcesEnum;
use App\Enums\ApiErrorEnum;
use App\Enums\ApiKeyNameEnum;
use jcobhams\NewsApi\NewsApi;
use App\Traits\FetchDataTrait;
use Illuminate\Support\Facades\Log;
use App\Interfaces\NewsSourceInterface;

class NewsApiOrgService implements NewsSourceInterface
{
    use FetchDataTrait;


    protected $newsApi;
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.newsapiorg.api_key');
        $this->baseUrl = config('services.newsapiorg.base_url');
        $this->newsApi = new NewsApi($this->apiKey);
    }

    public function searchArticles(array $filter): array
    {

        return $this->fetchArticlesByFilter('q', $filter, ApiKeyNameEnum::NEWSAPIORG->value);
    }

    public function filterArticles(array $filters): array
    {
        $filteredArticles = [];
        if (isset($filters['category'])) {
            $filteredArticles = $this->fetchArticlesByFilter('category', $filters, ApiKeyNameEnum::NEWSAPIORG->value);
        }

        if (isset($filters['source'])) {
            $filteredArticles = $this->fetchArticlesByFilter('sources', $filters, ApiKeyNameEnum::NEWSAPIORG->value);
        }

        if (isset($filters['date'])) {
            $filteredArticles = $this->fetchArticlesByDate($filters['date']);
        }

        return $filteredArticles ?? [];
    }

    public function fetchArticles(array $preferences): array
    {
        $articlesByCategories = $this->fetchArticlesByFilter('category', $preferences['preferred_categories'], ApiKeyNameEnum::NEWSAPIORG->value);
        $articlesBySources = $this->fetchArticlesByFilter('sources', $preferences['preferred_sources'], ApiKeyNameEnum::NEWSAPIORG->value);
        $articlesByAuthors = $this->fetchArticlesByAuthors($preferences['preferred_authors']);

        return [
            'articlesByCategories' => $articlesByCategories,
            'articlesBySources' => $articlesBySources,
            'articlesByAuthors' => $articlesByAuthors
        ];
    }

    protected function getSearchEndpoint(): string
    {
        return 'top-headlines';
    }

    protected function extractArticles(array $data): array
    {
        return $data['articles'] ?? [];
    }

    protected function fetchArticlesByDate(string $date): array
    {
        $allArticles = [];

        try {
            $data = $this->fetchFromApi(
                $this->baseUrl,
                'everything',
                [
                    'q' => "all",
                    'apiKey' => $this->apiKey,
                    'pageSize' => 10,
                    'language' => 'en',
                    'from' => $date,
                    'to' => $date
                ],
                'articles'
            );

            if (empty($data)) {
                throw new \Exception(ApiErrorEnum::NOCONTENT->value);
            }

            $allArticles = $data['articles'] ?? [];
        } catch (\Exception $e) {
            Log::error('Failed to fetch articles for given date: ' . $e->getMessage());
        }

        return $this->normalizeArticles($allArticles);
    }

    protected function fetchArticlesByAuthors(array $authors): array
    {
        $allArticles = [];

        try {
            $data = $this->fetchFromApi(
                $this->baseUrl,
                'everything',
                [
                    'q' => "all",
                    'apiKey' => $this->apiKey,
                    'pageSize' => 10,
                    'language' => 'en',
                    'sortBy' => 'publishedAt'
                ],
                'articles'
            );

            if (empty($data)) {
                throw new \Exception(ApiErrorEnum::NOCONTENT->value);
            }

            $articles = $data['articles'] ?? [];

            foreach ($articles as $article) {
                if (in_array($article['author'], $authors)) {
                    $allArticles[] = $article;
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to fetch articles for authors: ' . $e->getMessage());
        }

        return $this->normalizeArticles($allArticles);
    }

    protected function normalizeArticles(array $articles): array
    {
        return array_map(function ($article) {
            return [
                'title' => $article['title'] ?? '',
                'description' => $article['description'] ?? '',
                'url' => $article['url'] ?? '',
                'source' => $article['source']['name'] ?? SourcesEnum::NEWSAPIORG->value,
                'author' => $article['author'] ?? '',
                'image' => $article['urlToImage'] ?? '',
                'publishedAt' => isset($article['publishedAt']) ? Carbon::parse($article['publishedAt'])->format('Y-m-d') : '',
            ];
        }, $articles);
    }


    public function fetchSources(): array
    {
        try {

            $data = $this->fetchFromApi(
                $this->baseUrl,
                'sources',
                ['apiKey' => $this->apiKey],
                'sources'
            );

            if (empty($data)) {
                throw new \Exception(ApiErrorEnum::NOCONTENT->value);
            }

            $sources = $data['sources'] ?? [];

            $sourcesNames = $this->mappingData($sources, false, 'id');

            Log::channel('api')->info('Fetching Sources Success from NewsApi.org');

            return $sourcesNames;
        } catch (\Exception $e) {
            Log::channel('api')->error('Exception while fetching sources from NewsApi.org', [
                'error' => $e->getMessage(),
            ]);

            throw new \Exception('Failed to fetch sources from NewsApi.org');
        }
    }

    public function fetchCategories(): array
    {
        try {
            Log::channel('api')->info('Fetching News Categories from NewsApi.org');

            $response = $this->newsApi->getCategories();

            Log::channel('api')->info('Fetching Categories Success from NewsApi.org');

            return $response;
        } catch (\Exception $e) {
            Log::channel('api')->error('Exception while fetching categories from NewsApi.org', [
                'error' => $e->getMessage(),
            ]);

            throw new \Exception('Failed to fetch categories from NewsApi.org');
        }
    }

    public function fetchAuthors(): array
    {
        try {
            $data = $this->fetchFromApi(
                $this->baseUrl,
                'everything',
                [
                    'apiKey' => $this->apiKey,
                    'q' => "all",
                    'page' => 1,
                    'pageSize' => 25,
                ],
                'authors'
            );

            if (empty($data)) {
                throw new \Exception(ApiErrorEnum::NOCONTENT->value);
            }

            $articles = $data['articles'] ?? [];

            $authors =  $this->mappingData($articles, true, 'author');

            Log::channel('api')->info('Successfully fetched authors from NewsApi.org');

            return array_unique($authors);
        } catch (\Exception $e) {
            Log::channel('api')->error('Exception while fetching authors from NewsApi.org', [
                'error' => $e->getMessage(),
            ]);

            throw new \Exception('Failed to fetch authors from NewsApi.org');
        }
    }
}
