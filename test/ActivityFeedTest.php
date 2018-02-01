<?php

use Villermen\RuneScape\ActivityFeed\ActivityFeed;
use PHPUnit\Framework\TestCase;
use Villermen\RuneScape\Player;

class ActivityFeedTest extends TestCase
{
    /** @var ActivityFeed */
    private $activityFeed1;

    /** @var ActivityFeed */
    private $activityFeed2;

    public function setUp()
    {
        $this->activityFeed1 = ActivityFeed::fromData(new Player("Ardanwen"), file_get_contents(__DIR__ . "/fixtures/activity-feed1.xml"));
        $this->activityFeed2 = ActivityFeed::fromData(new Player("Ardanwen"), file_get_contents(__DIR__ . "/fixtures/activity-feed2.xml"));
    }

    public function testGetItems()
    {
        self::assertCount(20, $this->activityFeed1->getItems());
        self::assertCount(11, $this->activityFeed2->getItems());

        self::assertEquals("Quest complete: The Jack of Spades", $this->activityFeed1->getItems()[19]->getTitle());
        self::assertStringStartsWith("I've", $this->activityFeed1->getItems()[19]->getDescription());
        self::assertEquals(new DateTime("2017-08-17 0:00", new DateTimeZone("UTC")), $this->activityFeed1->getItems()[19]->getTime());
        self::assertEquals("787487639", $this->activityFeed1->getItems()[19]->getId());
    }

    public function testGetItemsAfter()
    {
        self::assertCount(5, $this->activityFeed2->getItemsAfter($this->activityFeed1->getItems()[0]));
    }

    public function testMerge()
    {
        $mergedActivityFeed = $this->activityFeed1->merge($this->activityFeed2);
        self::assertNotSame($this->activityFeed1, $mergedActivityFeed);
        self::assertCount(25, $mergedActivityFeed->getItems());
    }
}
