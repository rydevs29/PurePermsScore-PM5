<?php
declare(strict_types=1);

namespace Ifera\PurePermsScore;

use _64FF00\PurePerms\PurePerms;
use Ifera\PurePermsScore\listeners\EventListener;
use Ifera\PurePermsScore\listeners\TagResolveListener;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\PluginManager;

class Main extends PluginBase {

    private ?PurePerms $purePerms = null;

    protected function onEnable(): void {
        $pluginManager = $this->getServer()->getPluginManager();

        // Cek apakah PurePerms tersedia
        $this->purePerms = $pluginManager->getPlugin("PurePerms");
        if (!$this->purePerms instanceof PurePerms) {
            $this->getLogger()->error("PurePerms tidak ditemukan! Plugin akan dinonaktifkan.");
            $this->getServer()->getPluginManager()->disablePlugin($this);
            return;
        }

        // Registrasi event listener
        $pluginManager->registerEvents(new EventListener($this), $this);
        $pluginManager->registerEvents(new TagResolveListener($this), $this);
    }

    public function getPlayerRank(Player $player): string {
        $group = $this->purePerms->getUserDataMgr()->getGroup($player);
        return $group !== null ? $group->getName() : "No Rank";
    }

    public function getPrefix(Player $player): string {
        $prefix = $this->purePerms->getUserDataMgr()->getNode($player, "prefix");
        return (!empty($prefix)) ? (string) $prefix : "No Prefix";
    }

    public function getSuffix(Player $player): string {
        $suffix = $this->purePerms->getUserDataMgr()->getNode($player, "suffix");
        return (!empty($suffix)) ? (string) $suffix : "No Suffix";
    }
}