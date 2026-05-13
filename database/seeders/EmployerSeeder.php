<?php

namespace Database\Seeders;

use App\Enums\JobStatus;
use App\Enums\UserRole;
use App\Models\JobListing;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EmployerSeeder extends Seeder
{
    public function run(): void
    {
        $employers = [
            [
                'user' => [
                    'name'  => 'BuildRight Construction Ltd',
                    'email' => 'hiring@buildright.test',
                ],
                'jobs' => [
                    [
                        'title'                   => 'Senior Electrician – Commercial Fit-Out',
                        'description'             => 'We need a qualified electrician for a major office fit-out in Canary Wharf. Must be 18th Edition qualified and hold a valid ECS card. Work involves first and second fix electrical installation across 6 floors.',
                        'trade'                   => 'Electrician',
                        'required_skills'         => ['electrical wiring', 'conduit installation', 'cable management', 'fault diagnosis'],
                        'required_certifications' => ['City & Guilds 2382 (18th Edition)', 'ECS Card'],
                        'location'                => 'Canary Wharf, London',
                        'latitude'                => 51.5055,
                        'longitude'               => -0.0230,
                        'hourly_rate'             => 30.00,
                        'start_date'              => '2026-06-01 08:00:00',
                        'end_date'                => '2026-09-30 17:00:00',
                        'workers_needed'          => 3,
                        'status'                  => JobStatus::Active,
                    ],
                    [
                        'title'                   => 'Scaffolding Team Lead – High-Rise Residential',
                        'description'             => 'Seeking an advanced scaffolder to lead a 5-person crew on a 20-storey residential tower in East London. CISRS Advanced card mandatory. IPAF licence preferred.',
                        'trade'                   => 'Scaffolder',
                        'required_skills'         => ['tube and fitting', 'system scaffolding', 'risk assessment', 'load calculations'],
                        'required_certifications' => ['CISRS Advanced Scaffolding Card'],
                        'location'                => 'Stratford, London',
                        'latitude'                => 51.5420,
                        'longitude'               => -0.0030,
                        'hourly_rate'             => 24.00,
                        'start_date'              => '2026-06-15 06:00:00',
                        'end_date'                => '2026-12-15 16:00:00',
                        'workers_needed'          => 1,
                        'status'                  => JobStatus::Active,
                    ],
                    [
                        'title'                   => 'Bricklayers – New-Build Housing Scheme',
                        'description'             => 'Multiple bricklayers required for a 120-unit new-build housing scheme in Newham. Cavity wall and blockwork experience essential. CSCS card required.',
                        'trade'                   => 'Bricklayer',
                        'required_skills'         => ['cavity wall construction', 'blockwork', 'brick cutting'],
                        'required_certifications' => ['CSCS Blue Card'],
                        'location'                => 'Newham, London',
                        'latitude'                => 51.5080,
                        'longitude'               => 0.0390,
                        'hourly_rate'             => 21.00,
                        'start_date'              => '2026-07-01 07:30:00',
                        'end_date'                => '2027-01-31 17:30:00',
                        'workers_needed'          => 8,
                        'status'                  => JobStatus::Active,
                    ],
                ],
            ],
            [
                'user' => [
                    'name'  => 'Meridian Facilities Management',
                    'email' => 'ops@meridianfm.test',
                ],
                'jobs' => [
                    [
                        'title'                   => 'HVAC Engineer – Data Centre Cooling',
                        'description'             => 'F-Gas certified HVAC engineer needed for ongoing maintenance and installation work at a Tier-3 data centre in West London. Knowledge of precision cooling and chillers essential.',
                        'trade'                   => 'HVAC Technician',
                        'required_skills'         => ['air conditioning installation', 'refrigeration', 'chillers', 'BMS controls'],
                        'required_certifications' => ['F-Gas Category I'],
                        'location'                => 'Hammersmith, London',
                        'latitude'                => 51.4930,
                        'longitude'               => -0.2240,
                        'hourly_rate'             => 28.00,
                        'start_date'              => '2026-06-01 08:00:00',
                        'end_date'                => '2026-11-30 18:00:00',
                        'workers_needed'          => 2,
                        'status'                  => JobStatus::Active,
                    ],
                    [
                        'title'                   => 'Health & Safety Officer – Infrastructure Project',
                        'description'             => 'NEBOSH-qualified H&S officer needed to oversee safety compliance on a highway improvement scheme. CDM regulations experience required. SMSTS preferred.',
                        'trade'                   => 'Health & Safety Officer',
                        'required_skills'         => ['risk assessment', 'CDM regulations', 'method statements', 'site induction'],
                        'required_certifications' => ['NEBOSH National General Certificate', 'SMSTS'],
                        'location'                => 'Southwark, London',
                        'latitude'                => 51.5040,
                        'longitude'               => -0.0810,
                        'hourly_rate'             => 33.00,
                        'start_date'              => '2026-06-01 08:00:00',
                        'end_date'                => '2026-10-31 17:00:00',
                        'workers_needed'          => 1,
                        'status'                  => JobStatus::Active,
                    ],
                    [
                        'title'                   => 'Plumber – Commercial Washroom Refurbishment',
                        'description'             => 'Experienced plumber required for a full washroom refurbishment across a 10-floor office building. Bathroom installation and pump systems experience required.',
                        'trade'                   => 'Plumber',
                        'required_skills'         => ['pipework', 'bathroom installation', 'pump systems'],
                        'required_certifications' => ['CSCS Blue Card'],
                        'location'                => 'Lewisham, London',
                        'latitude'                => 51.4615,
                        'longitude'               => -0.0125,
                        'hourly_rate'             => 25.00,
                        'start_date'              => '2026-07-01 07:00:00',
                        'end_date'                => '2026-10-31 17:00:00',
                        'workers_needed'          => 2,
                        'status'                  => JobStatus::Active,
                    ],
                ],
            ],
            [
                'user' => [
                    'name'  => 'Apex Civil Engineering plc',
                    'email' => 'recruitment@apexcivil.test',
                ],
                'jobs' => [
                    [
                        'title'                   => 'Chartered Civil Engineer – Highways',
                        'description'             => 'Senior civil engineer needed to oversee earthworks, drainage, and concrete structures on a major A-road improvement project. ICE membership required.',
                        'trade'                   => 'Civil Engineer',
                        'required_skills'         => ['earthworks', 'drainage', 'road construction', 'concrete works', 'site supervision'],
                        'required_certifications' => ['ICE Chartered Engineer', 'SMSTS'],
                        'location'                => 'Southwark, London',
                        'latitude'                => 51.5030,
                        'longitude'               => -0.0800,
                        'hourly_rate'             => 38.00,
                        'start_date'              => '2026-06-01 08:00:00',
                        'end_date'                => '2026-12-31 17:00:00',
                        'workers_needed'          => 1,
                        'status'                  => JobStatus::Active,
                    ],
                    [
                        'title'                   => 'Coded Welder – Structural Steelwork',
                        'description'             => 'Coded MIG/TIG welder required for structural steel connections on a new rail station canopy. CSWIP qualification essential.',
                        'trade'                   => 'Welder',
                        'required_skills'         => ['MIG welding', 'TIG welding', 'structural steel'],
                        'required_certifications' => ['CSWIP 3.1 Welding Inspector'],
                        'location'                => 'Dagenham, London',
                        'latitude'                => 51.5400,
                        'longitude'               => 0.1440,
                        'hourly_rate'             => 26.00,
                        'start_date'              => '2026-06-15 06:00:00',
                        'end_date'                => '2026-09-30 16:00:00',
                        'workers_needed'          => 4,
                        'status'                  => JobStatus::Active,
                    ],
                    [
                        'title'                   => 'EV Infrastructure Electrician',
                        'description'             => 'Looking for an electrician with specific EV charger installation experience for a fleet depot in South East London. Smart systems knowledge a plus.',
                        'trade'                   => 'Electrician',
                        'required_skills'         => ['EV charger installation', 'electrical wiring', 'testing & inspection'],
                        'required_certifications' => ['City & Guilds 2382 (18th Edition)', 'ECS Card'],
                        'location'                => 'Greenwich, London',
                        'latitude'                => 51.4828,
                        'longitude'               => -0.0079,
                        'hourly_rate'             => 32.00,
                        'start_date'              => '2026-07-01 08:00:00',
                        'end_date'                => '2026-09-30 17:00:00',
                        'workers_needed'          => 2,
                        'status'                  => JobStatus::Active,
                    ],
                ],
            ],
        ];

        foreach ($employers as $data) {
            $employer = User::updateOrCreate(
                ['email' => $data['user']['email']],
                array_merge($data['user'], [
                    'password'          => Hash::make('Employer@1234'),
                    'role'              => UserRole::Employer,
                    'email_verified_at' => now(),
                ])
            );

            foreach ($data['jobs'] as $job) {
                JobListing::updateOrCreate(
                    ['employer_id' => $employer->id, 'title' => $job['title']],
                    array_merge($job, ['employer_id' => $employer->id])
                );
            }
        }

        $this->command->info('Seeded 3 employers with ' . collect($employers)->sum(fn ($e) => count($e['jobs'])) . ' job listings.');
    }
}
