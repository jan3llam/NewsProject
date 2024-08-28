<?php

namespace App\Services\Api;

use App\Traits\FetchDataTrait;


class ArticleService
{
    use FetchDataTrait;

    protected $newsSources;

    public function __construct(array $newsSources)
    {
        $this->newsSources = $newsSources;
    }

    public function searchArticles(array $filter): array
    {
        $articles = $this->fetchDataFromSources('searchArticles', 'articles', $filter);

        return $this->paginateData($articles,'articles');
    }


    public function fetchArticles(array $preferences): array
    {
        return  $this->fetchDataFromSources('fetchArticles', 'articles' , $preferences);
    }

    public function filterArticles(array $filters): array
    {
        $articles = $this->fetchDataFromSources('filterArticles', 'articles', $filters);

        return $this->paginateData($articles,'articles');

    }
}






