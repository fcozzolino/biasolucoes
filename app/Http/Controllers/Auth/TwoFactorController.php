<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\LoginAttempt;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;


class TwoFactorController extends Controller
{
    protected $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    /**
     * Enable 2FA - show QR code.
     */
    public function enable(Request $request)
    {
        $user = Auth::user();

        if ($user->hasTwoFactorEnabled()) {
            return response()->json([
                'message' => 'Autenticação de dois fatores já está ativada.',
            ], 400);
        }

        // Generate secret
        $secret = $this->google2fa->generateSecretKey();

        // Temporarily store the secret
        $user->update([
            'two_factor_secret' => Crypt::encryptString($secret),
        ]);

        // Generate QR code
        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        // Generate QR code image
        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $qrCode = $writer->writeString($qrCodeUrl);

        // Generate recovery codes
        $recoveryCodes = $this->generateRecoveryCodes();

        return response()->json([
            'secret' => $secret,
            'qr_code' => 'data:image/svg+xml;base64,' . base64_encode($qrCode),
            'recovery_codes' => $recoveryCodes,
            'message' => 'Escaneie o QR code com seu aplicativo autenticador.',
        ]);
    }

    /**
     * Confirm 2FA enable.
     */
    public function confirmEnable(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = Auth::user();

        if (!$user->two_factor_secret) {
            return response()->json([
                'message' => 'Nenhuma configuração 2FA pendente.',
            ], 400);
        }

        $secret = Crypt::decryptString($user->two_factor_secret);
        $valid = $this->google2fa->verifyKey($secret, $request->code);

        if (!$valid) {
            throw ValidationException::withMessages([
                'code' => ['Código inválido. Tente novamente.'],
            ]);
        }

        // Enable 2FA
        $recoveryCodes = $this->generateRecoveryCodes();

        $user->update([
            'two_factor_enabled' => true,
            'two_factor_recovery_codes' => Crypt::encryptString(json_encode($recoveryCodes)),
        ]);

        // Log activity
        ActivityLog::log('2fa_enabled', 'Autenticação de dois fatores ativada');

        return response()->json([
            'message' => '2FA ativado com sucesso!',
            'recovery_codes' => $recoveryCodes,
        ]);
    }

    /**
     * Disable 2FA.
     */
    public function disable(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = Auth::user();

        if (!$user->hasTwoFactorEnabled()) {
            return response()->json([
                'message' => '2FA não está ativado.',
            ], 400);
        }

        // Verify password
        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['Senha incorreta.'],
            ]);
        }

        // Disable 2FA
        $user->update([
            'two_factor_enabled' => false,
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
        ]);

        // Log activity
        ActivityLog::log('2fa_disabled', 'Autenticação de dois fatores desativada');

        return response()->json([
            'message' => '2FA desativado com sucesso!',
        ]);
    }

    /**
     * Verify 2FA code during login.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $userId = session('2fa_user_id');

        if (!$userId) {
            return response()->json([
                'message' => 'Sessão expirada. Faça login novamente.',
            ], 401);
        }

        $user = User::find($userId);

        if (!$user || !$user->hasTwoFactorEnabled()) {
            return response()->json([
                'message' => 'Configuração inválida.',
            ], 400);
        }

        $code = $request->code;
        $valid = false;

        // Check if it's a recovery code
        if (strlen($code) > 6) {
            $valid = $this->verifyRecoveryCode($user, $code);
        } else {
            // Verify TOTP code
            $secret = Crypt::decryptString($user->two_factor_secret);
            $valid = $this->google2fa->verifyKey($secret, $code);
        }

        if (!$valid) {
            LoginAttempt::recordFailure($user->email, '2FA code invalid');

            throw ValidationException::withMessages([
                'code' => ['Código inválido. Tente novamente.'],
            ]);
        }

        // Clear 2FA session
        session()->forget('2fa_user_id');

        // Login user
        Auth::login($user);
        $request->session()->regenerate();

        // Record successful login
        LoginAttempt::recordSuccess($user);
        $user->recordLogin($request->ip());

        // Log activity
        ActivityLog::log('login', 'Login realizado com 2FA');

        // Load relationships
        $user->load(['company', 'modules']);

        return response()->json([
            'message' => 'Login realizado com sucesso!',
            'user' => $user,
            'redirect' => $user->company_id ? '/dashboard' : '/personal/tasks',
        ]);
    }

    /**
     * Generate new recovery codes.
     */
    public function regenerateRecoveryCodes(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = Auth::user();

        if (!$user->hasTwoFactorEnabled()) {
            return response()->json([
                'message' => '2FA não está ativado.',
            ], 400);
        }

        // Verify password
        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['Senha incorreta.'],
            ]);
        }

        // Generate new codes
        $recoveryCodes = $this->generateRecoveryCodes();

        $user->update([
            'two_factor_recovery_codes' => Crypt::encryptString(json_encode($recoveryCodes)),
        ]);

        // Log activity
        ActivityLog::log('2fa_recovery_codes_regenerated', 'Códigos de recuperação regenerados');

        return response()->json([
            'message' => 'Novos códigos gerados com sucesso!',
            'recovery_codes' => $recoveryCodes,
        ]);
    }

    /**
     * Generate recovery codes.
     */
    protected function generateRecoveryCodes(): array
    {
        $codes = [];

        for ($i = 0; $i < 8; $i++) {
            $codes[] = Str::random(10);
        }

        return $codes;
    }

    /**
     * Verify recovery code.
     */
    protected function verifyRecoveryCode(User $user, string $code): bool
    {
        if (!$user->two_factor_recovery_codes) {
            return false;
        }

        $codes = json_decode(
            Crypt::decryptString($user->two_factor_recovery_codes),
            true
        );

        if (!in_array($code, $codes)) {
            return false;
        }

        // Remove used code
        $codes = array_values(array_diff($codes, [$code]));

        $user->update([
            'two_factor_recovery_codes' => Crypt::encryptString(json_encode($codes)),
        ]);

        // Log activity
        ActivityLog::log('2fa_recovery_code_used', 'Código de recuperação usado');

        return true;
    }
}
