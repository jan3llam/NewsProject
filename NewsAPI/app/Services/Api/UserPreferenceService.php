<?php

namespace App\Services\Api;

use App\Models\User;
use App\Models\UserPreference;

class UserPreferenceService
{

    public function getUserPreferences(User $user): array
    {
        return json_decode($user->preferences, true) ?? [];
    }


    public function updateUserPreferences(User $user, array $data): UserPreference
    {
        $preference = UserPreference::updateOrCreate(
            ['user_id' => $user->id],
            [
                'preferred_sources' => $data['preferred_sources'] ?? [],
                'preferred_categories' => $data['preferred_categories'] ?? [],
                'preferred_authors' => $data['preferred_authors'] ?? [],
            ]
        );

        return $preference;
    }
}
