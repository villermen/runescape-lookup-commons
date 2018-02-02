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
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}
