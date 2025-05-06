<?php

namespace Database\Seeders;

use App\Models\University;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UniversitiesSeeder extends Seeder
{
    public function run(): void
    {
        $universities = [
            [
                'id' => Str::uuid(),
                'pt_code' => 'UNIV002',
                'name' => 'Universitas Indonesia',
                'address' => 'Kampus UI, Depok, Jawa Barat',
                'website' => 'https://ui.ac.id',
            ],
            [
                'id' => Str::uuid(),
                'pt_code' => 'UNIV003',
                'name' => 'Universitas Gadjah Mada',
                'address' => 'Jl. Sosio Humaniora No.1, Bulaksumur',
                'website' => 'https://ugm.ac.id',
            ],
            [
                'id' => Str::uuid(),
                'pt_code' => 'UNIV004',
                'name' => 'Institut Teknologi Bandung',
                'address' => 'Jl. Ganesha No.10, Bandung',
                'website' => 'https://itb.ac.id',
            ],
            [
                'id' => Str::uuid(),
                'pt_code' => 'UNIV005',
                'name' => 'Universitas Airlangga',
                'address' => 'Kampus C Mulyorejo, Surabaya',
                'website' => 'https://unair.ac.id',
            ],
            [
                'id' => Str::uuid(),
                'pt_code' => 'UNIV006',
                'name' => 'Institut Pertanian Bogor',
                'address' => 'Jl. Raya Dramaga, Bogor',
                'website' => 'https://ipb.ac.id',
            ],
        ];

        foreach ($universities as $university) {
            University::firstOrCreate(['pt_code' => $university['pt_code']], $university);
        }
    }
}
