<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginAttempt extends Model
{
  use HasFactory;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'user_id',
    'email',
    'ip_address',
    'user_agent',
    'phone',
    'attempted_at',
    'successful', // <-- correto
    'failure_reason',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'attempted_at' => 'datetime',
    'successful' => 'boolean',
  ];

  /**
   * Get the user associated with the login attempt.
   */
  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  /**
   * Record a successful login attempt.
   */
  public static function recordSuccess(User $user): self
  {
    return static::create([
      'user_id' => $user->id,
      'email' => $user->email,
      'ip_address' => request()->ip(),
      'user_agent' => request()->userAgent(),
      'attempted_at' => now(),
      'successful' => true,
    ]);
  }

  public static function recordFailure(string $email, string $reason): self
{
    return static::create([
        'user_id' => null,
        'email' => $email,
        'ip_address' => request()->ip(),
        'user_agent' => request()->userAgent(),
        'attempted_at' => now(),
        'successful' => false, // <--- corrigido aqui
        'failure_reason' => $reason,
    ]);
}



  /**
   * Check if IP has too many failed attempts.
   */
  public static function tooManyFailedAttempts(string $ip, int $minutes = 15, int $maxAttempts = 5): bool
  {
    return static::where('ip_address', $ip)
      ->where('successful', false)
      ->where('attempted_at', '>=', now()->subMinutes($minutes))
      ->count() >= $maxAttempts;
  }

  /**
   * Get failed attempts count for an email.
   */
  public static function failedAttemptsCount(string $email, int $minutes = 15): int
  {
    return static::where('email', $email)
      ->where('successful', false)
      ->where('attempted_at', '>=', now()->subMinutes($minutes))
      ->count();
  }

  /**
   * Clear old login attempts (for cleanup).
   */
  public static function clearOldAttempts(int $days = 30): int
  {
    return static::where('attempted_at', '<', now()->subDays($days))->delete();
  }

  /**
   * Scope to filter by successful attempts.
   */
  public function scopeSuccessful($query)
  {
    return $query->where('successful', true);
  }

  public function scopeFailed($query)
  {
    return $query->where('successful', false);
  }
}
