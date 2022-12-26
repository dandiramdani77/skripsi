<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('setting')->insert([
            'id_setting' => 1,
            'nama_perusahaan' => 'Distributor Nibras',
            'alamat' => 'Jl. Jl. Pd. Aren/Ceger Raya No.9, Pd. Aren, Kec. Pd. Aren, Kota Tangerang Selatan, Banten 15224',
            'telepon' => '08961377331',
            'tipe_nota' => 1, // kecil
            'diskon' => 5,
            'path_logo' => '/img/logo-nibras.png',
            'path_kartu_member' => '/img/member.png',
        ]);
    }
}
