<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Certification;
use App\Models\User;
use App\Models\WorkerProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class WorkerSeeder extends Seeder
{
    public function run(): void
    {
        $workers = [
            [
                'user' => [
                    'name'  => 'James Okafor',
                    'email' => 'james.okafor@workers.test',
                ],
                'profile' => [
                    'trade'          => 'Electrician',
                    'skills'         => ['electrical wiring', 'solar panel installation', 'fault diagnosis', 'conduit installation', 'cable management'],
                    'location'       => 'Canary Wharf, London',
                    'latitude'       => 51.5051,
                    'longitude'      => -0.0235,
                    'hourly_rate'    => 32.00,
                    'bio'            => 'City & Guilds qualified electrician with 8 years in commercial fit-outs.',
                    'is_available'   => true,
                    'average_rating' => 4.8,
                    'total_jobs_completed' => 94,
                    'availability_schedule' => ['mon' => '08:00-18:00', 'tue' => '08:00-18:00', 'wed' => '08:00-18:00', 'thu' => '08:00-18:00', 'fri' => '08:00-17:00'],
                ],
                'certifications' => [
                    ['name' => 'City & Guilds 2382 (18th Edition)', 'issuing_body' => 'City & Guilds', 'certificate_number' => 'CG-2382-08241', 'issued_at' => '2022-03-01', 'expires_at' => '2027-03-01', 'is_verified' => true],
                    ['name' => 'CSCS Gold Card', 'issuing_body' => 'CSCS', 'certificate_number' => 'CSCS-G-44521', 'issued_at' => '2021-06-15', 'expires_at' => '2026-06-15', 'is_verified' => true],
                    ['name' => 'ECS Card', 'issuing_body' => 'JIB', 'certificate_number' => 'ECS-78901', 'issued_at' => '2021-06-15', 'expires_at' => '2026-06-15', 'is_verified' => true],
                ],
            ],
            [
                'user' => [
                    'name'  => 'Fatima Al-Hassan',
                    'email' => 'fatima.alhassan@workers.test',
                ],
                'profile' => [
                    'trade'          => 'Plumber',
                    'skills'         => ['pipework', 'central heating', 'gas fitting', 'bathroom installation', 'leak detection'],
                    'location'       => 'Stratford, London',
                    'latitude'       => 51.5416,
                    'longitude'      => -0.0026,
                    'hourly_rate'    => 28.50,
                    'bio'            => 'Gas Safe registered plumber specialising in residential and light commercial projects.',
                    'is_available'   => true,
                    'average_rating' => 4.6,
                    'total_jobs_completed' => 67,
                    'availability_schedule' => ['mon' => '07:00-17:00', 'tue' => '07:00-17:00', 'wed' => '07:00-17:00', 'fri' => '07:00-17:00'],
                ],
                'certifications' => [
                    ['name' => 'Gas Safe Registration', 'issuing_body' => 'Gas Safe Register', 'certificate_number' => 'GSR-551234', 'issued_at' => '2023-01-10', 'expires_at' => '2026-01-10', 'is_verified' => true],
                    ['name' => 'CSCS Blue Card', 'issuing_body' => 'CSCS', 'certificate_number' => 'CSCS-B-32110', 'issued_at' => '2022-08-20', 'expires_at' => '2027-08-20', 'is_verified' => true],
                ],
            ],
            [
                'user' => [
                    'name'  => 'David Mensah',
                    'email' => 'david.mensah@workers.test',
                ],
                'profile' => [
                    'trade'          => 'Scaffolder',
                    'skills'         => ['tube and fitting', 'system scaffolding', 'suspended scaffolding', 'risk assessment', 'load calculations'],
                    'location'       => 'Hackney, London',
                    'latitude'       => 51.5450,
                    'longitude'      => -0.0553,
                    'hourly_rate'    => 25.00,
                    'bio'            => 'CISRS Advanced scaffolder with experience on high-rise residential and industrial sites.',
                    'is_available'   => true,
                    'average_rating' => 4.5,
                    'total_jobs_completed' => 112,
                    'availability_schedule' => ['mon' => '06:00-16:00', 'tue' => '06:00-16:00', 'wed' => '06:00-16:00', 'thu' => '06:00-16:00', 'fri' => '06:00-14:00'],
                ],
                'certifications' => [
                    ['name' => 'CISRS Advanced Scaffolding Card', 'issuing_body' => 'CISRS', 'certificate_number' => 'CISRS-ADV-19944', 'issued_at' => '2022-04-01', 'expires_at' => '2027-04-01', 'is_verified' => true],
                    ['name' => 'IPAF Operator Licence', 'issuing_body' => 'IPAF', 'certificate_number' => 'IPAF-OP-7712', 'issued_at' => '2023-05-20', 'expires_at' => '2028-05-20', 'is_verified' => true],
                    ['name' => 'CSCS Gold Card', 'issuing_body' => 'CSCS', 'certificate_number' => 'CSCS-G-88430', 'issued_at' => '2020-09-01', 'expires_at' => '2025-09-01', 'is_verified' => true],
                ],
            ],
            [
                'user' => [
                    'name'  => 'Aisha Bello',
                    'email' => 'aisha.bello@workers.test',
                ],
                'profile' => [
                    'trade'          => 'Health & Safety Officer',
                    'skills'         => ['risk assessment', 'site induction', 'COSHH', 'method statements', 'accident investigation', 'CDM regulations'],
                    'location'       => 'Wembley, London',
                    'latitude'       => 51.5560,
                    'longitude'      => -0.2796,
                    'hourly_rate'    => 35.00,
                    'bio'            => 'NEBOSH-qualified H&S officer with 6 years on major infrastructure projects.',
                    'is_available'   => true,
                    'average_rating' => 4.9,
                    'total_jobs_completed' => 43,
                    'availability_schedule' => ['mon' => '08:00-17:00', 'tue' => '08:00-17:00', 'wed' => '08:00-17:00', 'thu' => '08:00-17:00', 'fri' => '08:00-17:00'],
                ],
                'certifications' => [
                    ['name' => 'NEBOSH National General Certificate', 'issuing_body' => 'NEBOSH', 'certificate_number' => 'NEBOSH-NGC-54321', 'issued_at' => '2021-11-01', 'expires_at' => null, 'is_verified' => true],
                    ['name' => 'SMSTS', 'issuing_body' => 'CITB', 'certificate_number' => 'SMSTS-22001', 'issued_at' => '2023-02-14', 'expires_at' => '2028-02-14', 'is_verified' => true],
                    ['name' => 'CSCS Black Card', 'issuing_body' => 'CSCS', 'certificate_number' => 'CSCS-BLK-12001', 'issued_at' => '2021-11-01', 'expires_at' => '2026-11-01', 'is_verified' => true],
                ],
            ],
            [
                'user' => [
                    'name'  => 'Emeka Nwosu',
                    'email' => 'emeka.nwosu@workers.test',
                ],
                'profile' => [
                    'trade'          => 'Bricklayer',
                    'skills'         => ['cavity wall construction', 'repointing', 'blockwork', 'arches', 'trowel skills'],
                    'location'       => 'Croydon, London',
                    'latitude'       => 51.3762,
                    'longitude'      => -0.0982,
                    'hourly_rate'    => 22.00,
                    'bio'            => 'Experienced bricklayer with a strong portfolio in new-build housing developments.',
                    'is_available'   => false,
                    'average_rating' => 4.2,
                    'total_jobs_completed' => 78,
                    'availability_schedule' => ['mon' => '07:30-17:30', 'tue' => '07:30-17:30', 'wed' => '07:30-17:30', 'thu' => '07:30-17:30'],
                ],
                'certifications' => [
                    ['name' => 'CSCS Blue Card', 'issuing_body' => 'CSCS', 'certificate_number' => 'CSCS-B-90012', 'issued_at' => '2020-03-10', 'expires_at' => '2025-03-10', 'is_verified' => true],
                ],
            ],
            [
                'user' => [
                    'name'  => 'Sofia Reyes',
                    'email' => 'sofia.reyes@workers.test',
                ],
                'profile' => [
                    'trade'          => 'HVAC Technician',
                    'skills'         => ['air conditioning installation', 'ventilation systems', 'refrigeration', 'BMS controls', 'F-gas handling'],
                    'location'       => 'Hammersmith, London',
                    'latitude'       => 51.4927,
                    'longitude'      => -0.2233,
                    'hourly_rate'    => 30.00,
                    'bio'            => 'F-Gas certified HVAC engineer experienced in office fit-outs and data centres.',
                    'is_available'   => true,
                    'average_rating' => 4.7,
                    'total_jobs_completed' => 55,
                    'availability_schedule' => ['mon' => '08:00-18:00', 'tue' => '08:00-18:00', 'thu' => '08:00-18:00', 'fri' => '08:00-18:00'],
                ],
                'certifications' => [
                    ['name' => 'F-Gas Category I', 'issuing_body' => 'City & Guilds', 'certificate_number' => 'FGAS-CAT1-33201', 'issued_at' => '2022-07-01', 'expires_at' => null, 'is_verified' => true],
                    ['name' => 'CSCS Gold Card', 'issuing_body' => 'CSCS', 'certificate_number' => 'CSCS-G-55601', 'issued_at' => '2021-07-01', 'expires_at' => '2026-07-01', 'is_verified' => true],
                ],
            ],
            [
                'user' => [
                    'name'  => 'Patrick O\'Brien',
                    'email' => 'patrick.obrien@workers.test',
                ],
                'profile' => [
                    'trade'          => 'Carpenter',
                    'skills'         => ['first fix', 'second fix', 'formwork', 'timber framing', 'joinery', 'door hanging'],
                    'location'       => 'Islington, London',
                    'latitude'       => 51.5366,
                    'longitude'      => -0.1032,
                    'hourly_rate'    => 24.00,
                    'bio'            => 'Multi-skilled carpenter with 10 years experience across residential and commercial sectors.',
                    'is_available'   => true,
                    'average_rating' => 4.4,
                    'total_jobs_completed' => 130,
                    'availability_schedule' => ['mon' => '07:00-17:00', 'tue' => '07:00-17:00', 'wed' => '07:00-17:00', 'thu' => '07:00-17:00', 'fri' => '07:00-15:00'],
                ],
                'certifications' => [
                    ['name' => 'NVQ Level 3 Carpentry & Joinery', 'issuing_body' => 'City & Guilds', 'certificate_number' => 'NVQ-CARP-70091', 'issued_at' => '2019-09-01', 'expires_at' => null, 'is_verified' => true],
                    ['name' => 'CSCS Blue Card', 'issuing_body' => 'CSCS', 'certificate_number' => 'CSCS-B-70091', 'issued_at' => '2019-09-01', 'expires_at' => '2024-09-01', 'is_verified' => true],
                ],
            ],
            [
                'user' => [
                    'name'  => 'Yemi Adeyemi',
                    'email' => 'yemi.adeyemi@workers.test',
                ],
                'profile' => [
                    'trade'          => 'Electrician',
                    'skills'         => ['electrical wiring', 'EV charger installation', 'testing & inspection', 'smart home systems', 'data cabling'],
                    'location'       => 'Greenwich, London',
                    'latitude'       => 51.4826,
                    'longitude'      => -0.0077,
                    'hourly_rate'    => 34.00,
                    'bio'            => 'Qualified electrician with expertise in EV infrastructure and smart building systems.',
                    'is_available'   => true,
                    'average_rating' => 4.9,
                    'total_jobs_completed' => 61,
                    'availability_schedule' => ['mon' => '08:00-17:00', 'tue' => '08:00-17:00', 'wed' => '08:00-17:00', 'thu' => '08:00-17:00', 'fri' => '08:00-17:00'],
                ],
                'certifications' => [
                    ['name' => 'City & Guilds 2382 (18th Edition)', 'issuing_body' => 'City & Guilds', 'certificate_number' => 'CG-2382-09912', 'issued_at' => '2023-01-15', 'expires_at' => '2028-01-15', 'is_verified' => true],
                    ['name' => 'City & Guilds 2391 (Testing & Inspection)', 'issuing_body' => 'City & Guilds', 'certificate_number' => 'CG-2391-09913', 'issued_at' => '2023-03-10', 'expires_at' => null, 'is_verified' => true],
                    ['name' => 'ECS Card', 'issuing_body' => 'JIB', 'certificate_number' => 'ECS-91100', 'issued_at' => '2023-01-15', 'expires_at' => '2028-01-15', 'is_verified' => true],
                ],
            ],
            [
                'user' => [
                    'name'  => 'Miriam Osei',
                    'email' => 'miriam.osei@workers.test',
                ],
                'profile' => [
                    'trade'          => 'Welder',
                    'skills'         => ['MIG welding', 'TIG welding', 'structural steel', 'pipe welding', 'aluminium welding'],
                    'location'       => 'Dagenham, London',
                    'latitude'       => 51.5399,
                    'longitude'      => 0.1441,
                    'hourly_rate'    => 27.00,
                    'bio'            => 'Coded welder with experience on structural steel and industrial pipework contracts.',
                    'is_available'   => true,
                    'average_rating' => 4.3,
                    'total_jobs_completed' => 88,
                    'availability_schedule' => ['mon' => '06:00-16:00', 'tue' => '06:00-16:00', 'wed' => '06:00-16:00', 'thu' => '06:00-16:00', 'fri' => '06:00-14:00'],
                ],
                'certifications' => [
                    ['name' => 'CSWIP 3.1 Welding Inspector', 'issuing_body' => 'TWI', 'certificate_number' => 'CSWIP-31-44120', 'issued_at' => '2021-10-01', 'expires_at' => '2024-10-01', 'is_verified' => true],
                    ['name' => 'CSCS Blue Card', 'issuing_body' => 'CSCS', 'certificate_number' => 'CSCS-B-44120', 'issued_at' => '2021-10-01', 'expires_at' => '2026-10-01', 'is_verified' => true],
                ],
            ],
            [
                'user' => [
                    'name'  => 'Tunde Fashola',
                    'email' => 'tunde.fashola@workers.test',
                ],
                'profile' => [
                    'trade'          => 'Civil Engineer',
                    'skills'         => ['earthworks', 'drainage', 'road construction', 'concrete works', 'site supervision', 'AutoCAD'],
                    'location'       => 'Southwark, London',
                    'latitude'       => 51.5035,
                    'longitude'      => -0.0804,
                    'hourly_rate'    => 40.00,
                    'bio'            => 'Chartered civil engineer with 12 years on highways and infrastructure projects.',
                    'is_available'   => true,
                    'average_rating' => 5.0,
                    'total_jobs_completed' => 38,
                    'availability_schedule' => ['mon' => '08:00-18:00', 'tue' => '08:00-18:00', 'wed' => '08:00-18:00', 'thu' => '08:00-18:00', 'fri' => '08:00-17:00'],
                ],
                'certifications' => [
                    ['name' => 'ICE Chartered Engineer', 'issuing_body' => 'Institution of Civil Engineers', 'certificate_number' => 'ICE-CE-22001', 'issued_at' => '2020-06-01', 'expires_at' => null, 'is_verified' => true],
                    ['name' => 'CSCS Black Card', 'issuing_body' => 'CSCS', 'certificate_number' => 'CSCS-BLK-22001', 'issued_at' => '2020-06-01', 'expires_at' => '2025-06-01', 'is_verified' => true],
                    ['name' => 'SMSTS', 'issuing_body' => 'CITB', 'certificate_number' => 'SMSTS-19001', 'issued_at' => '2022-09-01', 'expires_at' => '2027-09-01', 'is_verified' => true],
                ],
            ],
            [
                'user' => [
                    'name'  => 'Grace Nkemdirim',
                    'email' => 'grace.nkemdirim@workers.test',
                ],
                'profile' => [
                    'trade'          => 'Painter & Decorator',
                    'skills'         => ['emulsion painting', 'gloss work', 'wallpapering', 'spray painting', 'external rendering'],
                    'location'       => 'Barnet, London',
                    'latitude'       => 51.6525,
                    'longitude'      => -0.1923,
                    'hourly_rate'    => 20.00,
                    'bio'            => 'Decorator with a keen eye for detail; residential and commercial interiors.',
                    'is_available'   => true,
                    'average_rating' => 4.1,
                    'total_jobs_completed' => 145,
                    'availability_schedule' => ['mon' => '08:00-17:00', 'tue' => '08:00-17:00', 'wed' => '08:00-17:00', 'thu' => '08:00-17:00', 'fri' => '08:00-17:00'],
                ],
                'certifications' => [
                    ['name' => 'CSCS Green Card', 'issuing_body' => 'CSCS', 'certificate_number' => 'CSCS-GRN-60031', 'issued_at' => '2022-05-15', 'expires_at' => '2027-05-15', 'is_verified' => true],
                ],
            ],
            [
                'user' => [
                    'name'  => 'Ahmed Khalil',
                    'email' => 'ahmed.khalil@workers.test',
                ],
                'profile' => [
                    'trade'          => 'Scaffolder',
                    'skills'         => ['tube and fitting', 'system scaffolding', 'beamwork', 'loading bay construction', 'inspection'],
                    'location'       => 'Tower Hamlets, London',
                    'latitude'       => 51.5100,
                    'longitude'      => -0.0390,
                    'hourly_rate'    => 23.00,
                    'bio'            => 'CISRS Intermediate scaffolder; comfortable on complex access schemes.',
                    'is_available'   => false,
                    'average_rating' => 3.8,
                    'total_jobs_completed' => 59,
                    'availability_schedule' => ['mon' => '07:00-17:00', 'wed' => '07:00-17:00', 'fri' => '07:00-17:00'],
                ],
                'certifications' => [
                    ['name' => 'CISRS Intermediate Scaffolding Card', 'issuing_body' => 'CISRS', 'certificate_number' => 'CISRS-INT-88210', 'issued_at' => '2021-03-01', 'expires_at' => '2026-03-01', 'is_verified' => true],
                ],
            ],
            [
                'user' => [
                    'name'  => 'Chidinma Eze',
                    'email' => 'chidinma.eze@workers.test',
                ],
                'profile' => [
                    'trade'          => 'Plumber',
                    'skills'         => ['pipework', 'underfloor heating', 'bathroom installation', 'pump systems', 'water treatment'],
                    'location'       => 'Lewisham, London',
                    'latitude'       => 51.4612,
                    'longitude'      => -0.0121,
                    'hourly_rate'    => 26.00,
                    'bio'            => 'Specialist in high-spec bathroom installations and underfloor heating systems.',
                    'is_available'   => true,
                    'average_rating' => 4.6,
                    'total_jobs_completed' => 72,
                    'availability_schedule' => ['tue' => '07:00-17:00', 'wed' => '07:00-17:00', 'thu' => '07:00-17:00', 'fri' => '07:00-17:00'],
                ],
                'certifications' => [
                    ['name' => 'NVQ Level 3 Plumbing', 'issuing_body' => 'City & Guilds', 'certificate_number' => 'NVQ-PLMB-55312', 'issued_at' => '2020-07-01', 'expires_at' => null, 'is_verified' => true],
                    ['name' => 'CSCS Blue Card', 'issuing_body' => 'CSCS', 'certificate_number' => 'CSCS-B-55312', 'issued_at' => '2020-07-01', 'expires_at' => '2025-07-01', 'is_verified' => true],
                ],
            ],
            [
                'user' => [
                    'name'  => 'Kofi Asante',
                    'email' => 'kofi.asante@workers.test',
                ],
                'profile' => [
                    'trade'          => 'HVAC Technician',
                    'skills'         => ['air conditioning installation', 'heat pumps', 'chillers', 'commissioning', 'planned maintenance'],
                    'location'       => 'Ealing, London',
                    'latitude'       => 51.5130,
                    'longitude'      => -0.3089,
                    'hourly_rate'    => 29.00,
                    'bio'            => 'HVAC technician specialising in heat pump installations and commercial HVAC commissioning.',
                    'is_available'   => true,
                    'average_rating' => 4.5,
                    'total_jobs_completed' => 49,
                    'availability_schedule' => ['mon' => '08:00-17:00', 'tue' => '08:00-17:00', 'wed' => '08:00-17:00', 'thu' => '08:00-17:00'],
                ],
                'certifications' => [
                    ['name' => 'F-Gas Category I', 'issuing_body' => 'City & Guilds', 'certificate_number' => 'FGAS-CAT1-77801', 'issued_at' => '2021-04-01', 'expires_at' => null, 'is_verified' => true],
                    ['name' => 'CSCS Blue Card', 'issuing_body' => 'CSCS', 'certificate_number' => 'CSCS-B-77801', 'issued_at' => '2021-04-01', 'expires_at' => '2026-04-01', 'is_verified' => true],
                ],
            ],
            [
                'user' => [
                    'name'  => 'Blessing Uche',
                    'email' => 'blessing.uche@workers.test',
                ],
                'profile' => [
                    'trade'          => 'Health & Safety Officer',
                    'skills'         => ['risk assessment', 'COSHH', 'fire safety', 'environmental compliance', 'toolbox talks'],
                    'location'       => 'Wandsworth, London',
                    'latitude'       => 51.4571,
                    'longitude'      => -0.1928,
                    'hourly_rate'    => 32.00,
                    'bio'            => 'IOSH-qualified H&S advisor; strong track record reducing LTIs on busy construction sites.',
                    'is_available'   => true,
                    'average_rating' => 4.7,
                    'total_jobs_completed' => 31,
                    'availability_schedule' => ['mon' => '08:00-17:00', 'tue' => '08:00-17:00', 'wed' => '08:00-17:00', 'thu' => '08:00-17:00', 'fri' => '08:00-17:00'],
                ],
                'certifications' => [
                    ['name' => 'IOSH Managing Safely', 'issuing_body' => 'IOSH', 'certificate_number' => 'IOSH-MS-99301', 'issued_at' => '2022-11-01', 'expires_at' => '2025-11-01', 'is_verified' => true],
                    ['name' => 'SSSTS', 'issuing_body' => 'CITB', 'certificate_number' => 'SSSTS-22401', 'issued_at' => '2022-09-01', 'expires_at' => '2027-09-01', 'is_verified' => true],
                    ['name' => 'CSCS Gold Card', 'issuing_body' => 'CSCS', 'certificate_number' => 'CSCS-G-99301', 'issued_at' => '2022-11-01', 'expires_at' => '2027-11-01', 'is_verified' => true],
                ],
            ],
            [
                'user' => [
                    'name'  => 'Samuel Ikenna',
                    'email' => 'samuel.ikenna@workers.test',
                ],
                'profile' => [
                    'trade'          => 'Bricklayer',
                    'skills'         => ['cavity wall construction', 'blockwork', 'stonework', 'brick cutting', 'pointing'],
                    'location'       => 'Newham, London',
                    'latitude'       => 51.5077,
                    'longitude'      => 0.0387,
                    'hourly_rate'    => 21.50,
                    'bio'            => 'Reliable bricklayer with experience on major housing schemes across East London.',
                    'is_available'   => true,
                    'average_rating' => 4.0,
                    'total_jobs_completed' => 95,
                    'availability_schedule' => ['mon' => '07:30-17:30', 'tue' => '07:30-17:30', 'wed' => '07:30-17:30', 'thu' => '07:30-17:30', 'fri' => '07:30-15:30'],
                ],
                'certifications' => [
                    ['name' => 'CSCS Blue Card', 'issuing_body' => 'CSCS', 'certificate_number' => 'CSCS-B-11203', 'issued_at' => '2021-02-01', 'expires_at' => '2026-02-01', 'is_verified' => true],
                ],
            ],
            [
                'user' => [
                    'name'  => 'Rachel Okonkwo',
                    'email' => 'rachel.okonkwo@workers.test',
                ],
                'profile' => [
                    'trade'          => 'Carpenter',
                    'skills'         => ['second fix', 'shopfitting', 'kitchen installation', 'site carpentry', 'finishing'],
                    'location'       => 'Lambeth, London',
                    'latitude'       => 51.4966,
                    'longitude'      => -0.1224,
                    'hourly_rate'    => 23.00,
                    'bio'            => 'Skilled carpenter with strong shopfitting and joinery background in retail and hospitality.',
                    'is_available'   => true,
                    'average_rating' => 4.3,
                    'total_jobs_completed' => 107,
                    'availability_schedule' => ['mon' => '07:00-17:00', 'tue' => '07:00-17:00', 'wed' => '07:00-17:00', 'thu' => '07:00-17:00'],
                ],
                'certifications' => [
                    ['name' => 'NVQ Level 2 Wood Occupations', 'issuing_body' => 'City & Guilds', 'certificate_number' => 'NVQ-WOOD-88801', 'issued_at' => '2018-06-01', 'expires_at' => null, 'is_verified' => true],
                    ['name' => 'CSCS Blue Card', 'issuing_body' => 'CSCS', 'certificate_number' => 'CSCS-B-88801', 'issued_at' => '2018-06-01', 'expires_at' => '2023-06-01', 'is_verified' => false],
                ],
            ],
        ];

        foreach ($workers as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['user']['email']],
                array_merge($data['user'], [
                    'password'          => Hash::make('Worker@1234'),
                    'role'              => UserRole::Worker,
                    'email_verified_at' => now(),
                ])
            );

            $profile = WorkerProfile::updateOrCreate(
                ['user_id' => $user->id],
                $data['profile']
            );

            foreach ($data['certifications'] as $cert) {
                Certification::updateOrCreate(
                    ['worker_profile_id' => $profile->id, 'certificate_number' => $cert['certificate_number']],
                    $cert
                );
            }
        }

        $this->command->info('Seeded ' . count($workers) . ' workers with profiles and certifications.');
    }
}
