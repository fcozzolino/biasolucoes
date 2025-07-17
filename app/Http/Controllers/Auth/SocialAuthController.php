<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SocialAccount;
use App\Models\LoginAttempt;
use App\Models\ActivityLog;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * Redirect to provider.
     */
    public function redirect(string $provider)
    {
        $this->validateProvider($provider);

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle provider callback.
     */
    public function callback(string $provider)
    {
        $this->validateProvider($provider);

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Erro ao autenticar com ' . $provider);
        }

        DB::beginTransaction();

        try {
            // Check if social account exists
            $socialAccount = SocialAccount::where('provider', $provider)
                ->where('provider_user_id', $socialUser->getId())
                ->first();

            if ($socialAccount) {
                // Existing social account - login
                $user = $socialAccount->user;

                // Update tokens
                $socialAccount->updateTokens([
                    'token' => $socialUser->token,
                    'refreshToken' => $socialUser->refreshToken ?? null,
                    'expiresIn' => $socialUser->expiresIn ?? null,
                ]);

                // Update user info if needed
                if ($socialUser->getAvatar() && $socialUser->getAvatar() !== $user->profile_photo) {
                    $user->update(['profile_photo' => $socialUser->getAvatar()]);
                }

            } else {
                // Check if user exists with same email
                $user = User::where('email', $socialUser->getEmail())->first();

                if ($user) {
                    // Link social account to existing user
                    $this->createSocialAccount($user, $provider, $socialUser);
                } else {
                    // Create new user
                    $user = $this->createUserFromSocialite($provider, $socialUser);
                }
            }

            // Check if user is active
            if (!$user->is_active) {
                DB::rollBack();
                return redirect('/login')->with('error', 'Sua conta está inativa.');
            }

            // Login user
            Auth::login($user, true);
            request()->session()->regenerate();

            // Record successful login
            LoginAttempt::recordSuccess($user, 'social', $provider);
            $user->recordLogin(request()->ip());

            // Log activity
            ActivityLog::log('login', "Login realizado via {$provider}");

            DB::commit();

            // Redirect based on account type
            if ($user->company_id) {
                return redirect('/dashboard');
            } else {
                return redirect('/personal/tasks');
            }

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect('/login')->with('error', 'Erro ao processar autenticação.');
        }
    }

    /**
     * Unlink social account.
     */
    public function unlink(Request $request, string $provider)
    {
        $this->validateProvider($provider);

        $user = Auth::user();

        // Check if user has password (needs at least one login method)
        $socialAccountsCount = $user->socialAccounts()->count();

        if (!$user->password && $socialAccountsCount <= 1) {
            return response()->json([
                'message' => 'Você precisa definir uma senha antes de desvincular sua única forma de login.',
            ], 400);
        }

        // Remove social account
        $deleted = $user->socialAccounts()
            ->where('provider', $provider)
            ->delete();

        if ($deleted) {
            // Log activity
            ActivityLog::log('social_unlinked', "Conta {$provider} desvinculada");

            return response()->json([
                'message' => "Conta {$provider} desvinculada com sucesso!",
            ]);
        }

        return response()->json([
            'message' => 'Conta social não encontrada.',
        ], 404);
    }

    /**
     * Create user from socialite data.
     */
    protected function createUserFromSocialite(string $provider, $socialUser): User
    {
        $user = User::create([
            'name' => $socialUser->getName(),
            'email' => $socialUser->getEmail(),
            'email_verified_at' => now(),
            'password' => null, // No password for social login
            'profile_photo' => $socialUser->getAvatar(),
            'account_type' => 'personal',
            'is_active' => true,
        ]);

        // Create social account
        $this->createSocialAccount($user, $provider, $socialUser);

        // Give access to free Tasks module
        $tasksModule = Module::where('slug', 'tarefas')->first();

        if ($tasksModule) {
            $user->modules()->attach($tasksModule->id, [
                'is_active' => true,
                'activated_at' => now(),
                'settings' => [
                    'boards_limit' => 5,
                    'cards_per_board' => 100,
                ],
            ]);
        }

        // Log activity
        ActivityLog::logForModel($user, 'register', "Novo usuário via {$provider}");

        return $user;
    }

    /**
     * Create social account record.
     */
    protected function createSocialAccount(User $user, string $provider, $socialUser): SocialAccount
    {
        return SocialAccount::create([
            'user_id' => $user->id,
            'provider' => $provider,
            'provider_user_id' => $socialUser->getId(),
            'name' => $socialUser->getName(),
            'email' => $socialUser->getEmail(),
            'avatar' => $socialUser->getAvatar(),
            'token' => $socialUser->token,
            'refresh_token' => $socialUser->refreshToken ?? null,
            'token_expires_at' => isset($socialUser->expiresIn)
                ? now()->addSeconds($socialUser->expiresIn)
                : null,
        ]);
    }

    /**
     * Validate provider.
     */
    protected function validateProvider(string $provider): void
    {
        if (!in_array($provider, ['google', 'microsoft', 'github'])) {
            abort(404, 'Provider não suportado.');
        }
    }
}
