<?php

namespace History;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as A;

use History\commands\{GiveCmd, NpcCommand};
use History\npc\NpcDialog;

class History extends PluginBase {

    const HISTORY = A::BOLD.A::GREEN."Tropica".A::AQUA."Land".A::RESET;
    const VALID_FORMATS = ["minutes", "hours", "seconds", "days"];

    protected static $logger = null, $data = null, $provider = null, $mysql = null;

    public function onEnable() {
        $this->getLogger()->info(self::HISTORY. A::AQUA." fue cargado correctamente!");
        $this->getServer()->getPluginManager()->registerEvents(new PlayerListener(), $this);
        NpcDialog::register($this);
        $this->registerCommand(new GiveCmd);
        $this->registerCommand(new NpcCommand);
    }

    public static function getInstance(): History {
        if(self::$logger === null) {
            throw new \RuntimeException("History > Couldn't create instance of variable!");
        }
        return self::$logger;
    }

    private function registerCommand($command) {
        $this->getServer()->getCommandMap()->register("history", $command);
    }

    public static function intToString(String $timeformat = null) {
        $format = str_split($timeformat);
        $time = null;
        for($t = 0; $t < count($format); $t++) {
            switch($format[$t]) {
                case "m":
                    $time = "minutes";
                    break;
                case "h":
                    $time = "hours";
                    break;
                case "d":
                    $time = "days";
                    break;
                case "s":
                    $time = "seconds";
                    break;
            }
        }
        return $time;
    }

    public static function getFormatTime(int $time, String $timeFormat): int {
        $value = null;
        switch(self::intToString($timeFormat)) {
            case "minutes":
                $value = time() + ($time * 60);
                break;
            case "hours":
                $value = time() + ($time * 3600);
                break;
            case "days":
                $value = time() + ($time * 86400);
                break;
            case "seconds":
                $value = time() + ceil($time);
                break;
        }
        return $value;
    }

    public static function stringToInt(String $timeformat = null): ?int {
        $format = str_split($timeformat);
        for($t = 0; $t < count($format); $t++) {
            if(is_numeric($format[$t])) {
                return $format[$t];
            }
        }
    }

    public static function isTemporarilyBanned(String $playername): bool {
        $config = new Config(History::getInstance()->getDataFolder()."players_banneds.yml", Config::YAML);
        if($config->exists($playername)) {
            return true;
        } else {
            return false;
        }
        return false;
    }

    public static function deleteBan(String $playername) {
        $config = new Config(History::getInstance()->getDataFolder()."players_banneds.yml", Config::YAML);
        $config->remove($playername);
        $config->save();
    }

    public static function addBan(String $playername) {
        $date = date("d/m/y H:i:s");
        $config = new Config(History::getInstance()->getDataFolder()."players_banneds.yml", Config::YAML);
        $config->set($playername, ["sender_name" => $senderName, "reason_ban" => $reason, "time_ban" => $time, "date" => $date]);
        $config->save();
    }
}