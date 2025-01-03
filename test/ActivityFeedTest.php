<?php

namespace Villermen\RuneScape\Test;

use PHPUnit\Framework\TestCase;
use Villermen\RuneScape\ActivityFeed\ActivityFeed;
use Villermen\RuneScape\ActivityFeed\ActivityFeedItem;

class ActivityFeedTest extends TestCase
{
    private ActivityFeed $feed1;

    private ActivityFeed $feed2;

    protected function setUp(): void
    {
        $this->feed1 = new ActivityFeed([
            new ActivityFeedItem(
                new \DateTimeImmutable('2025-01-05 22:00:00'),
                'Levelled up Divination.',
                'I levelled my Divination skill, I am now level 38.'
            ),
            new ActivityFeedItem(
                new \DateTimeImmutable('2025-01-03 04:15:00'),
                'I killed 2 boss monsters in Daemonheim.',
                'I killed 2 boss monsters   called:  Night-gazer Khighorahk and a sagittare   in Daemonheim.'
            ),
            new ActivityFeedItem(
                new \DateTimeImmutable('2025-01-03 21:45:00'),
                'Quest complete: The Jack of Spades',
                'I\'ve been invited into the city of Menaphos, but already it\'s clear this place has a lot of problems.'
            ),
        ]);
        $this->feed2 = new ActivityFeed([
            new ActivityFeedItem(
                new \DateTimeImmutable('2025-01-05 00:00:00'),
                'Levelled up Divination.',
                'I levelled my Divination skill, I am now level 39.'
            ),
            new ActivityFeedItem(
                new \DateTimeImmutable('2025-01-05 00:00:00'),
                'Levelled up Divination.',
                'I levelled my Divination skill, I am now level 38.'
            ),
            new ActivityFeedItem(
                new \DateTimeImmutable('2025-01-03 00:00:00'),
                'I killed 2 boss monsters in Daemonheim.',
                'I killed 2 boss monsters   called:  Night-gazer Khighorahk and a sagittare   in Daemonheim.'
            ),
        ]);
    }

    public function testMerge(): void
    {
        $itemsAfter = $this->feed2->getItemsAfter($this->feed1->getItems()[1]);
        self::assertCount(2, $itemsAfter);
        self::assertEquals('I levelled my Divination skill, I am now level 38.', $itemsAfter[1]->getDescription());

        $mergedFeed = $this->feed1->merge($this->feed2);
        self::assertNotSame($this->feed1, $mergedFeed);
        self::assertCount(4, $mergedFeed->getItems());
    }
}
