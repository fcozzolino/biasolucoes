<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\OtpCode;
use App\Models\User;
use App\Models\LoginAttempt;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class OtpController extends Controller
{
    /**
     * Send OTP code.
     */
    public function send(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string', // email or phone
            'type' => 'required|in:login,verification,password_reset',
        ]);

        $identifier = $request->identifier;
        $type = $request->type;

        // Check if can request new OTP (rate limiting)
        if (!OtpCode::canRequestNew($identifier)) {
            throw ValidationException::withMessages([
                'identifier' => ['Aguarde um minuto antes de solicitar um novo código.'],
            ]);
        }

        // Find user
        $user = null;
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $identifier)->first();
        } else {
            $phone = preg_replace('/\D/', '', $identifier);
            $user = User::where('phone', $phone)->first();
        }

        if (!$user && $type !== 'verification') {
            throw ValidationException::withMessages([
                'identifier' => ['Usuário não encontrado.'],
            ]);
        }

        // Generate OTP
        $otp = OtpCode::generate($identifier, $type, $user);

        // Send OTP (implement your SMS/Email service here)
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            // Send via email
            // Mail::to($identifier)->send(new OtpMail($otp->code));
        } else {
            // Send via SMS
            // SmsService::send($identifier, "Seu código de verificação: {$otp->code}");
        }

        // In development, return OTP
        if (app()->environment('local')) {
            return response()->json([
                'message' => 'Código enviado!',
                'otp' => $otp->code, // Remove in production
                'expires_in' => 600,
            ]);
        }

        return response()->json([
            'message' => 'Código enviado com sucesso!',
            'expires_in' => 600,
        ]);
    }

    /**
     * Verify OTP code.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
            'code' => 'required|string|size:6',
            'type' => 'required|in:login,verification,password_reset',
        ]);

        $identifier = $request->identifier;

        // Clean phone if needed
        if (!filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            $identifier = preg_replace('/\D/', '', $identifier);
        }

        // Get latest OTP
        $otp = OtpCode::getLatestValid($identifier, $request->type);

        if (!$otp) {
            throw ValidationException::withMessages([
                'code' => ['Código inválido ou expirado.'],
            ]);
        }

        // Verify code
        if (!$otp->verify($request->code)) {
            throw ValidationException::withMessages([
                'code' => ['Código incorreto.'],
            ]);
        }

        // Handle based on type
        switch ($request->type) {
            case 'login':
                return $this->handleLoginVerification($otp);

            case 'verification':
                return $this->handleAccountVerification($otp);

            case 'password_reset':
                return $this->handlePasswordReset($otp);
        }
    }

    /**
     * Handle login verification.
     */
    protected function handleLoginVerification(OtpCode $otp)
    {
        $user = $otp->user;

        if (!$user) {
            throw ValidationException::withMessages([
                'code' => ['Usuário não encontrado.'],
            ]);
        }

        // Login user
        Auth::login($user);
        request()->session()->regenerate();

        // Record successful login
        LoginAttempt::recordSuccess($user, 'phone');
        $user->recordLogin(request()->ip());

        // Log activity
        ActivityLog::log('login', 'Login realizado via OTP');

        // Load relationships
        $user->load(['company', 'modules']);

        return response()->json([
            'message' => 'Login realizado com sucesso!',
            'user' => $user,
            'redirect' => $user->company_id ? '/dashboard' : '/personal/tasks',
        ]);
    }

    /**
     * Handle account verification.
     */
    protected function handleAccountVerification(OtpCode $otp)
    {
        $user = $otp->user;

        if (!$user) {
            return response()->json([
                'message' => 'Verificação realizada com sucesso!',
            ]);
        }

        // Mark phone as verified
        if ($otp->identifier === $user->phone) {
            $user->update(['phone_verified_at' => now()]);
        }

        // Log activity
        ActivityLog::log('verification', 'Telefone verificado via OTP');

        return response()->json([
            'message' => 'Telefone verificado com sucesso!',
            'verified' => true,
        ]);
    }

    /**
     * Handle password reset.
     */
    protected function handlePasswordReset(OtpCode $otp)
    {
        // Store verification in session for password reset
        session([
            'password_reset_verified' => true,
            'password_reset_user_id' => $otp->user_id,
            'password_reset_identifier' => $otp->identifier,
        ]);

        return response()->json([
            'message' => 'Código verificado! Agora você pode redefinir sua senha.',
            'next_step' => 'new_password',
        ]);
    }
}
