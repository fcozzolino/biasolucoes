<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\User;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::create([
            'name' => 'Empresa Teste',
            'slug' => 'empresa-teste',
            'status' => 'trial',
            'trial_ends_at' => now()->addDays(30),
            'plan_id' => 1,
        ]);

        User::create([
            'name' => 'Admin Teste',
            'email' => 'admin@teste.com',
            'password' => bcrypt('senha123'), // Senha criptografada
            'company_id' => $company->id,
        ]);
    }
}
