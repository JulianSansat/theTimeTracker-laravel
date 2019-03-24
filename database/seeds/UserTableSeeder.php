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
            'first_name'   => 'admin',
            'last_name'    => 'admin',
            'email'        => 'admin@timetracker.com',
            'password'     => bcrypt('123456'),
            'usergroup_id' => 1
        ]);
    }
}
