<?php

namespace App\Services\Api;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Services\Api\ArticleService;
use Illuminate\Support\Facades\Cache;


class NewsFeedService
{

    protected $articleService;

    public function __construct(ArticleService $articleService)
    {
        $this->articleService = $articleService;
    }

    public function searchByKeyword(array $filter): array
    {
        $keyword = $filter['keyword'];

        Log::channel('user')->info('Searching articles for keyword', [
            'keyword' => $keyword
        ]);

        $cacheKey = 'search_articles_' . md5($keyword);

        $articles = Cache::get($cacheKey);

        if (!$articles) {

            $articles = $this->articleService->searchArticles($filter);

            Cache::put($cacheKey, $articles, now()->addMinutes(10));
        }

        return $articles;
    }

    public function getFilteredNews(array $filters): array
    {
        Log::channel('user')->info('Filtering articles with: ', [
            'filters' => $filters
        ]);


        return $this->articleService->filterArticles($filters);
    }

    public function getPersonalizedNews(User $user): array
    {
        Log::channel('user')->info('Fetching user preferences for personalized news.', [
            'user_id' => $user->id
        ]);


        $preferences = $user->preferences;

        if (
            !$preferences
            || (
                $preferences
                && empty($preferences->preferred_categories)
                && empty($preferences->preferred_sources)
                && empty($preferences->preferred_authors)
            )
        ) {
            Log::channel('user')->warning('User has no preferences set, returning empty news feed.', [
                'user_id' => $user->id
            ]);

            return [];
        }

        $cacheKey = 'personalized_news_user_' . $user->id . '_' . md5(json_encode($preferences->only('preferred_categories', 'preferred_sources', 'preferred_authors')));

        $articles = Cache::remember($cacheKey, 10, function () use ($preferences) {
            return $this->articleService->fetchArticles($preferences->only('preferred_categories', 'preferred_sources', 'preferred_authors'));
        });


        return $articles;
    }
}
