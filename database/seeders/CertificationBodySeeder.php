<?php

namespace Database\Seeders;

use App\Models\CertificationBody;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CertificationBodySeeder extends Seeder
{
    public function run(): void
    {
        $bodies = [
            ['name' => 'City & Guilds',                                      'abbreviation' => 'C&G'],
            ['name' => 'EAL',                                                 'abbreviation' => 'EAL'],
            ['name' => 'NAPIT',                                               'abbreviation' => 'NAPIT'],
            ['name' => 'Blue Flame Certification',                            'abbreviation' => 'BFC'],
            ['name' => 'Chartered Institute of Plumbing and Heating Engineering', 'abbreviation' => 'CIPHE'],
            ['name' => 'Arboricultural Association',                          'abbreviation' => null],
            ['name' => 'CABWI Awarding Body',                                 'abbreviation' => 'CABWI'],
            ['name' => 'Lantra',                                              'abbreviation' => null],
            ['name' => 'Construction Skills Certification Scheme',            'abbreviation' => 'SCS'],
            ['name' => 'Construction Plant Competence Scheme',                'abbreviation' => 'CPCS'],
            ['name' => 'National Plant Operators Registration Scheme',        'abbreviation' => 'NPORS'],
            ['name' => 'Federation of Master Builders',                       'abbreviation' => 'FMB'],
            ['name' => 'United Kingdom Accreditation Service',                'abbreviation' => 'UKAS'],
            ['name' => 'Ofqual',                                              'abbreviation' => null],
            ['name' => 'TrustMark',                                           'abbreviation' => null],
            ['name' => 'British Board of Agrément',                           'abbreviation' => 'BBA'],
        ];

        foreach ($bodies as $body) {
            CertificationBody::firstOrCreate(
                ['slug' => Str::slug($body['name'])],
                array_merge($body, ['slug' => Str::slug($body['name']), 'is_active' => true])
            );
        }
    }
}
