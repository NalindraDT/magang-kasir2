<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'username' => 'dimas',
            'email'    => 'dimas@gmail.com',
            'password' => password_hash('dimas123', PASSWORD_DEFAULT),
        ];

        $this->db->table('users')->insert($data);
    }
}