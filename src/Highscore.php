<?php

namespace Villermen\RuneScape;
use DateTime;
use Iterator;

/**
 * Represents a player's highscore at a specific moment in time.
 */
class Highscore implements Iterator
{
    /** @var Player|null*/
    private $player;

    /** @var string */
    private $rawData;

    /** @var HighscoreSkill[]  */
    private $skills = [];

    /** @var HighscoreActivity[] */
    private $activities = [];

    /** @var DateTime */
    protected $time;

    /**
     * Creates a Highscore object from a raw high score data response.
     *
     * @param Player|null $player
     * @param string $rawData Data as returned from Jagex's lookup API.
     * @param DateTime|null $time
     * @throws RuneScapeException
     */
    public function __construct(Player $player = null, string $rawData, DateTime $time = null)
    {
        $this->player = $player;
        $this->rawData = $rawData;

        if ($time) {
            $this->time = $time;
        } else {
            $this->time = new DateTime();
        }

        $entries = explode("\n", trim($rawData));

        $skillId = 0;
        $activityId = 0;

        $skills = Constants::getSkills();
        $activities = Constants::getActivities();

        foreach($entries as $entry) {
            $entryArray = explode(",", $entry);

            if (count($entryArray) == 3) {
                // Skill
                list($rank, $level, $xp) = $entryArray;

                if (isset($skills[$skillId])) {
                    $this->skills[] = new HighscoreSkill($skills[$skillId], $rank, $level, $xp);
                }

                $skillId++;
            } elseif (count($entryArray) == 2) {
                // Activity
                list($rank, $score) = $entryArray;

                if (isset($activities[$activityId])) {
                    $this->activities[] = new HighscoreActivity($activities[$activityId], $rank, $score);
                }

                $activityId++;
            } else {
                throw new RuneScapeException("Invalid highscore data supplied.");
            }
        }

        if (!$skillId) {
            throw new RuneScapeException("No highscore obtained from data.");
        }
    }

    /**
     * @return array 0: Combat level, 1: Combat level without summoning, 2: Summoning addition.
     */
    public function getCombatLevel()
    {
        // TODO: Implement getCombatLevel()
        throw new RuneScapeException("Not implemented.");

        $attackLevel = max($this->data["skills"][1]["level"], 1);
        $defenceLevel = max($this->data["skills"][2]["level"], 1);
        $strengthLevel = max($this->data["skills"][3]["level"], 1);
        $constitutionLevel = max($this->data["skills"][4]["level"], 10);
        $rangedLevel = max($this->data["skills"][5]["level"], 1);
        $prayerLevel = max($this->data["skills"][6]["level"], 1);
        $magicLevel = max($this->data["skills"][7]["level"], 1);

        if (!isset($this->data["skills"][24]))
            $summoningLevel = 1;
        else
            $summoningLevel = max($this->data["skills"][24]["level"], 1);

        $summoningAddition = 0.25 * floor(0.5 * $summoningLevel);

        $result = [
            0 => 0,
            1 => 0,
            2 => 0
        ];

        $result[0] = (int) floor(0.25 * (floor(13 / 10 * max($attackLevel + $strengthLevel, 2 * $magicLevel, 2 * $rangedLevel)) +
                $defenceLevel + $constitutionLevel + floor(0.5 * $prayerLevel) + floor(0.5 * $summoningLevel)));
        $result[1] = (int) floor(0.25 * (floor(13 / 10 * max($attackLevel + $strengthLevel, 2 * $magicLevel, 2 * $rangedLevel)) +
                $defenceLevel + $constitutionLevel + floor(0.5 * $prayerLevel)));
        $result[2] = $result[0] - $result[1];

        return $result;
    }

    public function compareTo(Highscore $highscore)
    {
        // TODO: Implement compareTo()
        throw new RuneScapeException("Not implemented.");

        $result = [];

        foreach($this->data["skills"] as $skillId => $skill1)
        {
            if (!isset($stats->data["skills"][$skillId]) ||
                $skill1["level"] == -1 ||
                $stats->data["skills"][$skillId]["level"] == -1)
            {
                $result[$skillId] = [
                    "level" => false,
                    "xp" => false,
                    "rank" => false
                ];

                continue;
            }

            $skill2 = $stats->data["skills"][$skillId];

            $result[$skillId]["level"] = $skill1["level"] - $skill2["level"];
            $result[$skillId]["xp"] = $skill1["xp"] - $skill2["xp"];
            $result[$skillId]["rank"] = $skill2["rank"] - $skill1["rank"];
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getRawData(): string
    {
        return $this->rawData;
    }

    /**
     * @return DateTime
     */
    public function getTime(): DateTime
    {
        return $this->time;
    }

    /**
     * @return Player|null
     */
    public function getPlayer(): Player
    {
        return $this->player;
    }

    /**
     * @return HighscoreSkill[]
     */
    public function getSkills(): array
    {
        return $this->skills;
    }

    /**
     * @return HighscoreActivity[]
     */
    public function getActivities(): array
    {
        return $this->activities;
    }

    /** @inheritdoc */
    public function current()
    {
        // TODO: Implement current() method.
        throw new RuneScapeException("Not implemented.");
    }

    /** @inheritdoc */
    public function next()
    {
        // TODO: Implement next() method.
        throw new RuneScapeException("Not implemented.");
    }

    /** @inheritdoc */
    public function key()
    {
        // TODO: Implement key() method.
        throw new RuneScapeException("Not implemented.");
    }

    /** @inheritdoc */
    public function valid()
    {
        // TODO: Implement valid() method.
        throw new RuneScapeException("Not implemented.");
    }

    /** @inheritdoc */
    public function rewind()
    {
        // TODO: Implement rewind() method.
        throw new RuneScapeException("Not implemented.");
    }
}
