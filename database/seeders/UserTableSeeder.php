<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'admin@demo.com',
            'password' => app('hash')->make('admin'),
            'role' =>'admin',
            'remember_token' => str_random(10),
        ]);
    }
}
