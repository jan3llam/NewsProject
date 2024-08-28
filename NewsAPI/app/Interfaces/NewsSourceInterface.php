<?php

namespace App\Interfaces;

interface NewsSourceInterface
{
    public function fetchArticles(array $preferences): array;
    public function fetchCategories(): array;
    public function fetchSources(): array;
    public function fetchAuthors(): array;
    public function searchArticles(array $keyword):array;
}
