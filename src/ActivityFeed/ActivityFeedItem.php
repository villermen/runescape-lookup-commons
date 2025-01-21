<?php

namespace Villermen\RuneScape\ActivityFeed;

class ActivityFeedItem
{
    /**
     * Time difference between same activity reported in Adventurer's log and RuneMetrics can be up to 25 hours...
     * Adventurer's log: yesterday 0:00, RuneMetrics today 0:59. Something probably doesn't go right with timezones on
     * the Adventurer's log.
     */
    protected const COMPARISON_TIME_TOLERANCE = 25 * 60 * 60;

    public readonly string $title;

    public readonly string $description;

    public function __construct(
        public readonly \DateTimeImmutable $time,
        string $title,
        string $description
    ) {
        $this->title = trim($title);
        $this->description = trim($description);
    }

    /**
     * Compares this feed item to another. Only the date part of the time is compared, because adventurer's log feeds
     * only contain accurate date parts.
     */
    public function equals(ActivityFeedItem $otherItem): bool
    {
        return (
            abs($this->time->getTimestamp() - $otherItem->time->getTimestamp()) <= self::COMPARISON_TIME_TOLERANCE &&
            $this->title === $otherItem->title &&
            $this->description === $otherItem->description
        );
    }
}
