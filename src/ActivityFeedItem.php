<?php

namespace Villermen\RuneScape;

use DateTime;

class ActivityFeedItem
{
    /** @var string */
    private $id;

    /** @var DateTime */
    private $time;

    /** @var string */
    private $title;

    /** @var string */
    private $description;

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
