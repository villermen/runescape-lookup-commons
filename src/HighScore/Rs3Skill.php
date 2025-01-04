<?php

namespace Villermen\RuneScape\HighScore;

enum Rs3Skill: int implements SkillInterface
{
    use SkillTrait;

    case TOTAL = 0;
    case ATTACK = 1;
    case DEFENCE = 2;
    case STRENGTH = 3;
    case CONSTITUTION = 4;
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
    case RUNECRAFTING = 21;
    case HUNTER = 22;
    case CONSTRUCTION = 23;
    case SUMMONING = 24;
    case DUNGEONEERING = 25;
    case DIVINATION = 26;
    case INVENTION = 27;
    case ARCHAEOLOGY = 28;
    case NECROMANCY = 29;

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
            self::CONSTITUTION => 'Constitution',
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
            self::RUNECRAFTING => 'Runecrafting',
            self::HUNTER => 'Hunter',
            self::CONSTRUCTION => 'Construction',
            self::SUMMONING => 'Summoning',
            self::DUNGEONEERING => 'Dungeoneering',
            self::DIVINATION => 'Divination',
            self::INVENTION => 'Invention',
            self::ARCHAEOLOGY => 'Archaeology',
            self::NECROMANCY => 'Necromancy',
        };
    }

    public function getMinimumLevel(): int
    {
        return match ($this) {
            self::CONSTITUTION => 10,
            default => 1,
        };
    }

    public function getMaximumLevel(): int
    {
        return match ($this) {
            self::HERBLORE,
            self::SLAYER,
            self::FARMING,
            self::DUNGEONEERING,
            self::INVENTION,
            self::ARCHAEOLOGY,
            self::NECROMANCY => 120,
            self::MINING,
            self::SMITHING => 110,
            default => 99,
        };
    }

    /**
     * @return array<int, int>
     */
    private function getXpTable(): array
    {
        return $this->isElite() ? SkillInterface::XP_TABLE_ELITE : SkillInterface::XP_TABLE;
    }

    public function isElite(): bool
    {
        return match ($this) {
            self::INVENTION => true,
            default => false,
        };
    }
}
