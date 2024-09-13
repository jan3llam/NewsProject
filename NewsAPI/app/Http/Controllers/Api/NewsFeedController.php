<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Enums\HttpResponseEnum;
use App\Helpers\ResponseHelper;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\FilterRequest;
use App\Http\Requests\SearchRequest;
use Illuminate\Support\Facades\Auth;
use App\Services\Api\NewsFeedService;

class NewsFeedController extends Controller
{
    protected $newsFeedService;
    protected $responseHelper;


    public function __construct(NewsFeedService $newsFeedService, ResponseHelper $responseHelper)
    {
        $this->newsFeedService = $newsFeedService;
        $this->responseHelper = $responseHelper;
    }

    public function getNewsFeed()
    {

        try {
            $user = User::with('preferences')->findOrFail(Auth::id());
            Log::channel('api')->info('Getting user news feed', [
                'user_id' => $user->id
            ]);

            $articles = $this->newsFeedService->getPersonalizedNews($user);

            if (empty($articles)) {
                return $this->responseHelper->successResponse(
                    'api',
                    'No news articles found for your preferences.',
                    [],
                    HttpResponseEnum::OK->value
                );
            }

            return $this->responseHelper->successResponse(
                'api',
                'Personalized news feed retrieved successfully',
                $articles,
                HttpResponseEnum::OK->value
            );
        } catch (\Exception $e) {
            return $this->responseHelper->errorResponse(
                'api',
                'Failed to retrieve personalized news feed',
                $e->getMessage(),
                HttpResponseEnum::INTERNAL_SERVER_ERROR->value
            );
        }
    }

    public function filterArticles(FilterRequest $request)
    {
        try {
            Log::channel('api')->info('Filter Request Validation Success');

            $validatedData = $request->validated();

            Log::channel('api')->info('Filtering user news feed');

            $articles = $this->newsFeedService->getFilteredNews($validatedData['filters']);

            if (empty($articles)) {
                return $this->responseHelper->successResponse(
                    'api',
                    'No news articles found for your filters.',
                    [],
                    HttpResponseEnum::OK->value
                );
            }

            return $this->responseHelper->successResponse(
                'api',
                'Filtered news feed retrieved successfully',
                $articles,
                HttpResponseEnum::OK->value
            );
        } catch (\Exception $e) {
            return $this->responseHelper->errorResponse(
                'api',
                'Failed to retrieve filtered news feed',
                $e->getMessage(),
                HttpResponseEnum::INTERNAL_SERVER_ERROR->value
            );
        }
    }


    public function searchArticles(SearchRequest $request)
    {
        try {
            Log::channel('api')->info('Search Request Validation Success');

            $validatedData = $request->validated();
            $keyword = $validatedData['keyword'];

            Log::channel('api')->info('Searching news feed by: ', [
                'keyword' => $keyword
            ]);

            $articles = $this->newsFeedService->searchByKeyword($validatedData);

            if (empty($articles)) {
                return $this->responseHelper->successResponse(
                    'api',
                    'No news articles found for entered keyword',
                    [],
                    HttpResponseEnum::OK->value
                );
            }

            return $this->responseHelper->successResponse(
                'api',
                'Articles results retrieved successfully',
                $articles,
                HttpResponseEnum::OK->value
            );
        } catch (\Exception $e) {
            return $this->responseHelper->errorResponse(
                'api',
                'Failed to retrieve articles results',
                $e->getMessage(),
                HttpResponseEnum::INTERNAL_SERVER_ERROR->value
            );
        }
    }
}
