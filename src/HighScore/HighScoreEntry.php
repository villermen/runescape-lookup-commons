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
     * @param int $rank
     */
    protected function __construct(int $rank)
    {
        if ($rank < 1) {
            $rank = false;
        }

        $this->rank = $rank;
    }

    /**
     * Returns the name of the stat for display purposes.
     *
     * @return string
     */
    abstract public function getName(): string;

    /**
     * @return int|false
     */
    public function getRank()
    {
        return $this->rank;
    }
}
