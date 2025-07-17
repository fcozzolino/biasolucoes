<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class OtpCode extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'identifier', // email ou phone
        'code',
        'type', // login, verification, password_reset
        'expires_at',
        'verified_at',
        'attempts',
        'max_attempts',
        'ip_address',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
        'attempts' => 'integer',
        'max_attempts' => 'integer',
    ];

    /**
     * Get the user that owns the OTP code.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate a new OTP code.
     */
    public static function generate(string $identifier, string $type = 'login', ?User $user = null): self
    {
        // Invalidate any existing codes for this identifier
        static::where('identifier', $identifier)
            ->where('type', $type)
            ->whereNull('verified_at')
            ->update(['verified_at' => now()]);

        return static::create([
            'user_id' => $user?->id,
            'identifier' => $identifier,
            'code' => static::generateCode(),
            'type' => $type,
            'expires_at' => now()->addMinutes(10),
            'attempts' => 0,
            'max_attempts' => 3,
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Generate a random 6-digit code.
     */
    protected static function generateCode(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Verify the OTP code.
     */
    public function verify(string $code): bool
    {
        // Check if already verified
        if ($this->verified_at) {
            return false;
        }

        // Check if expired
        if ($this->isExpired()) {
            return false;
        }

        // Check attempts
        if ($this->attempts >= $this->max_attempts) {
            return false;
        }

        // Increment attempts
        $this->increment('attempts');

        // Check code
        if ($this->code !== $code) {
            return false;
        }

        // Mark as verified
        $this->update(['verified_at' => now()]);

        return true;
    }

    /**
     * Check if the OTP code is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if the OTP code is valid (not verified and not expired).
     */
    public function isValid(): bool
    {
        return !$this->verified_at && !$this->isExpired() && $this->attempts < $this->max_attempts;
    }

    /**
     * Get the latest valid OTP for an identifier.
     */
    public static function getLatestValid(string $identifier, string $type = 'login'): ?self
    {
        return static::where('identifier', $identifier)
            ->where('type', $type)
            ->whereNull('verified_at')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();
    }

    /**
     * Check if user can request a new OTP (rate limiting).
     */
    public static function canRequestNew(string $identifier, int $minutes = 1): bool
    {
        $lastOtp = static::where('identifier', $identifier)
            ->latest()
            ->first();

        if (!$lastOtp) {
            return true;
        }

        return $lastOtp->created_at->addMinutes($minutes)->isPast();
    }

    /**
     * Clear expired OTP codes (for cleanup).
     */
    public static function clearExpired(): int
    {
        return static::where('expires_at', '<', now())
            ->orWhereNotNull('verified_at')
            ->delete();
    }
}
