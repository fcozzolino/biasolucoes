<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use App\Models\Module;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    /**
     * Show the registration form.
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle user registration - step 1.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
            'phone' => 'nullable|string|max:20',
            'account_type' => 'required|in:personal,business',
            'terms' => 'accepted',
        ]);

        DB::beginTransaction();

        try {
            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone ? preg_replace('/\D/', '', $request->phone) : null,
                'account_type' => $request->account_type,
                'is_active' => true,
            ]);

            // If personal account, give access to free Tasks module
            if ($request->account_type === 'personal') {
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
            }

            // Send email verification
            $user->sendEmailVerificationNotification();

            // Log activity
            ActivityLog::logForModel($user, 'register', 'Novo usuário registrado');

            DB::commit();

            // Auto login
            Auth::login($user);

            return response()->json([
                'message' => 'Cadastro realizado com sucesso!',
                'user' => $user,
                'next_step' => $request->account_type === 'business' ? 'company_setup' : 'email_verification',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Erro ao criar conta. Tente novamente.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle company setup - step 2 for business accounts.
     */
    public function setupCompany(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'cnpj' => 'nullable|string|max:20',
            'company_phone' => 'nullable|string|max:20',
            'plan_id' => 'required|exists:plans,id',
        ]);

        $user = Auth::user();

        if ($user->account_type !== 'business' || $user->company_id) {
            return response()->json([
                'message' => 'Usuário não pode criar empresa.',
            ], 403);
        }

        DB::beginTransaction();

        try {
            // Create company
            $company = Company::create([
                'name' => $request->company_name,
                'slug' => Str::slug($request->company_name),
                'cnpj' => $request->cnpj ? preg_replace('/\D/', '', $request->cnpj) : null,
                'phone' => $request->company_phone,
                'status' => 'active',
                'trial_ends_at' => now()->addDays(30), // 30 days trial
                'plan_id' => $request->plan_id,
            ]);

            // Add user as owner
            $company->addUser($user, 'owner');

            // Update user with company
            $user->update(['company_id' => $company->id]);

            // Log activity
            ActivityLog::logForModel($company, 'company_created', 'Nova empresa criada');

            DB::commit();

            return response()->json([
                'message' => 'Empresa criada com sucesso!',
                'company' => $company,
                'next_step' => 'module_selection',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Erro ao criar empresa. Tente novamente.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle module selection - step 3 for business accounts.
     */
    public function selectModules(Request $request)
    {
        $request->validate([
            'modules' => 'required|array',
            'modules.*' => 'exists:modules,id',
        ]);

        $user = Auth::user();
        $company = $user->company;

        if (!$company || !$user->company_id) {
            return response()->json([
                'message' => 'Empresa não encontrada.',
            ], 404);
        }

        DB::beginTransaction();

        try {
            // Activate selected modules for the company
            foreach ($request->modules as $moduleId) {
                $module = Module::find($moduleId);

                if ($module && $module->type !== 'personal') {
                    $company->activateModule($module);
                }
            }

            // Log activity
            ActivityLog::log('modules_selected', 'Módulos selecionados para a empresa');

            DB::commit();

            return response()->json([
                'message' => 'Módulos ativados com sucesso!',
                'redirect' => '/dashboard',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Erro ao ativar módulos. Tente novamente.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Resend verification email.
     */
    public function resendVerification(Request $request)
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email já verificado.',
            ]);
        }

        $user->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'Email de verificação reenviado!',
        ]);
    }
}
