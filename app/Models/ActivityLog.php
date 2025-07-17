<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Auth;

class ActivityLog extends Model
{
  use HasFactory;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'user_id',
    'company_id',
    'description',
    'event',
    'type', 
    'subject_type',
    'subject_id',
    'causer_type',
    'causer_id',
    'properties',
    'ip_address',
    'user_agent',
    'session_id',
    'url',
    'method',
    'status',
];



  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'properties' => 'array',
  ];

  /**
   * Get the user that performed the activity.
   */
  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  /**
   * Get the company where the activity occurred.
   */
  public function company(): BelongsTo
  {
    return $this->belongsTo(Company::class);
  }

  /**
   * Get the subject of the activity.
   */
  public function subject(): MorphTo
  {
    return $this->morphTo();
  }

  /**
   * Get the causer of the activity.
   */
  public function causer(): MorphTo
  {
    return $this->morphTo();
  }

  /**
   * Log a new activity.
   */
  public static function log(string $event, string $description, array $properties = [], string $type = 'system'): self
{
    return static::create([
        'user_id'    => Auth::check() ? Auth::id() : null,
        'company_id' => session('current_company_id'),
        'event'      => $event,
        'type'       => $type, // agora recebe dinamicamente
        'description'=> $description,
        'properties' => $properties,
        'ip_address' => request()->ip(),
        'user_agent' => request()->userAgent(),
        'session_id' => session()->getId(),
        'url'        => request()->fullUrl(),
        'method'     => request()->method(),
        'status'     => 'success',
    ]);
}



  /**
   * Log an activity for a specific model.
   */
  public static function logForModel($model, string $event, string $description, array $properties = []): self
  {
    $log = static::log($event, $description, $properties);

    $log->update([
      'subject_type' => get_class($model),
      'subject_id' => $model->id,
    ]);

    return $log;
  }

  /**
   * Log a failed activity.
   */
  public static function logFailure(string $event, string $description, array $properties = []): self
  {
    $log = static::log($event, $description, $properties);
    $log->update(['status' => 'failed']);

    return $log;
  }

  /**
   * Scope to filter by event.
   */
  public function scopeByEvent($query, string $event)
  {
    return $query->where('event', $event);
  }

  /**
   * Scope to filter by user.
   */
  public function scopeByUser($query, int $userId)
  {
    return $query->where('user_id', $userId);
  }

  /**
   * Scope to filter by date range.
   */
  public function scopeBetweenDates($query, $startDate, $endDate)
  {
    return $query->whereBetween('created_at', [$startDate, $endDate]);
  }
}
