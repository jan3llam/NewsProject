<?php

namespace App\Services\ExternalApi;

use Carbon\Carbon;
use App\Enums\SourcesEnum;
use App\Enums\ApiErrorEnum;
use App\Enums\ApiKeyNameEnum;
use App\Traits\FetchDataTrait;
use Illuminate\Support\Facades\Log;
use App\Interfaces\NewsSourceInterface;

class NYTApiService implements NewsSourceInterface
{
    use FetchDataTrait;


    protected $apiKey;
    protected $baseUrl;
    protected $imageUrl;

    public function __construct()
    {
        $this->apiKey = config('services.nyt.api_key');
        $this->baseUrl = config('services.nyt.base_url');
        $this->imageUrl = config('services.nyt.image_url');
    }

    public function searchArticles(array $filter): array
    {

        return $this->fetchArticlesByFilter('q', $filter, ApiKeyNameEnum::OTHER->value);
    }

    public function filterArticles(array $filters): array
    {
        $filteredArticles = [];
        if (isset($filters['category'])) {
            $filteredArticles = $this->fetchArticlesByFilter('fq', $this->prepareCategoryFilters($filters), ApiKeyNameEnum::OTHER->value);
        }

        if (isset($filters['source']) && in_array(SourcesEnum::NYT->value, $filters)) {
            $filteredArticles = $this->fetchArticlesByFilter('fq', $this->prepareSourceFilters($filters), ApiKeyNameEnum::OTHER->value);
        }

        if (isset($filters['date'])) {
            $filteredArticles = $this->fetchArticlesByFilter('fq', $this->prepareDateFilters($filters['date']), ApiKeyNameEnum::OTHER->value);
        }

        return $filteredArticles ?? [];
    }

    public function fetchArticles(array $preferences): array
    {
        $articlesByCategories = $this->fetchArticlesByFilter('fq', $this->prepareCategoryFilters($preferences['preferred_categories']), ApiKeyNameEnum::OTHER->value);

        if (in_array(SourcesEnum::NYT->value, $preferences['preferred_sources'])) {
            $articlesBySources = $this->fetchArticlesByFilter('fq', $this->prepareSourceFilters($preferences['preferred_sources']), ApiKeyNameEnum::OTHER->value);
        }

        $articlesByAuthors = $this->fetchArticlesByFilter('fq', $preferences['preferred_authors'], ApiKeyNameEnum::OTHER->value);

        return [
            'articlesByCategories' => $articlesByCategories,
            'articlesBySources' => $articlesBySources ?? [],
            'articlesByAuthors' => $articlesByAuthors
        ];
    }

    protected function getSearchEndpoint(): string
    {
        return 'search/v2/articlesearch.json';
    }

    protected function extractArticles(array $data): array
    {
        return $data['response']['docs'] ?? [];
    }

    protected function normalizeArticles(array $articles): array
    {
        return array_map(function ($article) {
            $image = '';
            if (!empty($article['multimedia']) && is_array($article['multimedia'])) {
                foreach ($article['multimedia'] as $media) {
                    if ($media['type'] === 'image') {
                        $image = $this->imageUrl.$media['url'];
                        break;
                    }
                }
            }

            return [
                'title' => $article['headline']['main'] ?? '',
                'description' => $article['abstract'] ?? '',
                'url' => $article['web_url'] ?? '',
                'source' => SourcesEnum::NYT->value,
                'author' => $article['byline']['original'] ?? [],
                'image' => $image,
                'publishedAt' => isset($article['pub_date']) ? Carbon::parse($article['pub_date'])->format('Y-m-d') : '',
            ];
        }, $articles);
    }

    protected function prepareCategoryFilters(array $categories): array
    {
        return array_map(function ($category) {
            return 'section_name:("' . $category . '")';
        }, $categories);
    }

    protected function prepareSourceFilters(array $sources): array
    {
        return array_map(function ($source) {
            return 'source:("' . $source . '")';
        }, $sources);
    }

    protected function prepareDateFilters(string $date): array
    {
        return ['pub_date:("' . $date . '")'];
    }

    public function fetchSources(): array
    {
        return array(SourcesEnum::NYT->value);
    }

    public function fetchCategories(): array
    {
        try {

            $data = $this->fetchFromApi(
                $this->baseUrl,
                'news/v3/content/section-list.json',
                ['api-key' => $this->apiKey],
                'categories'
            );

            if (empty($data)) {
                throw new \Exception(ApiErrorEnum::NOCONTENT->value);
            }

            $categories = $data['results'] ?? [];

            $categoryNames = $this->mappingData($categories, false, 'section');

            Log::channel('api')->info('Successfully fetched categories from The Guardian');

            return $categoryNames;
        } catch (\Exception $e) {
            Log::channel('api')->error('Exception while fetching categories', [
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    public function fetchAuthors(): array
    {
        try {

            $data = $this->fetchFromApi(
                $this->baseUrl,
                'mostpopular/v2/viewed/7.json',
                [
                    'api-key' => $this->apiKey,
                ],
                'authors'
            );

            if (empty($data)) {
                throw new \Exception(ApiErrorEnum::NOCONTENT->value);
            }

            $articles = $data['results'] ?? [];

            $authors = $this->mappingData($articles, true, 'byline');

            Log::channel('api')->info('Successfully fetched authors from NYT');

            return array_unique($authors);
        } catch (\Exception $e) {
            Log::channel('api')->error('Exception while fetching authors from NYT', [
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }
}
