<?php
declare(strict_types=1);

namespace Ifera\PurePermsScore;

use _64FF00\PurePerms\PurePerms;
use Ifera\PurePermsScore\listeners\EventListener;
use Ifera\PurePermsScore\listeners\TagResolveListener;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase {

    private ?PurePerms $purePerms = null;

    protected function onEnable(): void {
        $pluginManager = $this->getServer()->getPluginManager();

        // 1. Simpan ke variabel lokal dulu (tipe: Plugin|null)
        $plugin = $pluginManager->getPlugin("PurePerms");

        // 2. Cek apakah plugin tersebut ADA dan merupakan INSTANCE dari PurePerms
        if (!$plugin instanceof PurePerms) {
            $this->getLogger()->error("PurePerms tidak ditemukan! Plugin akan dinonaktifkan.");
            $pluginManager->disablePlugin($this); // Koreksi: Gunakan variable $pluginManager yang sudah ada
            return;
        }

        // 3. Sekarang aman untuk di-assign karena PHPStan tahu $plugin pasti PurePerms
        $this->purePerms = $plugin;

        // Registrasi event listener
        $pluginManager->registerEvents(new EventListener($this), $this);
        $pluginManager->registerEvents(new TagResolveListener($this), $this);
    }

    public function getPlayerRank(Player $player): string {
        // Tambahkan pengecekan null safety jaga-jaga jika PurePerms error
        if ($this->purePerms === null) return "No Rank";
        
        $group = $this->purePerms->getUserDataMgr()->getGroup($player);
        return $group !== null ? $group->getName() : "No Rank";
    }

    public function getPrefix(Player $player): string {
        if ($this->purePerms === null) return "";

        $prefix = $this->purePerms->getUserDataMgr()->getNode($player, "prefix");
        return (!empty($prefix)) ? (string) $prefix : "No Prefix";
    }

    public function getSuffix(Player $player): string {
        if ($this->purePerms === null) return "";

        $suffix = $this->purePerms->getUserDataMgr()->getNode($player, "suffix");
        return (!empty($suffix)) ? (string) $suffix : "No Suffix";
    }
}
