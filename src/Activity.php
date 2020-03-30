<?php

namespace Villermen\RuneScape;

use Villermen\RuneScape\Exception\RuneScapeException;

class Activity
{
    const ACTIVITY_BOUNTY_HUNTER = 0;
    const ACTIVITY_BOUNTY_HUNTER_ROGUE = 1;
    const ACTIVITY_DOMINION_TOWER = 2;
    const ACTIVITY_CRUCIBLE = 3;
    const ACTIVITY_CASTLE_WARS = 4;
    const ACTIVITY_BARBARIAN_ASSAULT_ATTACKER = 5;
    const ACTIVITY_BARBARIAN_ASSAULT_DEFENDER = 6;
    const ACTIVITY_BARBARIAN_ASSAULT_COLLECTOR = 7;
    const ACTIVITY_BARBARIAN_ASSAULT_HEALER = 8;
    const ACTIVITY_DUEL_TOURNAMENT = 9;
    const ACTIVITY_MOBILISING_ARMIES = 10;
    const ACTIVITY_CONQUEST = 11;
    const ACTIVITY_FIST_OF_GUTHIX = 12;
    const ACTIVITY_GIELINOR_GAMES_RESOURCE_RACE = 13;
    const ACTIVITY_GIELINOR_GAMES_ATHLETICS = 14;
    const ACTIVITY_WORLD_EVENT_2_ARMADYL_CONTRIBUTION = 15;
    const ACTIVITY_WORLD_EVENT_2_BANDOS_CONTRIBUTION = 16;
    const ACTIVITY_WORLD_EVENT_2_ARMADYL_KILLS = 17;
    const ACTIVITY_WORLD_EVENT_2_BANDOS_KILLS = 18;
    const ACTIVITY_HEIST_GUARD = 19;
    const ACTIVITY_HEIST_ROBBER = 20;
    const ACTIVITY_CABBAGE_FACEPUNCH_BONANZA = 21;
    const ACTIVITY_APRIL_FOOLS_2015_COW_TIPPING = 22;
    const ACTIVITY_APRIL_FOOLS_2015_RAT_KILLS = 23;
    const ACTIVITY_RUNESCORE = 24;
    const ACTIVITY_EASY_CLUE_SCROLLS = 25;
    const ACTIVITY_MEDIUM_CLUE_SCROLLS = 26;
    const ACTIVITY_HARD_CLUE_SCROLLS = 27;
    const ACTIVITY_ELITE_CLUE_SCROLLS = 28;
    const ACTIVITY_MASTER_CLUE_SCROLLS = 29;

    const ACTIVITY_OLD_SCHOOL_EASY_CLUE_SCROLLS = 1000;
    const ACTIVITY_OLD_SCHOOL_MEDIUM_CLUE_SCROLLS = 1001;
    const ACTIVITY_OLD_SCHOOL_ALL_CLUE_SCROLLS = 1002;
    const ACTIVITY_OLD_SCHOOL_BOUNTY_HUNTER_ROGUE = 1003;
    const ACTIVITY_OLD_SCHOOL_BOUNTY_HUNTER = 1004;
    const ACTIVITY_OLD_SCHOOL_HARD_CLUE_SCROLLS = 1005;
    const ACTIVITY_OLD_SCHOOL_LAST_MAN_STANDING = 1006;
    const ACTIVITY_OLD_SCHOOL_ELITE_CLUE_SCROLLS = 1007;
    const ACTIVITY_OLD_SCHOOL_MASTER_CLUE_SCROLLS = 1008;
    // Old School added activity highscores for pretty much every boss kill and I can't be asked to add them all.

    /** @var Activity[] */
    private static $activities;

    /**
     * Initializes the activities array. In a method because expressions can't be used to initialize properties.
     */
    public static function initializeActivities(): void
    {
        if (self::$activities) {
            return;
        }

        self::$activities = [
            self::ACTIVITY_BOUNTY_HUNTER => new Activity(self::ACTIVITY_BOUNTY_HUNTER, "Bounty Hunter: Bounty Kills"),
            self::ACTIVITY_BOUNTY_HUNTER_ROGUE => new Activity(self::ACTIVITY_BOUNTY_HUNTER_ROGUE, "Bounty Hunter: Rogue Kills"),
            self::ACTIVITY_DOMINION_TOWER => new Activity(self::ACTIVITY_DOMINION_TOWER, "Dominion Tower"),
            self::ACTIVITY_CRUCIBLE => new Activity(self::ACTIVITY_CRUCIBLE, "The Crucible"),
            self::ACTIVITY_CASTLE_WARS => new Activity(self::ACTIVITY_CASTLE_WARS, "Castle Wars Games"),
            self::ACTIVITY_BARBARIAN_ASSAULT_ATTACKER => new Activity(self::ACTIVITY_BARBARIAN_ASSAULT_ATTACKER, "Barbarian Assault Attackers"),
            self::ACTIVITY_BARBARIAN_ASSAULT_DEFENDER => new Activity(self::ACTIVITY_BARBARIAN_ASSAULT_DEFENDER, "Barbarian Assault Defenders"),
            self::ACTIVITY_BARBARIAN_ASSAULT_COLLECTOR => new Activity(self::ACTIVITY_BARBARIAN_ASSAULT_COLLECTOR, "Barbarian Assault Collectors"),
            self::ACTIVITY_BARBARIAN_ASSAULT_HEALER => new Activity(self::ACTIVITY_BARBARIAN_ASSAULT_HEALER, "Barbarian Assault Healers"),
            self::ACTIVITY_DUEL_TOURNAMENT => new Activity(self::ACTIVITY_DUEL_TOURNAMENT, "Duel Tournament"),
            self::ACTIVITY_MOBILISING_ARMIES => new Activity(self::ACTIVITY_MOBILISING_ARMIES, "Mobilising Armies"),
            self::ACTIVITY_CONQUEST => new Activity(self::ACTIVITY_CONQUEST, "Conquest"),
            self::ACTIVITY_FIST_OF_GUTHIX => new Activity(self::ACTIVITY_FIST_OF_GUTHIX, "Fist of Guthix"),
            self::ACTIVITY_GIELINOR_GAMES_RESOURCE_RACE => new Activity(self::ACTIVITY_GIELINOR_GAMES_RESOURCE_RACE, "Gielinor Games: Resource Race"),
            self::ACTIVITY_GIELINOR_GAMES_ATHLETICS => new Activity(self::ACTIVITY_GIELINOR_GAMES_ATHLETICS, "Gielinor Games: Athletics"),
            self::ACTIVITY_WORLD_EVENT_2_ARMADYL_CONTRIBUTION => new Activity(self::ACTIVITY_WORLD_EVENT_2_ARMADYL_CONTRIBUTION, "World Event 2: Armadyl Lifetime Contribution"),
            self::ACTIVITY_WORLD_EVENT_2_BANDOS_CONTRIBUTION => new Activity(self::ACTIVITY_WORLD_EVENT_2_BANDOS_CONTRIBUTION, "World Event 2: Bandos Lifetime Contribution"),
            self::ACTIVITY_WORLD_EVENT_2_ARMADYL_KILLS => new Activity(self::ACTIVITY_WORLD_EVENT_2_ARMADYL_KILLS, "World Event 2: Armadyl PvP Kills"),
            self::ACTIVITY_WORLD_EVENT_2_BANDOS_KILLS => new Activity(self::ACTIVITY_WORLD_EVENT_2_BANDOS_KILLS, "World Event 2: Bandos PvP Kills"),
            self::ACTIVITY_HEIST_GUARD => new Activity(self::ACTIVITY_HEIST_GUARD, "Heist Guard Level"),
            self::ACTIVITY_HEIST_ROBBER => new Activity(self::ACTIVITY_HEIST_ROBBER, "Heist Robber Level"),
            self::ACTIVITY_CABBAGE_FACEPUNCH_BONANZA => new Activity(self::ACTIVITY_CABBAGE_FACEPUNCH_BONANZA, "Cabbage Facepunch Bonanza: 5 Game Average"),
            self::ACTIVITY_APRIL_FOOLS_2015_COW_TIPPING => new Activity(self::ACTIVITY_APRIL_FOOLS_2015_COW_TIPPING, "April Fools 2015: Cow Tipping"),
            self::ACTIVITY_APRIL_FOOLS_2015_RAT_KILLS => new Activity(self::ACTIVITY_APRIL_FOOLS_2015_RAT_KILLS, "April Fools 2015: Rat Kills"),
            self::ACTIVITY_RUNESCORE => new Activity(self::ACTIVITY_RUNESCORE, "RuneScore"),
            self::ACTIVITY_EASY_CLUE_SCROLLS => new Activity(self::ACTIVITY_EASY_CLUE_SCROLLS, "Clue Scrolls (easy)"),
            self::ACTIVITY_MEDIUM_CLUE_SCROLLS => new Activity(self::ACTIVITY_MEDIUM_CLUE_SCROLLS, "Clue Scrolls (medium)"),
            self::ACTIVITY_HARD_CLUE_SCROLLS => new Activity(self::ACTIVITY_HARD_CLUE_SCROLLS, "Clue Scrolls (hard)"),
            self::ACTIVITY_ELITE_CLUE_SCROLLS => new Activity(self::ACTIVITY_ELITE_CLUE_SCROLLS, "Clue Scrolls (elite)"),
            self::ACTIVITY_MASTER_CLUE_SCROLLS => new Activity(self::ACTIVITY_MASTER_CLUE_SCROLLS, "Clue Scrolls (master)"),

            self::ACTIVITY_OLD_SCHOOL_EASY_CLUE_SCROLLS => new Activity(self::ACTIVITY_OLD_SCHOOL_EASY_CLUE_SCROLLS, "Clue Scrolls (easy)"),
            self::ACTIVITY_OLD_SCHOOL_MEDIUM_CLUE_SCROLLS => new Activity(self::ACTIVITY_OLD_SCHOOL_MEDIUM_CLUE_SCROLLS, "Clue Scrolls (hard)"),
            self::ACTIVITY_OLD_SCHOOL_ALL_CLUE_SCROLLS => new Activity(self::ACTIVITY_OLD_SCHOOL_ALL_CLUE_SCROLLS, "Clue Scrolls (all)"),
            self::ACTIVITY_OLD_SCHOOL_BOUNTY_HUNTER_ROGUE => new Activity(self::ACTIVITY_OLD_SCHOOL_BOUNTY_HUNTER_ROGUE, "Bounty Hunter: Rogue Kills"),
            self::ACTIVITY_OLD_SCHOOL_BOUNTY_HUNTER => new Activity(self::ACTIVITY_OLD_SCHOOL_BOUNTY_HUNTER, "Bounty Hunter: Bounty Kills"),
            self::ACTIVITY_OLD_SCHOOL_HARD_CLUE_SCROLLS => new Activity(self::ACTIVITY_OLD_SCHOOL_ALL_CLUE_SCROLLS, "Clue Scrolls (hard)"),
            self::ACTIVITY_OLD_SCHOOL_LAST_MAN_STANDING => new Activity(self::ACTIVITY_OLD_SCHOOL_LAST_MAN_STANDING, "Last Man Standing"),
            self::ACTIVITY_OLD_SCHOOL_ELITE_CLUE_SCROLLS => new Activity(self::ACTIVITY_OLD_SCHOOL_ELITE_CLUE_SCROLLS, "Clue Scrolls (elite)"),
            self::ACTIVITY_OLD_SCHOOL_MASTER_CLUE_SCROLLS => new Activity(self::ACTIVITY_OLD_SCHOOL_MASTER_CLUE_SCROLLS, "Clue Scrolls (master)")
        ];
    }

    /**
     * @return Activity[]
     */
    public static function getActivities(): array
    {
        self::initializeActivities();

        return self::$activities;
    }

    /**
     * Retrieve an activity by ID. Use the ACTIVITY_* constants in this class for IDs.
     */
    public static function getActivity(int $id): Activity
    {
        self::initializeActivities();

        if (!isset(self::$activities[$id])) {
            throw new \InvalidArgumentException(sprintf("Activity with id %d does not exist.", $id));
        }

        return self::$activities[$id];
    }

    /** @var int */
    protected $id;

    /** @var string */
    protected $name;

    public function __construct(int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
