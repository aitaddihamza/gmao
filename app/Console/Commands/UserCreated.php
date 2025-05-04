<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class UserCreated extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create {name} {role}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'creates user ';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $role = $this->argument('role');
        $prenom = Str::random(10);
        $telephone = rand(1000000000, 9999999999);
        $email = $name . "@gmail.com";

        $user = new User([
            'name' => $name,
            'prenom' => $prenom,
            'telephone' => $telephone,
            'role' => $role,
            'email' => $email,
            'password' => bcrypt('password')
        ]);


        $user->save();

        return $this->info("User {$name}, email: {$email} created with role {$role}");
    }
}
