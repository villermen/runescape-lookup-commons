<?php

namespace Villermen\RuneScape\Highscore;

class HighscoreSkillComparison extends HighscoreEntryComparison
{
    /** @var int */
    private $levelDifference = false;

    /** @var int */
    private $xpDifference = false;

    public function __construct(HighscoreSkill $activity1, HighscoreSkill $activity2, bool $uncapped = false)
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
