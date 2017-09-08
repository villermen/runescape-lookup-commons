<?php

namespace Villermen\RuneScape\HighScore;

/**
 * Represents a skill or activity in a high score.
 */
abstract class HighScoreEntry
{
    /** @var int|false */
    protected $rank;

    /**
     * @param int|false $rank
     */
    public function __construct($rank)
    {
        $this->setRank($rank);
    }

    /**
     * Returns the name of the stat for display purposes.
     *
     * @return string
     */
    abstract public function getName(): string;

    /**
     * @param int|false $rank
     *
     * @return HighScoreEntry
     */
    private function setRank($rank): HighScoreEntry
    {
        if (!is_int($rank) || $rank < 1) {
            $rank = false;
        }

        $this->rank = $rank;

        return $this;
    }

    /**
     * @return int|false
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * @param HighScoreEntry $entry
     * @return HighScoreEntryComparison
     */
    public function compareTo(HighScoreEntry $entry): HighScoreEntryComparison
    {
        return new HighScoreEntryComparison($this, $entry);
    }
}
