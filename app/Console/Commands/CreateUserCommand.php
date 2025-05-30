<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CreateUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-user-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = User::create([
            'email' => 'test@test.com',
            'password' => bcrypt('password'),
            'name' => 'Test',
        ]);
        $token = $user->createToken('API Token')->plainTextToken;
        $this->info($token);
    }
}
