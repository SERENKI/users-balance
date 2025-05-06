<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AddUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:add {login} {email} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $validator = Validator::make($this->arguments(), [
            'login'         => ['required'],
            'email'         => ['required', 'email'],
            'password'      => ['required']
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        try {
            DB::transaction(function () {
                $user = User::create([
                    'name'      => $this->argument('login'),
                    'login'     => $this->argument('login'),
                    'email'     => $this->argument('email'),
                    'password'  => Hash::make($this->argument('password'))
                ]);

                $user->balance()->create([
                    'amount' => 0
                ]);
            });

            $this->info('User and balance created successfully!');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("Error: {$e->getMessage()}");

            return self::FAILURE;
        }
    }
}
