<?php

namespace Villermen\RuneScape\HighScore;

abstract class HighScoreEntryComparison
{
    /** @var int|null */
    protected $rankDifference;

    protected function __construct(?HighScoreEntry $entry1, ?HighScoreEntry $entry2)
    {
        if (!$entry1 && !$entry2) {
            throw new \InvalidArgumentException("At least one of the entries must be given in a comparison.");
        }

        if ($entry1 && $entry2 && $entry1->getRank() && $entry2->getRank()) {
            $this->rankDifference = $entry2->getRank() - $entry1->getRank();
        } else {
            $this->rankDifference = null;
        }
    }

    /**
     * Difference in rank between the two entries. False when either one of them does not have a rank.
     */
    public function getRankDifference(): ?int
    {
        return $this->rankDifference;
    }
}
