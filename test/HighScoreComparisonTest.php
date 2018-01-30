<?php

use Villermen\RuneScape\Activity;
use Villermen\RuneScape\HighScore\HighScore;
use PHPUnit\Framework\TestCase;
use Villermen\RuneScape\HighScore\HighScoreComparison;
use Villermen\RuneScape\Player;
use Villermen\RuneScape\Skill;

class HighScoreComparisonTest extends TestCase
{
    /** @var HighScoreComparison */
    private $comparison;

    public function setUp()
    {
        $highScore1 = new HighScore(new Player("Villermen"), file_get_contents(__DIR__ . "/fixtures/high-score1.csv"));
        $highScore2 = new HighScore(new Player("Chloronium"), file_get_contents(__DIR__ . "/fixtures/high-score2.csv"));

        $this->comparison = $highScore1->compareTo($highScore2);
    }

    public function testComparison()
    {
        // Skill
        $totalComparison = $this->comparison->getSkill(Skill::SKILL_TOTAL);
        self::assertEquals(426, $totalComparison->getLevelDifference());
        self::assertEquals(786391181, $totalComparison->getXpDifference());
        self::assertEquals(155962, $totalComparison->getRankDifference());

        // Activity, negative
        $fogComparison = $this->comparison->getActivity(Activity::ACTIVITY_FIST_OF_GUTHIX);
        self::assertEquals(-1056, $fogComparison->getScoreDifference());
        self::assertEquals(-101623, $fogComparison->getRankDifference());

        // One unranked
        $invComparison = $this->comparison->getSkill(Skill::SKILL_INVENTION);
        self::assertEquals(119, $invComparison->getLevelDifference());
        self::assertEquals(89610570, $invComparison->getXpDifference());
        self::assertFalse($invComparison->getRankDifference());

        // Uncapped level
        self::assertEquals(122, $invComparison->getLevelDifference(true));

        // Both unranked
        $duelComparison = $this->comparison->getActivity(Activity::ACTIVITY_DUEL_TOURNAMENT);
        self::assertEquals(0, $duelComparison->getScoreDifference());
        self::assertFalse($duelComparison->getRankDifference());
    }
}
