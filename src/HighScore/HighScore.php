<?php

namespace Villermen\RuneScape\HighScore;

use Villermen\RuneScape\Activity;
use Villermen\RuneScape\Player;
use Villermen\RuneScape\RuneScapeException;
use Villermen\RuneScape\Skill;

/**
 * Represents a player's high score at a specific moment in time.
 */
class HighScore
{
    /** @var Player */
    protected $player;

    /** @var HighScoreSkill[]  */
    protected $skills = [];

    /** @var HighScoreActivity[] */
    protected $activities = [];

    /** @var int */
    protected $iteratorKey;

    /**
     * Creates a HighScore object from a raw high score data response.
     *
     * @param Player $player
     * @param string $data Data as returned from Jagex's lookup API.
     * @throws RuneScapeException
     */
    public function __construct(Player $player, string $data)
    {
        $entries = explode("\n", trim($data));

        $skillId = 0;
        $activityId = 0;

        foreach($entries as $entry) {
            $entryArray = explode(",", $entry);

            if (count($entryArray) == 3) {
                // Skill
                try {
                    $skill = Skill::getSkill($skillId);
                    list($rank, $level, $xp) = $entryArray;
                    $this->skills[$skillId] = new HighScoreSkill($skill, $rank, $level, $xp);
                } catch (RuneScapeException $exception) {
                }

                $skillId++;
            } elseif (count($entryArray) == 2) {
                // Activity
                try {
                    $activity = Activity::getActivity($activityId);
                    list($rank, $score) = $entryArray;
                    $this->activities[$activityId] = new HighScoreActivity($activity, $rank, $score);
                } catch (RuneScapeException $exception) {
                }

                $activityId++;
            } else {
                throw new RuneScapeException("Invalid high score data supplied.");
            }
        }

        if (!$skillId) {
            throw new RuneScapeException("No high score obtained from data.");
        }
    }

    /**
     * Returns the combat level of this high score.
     *
     * @param bool $includeSummoning Whether to include the summoning skill while calculating the combat level.
     * @param bool $uncapped
     * @return int
     */
    public function getCombatLevel($includeSummoning = true, $uncapped = false): int
    {
        $attackLevel = $this->getSkill(Skill::SKILL_ATTACK)->getLevel($uncapped);
        $defenceLevel = $this->getSkill(Skill::SKILL_DEFENCE)->getLevel($uncapped);
        $strengthLevel = $this->getSkill(Skill::SKILL_STRENGTH)->getLevel($uncapped);
        $constitutionLevel = $this->getSkill(Skill::SKILL_CONSTITUTION)->getLevel($uncapped);
        $rangedLevel = $this->getSkill(Skill::SKILL_RANGED)->getLevel($uncapped);
        $prayerLevel = $this->getSkill(Skill::SKILL_PRAYER)->getLevel($uncapped);
        $magicLevel = $this->getSkill(Skill::SKILL_MAGIC)->getLevel($uncapped);

        $summoningSkill = $this->getSkill(Skill::SKILL_SUMMONING);

        if ($includeSummoning && $summoningSkill) {
            $summoningLevel = $summoningSkill->getLevel($uncapped);
        } else {
            $summoningLevel = 1;
        }

        return (int)((
            max($attackLevel + $strengthLevel, $magicLevel * 2, $rangedLevel * 2) * 1.3 +
            $defenceLevel + $constitutionLevel +
            floor($prayerLevel / 2) + floor($summoningLevel / 2)
        ) / 4);
    }

    /**
     * @return HighScoreSkill[]
     */
    public function getSkills(): array
    {
        return $this->skills;
    }

    /**
     * @return HighScoreActivity[]
     */
    public function getActivities(): array
    {
        return $this->activities;
    }

    /**
     * @param $id
     * @return HighScoreSkill|null
     */
    public function getSkill($id)
    {
        if (!isset($this->skills[$id])) {
            return null;
        }

        return $this->skills[$id];
    }

    /**
     * @param $id
     * @return HighScoreActivity|null
     */
    public function getActivity($id)
    {
        if (!isset($this->activities[$id])) {
            return null;
        }

        return $this->activities[$id];
    }

    /**
     * @return Player
     */
    public function getPlayer(): Player
    {
        return $this->player;
    }

    /**
     * Creates a comparison between this high score and the given high score.
     *
     * @param HighScore $otherHighScore
     * @param bool $uncapped
     * @return HighScoreComparison
     */
    public function compareTo(HighScore $otherHighScore, bool $uncapped = false): HighScoreComparison
    {
        return new HighScoreComparison($this, $otherHighScore, $uncapped);
    }
}
