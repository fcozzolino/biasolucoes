<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Module extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'is_active',
        'is_premium',
        'price',
        'type', // personal, business, both
        'settings',
        'features',
        'limits',
        'permissions',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'is_premium' => 'boolean',
        'price' => 'decimal:2',
        'settings' => 'array',
        'features' => 'array',
        'limits' => 'array',
        'permissions' => 'array',
    ];

    /**
     * Get the users that have this module.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_modules')
            ->withPivot(['is_active', 'activated_at', 'expires_at', 'settings'])
            ->withTimestamps();
    }

    /**
     * Get the companies that have this module.
     */
    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'company_modules')
            ->withPivot(['is_active', 'activated_at', 'expires_at', 'settings'])
            ->withTimestamps();
    }

    /**
     * Get the plans that include this module.
     */
    public function plans(): BelongsToMany
    {
        return $this->belongsToMany(Plan::class, 'plan_modules')
            ->withPivot(['is_included', 'limits'])
            ->withTimestamps();
    }

    /**
     * Check if module is available for a specific type.
     */
    public function isAvailableFor(string $type): bool
    {
        return in_array($this->type, [$type, 'both']);
    }

    /**
     * Get a specific feature value.
     */
    public function getFeature(string $key)
    {
        return $this->features[$key] ?? null;
    }

    /**
     * Get a specific limit value.
     */
    public function getLimit(string $key)
    {
        return $this->limits[$key] ?? null;
    }

    /**
     * Scope to get only active modules.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get modules by type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->whereIn('type', [$type, 'both']);
    }
}
