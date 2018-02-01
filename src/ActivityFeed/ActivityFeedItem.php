<?php

namespace Villermen\RuneScape\ActivityFeed;

use DateTime;

class ActivityFeedItem
{
    // TODO: Remove id
    /** @var string */
    protected $id;

    /** @var DateTime */
    protected $time;

    /** @var string */
    protected $title;

    /** @var string */
    protected $description;

    /**
     * @param string $id
     * @param DateTime $time
     * @param string $title
     * @param string $description
     */
    public function __construct(string $id, DateTime $time, string $title, string $description)
    {
        $this->id = $id;
        $this->time = $time;
        $this->title = $title;
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
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
