<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User();
        $user->name = "Wilson Ladino";
        $user->email = "wilsonladino@vistainmobiliariasas.com";
        $user->password = Hash::make("wilson2017");
        $user->save();
    }
}
