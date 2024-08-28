<?php

namespace App\Http\Controllers\Api;

use App\Enums\HttpResponseEnum;
use App\Helpers\ResponseHelper;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\Api\NewsPreferencesService;

class NewsPreferencesController extends Controller
{
    protected $newsPreferencesService;
    protected $responseHelper;

    public function __construct(NewsPreferencesService $newsPreferencesService, ResponseHelper $responseHelper)
    {
        $this->newsPreferencesService = $newsPreferencesService;
        $this->responseHelper = $responseHelper;
    }

    public function getCategories()
    {
        try {
            Log::channel('api')->info('Fetching News Categories');

            $categories = $this->newsPreferencesService->getCategories();

            return $this->responseHelper->successResponse(
                'api',
                'Categories retrieved successfully',
                $categories
            );
        } catch (\Exception $e) {
            return $this->responseHelper->errorResponse(
                'api',
                'Failed to retrieve categories',
                $e->getMessage(),
                HttpResponseEnum::INTERNAL_SERVER_ERROR->value
            );
        }
    }

    public function getAuthors()
    {
        try {
            $authors = $this->newsPreferencesService->getAllAuthors();

            return $this->responseHelper->successResponse(
                'api',
                'Authors retrieved successfully',
                $authors
            );
        } catch (\Exception $e) {
            return $this->responseHelper->errorResponse(
                'api',
                'Failed to retrieve authors',
                $e->getMessage(),
                HttpResponseEnum::INTERNAL_SERVER_ERROR->value
            );
        }
    }

    public function getSources()
    {
        try {
            $sources = $this->newsPreferencesService->getSources();

            return $this->responseHelper->successResponse(
                'api',
                'Sources retrieved successfully',
                $sources
            );
        } catch (\Exception $e) {
            return $this->responseHelper->errorResponse(
                'api',
                'Failed to retrieve sources',
                $e->getMessage(),
                HttpResponseEnum::INTERNAL_SERVER_ERROR->value
            );
        }
    }

}
