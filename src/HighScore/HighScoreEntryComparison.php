<?php

namespace Villermen\RuneScape\HighScore;

use Villermen\RuneScape\Exception\RuneScapeException;

abstract class HighScoreEntryComparison
{
    /** @var HighScoreEntry */
    private $entry1;

    /** @var HighScoreEntry */
    private $entry2;

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

        $this->entry1 = $entry1;
        $this->entry2 = $entry2;
    }

    /**
     * Difference in rank between the two entries.
     * False when either one of them does not have a rank.
     *
     * @return false|int
     */
    public function getRankDifference()
    {
        if (!$this->entry1 || !$this->entry1->getRank() || !$this->entry2 || !$this->entry2->getRank()) {
            return false;
        }

        return $this->entry2->getRank() - $this->entry1->getRank();
    }
}
