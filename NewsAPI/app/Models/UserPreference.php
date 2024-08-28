<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'preferred_sources',
        'preferred_categories',
        'preferred_authors',
    ];

    protected $casts = [
        'preferred_sources' => 'array',
        'preferred_categories' => 'array',
        'preferred_authors' => 'array',
    ];

    public function getPreferredSourcesAsStringAttribute()
    {
        return implode(',', $this->preferred_sources);
    }

    public function getPreferredCategoriesAsStringAttribute()
    {
        return implode(',', $this->preferred_categories);
    }

    public function getPreferredAuthorsAsStringAttribute()
    {
        return implode(',', $this->preferred_authors);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
