<?php

namespace Villermen\RuneScape\HighScore;

class HighScoreSkillComparison extends HighScoreEntryComparison
{
    /** @var int */
    protected $levelDifference = false;

    /** @var int */
    protected $xpDifference = false;

    public function __construct(HighScoreSkill $activity1, HighScoreSkill $activity2, bool $uncapped = false)
    {
        parent::__construct($activity1, $activity2);

        $this->levelDifference = $activity1->getLevel($uncapped) - $activity2->getLevel($uncapped);
        $this->xpDifference = $activity1->getXp() - $activity2->getXp();
    }

    /**
     * @return int
     */
    public function getLevelDifference(): int
    {
        return $this->levelDifference;
    }

    /**
     * @return int
     */
    public function getXpDifference(): int
    {
        return $this->xpDifference;
    }
}
