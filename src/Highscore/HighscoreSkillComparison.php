<?php

namespace Villermen\RuneScape\Highscore;

class HighscoreSkillComparison extends HighscoreEntryComparison
{
    /** @var int|false */
    private $levelDifference = false;

    /** @var int|false */
    private $xpDifference = false;

    public function __construct(HighscoreSkill $skill1, HighscoreSkill $skill2, bool $uncapped = false)
    {
        parent::__construct($skill1, $skill2);

        $this->levelDifference = $skill1->getLevel($uncapped) - $skill2->getLevel($uncapped);
        $this->xpDifference = $skill1->getXp() - $skill2->getXp();
    }

    /**
     * @return false|int
     */
    public function getLevelDifference()
    {
        return $this->levelDifference;
    }

    /**
     * @return false|int
     */
    public function getXpDifference()
    {
        return $this->xpDifference;
    }
}
