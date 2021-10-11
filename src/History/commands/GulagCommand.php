<?php

namespace History\commands;

use AdvancedBan\DataBase\Data;
use AdvancedBan\DataBase\Time;
use History\History;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\entity\Entity;
use pocketmine\level\Location;
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

    public function execute(CommandSender $sender, string $commandLabel, array $args, Entity $entity) {
        if($sender->hasPermission("gulag.send.use")) {
            $sender->sendMessage(A::RED."You cannot execute this command!");
            return;
        }
        if(!isset($args[0])) {
            $sender->sendMessage(A::RED."Usage: /gulag [string:player] [int:time] [string:reason]");
            return;
        }
        if(!in_array(History::intToString($args[0]), History::VALID_FORMATS)) {
            $sender->sendMessage(A::RED."The format time is invalid");
            return;
        }
        if($this->plugin->getServer()->getPlayer($args[0]) instanceof Player) {
            if(History::isTemporarilyBanned($this->plugin->getServer()->getPlayer($args[0])->getName())) {
                $sender->sendMessage(A::RED."{$this->plugin->getServer()->getPlayer($args[0])->getName()} already in gulag!");
                return;
            }
            $argument = implode(" ", $args);
            $exploded = explode(" ", $argument);
            unset($exploded[0]);
            unset($exploded[1]);
            $reason = implode(" ", $exploded);

            //History::addBan($this->plugin->getServer()->getPlayer($args[0])->getName(), $reason, $sender->getName(), History::getFormatTime(History::stringToInt($args[1]), $args[1]));
        }
        if($this->plugin->getServer()->getPlayer($args[0]) === null) {
            $sender->sendMessage(A::RED."Player no found!");
            return;
        }
        $gulag = $this->plugin->getServer()->getPlayer($args[0]);
        $gulaglvl = $this->plugin->getServer()->getLevelByName("gulag");
        $entity->teleport(new Location("0", "80", 0, 180, 0, $gulaglvl));
        $sender->sendMessage(A::RED."Haz sido mandado al gulag!");
    }
}