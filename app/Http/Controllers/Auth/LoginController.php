<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LoginAttempt;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
  /**
   * Display the login view.
   */
  public function showLoginForm()
  {
    $a = rand(1, 9);
    $b = rand(1, 9);
    session([
      'captcha_result' => $a + $b,
      'captcha_a' => $a,
      'captcha_b' => $b,
    ]);
    return view('auth.login', compact('a', 'b'));
  }


  /**
   * Handle login request with email/password.
   */
  public function login(Request $request)
  {
    $request->validate([
      'email' => 'required|email',
      'password' => 'required|string',
      'remember' => 'boolean',
      'captcha' => ['required', function ($attribute, $value, $fail) {
        if (session('captcha_result') != $value) {
          $fail('CAPTCHA incorreto');
        }
      }],
    ]);


    // Check rate limiting
    $this->checkRateLimit($request);

    // Find user
    $user = User::where('email', $request->email)->first();

    // Validate credentials
    if (!$user || !Hash::check($request->password, $user->password)) {
      // Record failed attempt
      LoginAttempt::recordFailure($request->email, 'Invalid credentials');

      // Increment rate limit
      RateLimiter::hit($this->throttleKey($request));

      throw ValidationException::withMessages([
        'email' => ['As credenciais informadas estão incorretas.'],
      ]);
    }

    // Check if user is active
    if (!$user->is_active) {
      LoginAttempt::recordFailure($request->email, 'Account inactive');

      throw ValidationException::withMessages([
        'email' => ['Sua conta está inativa. Entre em contato com o suporte.'],
      ]);
    }

    // Check if email is verified
    if (!$user->hasVerifiedEmail()) {
      LoginAttempt::recordFailure($request->email, 'Email not verified');

      return response()->json([
        'message' => 'Por favor, verifique seu email antes de fazer login.',
        'needs_verification' => true,
      ], 403);
    }

    // Check if 2FA is enabled
    if ($user->hasTwoFactorEnabled()) {
      // Store user ID in session for 2FA verification
      session(['2fa_user_id' => $user->id]);

      return response()->json([
        'message' => 'Verificação em duas etapas necessária.',
        'requires_2fa' => true,
      ]);
    }

    // Clear rate limiter
    RateLimiter::clear($this->throttleKey($request));

    // Perform login
    Auth::login($user, $request->boolean('remember'));

    // Regenerate session
    $request->session()->regenerate();

    // Record successful login
    LoginAttempt::recordSuccess($user);
    $user->recordLogin($request->ip());

    // Log activity
    //ActivityLog::log('login', 'Usuário fez login via email/senha');
    try {
      ActivityLog::log('login', 'Usuário fez login via email/senha');
    } catch (\Exception $e) {
      logger('Erro ao salvar log de atividade: ' . $e->getMessage());
    }

    // Get user data with relationships
    $user->load(['company', 'modules']);

    return response()->json([
      'message' => 'Login realizado com sucesso!',
      'user' => $user,
      'redirect' => $user->company_id ? '/dashboard' : '/personal/tasks',
    ]);
  }

  /**
   * Handle login with phone number.
   */
  public function loginWithPhone(Request $request)
  {
    $request->validate([
      'phone' => 'required|string',
    ]);

    // Clean phone number
    $phone = preg_replace('/\D/', '', $request->phone);

    // Find user by phone
    $user = User::where('phone', $phone)->first();

    if (!$user) {
      throw ValidationException::withMessages([
        'phone' => ['Número de telefone não encontrado.'],
      ]);
    }

    // Generate and send OTP
    $otp = \App\Models\OtpCode::generate($phone, 'login', $user);

    // Here you would send the OTP via SMS
    // For now, we'll return it in development
    if (app()->environment('local')) {
      return response()->json([
        'message' => 'Código enviado!',
        'otp' => $otp->code, // Remove this in production
        'expires_in' => 600, // 10 minutes
      ]);
    }

    return response()->json([
      'message' => 'Um código foi enviado para seu telefone.',
      'expires_in' => 600,
    ]);
  }

  /**
   * Logout the user.
   */
  public function logout(Request $request)
  {
    $user = Auth::user();

    // Log activity
    if ($user) {
      //ActivityLog::log('logout', 'Usuário fez logout');
    }

    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return response()->json([
      'message' => 'Logout realizado com sucesso!',
    ]);
  }

  /**
   * Check rate limiting.
   */
  protected function checkRateLimit(Request $request)
  {
    $key = $this->throttleKey($request);

    if (RateLimiter::tooManyAttempts($key, 5)) {
      $seconds = RateLimiter::availableIn($key);

      throw ValidationException::withMessages([
        'email' => ["Muitas tentativas de login. Tente novamente em {$seconds} segundos."],
      ]);
    }
  }

  /**
   * Get the rate limiting throttle key.
   */
  protected function throttleKey(Request $request): string
  {
    return strtolower($request->input('email')) . '|' . $request->ip();
  }
}
