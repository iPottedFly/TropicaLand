<?php

namespace History;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as A;

use History\commands\{GiveCmd, GulagCommand, NpcCommand};
use History\npc\NpcDialog;
use History\events\Elevator;

class History extends PluginBase {

    const HISTORY = A::BOLD.A::GREEN."Tropica".A::AQUA."Land".A::RESET;
    const PREFIX = A::BOLD.A::DARK_GRAY."[".A::RED."Alert".A::DARK_GRAY."]".A::RESET;

    protected static $instance;
    private static $configData;

    public static function getSignLine(): int {
        return(int) self::$configData["signs"]["line"];
    }

    public static function getSignText(string $sign, bool $clean = false): string {
        $text = A::colorize(self::$configData["signs"][$sign]);

        if($clean) {
            return A::clean($text);
        }
        return $text;
    }



    public function onLoad() {
        self::$instance = $this;
    }

    public function onEnable() {
        $this->getLogger()->info(self::HISTORY. A::AQUA." fue cargado correctamente!");
        $this->getServer()->getPluginManager()->registerEvents(new PlayerListener(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new Elevator(), $this);
        NpcDialog::register($this);
        $this->registerCommand(new GiveCmd);
        $this->registerCommand(new GulagCommand($this));
        $this->registerCommand(new NpcCommand);
    }

    public static function getInstance(): History {
        return self::$instance;
    }

    private function registerCommand($command) {
        $this->getServer()->getCommandMap()->register("history", $command);
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


    public static function isPermanentlyBanned(String $playerName) : bool {
        $config = new Config(History::getInstance()->getDataFolder()."players_banneds.yml", Config::YAML);
        if($config->exists($playerName)){
            return true;
        }else{
            return false;
        }
        return false;
    }
}