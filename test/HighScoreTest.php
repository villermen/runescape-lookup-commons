<?php

use PHPUnit\Framework\TestCase;
use Villermen\RuneScape\HighScore\Rs3Activity;
use Villermen\RuneScape\HighScore\ActivityHighScore;
use Villermen\RuneScape\HighScore\HighScore;
use Villermen\RuneScape\HighScore\HighScoreSkill;
use Villermen\RuneScape\HighScore\Rs3Skill;
use Villermen\RuneScape\PlayerDataConverter;

class HighScoreTest extends TestCase
{
    /** @var HighScore */
    protected $skillHighScore1;

    /** @var ActivityHighScore */
    protected $activityHighScore1;

    /** @var HighScore */
    protected $skillHighScore2;

    /** @var ActivityHighScore */
    protected $activityHighScore2;

    /** @var HighScore */
    protected $skillHighScore3;

    public function setUp(): void
    {
        $dataConverter = new PlayerDataConverter();
        $convertedData1 = $dataConverter->convertIndexLite(file_get_contents(__DIR__ . "/fixtures/high-score-test-index-lite.csv"));
        $this->skillHighScore1 = $convertedData1[PlayerDataConverter::CACHE_KEY_SKILL_HIGH_SCORE];
        $this->activityHighScore1 = $convertedData1[PlayerDataConverter::CACHE_KEY_ACTIVITY_HIGH_SCORE];

        $convertedData2 = $dataConverter->convertRuneMetrics(file_get_contents(__DIR__ . "/fixtures/high-score-test-runemetrics.json"));
        $this->skillHighScore2 = $convertedData2[PlayerDataConverter::CACHE_KEY_SKILL_HIGH_SCORE];

        $convertedData3 = $dataConverter->convertIndexLite(file_get_contents(__DIR__ . "/fixtures/high-score-test-index-lite2.csv"));
        $this->activityHighScore2 = $convertedData3[PlayerDataConverter::CACHE_KEY_ACTIVITY_HIGH_SCORE];

        $this->skillHighScore3 = new HighScore([
            new HighScoreSkill(Rs3Skill::getSkill(Rs3Skill::SKILL_ATTACK), -1, -1, -1),
            new HighScoreSkill(Rs3Skill::getSkill(Rs3Skill::SKILL_CONSTITUTION), -1, -1, -1)
        ]);
    }

    public function testEntries(): void
    {
        // Skill
        self::assertEquals(120, $this->skillHighScore1->getSkill(Rs3Skill::SKILL_INVENTION)->getLevel());
        self::assertEquals(99, $this->skillHighScore1->getSkill(Rs3Skill::SKILL_SMITHING)->getLevel());
        self::assertEquals(106, $this->skillHighScore1->getSkill(Rs3Skill::SKILL_SMITHING)->getVirtualLevel());
        self::assertEquals(2733, $this->skillHighScore1->getSkill(Rs3Skill::SKILL_TOTAL)->getLevel());
        self::assertEquals(2733, $this->skillHighScore1->getSkill(Rs3Skill::SKILL_TOTAL)->getVirtualLevel());
        self::assertNull($this->skillHighScore1->getSkill(200));

        $invSkill = $this->skillHighScore1->getSkill(Rs3Skill::SKILL_INVENTION);
        self::assertEquals(120, $invSkill->getLevel());
        self::assertEquals(123, $invSkill->getVirtualLevel());
        self::assertEquals(89610570, $invSkill->getXp());
        self::assertEquals(2402334, $invSkill->getXpToNextLevel());
        self::assertEquals(29334, $invSkill->getRank());
        self::assertSame(Rs3Skill::getSkill(Rs3Skill::SKILL_INVENTION), $invSkill->getSkill());
        self::assertEquals(2402334, $invSkill->getXpToNextLevel());
        self::assertEquals(0.1846, round($invSkill->getProgressToNextLevel(), 4));

        // Test division of XP for RuneMetrics
        self::assertEquals(2497610, $this->skillHighScore2->getSkill(Rs3Skill::SKILL_AGILITY)->getXp());

        // Activity
        self::assertEquals(1362803, $this->activityHighScore1->getActivity(Rs3Activity::ACTIVITY_DOMINION_TOWER)->getScore());
        self::assertNull($this->activityHighScore1->getActivity(200));
        self::assertNull($this->activityHighScore1->getActivity(Rs3Activity::ACTIVITY_GIELINOR_GAMES_RESOURCE_RACE)->getRank());
        self::assertEquals(0, $this->activityHighScore1->getActivity(Rs3Activity::ACTIVITY_WORLD_EVENT_2_BANDOS_KILLS)->getScore());

        // Constitution initialized with no level
        self::assertEquals(10, $this->skillHighScore3->getSkill(Rs3Skill::SKILL_CONSTITUTION)->getLevel());
        self::assertEquals(1154, $this->skillHighScore3->getSkill(Rs3Skill::SKILL_CONSTITUTION)->getXp());
    }

    public function testCombatLevel(): void
    {
        self::assertEquals(138, $this->skillHighScore1->getCombatLevel());
        self::assertEquals(126, $this->skillHighScore1->getCombatLevel(false));

        // With only attack and constitution set
        self::assertEquals(3, $this->skillHighScore3->getCombatLevel());
    }

    public function testComparison(): void
    {
        $skillComparison = $this->skillHighScore1->compareTo($this->skillHighScore2);

        // Skill
        $totalComparison = $skillComparison->getSkill(Rs3Skill::SKILL_TOTAL);
        self::assertEquals(424, $totalComparison->getLevelDifference());
        self::assertEquals(786160152, $totalComparison->getXpDifference());
        self::assertEquals(155432, $totalComparison->getRankDifference());

        // One unranked
        $invComparison = $skillComparison->getSkill(Rs3Skill::SKILL_INVENTION);
        self::assertEquals(119, $invComparison->getLevelDifference());
        self::assertEquals(89610570, $invComparison->getXpDifference());
        self::assertNull($invComparison->getRankDifference());

        // Uncapped level
        self::assertEquals(122, $invComparison->getVirtualLevelDifference());

        $activityComparison = $this->activityHighScore1->compareTo($this->activityHighScore2);

        // Activity, negative
        $fogComparison = $activityComparison->getActivity(Rs3Activity::ACTIVITY_FIST_OF_GUTHIX);
        self::assertEquals(-1056, $fogComparison->getScoreDifference());
        self::assertEquals(-101623, $fogComparison->getRankDifference());

        // Both unranked
        $duelComparison = $activityComparison->getActivity(Rs3Activity::ACTIVITY_DUEL_TOURNAMENT);
        self::assertEquals(0, $duelComparison->getScoreDifference());
        self::assertNull($duelComparison->getRankDifference());
    }
}
