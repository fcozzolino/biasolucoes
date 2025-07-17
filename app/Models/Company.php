<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Company extends Model
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
        'cnpj',
        'phone',
        'status',
        'trial_ends_at',
        'plan_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'trial_ends_at' => 'datetime',
    ];

    /**
     * Get the plan that the company belongs to.
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Get the users that belong to the company through pivot.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'company_users')
            ->withPivot(['role', 'department', 'position', 'is_active', 'joined_at', 'permissions'])
            ->withTimestamps();
    }

    /**
     * Get the company users pivot records.
     */
    public function companyUsers(): HasMany
    {
        return $this->hasMany(CompanyUser::class);
    }

    /**
     * Get the modules that belong to the company.
     */
    public function modules(): BelongsToMany
    {
        return $this->belongsToMany(Module::class, 'company_modules')
            ->withPivot(['is_active', 'activated_at', 'expires_at', 'settings'])
            ->withTimestamps();
    }

    /**
     * Get the activity logs for the company.
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Get the owner of the company.
     */
    public function owner()
    {
        return $this->users()
            ->wherePivot('role', 'owner')
            ->first();
    }

    /**
     * Add a user to the company.
     */
    public function addUser(User $user, string $role = 'member', array $additionalData = []): CompanyUser
    {
        return CompanyUser::create(array_merge([
            'company_id' => $this->id,
            'user_id' => $user->id,
            'role' => $role,
            'is_active' => true,
            'joined_at' => now(),
        ], $additionalData));
    }

    /**
     * Remove a user from the company.
     */
    public function removeUser(User $user): bool
    {
        return $this->companyUsers()
            ->where('user_id', $user->id)
            ->delete();
    }

    /**
     * Check if company has a specific module.
     */
    public function hasModule(string $slug): bool
    {
        return $this->modules()
            ->where('slug', $slug)
            ->wherePivot('is_active', true)
            ->exists();
    }

    /**
     * Check if company can add more users based on plan limits.
     */
    public function canAddMoreUsers(): bool
    {
        if (!$this->plan) {
            return true; // Sem plano, sem limite
        }

        $userLimit = $this->plan->user_limit;

        if ($userLimit === null || $userLimit === 0) {
            return true; // Sem limite
        }

        return $this->users()->count() < $userLimit;
    }

    /**
     * Check if company is in trial period.
     */
    public function isInTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    /**
     * Check if company trial has expired.
     */
    public function hasTrialExpired(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isPast();
    }

    /**
     * Check if company is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Activate a module for the company.
     */
    public function activateModule(Module $module, array $settings = []): void
    {
        $this->modules()->syncWithoutDetaching([
            $module->id => [
                'is_active' => true,
                'activated_at' => now(),
                'settings' => $settings,
            ]
        ]);
    }

    /**
     * Deactivate a module for the company.
     */
    public function deactivateModule(Module $module): void
    {
        $this->modules()->updateExistingPivot($module->id, [
            'is_active' => false,
        ]);
    }

    /**
     * Get active modules count.
     */
    public function activeModulesCount(): int
    {
        return $this->modules()
            ->wherePivot('is_active', true)
            ->count();
    }

    /**
     * Scope to filter active companies.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to filter companies in trial.
     */
    public function scopeInTrial($query)
    {
        return $query->whereNotNull('trial_ends_at')
            ->where('trial_ends_at', '>', now());
    }
}
