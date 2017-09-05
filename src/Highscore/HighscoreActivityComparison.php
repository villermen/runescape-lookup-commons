<?php

namespace Villermen\RuneScape\Highscore;

class HighscoreActivityComparison extends HighscoreEntryComparison
{
    /** @var int */
    private $scoreDifference = false;

    public function __construct(HighscoreActivity $activity1, HighscoreActivity $activity2)
    {
        parent::__construct($activity1, $activity2);

        $this->scoreDifference = $activity1->getScore() - $activity2->getScore();
    }

    /**
     * @return int
     */
    public function getScoreDifference(): int
    {
        return $this->scoreDifference;
    }
}
