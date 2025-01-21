<?php

namespace Villermen\RuneScape\HighScore;

enum OsrsSkill: int implements SkillInterface
{
    use SkillTrait;

    case TOTAL = 0;
    case ATTACK = 1;
    case DEFENCE = 2;
    case STRENGTH = 3;
    case HITPOINTS = 4;
    case RANGED = 5;
    case PRAYER = 6;
    case MAGIC = 7;
    case COOKING = 8;
    case WOODCUTTING = 9;
    case FLETCHING = 10;
    case FISHING = 11;
    case FIREMAKING = 12;
    case CRAFTING = 13;
    case SMITHING = 14;
    case MINING = 15;
    case HERBLORE = 16;
    case AGILITY = 17;
    case THIEVING = 18;
    case SLAYER = 19;
    case FARMING = 20;
    case RUNECRAFT = 21;
    case HUNTER = 22;
    case CONSTRUCTION = 23;

    public function getId(): int
    {
        return $this->value;
    }

    public function getName(): string
    {
        return match ($this) {
            self::TOTAL => 'Total',
            self::ATTACK => 'Attack',
            self::DEFENCE => 'Defence',
            self::STRENGTH => 'Strength',
            self::HITPOINTS => 'Hitpoints',
            self::RANGED => 'Ranged',
            self::PRAYER => 'Prayer',
            self::MAGIC => 'Magic',
            self::COOKING => 'Cooking',
            self::WOODCUTTING => 'Woodcutting',
            self::FLETCHING => 'Fletching',
            self::FISHING => 'Fishing',
            self::FIREMAKING => 'Firemaking',
            self::CRAFTING => 'Crafting',
            self::SMITHING => 'Smithing',
            self::MINING => 'Mining',
            self::HERBLORE => 'Herblore',
            self::AGILITY => 'Agility',
            self::THIEVING => 'Thieving',
            self::SLAYER => 'Slayer',
            self::FARMING => 'Farming',
            self::RUNECRAFT => 'Runecraft',
            self::HUNTER => 'Hunter',
            self::CONSTRUCTION => 'Construction',
        };
    }

    public function getMinimumLevel(): int
    {
        return match ($this) {
            self::HITPOINTS => 10,
            default => 1,
        };
    }

    public function getMaximumLevel(): int
    {
        return 99;
    }

    /**
     * @return array<int, int>
     */
    private function getXpTable(): array
    {
        return SkillInterface::XP_TABLE;
    }
}
