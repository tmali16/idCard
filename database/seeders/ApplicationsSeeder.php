<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ApplicationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $er = [
            [
                'username'=>'admin',
                'password'=>'$2a$10$f1rPr36gem6yrM1y1uERWOhMQlduENNNrgM6mIhWz/D/02wFQaV1K',
                'full_name'=>'admin',
                'is_active'=>true,
                'rules'=>5,
                'expire_at'=>null,
            ],[
                'username'=>'700',
                'password'=>'$2a$10$f1rPr36gem6yrM1y1uERWOhMQlduENNNrgM6mIhWz/D/02wFQaV1K',
                'full_name'=>'head',
                'is_active'=>true,
                'rules'=>0,
                'expire_at'=>null,
            ],
        ];
        foreach ($er as $u) {
            if (User::where('username', $u['username'])->count() == 0){
                User::create($u);
            }
        }
    }
}
