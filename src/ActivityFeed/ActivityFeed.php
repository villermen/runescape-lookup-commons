<?php

namespace Villermen\RuneScape\ActivityFeed;

use DateTime;
use Exception;
use SimpleXMLElement;
use Villermen\RuneScape\Player;
use Villermen\RuneScape\RuneScapeException;

class ActivityFeed
{
    /** @var Player */
    protected $player;

    /** @var ActivityFeedItem[] */
    protected $items = [];

    /**
     * @param Player $player
     * @param ActivityFeedItem[] $items
     */
    public function __construct(Player $player, array $items)
    {
        $this->player = $player;
        $this->items = array_values($items);
    }

    /**
     * Generate an ActivityFeed from the given data.
     *
     * @param Player $player
     * @param string $data Data as returned from the Adventurer's Log RSS feed.
     * @return ActivityFeed
     * @throws RuneScapeException
     */
    public static function fromData(Player $player, string $data): ActivityFeed
    {
        $feedItems = [];

        try {
            $feed = new SimpleXmlElement($data);
        } catch (Exception $exception) {
            throw new RuneScapeException("Could not parse the activity feed as XML.");
        }

        $itemElements = @$feed->xpath("//item");

        if ($itemElements === false) {
            throw new RuneScapeException("Could not obtain feed items from feed.");
        }

        foreach ($itemElements as $itemElement) {
            $id = trim((string)$itemElement->guid);
            $id = substr($id, strripos($id, "id=") + 3);
            $time = new DateTime($itemElement->pubDate);
            $title = trim((string)$itemElement->title);
            $description = trim((string)$itemElement->description);

            if (!$id || !$time || !$title || !$description) {
                throw new RuneScapeException(sprintf(
                    "Could not parse one of the activity feed items. (id: %s, time: %s, title: %s, description: %s)",
                    $id, $time ? $time->format("j-n-Y") : "", $title, $description
                ));
            }

            $feedItems[] = new ActivityFeedItem($id, $time, $title, $description);
        }

        return new ActivityFeed($player, $feedItems);
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
     * @param ActivityFeedItem $targetItem
     * @return ActivityFeedItem[]
     */
    public function getItemsAfter(ActivityFeedItem $targetItem): array
    {
        $newerItems = [];

        foreach($this->getItems() as $item) {
            if ($targetItem->getId() === $item->getId()) {
                break;
            }

            $newerItems[] = $item;
        }

        return $newerItems;
    }

    /**
     * Merges this ActivityFeed with a newer feed.
     * Returns a new feed with all new items from the newerFeed prepended to it.
     *
     * @param ActivityFeed $newerFeed
     * @return ActivityFeed
     */
    public function merge(ActivityFeed $newerFeed): ActivityFeed
    {
        if (count($this->getItems()) > 0) {
            $prepend = $newerFeed->getItemsAfter($this->getItems()[0]);
        } else {
            $prepend = $newerFeed->getItems();
        }

        return new ActivityFeed($this->player, array_merge($prepend, $this->getItems()));
    }

    /**
     * @return Player
     */
    public function getPlayer(): Player
    {
        return $this->player;
    }
}
