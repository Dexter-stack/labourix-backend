<?php

namespace App\Enums;

enum TradeCategory: string
{
    case ConstructionBuilding = 'construction_building';
    case EngineeringTechnical = 'engineering_technical';

    public function label(): string
    {
        return match($this) {
            self::ConstructionBuilding => 'Construction & Building Trades',
            self::EngineeringTechnical => 'Engineering & Technical Trades',
        };
    }
}
