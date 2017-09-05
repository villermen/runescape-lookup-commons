<?php

namespace Villermen\RuneScape\ActivityFeed;

use DateTime;
use SimpleXMLElement;
use Villermen\RuneScape\Player;
use Villermen\RuneScape\RuneScapeException;

class ActivityFeed
{
    /** @var Player */
    private $player;

    /** @var ActivityFeedItem[] */
    private $items = [];

    public function __construct(Player $player, string $data)
    {
        $this->player = $player;

        try {
            $feed = new SimpleXmlElement($data);
            $feedItems = $feed->xpath("//item");

            foreach ($feedItems as $feedItem) {
                $id = trim((string)$feedItem->guid);
                $id = substr($id, strripos($id, "id=") + 3);
                $feedTime = new DateTime($feedItem->pubDate);

                $this->items[] = new ActivityFeedItem($id, $feedTime, trim((string)$feedItem->title),
                    trim((string)$feedItem->description));
            }
        } catch (\Exception $ex) {
            throw new RuneScapeException("Could not parse player's activity feed.", 0, $ex);
        }
    }

    /**
     * @return Player
     */
    public function getPlayer(): Player
    {
        return $this->player;
    }

    /**
     * @return ActivityFeedItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * Returns all feed items in this feed that occur after the given item.
     *
     * @param ActivityFeedItem $lastItem
     * @return ActivityFeedItem[]
     */
    public function getNewerItems(ActivityFeedItem $lastItem): array
    {
        $newerItems = [];

        foreach($this->getItems() as $item) {
            if ($lastItem->getId() == $item->getId()) {
                break;
            }

            $newerItems[] = $item;
        }

        return $newerItems;
    }
}
