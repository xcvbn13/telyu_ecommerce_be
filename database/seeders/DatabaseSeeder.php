<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserType;
use App\Models\StatusOrder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        
        // user type 
        UserType::create([
            'name' => 'Admin',
            'description' => 'admin',
        ]);
        UserType::create([
            'name' => 'User',
            'description' => 'pengguna_umum',
        ]);
        // user 
        User::create([
            'name' => 'Admin',
            'alamat' => 'xxxxxxxxxx',
            'no_telp' => '000000000',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
            'user_type_id' => '1',
        ]);

        User::create([
            'name' => 'User',
            'alamat' => 'xxxxxxxxxx',
            'no_telp' => '000000000',
            'email' => 'biasa@biasa.com',
            'password' => bcrypt('password'),
            'user_type_id' => '2',
        ]);

        // status order 
        StatusOrder::create([
            'status'=>'Menunggu Pembayaran',
        ]);
        StatusOrder::create([
            'status'=>'Menunggu Verifikasi',
        ]);
        StatusOrder::create([
            'status'=>'Dibatalkan',
        ]);
        StatusOrder::create([
            'status'=>'Waktu Pembayaran Habis',
        ]);
        StatusOrder::create([
            'status'=>'Verifikasi Gagal',
        ]);
        StatusOrder::create([
            'status'=>'Selesai',
        ]);

    }
}
