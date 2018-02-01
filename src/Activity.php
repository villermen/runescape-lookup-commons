<?php

namespace Villermen\RuneScape;

use Villermen\RuneScape\Exception\RuneScapeException;

class Activity
{
    const ACTIVITY_BOUNTY_HUNTER = 0;
    const ACTIVITY_BOUNTY_HUNTER_ROGUES = 1;
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

    /** @var Activity[] */
    private static $activities;

    /**
     * Initializes the activities array.
     * Default values don't allow expressions (new objects).
     */
    public static function initializeActivities()
    {
        if (self::$activities) {
            return;
        }

        self::$activities = [
            self::ACTIVITY_BOUNTY_HUNTER => new Activity(self::ACTIVITY_BOUNTY_HUNTER, "Bounty Hunters"),
            self::ACTIVITY_BOUNTY_HUNTER_ROGUES => new Activity(self::ACTIVITY_BOUNTY_HUNTER_ROGUES, "Bounty Hunters Rogues"),
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
            self::ACTIVITY_APRIL_FOOLS_2015_RAT_KILLS => new Activity(self::ACTIVITY_APRIL_FOOLS_2015_RAT_KILLS, "April Fools 2015: Rat Kills")
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
     * Retrieve an activity by ID.
     * You can use the ACTIVITY_ constants in this class for IDs.
     *
     * @param int $id
     * @return Activity
     * @throws RuneScapeException When the requested activity does not exist.
     */
    public static function getActivity(int $id): Activity
    {
        self::initializeActivities();

        if (!isset(self::$activities[$id])) {
            throw new RuneScapeException(sprintf("Activity with id %d does not exist.", $id));
        }

        return self::$activities[$id];
    }

    /** @var int */
    protected $id;

    /** @var string */
    protected $name;

    /**
     * @param int $id
     * @param string $name
     */
    public function __construct(int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
}
