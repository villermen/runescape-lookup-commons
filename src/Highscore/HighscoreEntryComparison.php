<?php

namespace Villermen\RuneScape\Highscore;

class HighscoreEntryComparison
{
    /** @var int|false */
    private $rankDifference = false;

    public function __construct(HighscoreEntry $entry1, HighscoreEntry $entry2)
    {
        if ($entry1->getRank() !== false && $entry2->getRank() !== false) {
            $this->rankDifference = $entry2->getRank() - $entry1->getRank();
        }
    }

    /**
     * @return false|int
     */
    public function getRankDifference()
    {
        return $this->rankDifference;
    }
}
