<?php

namespace Villermen\RuneScape\HighScore;

enum Rs3Activity: int implements ActivityInterface
{
    case BOUNTY_HUNTER = 0;
    case BOUNTY_HUNTER_ROGUE = 1;
    case DOMINION_TOWER = 2;
    case CRUCIBLE = 3;
    case CASTLE_WARS = 4;
    case BARBARIAN_ASSAULT_ATTACKER = 5;
    case BARBARIAN_ASSAULT_DEFENDER = 6;
    case BARBARIAN_ASSAULT_COLLECTOR = 7;
    case BARBARIAN_ASSAULT_HEALER = 8;
    case DUEL_TOURNAMENT = 9;
    case MOBILISING_ARMIES = 10;
    case CONQUEST = 11;
    case FIST_OF_GUTHIX = 12;
    case GIELINOR_GAMES_ATHLETICS = 13;
    case GIELINOR_GAMES_RESOURCE_RACE = 14;
    case WORLD_EVENT_2_ARMADYL_CONTRIBUTION = 15;
    case WORLD_EVENT_2_BANDOS_CONTRIBUTION = 16;
    case WORLD_EVENT_2_ARMADYL_KILLS = 17;
    case WORLD_EVENT_2_BANDOS_KILLS = 18;
    case HEIST_GUARD = 19;
    case HEIST_ROBBER = 20;
    case CABBAGE_FACEPUNCH_BONANZA = 21;
    case APRIL_FOOLS_2015_COW_TIPPING = 22;
    case APRIL_FOOLS_2015_RAT_KILLS = 23;
    case RUNESCORE = 24;
    case CLUE_SCROLLS_EASY = 25;
    case CLUE_SCROLLS_MEDIUM = 26;
    case CLUE_SCROLLS_HARD = 27;
    case CLUE_SCROLLS_ELITE = 28;
    case CLUE_SCROLLS_MASTER = 29;

    public function getId(): int
    {
        return $this->value;
    }

    public function getName(): string
    {
        return match ($this) {
            self::BOUNTY_HUNTER => 'Bounty Hunter: Bounty Kills',
            self::BOUNTY_HUNTER_ROGUE => 'Bounty Hunter: Rogue Kills',
            self::DOMINION_TOWER => 'Dominion Tower',
            self::CRUCIBLE => 'The Crucible',
            self::CASTLE_WARS => 'Castle Wars Games',
            self::BARBARIAN_ASSAULT_ATTACKER => 'Barbarian Assault Attackers',
            self::BARBARIAN_ASSAULT_DEFENDER => 'Barbarian Assault Defenders',
            self::BARBARIAN_ASSAULT_COLLECTOR => 'Barbarian Assault Collectors',
            self::BARBARIAN_ASSAULT_HEALER => 'Barbarian Assault Healers',
            self::DUEL_TOURNAMENT => 'Duel Tournament',
            self::MOBILISING_ARMIES => 'Mobilising Armies',
            self::CONQUEST => 'Conquest',
            self::FIST_OF_GUTHIX => 'Fist of Guthix',
            self::GIELINOR_GAMES_RESOURCE_RACE => 'Gielinor Games: Resource Race',
            self::GIELINOR_GAMES_ATHLETICS => 'Gielinor Games: Athletics',
            self::WORLD_EVENT_2_ARMADYL_CONTRIBUTION => 'World Event 2: Armadyl Lifetime Contribution',
            self::WORLD_EVENT_2_BANDOS_CONTRIBUTION => 'World Event 2: Bandos Lifetime Contribution',
            self::WORLD_EVENT_2_ARMADYL_KILLS => 'World Event 2: Armadyl PvP Kills',
            self::WORLD_EVENT_2_BANDOS_KILLS => 'World Event 2: Bandos PvP Kills',
            self::HEIST_GUARD => 'Heist Guard Level',
            self::HEIST_ROBBER => 'Heist Robber Level',
            self::CABBAGE_FACEPUNCH_BONANZA => 'Cabbage Facepunch Bonanza: 5 Game Average',
            self::APRIL_FOOLS_2015_COW_TIPPING => 'April Fools 2015: Cow Tipping',
            self::APRIL_FOOLS_2015_RAT_KILLS => 'April Fools 2015: Rat Kills',
            self::RUNESCORE => 'RuneScore',
            self::CLUE_SCROLLS_EASY => 'Clue Scrolls (easy)',
            self::CLUE_SCROLLS_MEDIUM => 'Clue Scrolls (medium)',
            self::CLUE_SCROLLS_HARD => 'Clue Scrolls (hard)',
            self::CLUE_SCROLLS_ELITE => 'Clue Scrolls (elite)',
            self::CLUE_SCROLLS_MASTER => 'Clue Scrolls (master)',
        };
    }
}
