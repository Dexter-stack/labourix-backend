<?php

namespace Database\Seeders;

use App\Models\Trade;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TradeSeeder extends Seeder
{
    public function run(): void
    {
        $trades = [
            // Construction & Building Trades
            ['name' => 'Bricklayer/Brickie',       'category' => 'construction_building'],
            ['name' => 'Carpenter/Joiner',          'category' => 'construction_building'],
            ['name' => 'Plumber',                   'category' => 'construction_building'],
            ['name' => 'Gas Engineer/Boiler Engineer', 'category' => 'construction_building'],
            ['name' => 'Electrician',               'category' => 'construction_building'],
            ['name' => 'Plasterer/Dryliner',        'category' => 'construction_building'],
            ['name' => 'Roofer',                    'category' => 'construction_building'],
            ['name' => 'Painter and Decorator',     'category' => 'construction_building'],
            ['name' => 'Scaffolder',                'category' => 'construction_building'],
            ['name' => 'Groundworker',              'category' => 'construction_building'],
            ['name' => 'General Builder',           'category' => 'construction_building'],
            ['name' => 'Stonemason',                'category' => 'construction_building'],
            ['name' => 'Floorer/Carpet Fitter',     'category' => 'construction_building'],
            ['name' => 'Ceiling Fixer',             'category' => 'construction_building'],

            // Engineering & Technical Trades
            ['name' => 'Welder',                    'category' => 'engineering_technical'],
            ['name' => 'HVAC Technician',           'category' => 'engineering_technical'],
            ['name' => 'Fabricator/CNC Machinist',  'category' => 'engineering_technical'],
            ['name' => 'Auto Technician/Mechanic',  'category' => 'engineering_technical'],
            ['name' => 'Lift Technician/Engineer',  'category' => 'engineering_technical'],
            ['name' => 'CAD Technician',            'category' => 'engineering_technical'],
        ];

        foreach ($trades as $trade) {
            Trade::firstOrCreate(
                ['slug' => Str::slug($trade['name'])],
                array_merge($trade, ['slug' => Str::slug($trade['name']), 'is_active' => true])
            );
        }
    }
}
