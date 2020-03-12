<?php

use Illuminate\Database\Seeder;

class UserTestingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Super User Creation
        factory(App\User::class)->create([
            'id'=> 1,
            'first_name'=>'SuperUser',
            'last_name'=>'Tester',
            'email'=> 'SuperUser.Tester@yourcompany.com',
            'visibility'=>'global',
            'password'=>bcrypt('Test')
        ])->each(function($user) {
            $user->roles()->attach(['role_id'=>1]);
        });
    }
}