<?php

use Villermen\RuneScape\Constants;
use PHPUnit\Framework\TestCase;
use Villermen\RuneScape\Highscore\HighscoreActivity;
use Villermen\RuneScape\Highscore\HighscoreSkill;

class HighscoreEntryTest extends TestCase
{
    public function testCompareTo()
    {
        $attack1 = new HighscoreSkill(Constants::getSkill(Constants::SKILL_ATTACK), -1, -1, -1);
        $attack2 = new HighscoreSkill(Constants::getSkill(Constants::SKILL_ATTACK), 1000000, 34, 21856);
        $crucible1 = new HighscoreActivity(Constants::getActivity(Constants::ACTIVITY_CRUCIBLE), 10000, 20);
        $crucible2 = new HighscoreActivity(Constants::getActivity(Constants::ACTIVITY_CRUCIBLE), 34000, 5);

        $comparison1 = $attack1->compareTo($attack2);
        self::assertEquals(false, $comparison1->getRankDifference());
        self::assertEquals(-33, $comparison1->getLevelDifference());
        self::assertEquals(-21856, $comparison1->getXpDifference());

        $comparison2 = $crucible1->compareTo($crucible2);
        self::assertEquals(24000, $comparison2->getRankDifference());
        self::assertEquals(15, $comparison2->getScoreDifference());
    }
}
