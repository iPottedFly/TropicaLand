<?php

namespace History;

use pocketmine\utils\Config;

class Time {

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