<?php
declare(strict_types=1);

namespace Ifera\PurePermsScore\listeners;

use Ifera\PurePermsScore\Main;
use Ifera\ScoreHud\event\TagsResolveEvent;
use pocketmine\event\Listener;

class TagResolveListener implements Listener {

    private Main $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    public function onTagResolve(TagsResolveEvent $event): void {
        $player = $event->getPlayer();
        $tag = $event->getTag();
        $tags = explode('.', $tag->getName(), 2);

        if ($tags[0] !== 'ppscore' || count($tags) < 2) {
            return;
        }

        $value = match ($tags[1]) {
            "rank" => $this->plugin->getPlayerRank($player),
            "prefix" => $this->plugin->getPrefix($player),
            "suffix" => $this->plugin->getSuffix($player),
            default => "",
        };

        $tag->setValue($value);
    }
}