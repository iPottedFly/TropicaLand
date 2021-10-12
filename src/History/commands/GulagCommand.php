<?php

namespace History\commands;

use History\History;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\level\Location;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat as A;

class GulagCommand extends Command {

    /**
     * @var History
     */
    private $plugin;

    public function __construct(History $plugin) {
        $this->plugin = $plugin;
        parent::__construct("gulag", "Send a Player to Gulag", "/gulag");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {

        if($sender->hasPermission("gulag.send.use")) {
            $sender->sendMessage(A::RED . "You cannot execute this command!");
            return;
        }
        if(!isset($args[0])||!isset($args[1])) {
            $sender->sendMessage(A::RED . "Usage: /gulag [string:player] [string:reason]");
            return;
        }
        $player = Server::getInstance()->getPlayer($args[1]);
        $gulag = Server::getInstance()->getLevelByName("gulag");
        if($player instanceof Player) {
            if(History::isTemporarilyBanned($this->plugin->getServer()->getPlayer($args[0])->getName())) {
                $sender->sendMessage(A::RED."{$this->plugin->getServer()->getPlayer($args[0])->getName()} already banned because he disconnected");
                return;
            }
            $player->teleport(new Location("0", "80", 0, 120, 0, $gulag));
            Server::getInstance()->broadcastMessage(History::PREFIX.A::RED."Already in gulag");
            return;
        }
    }
}