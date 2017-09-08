<?php

use Villermen\RuneScape\ActivityFeed\ActivityFeed;
use PHPUnit\Framework\TestCase;
use Villermen\RuneScape\Player;

class ActivityFeedTest extends TestCase
{
    /** @var ActivityFeed */
    private $activityFeed;

    public function setUp()
    {
        $this->activityFeed = new ActivityFeed(file_get_contents(__DIR__ . "/fixtures/activity-feed.xml"));
    }

    public function testGetItems()
    {
        self::assertCount(20, $this->activityFeed->getItems());
        self::assertContains("Jack", $this->activityFeed->getItems()[19]->getTitle());
    }

    public function testGetNewerItems()
    {
        self::assertCount(3, $this->activityFeed->getNewerItems($this->activityFeed->getItems()[3]));
    }
}
