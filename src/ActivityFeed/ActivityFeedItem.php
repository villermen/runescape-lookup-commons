<?php

namespace Villermen\RuneScape\ActivityFeed;

use DateTime;

class ActivityFeedItem
{
    /** @var DateTime */
    protected $time;

    /** @var string */
    protected $title;

    /** @var string */
    protected $description;

    /**
     * @param DateTime $time
     * @param string $title
     * @param string $description
     */
    public function __construct(DateTime $time, string $title, string $description)
    {
        $this->time = $time;
        $this->title = $title;
        $this->description = $description;
    }

    /**
     * @return DateTime
     */
    public function getTime(): DateTime
    {
        return $this->time;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Compares this feed item to another.
     * Only the date part of the time is compared, because adventurer's log feeds only contain accurate date parts.
     *
     * @param ActivityFeedItem $otherItem
     * @return bool
     */
    public function equals(ActivityFeedItem $otherItem): bool
    {
        return $this->getTime()->format("Ymd") == $otherItem->getTime()->format("Ymd") &&
            $this->getTitle() == $otherItem->getTitle() &&
            $this->getDescription() == $otherItem->getDescription();
    }
}
