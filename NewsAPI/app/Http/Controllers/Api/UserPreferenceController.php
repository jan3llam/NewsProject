<?php

namespace App\Http\Controllers\Api;

use App\Enums\HttpResponseEnum;
use App\Helpers\ResponseHelper;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\Api\UserPreferenceService;
use App\Http\Requests\UpdatePreferencesRequest;

class UserPreferenceController extends Controller
{
    protected $userPreferenceService;
    protected $responseHelper;

    public function __construct(UserPreferenceService $userPreferenceService, ResponseHelper $responseHelper)
    {
        $this->userPreferenceService = $userPreferenceService;
        $this->responseHelper = $responseHelper;
    }

    public function get()
    {
        try {
            $user = Auth::user();
            $preferences = $this->userPreferenceService->getUserPreferences($user);

            return $this->responseHelper->successResponse(
                'user',
                'User preferences retrieved successfully',
                $preferences,
                HttpResponseEnum::OK->value

            );

        } catch (\Exception $e) {
            return $this->responseHelper->errorResponse(
                'user',
                'Failed to retrieve user preferences',
                $e->getMessage(),
                HttpResponseEnum::INTERNAL_SERVER_ERROR->value
            );
        }
    }



    public function update(UpdatePreferencesRequest $request)
    {
        try {

            Log::channel('user')->info('New User Preferences Validation Success');

            $validatedData = $request->validated();
            $user = Auth::user();
            $updatedPreferences = $this->userPreferenceService->updateUserPreferences($user, $validatedData);

            return $this->responseHelper->successResponse(
                'user',
                'Preferences updated successfully',
                json_decode($updatedPreferences, true),
                HttpResponseEnum::OK->value

            );
        } catch (\Exception $e) {
            return $this->responseHelper->errorResponse(
                'user',
                'Failed to update preferences',
                $e->getMessage(),
                HttpResponseEnum::INTERNAL_SERVER_ERROR->value
            );
        }
    }
}
