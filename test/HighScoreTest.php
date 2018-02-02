<?php

use Villermen\RuneScape\Activity;
use Villermen\RuneScape\HighScore\ActivityHighScore;
use Villermen\RuneScape\HighScore\SkillHighScore;
use PHPUnit\Framework\TestCase;
use Villermen\RuneScape\PlayerDataConverter;
use Villermen\RuneScape\Skill;

class HighScoreTest extends TestCase
{
    /** @var SkillHighScore */
    protected $skillHighScore1;

    /** @var ActivityHighScore */
    protected $activityHighScore1;

    /** @var SkillHighScore */
    protected $skillHighScore2;

    /** @var ActivityHighScore */
    protected $activityHighScore2;

    public function setUp()
    {
        $dataConverter = new PlayerDataConverter();
        $convertedData1 = $dataConverter->convertIndexLite(file_get_contents(__DIR__ . "/fixtures/high-score-test-index-lite.csv"));
        $this->skillHighScore1 = $convertedData1[PlayerDataConverter::KEY_SKILL_HIGH_SCORE];
        $this->activityHighScore1 = $convertedData1[PlayerDataConverter::KEY_ACTIVITY_HIGH_SCORE];

        $convertedData2 = $dataConverter->convertRuneMetrics(file_get_contents(__DIR__ . "/fixtures/high-score-test-runemetrics.json"));
        $this->skillHighScore2 = $convertedData2[PlayerDataConverter::KEY_SKILL_HIGH_SCORE];

        $convertedData3 = $dataConverter->convertIndexLite(file_get_contents(__DIR__ . "/fixtures/high-score-test-index-lite2.csv"));
        $this->activityHighScore2 = $convertedData3[PlayerDataConverter::KEY_ACTIVITY_HIGH_SCORE];
    }

    public function testEntries()
    {
        // Skill
        self::assertEquals(120, $this->skillHighScore1->getSkill(Skill::SKILL_INVENTION)->getLevel());
        self::assertEquals(99, $this->skillHighScore1->getSkill(Skill::SKILL_SMITHING)->getLevel());
        self::assertEquals(106, $this->skillHighScore1->getSkill(Skill::SKILL_SMITHING)->getLevel(true));
        self::assertEquals(2733, $this->skillHighScore1->getSkill(Skill::SKILL_TOTAL)->getLevel(true));
        self::assertNull($this->skillHighScore1->getSkill(200));

        $invSkill = $this->skillHighScore1->getSkill(Skill::SKILL_INVENTION);
        self::assertEquals(120, $invSkill->getLevel());
        self::assertEquals(123, $invSkill->getLevel(true));
        self::assertEquals(89610570, $invSkill->getXp());
        self::assertEquals(2402334, $invSkill->getXpToNextLevel());
        self::assertEquals(29334, $invSkill->getRank());
        self::assertSame(Skill::getSkill(Skill::SKILL_INVENTION), $invSkill->getSkill());
        self::assertEquals(2402334, $invSkill->getXpToNextLevel());
        self::assertEquals(0.1846, round($invSkill->getProgressToNextLevel(), 4));

        // Activity
        self::assertEquals(1362803, $this->activityHighScore1->getActivity(Activity::ACTIVITY_DOMINION_TOWER)->getScore());
        self::assertNull($this->activityHighScore1->getActivity(200));
        self::assertFalse($this->activityHighScore1->getActivity(Activity::ACTIVITY_GIELINOR_GAMES_RESOURCE_RACE)->getRank());
        self::assertEquals(0, $this->activityHighScore1->getActivity(Activity::ACTIVITY_WORLD_EVENT_2_BANDOS_KILLS)->getScore());

    }

    public function testCombatLevel()
    {
        self::assertEquals(138, $this->skillHighScore1->getCombatLevel());
        self::assertEquals(126, $this->skillHighScore1->getCombatLevel(false));
        self::assertEquals(157, $this->skillHighScore1->getCombatLevel(true, true));
    }

    public function testComparison()
    {
        $skillComparison = $this->skillHighScore1->compareTo($this->skillHighScore2);

        // Skill
        $totalComparison = $skillComparison->getSkill(Skill::SKILL_TOTAL);
        self::assertEquals(424, $totalComparison->getLevelDifference());
        self::assertEquals(786160152, $totalComparison->getXpDifference());
        self::assertEquals(155432, $totalComparison->getRankDifference());

        // One unranked
        $invComparison = $skillComparison->getSkill(Skill::SKILL_INVENTION);
        self::assertEquals(119, $invComparison->getLevelDifference());
        self::assertEquals(89610570, $invComparison->getXpDifference());
        self::assertFalse($invComparison->getRankDifference());

        // Uncapped level
        self::assertEquals(122, $invComparison->getLevelDifference(true));

        $activityComparison = $this->activityHighScore1->compareTo($this->activityHighScore2);

        // Activity, negative
        $fogComparison = $activityComparison->getActivity(Activity::ACTIVITY_FIST_OF_GUTHIX);
        self::assertEquals(-1056, $fogComparison->getScoreDifference());
        self::assertEquals(-101623, $fogComparison->getRankDifference());

        // Both unranked
        $duelComparison = $activityComparison->getActivity(Activity::ACTIVITY_DUEL_TOURNAMENT);
        self::assertEquals(0, $duelComparison->getScoreDifference());
        self::assertFalse($duelComparison->getRankDifference());
    }
}
