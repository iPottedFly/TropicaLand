<?php

namespace History\commands;

use History\History;
use History\Time;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\entity\Entity;
use pocketmine\Player;
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

    public function execute(CommandSender $sender, string $commandLabel, array $args, Entity $entity, Player $player) {

        if($sender->hasPermission("gulag.send.use")) {
            $sender->sendMessage(A::RED . "You cannot execute this command!");
            return;
        }
        if(!isset($args[0])||!isset($args[1])) {
            $sender->sendMessage(A::RED . "Usage: /gulag [string:player] [string:reason]");
            return;
        }
        if($this->plugin->getServer()->getPlayer($args[0]) instanceof Player) {
            if(History::isTemporarilyBanned($this->plugin->getServer()->getPlayer($args[0])->getName())) {
                $sender->sendMessage(A::RED."{$this->plugin->getServer()->getPlayer($args[0])->getName()} already banned because he disconnected");
                return;
            }

        }
    }
}