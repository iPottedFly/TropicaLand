<?php

namespace History\commands;

use AdvancedBan\Loader;
use History\Banned\Data;
use History\Banned\Time;
use History\History;
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
            $argument = implode(" ", $args);
            $exploded = explode(" ", $argument);
            //TODO:
            unset($exploded[0]);
            unset($exploded[1]);
            $reason = implode(" ", $exploded);

            Data::addBan($this->plugin->getServer()->getPlayer($args[0])->getName(), $reason, $sender->getName(), false, Time::getFormatTime(Time::stringToInt($args[1]), $args[1]));
            $this->plugin->getServer()->broadcastMessage(History::PREFIX.A::BOLD.A::GOLD.$this->plugin->getServer()->getPlayer($args[0])->getName().A::RESET.A::GRAY." was temporarily banned of the network by ".A::BOLD.A::YELLOW.$sender->getName().A::RESET.A::GRAY." for the reason of ".A::BOLD.A::GOLD.$reason.A::RESET);
            $this->plugin->getServer()->getPlayer($args[0])->close("", A::BOLD.A::RED."You were banned from the server temporarily".A::RESET."\n".A::GRAY."You were banned by: ".A::AQUA.$sender->getName().A::RESET."\n".A::GRAY."Reason: ".A::AQUA.$reason.A::RESET."\n".A::GRAY."Date: ".A::AQUA.date("d/m/y H:i:s").A::RESET."\n".A::BLUE."Discord: ".A::AQUA." https://discord.gg/xp7Jnnkcwk");
        } else {
            if(Data::isTemporarilyBanned($args[0])) {
                $sender->sendMessage(A::RED."{$args[0]} already banned form the Server");
                return;
            }
            $argument = implode(" ", $args);
            $exploded = explode(" ", $argument);
            //TODO:
            unset($exploded[0]);
            unset($exploded[1]);
            $reason = implode(" ", $exploded);

            Data::addBan($args[0], $reason, $sender->getName(), false, Time::getFormatTime(Time::stringToInt($args[1]), $args[1]));
            $this->plugin->getServer()->broadcastMessage(History::PREFIX.A::BOLD.A::GOLD.$args[0].A::RESET.A::GRAY." was temporarily banned of the network by ".A::BOLD.A::YELLOW.$sender->getName().A::RESET.A::GRAY." for the reason of ".A::BOLD.A::GOLD.$reason.A::RESET);
        }
    }
}