<?php

namespace Villermen\RuneScape\HighScore;

use Villermen\RuneScape\Exception\RuneScapeException;

abstract class HighScoreEntryComparison
{
    /** @var int|false */
    protected $rankDifference;

    /**
     *
     * @param HighScoreEntry|null $entry1
     * @param HighScoreEntry|null $entry2
     * @throws RuneScapeException
     */
    protected function __construct($entry1, $entry2)
    {
        if (!$entry1 && !$entry2) {
            throw new RuneScapeException("At least one of the entries must be given in a comparison.");
        }

        if ($entry1 && $entry2 && $entry1->getRank() && $entry2->getRank()) {
            $this->rankDifference = $entry2->getRank() - $entry1->getRank();
        } else {
            $this->rankDifference = false;
        }
    }

    /**
     * Difference in rank between the two entries.
     * False when either one of them does not have a rank.
     *
     * @return int|false
     */
    public function getRankDifference()
    {
        return $this->rankDifference;
    }
}
