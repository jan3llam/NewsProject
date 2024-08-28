<?php

namespace App\Services\Api;

use App\Traits\FetchDataTrait;

class NewsPreferencesService
{
    use FetchDataTrait;

    protected $newsSources;

    public function __construct(array $newsSources)
    {
        $this->newsSources = $newsSources;
    }

    public function getCategories(): array
    {
        $categories = $this->fetchDataFromSources('fetchCategories', 'categories');

        return $this->paginateData($categories, 'categories');
    }

    public function getSources(): array
    {
        $sources = $this->fetchDataFromSources('fetchSources', 'sources');

        return $this->paginateData($sources, 'sources');
    }

    public function getAllAuthors(): array
    {
        $authors = $this->fetchDataFromSources('fetchAuthors', 'authors');

        return $this->paginateData($authors, 'authors');
    }


}
