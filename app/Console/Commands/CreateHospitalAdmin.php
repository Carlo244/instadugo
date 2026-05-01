<?php

namespace App\Console\Commands;

use App\Models\HospitalAdmin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateHospitalAdmin extends Command
{
    protected $signature = 'hospital:create-admin
                            {hospital_name? : Hospital name}
                            {email? : Hospital admin email}
                            {contact? : Contact number}
                            {address? : Hospital address}
                            {--phlebotomists=1 : Number of phlebotomists on duty}
                            {--password= : Password for the hospital admin}
                            {--confirm-password= : Confirmation for the password}';

    protected $description = 'Create a hospital admin account (programmer-only via CLI)';

    public function handle(): int
    {
        $hospitalName = (string) ($this->argument('hospital_name') ?: $this->ask('Hospital name'));
        $email = (string) ($this->argument('email') ?: $this->ask('Email'));
        $contact = (string) ($this->argument('contact') ?: $this->ask('Contact number'));
        $address = (string) ($this->argument('address') ?: $this->ask('Address'));
        $phlebotomistCount = (int) $this->option('phlebotomists');
        $password = (string) ($this->option('password') ?: $this->secret('Password'));
        $confirmPassword = (string) ($this->option('confirm-password') ?: $this->secret('Confirm password'));

        if ($phlebotomistCount < 1) {
            $this->error('Phlebotomist count must be at least 1.');
            return self::FAILURE;
        }

        if (HospitalAdmin::where('email', $email)->exists()) {
            $this->error('A hospital admin with this email already exists.');
            return self::FAILURE;
        }

        if ($password === '' || $confirmPassword === '') {
            $this->error('Password and confirmation are required.');
            return self::FAILURE;
        }

        if ($password !== $confirmPassword) {
            $this->error('Passwords do not match.');
            return self::FAILURE;
        }

        $hospital = HospitalAdmin::create([
            'hospital_name' => $hospitalName,
            'email' => $email,
            'password' => Hash::make($password),
            'contact' => $contact,
            'address' => $address,
            'phlebotomist_count' => $phlebotomistCount,
        ]);

        $this->info('Hospital admin created successfully.');
        $this->line('ID: ' . $hospital->id);
        $this->line('Hospital: ' . $hospital->hospital_name);
        $this->line('Email: ' . $hospital->email);

        return self::SUCCESS;
    }
}
