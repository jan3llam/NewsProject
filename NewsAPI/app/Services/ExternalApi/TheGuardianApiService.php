<?php

namespace App\Services\ExternalApi;

use Carbon\Carbon;
use App\Enums\SourcesEnum;
use App\Enums\ApiErrorEnum;
use App\Enums\ApiKeyNameEnum;
use App\Traits\FetchDataTrait;
use Illuminate\Support\Facades\Log;
use App\Interfaces\NewsSourceInterface;

class TheGuardianApiService implements NewsSourceInterface
{
    use FetchDataTrait;

    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.theguardian.api_key');
        $this->baseUrl = config('services.theguardian.base_url');
    }

    public function searchArticles(array $filter): array
    {

        return $this->fetchArticlesByFilter('q', $filter, ApiKeyNameEnum::OTHER->value);
    }

    public function filterArticles(array $filters): array
    {
        $filteredArticles = [];

        if (isset($filters['category'])) {
            $filteredArticles = $this->fetchArticlesByFilter('section', $filters, ApiKeyNameEnum::OTHER->value);
        }
        if (isset($filters['source']) && in_array(SourcesEnum::THEGUARDIAN->value, $filters)) {
            $filteredArticles = $this->fetchArticlesByFilter('sources', $filters, ApiKeyNameEnum::OTHER->value);
        }

        if (isset($filters['date'])) {
            $filteredArticles = $this->fetchArticlesByFilter('from-date', $filters, ApiKeyNameEnum::OTHER->value);
        }
        return $filteredArticles ?? [];
    }

    public function fetchArticles(array $preferences): array
    {
        $articlesByCategories = $this->fetchArticlesByFilter('section', $preferences['preferred_categories'], ApiKeyNameEnum::OTHER->value);

        if (in_array(SourcesEnum::THEGUARDIAN->value, $preferences['preferred_sources'])) {
            $articlesBySources = $this->fetchArticlesByFilter('sources', $preferences['preferred_sources'], ApiKeyNameEnum::OTHER->value);
        }

        $articlesByAuthors = $this->fetchArticlesByFilter('q', $preferences['preferred_authors'], ApiKeyNameEnum::OTHER->value);

        return [
            'articlesByCategories' => $articlesByCategories,
            'articlesBySources' => $articlesBySources ?? [],
            'articlesByAuthors' => $articlesByAuthors
        ];
    }

    protected function getSearchEndpoint(): string
    {
        return 'search';
    }

    protected function extractArticles(array $data): array
    {
        return $data['response']['results'] ?? [];
    }

    protected function normalizeArticles(array $articles): array
    {
        return array_map(function ($article) {
            return [
                'title' => $article['webTitle'] ?? '',
                'description' => $article['fields']['trailText'] ?? '',
                'url' => $article['webUrl'] ?? '',
                'source' => SourcesEnum::THEGUARDIAN->value,
                'author' => $article['fields']['byline'] ?? '',
                'image' => $article['fields']['thumbnail'] ?? '',
                'publishedAt' => isset($article['webPublicationDate']) ? Carbon::parse($article['webPublicationDate'])->format('Y-m-d') : '',
            ];
        }, $articles);
    }


    public function fetchSources(): array
    {
        return array(SourcesEnum::THEGUARDIAN->value);
    }

    public function fetchCategories(): array
    {
        try {

            $data = $this->fetchFromApi(
                $this->baseUrl,
                'sections',
                ['api-key' => $this->apiKey],
                'categories'
            );

            if (empty($data)) {
                throw new \Exception(ApiErrorEnum::NOCONTENT->value);
            }

            $categories = $data['response']['results'] ?? [];

            $categoryNames = $this->mappingData($categories, false, 'webTitle');

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
                'search',
                [
                    'api-key' => $this->apiKey,
                    'show-fields' => 'byline',
                    'pageSize' => 25,
                ],
                'authors'
            );

            if (empty($data)) {
                throw new \Exception(ApiErrorEnum::NOCONTENT->value);
            }

            $articles = $data['response']['results'] ?? [];

            $authors = $this->mappingData($articles, true, 'fields', 'byline');

            Log::channel('api')->info('Successfully fetched authors from The Guardian');

            return array_unique($authors);
        } catch (\Exception $e) {
            Log::channel('api')->error('Exception while fetching authors from The Guardian', [
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }
}
