<?php

use Illuminate\Database\Seeder;

class UsergroupTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	factory(App\Usergroup::class)->states('admin')->create();

    	factory(App\Usergroup::class)->states('public')->create();
    }
}
