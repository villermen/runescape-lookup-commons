<?php

namespace Villermen\RuneScape\HighScore;

use Villermen\RuneScape\Exception\RuneScapeException;

class HighScoreSkillComparison extends HighScoreEntryComparison
{
    /** @var int */
    protected $levelDifference;

    /** @var int */
    protected $virtualLevelDifference;

    /** @var int */
    protected $xpDifference;

    /**
     * HighScoreComparisonSkill constructor.
     * @param HighScoreSkill|null $skill1
     * @param HighScoreSkill|null $skill2
     * @throws RuneScapeException
     */
    public function __construct($skill1, $skill2)
    {
        parent::__construct($skill1, $skill2);

        if (!$skill1) {
            $skill1 = new HighScoreSkill($skill2->getSkill(), -1, -1, -1);
        }

        if (!$skill2) {
            $skill2 = new HighScoreSkill($skill1->getSkill(), -1, -1, -1);
        }

        if ($skill1->getSkill() !== $skill2->getSkill()) {
            throw new RuneScapeException("Can't compare two different skills.");
        }

        $this->xpDifference = $skill1->getXp() - $skill2->getXp();
        $this->levelDifference = $skill1->getLevel() - $skill2->getLevel();
        $this->virtualLevelDifference = $skill1->getVirtualLevel() - $skill2->getVirtualLevel();
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
    public function getVirtualLevelDifference(): int
    {
        return $this->virtualLevelDifference;
    }

    /**
     * @return int
     */
    public function getXpDifference(): int
    {
        return $this->xpDifference;
    }
}
