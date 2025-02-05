<?php
declare(strict_types=1);

namespace Ifera\PurePermsScore\listeners;

use _64FF00\PurePerms\event\PPGroupChangedEvent;
use _64FF00\PurePerms\PurePerms;
use Ifera\PurePermsScore\Main;
use Ifera\ScoreHud\event\PlayerTagsUpdateEvent;
use Ifera\ScoreHud\scoreboard\ScoreTag;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\player\Player;

class EventListener implements Listener {

    private Main $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    public function onJoin(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();
        if (!$player->isOnline()) {
            return;
        }
        $this->sendUpdate($player);
    }

    public function onGroupChange(PPGroupChangedEvent $event): void {
        $player = $event->getPlayer();
        if (!$player instanceof Player || !$player->isOnline()) {
            return;
        }
        $this->sendUpdate($player);
    }

    // Mendeteksi perubahan prefix/suffix melalui chat sebagai solusi sementara
    public function onPlayerChat(PlayerChatEvent $event): void {
        $this->sendUpdate($event->getPlayer());
    }

    private function sendUpdate(Player $player): void {
        (new PlayerTagsUpdateEvent($player, [
            new ScoreTag("ppscore.rank", $this->plugin->getPlayerRank($player)),
            new ScoreTag("ppscore.prefix", $this->plugin->getPrefix($player)),
            new ScoreTag("ppscore.suffix", $this->plugin->getSuffix($player))
        ]))->call();
    }
}