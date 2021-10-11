<?php

namespace History\npc;

use pocketmine\plugin\Plugin;

class NpcDialog {

    static private $registered = false;

    static public function register(Plugin $plugin): void {
        if(!self::$registered) {
            $plugin->getServer()->getPluginManager()->registerEvents(new PacketListener(), $plugin);
            self::$registered = true;
        }
    }
}