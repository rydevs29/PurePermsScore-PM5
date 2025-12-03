<?php
declare(strict_types=1);

namespace Ifera\PurePermsScore\listeners;

use _64FF00\PurePerms\event\PPGroupChangedEvent;
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
        // Cek dasar, meski biasanya di onJoin player pasti online
        if (!$player->isOnline()) {
            return;
        }
        $this->sendUpdate($player);
    }

    /**
     * @param PPGroupChangedEvent $event
     * @phpstan-ignore-next-line
     */
    public function onGroupChange(PPGroupChangedEvent $event): void {
        // Method getPlayer() di PurePerms mungkin mengembalikan IPlayer/OfflinePlayer
        // Jadi kita harus memastikan itu adalah Player online
        $player = $event->getPlayer();
        
        if (!$player instanceof Player || !$player->isOnline()) {
            return;
        }
        $this->sendUpdate($player);
    }

    // Mendeteksi perubahan prefix/suffix melalui chat
    public function onPlayerChat(PlayerChatEvent $event): void {
        $this->sendUpdate($event->getPlayer());
    }

    private function sendUpdate(Player $player): void {
        // Pastikan ScoreHud terinstall agar class ini ada
        if (class_exists(PlayerTagsUpdateEvent::class) && class_exists(ScoreTag::class)) {
            (new PlayerTagsUpdateEvent($player, [
                new ScoreTag("ppscore.rank", $this->plugin->getPlayerRank($player)),
                new ScoreTag("ppscore.prefix", $this->plugin->getPrefix($player)),
                new ScoreTag("ppscore.suffix", $this->plugin->getSuffix($player))
            ]))->call();
        }
    }
}
