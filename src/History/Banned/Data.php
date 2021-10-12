<?php

namespace History\Banned;

use History\History;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as A;

class Data {

    /**
     * @param String $playerName
     * @param String $reason
     * @param String $address |null
     * @param String $senderName |null
     * @param bool $isPermanent |false
     * @param Int $dataTime |null
     */
    public static function addBan(string $playerName, string $reason, string $senderName = null, bool $isPermanent = false, $dataTime = null, string $address = null) {
        $date = date("d/m/y H:i:s");
        if ($isPermanent) {
            //TODO:
            $config = new Config(History::getInstance()->getDataFolder() . "players_banneds.yml", Config::YAML);
            $config->set($playerName, ["sender_name" => $senderName, "reason_of_ban" => $reason, "date" => $date]);
            $config->save();
        } else {
            //TODO:
            $config = new Config(History::getInstance()->getDataFolder() . "players_timebanneds.yml", Config::YAML);
            $config->set($playerName, ["sender_name" => $senderName, "reason_of_ban" => $reason, "time_ban" => $dataTime, "date" => $date]);
            $config->save();
        }
    }

    /**
     * @param String $playerName
     * @param bool $isPermanent
     */
    public static function deleteBan(string $playerName, bool $isPermanent = false) {
        if ($isPermanent) {
            //TODO:
            $config = new Config(History::getInstance()->getDataFolder() . "players_banneds.yml", Config::YAML);
            $config->remove($playerName);
            $config->save();
        } else {
            //TODO:
            $config = new Config(History::getInstance()->getDataFolder() . "players_timebanneds.yml", Config::YAML);
            $config->remove($playerName);
            $config->save();
        }
    }

    /**
     * @param String $playerName
     * @return bool
     */
    public static function isPermanentlyBanned(string $playerName): bool {
        $config = new Config(History::getInstance()->getDataFolder() . "players_banneds.yml", Config::YAML);
        if ($config->exists($playerName)) {
            return true;
        } else {
            return false;
        }
        return false;
    }

    /**
     * @param String $playerName
     * @return bool
     */
    public static function isTemporarilyBanned(string $playerName): bool {
        $config = new Config(History::getInstance()->getDataFolder() . "players_timebanneds.yml", Config::YAML);
        if ($config->exists($playerName)) {
            return true;
        } else {
            return false;
        }
        return false;
    }

    /**
     * @param String $playerName
     * @param String $reason
     * @param Int $warns
     */
    public static function registerWarn(string $playerName, string $senderName, string $reason, int $warns = 1) {
        $config = new Config(History::getInstance()->getDataFolder() . "WarnsData.yml", Config::YAML);
        $result = $config->get($playerName, []);
        $count = count($result);
        if ($count === 0) {
            $result[0] = ["playerName" => $playerName];
            $count++;
        }
        $result[$count] = ["reason" => $reason, "warned_by" => $senderName, "date" => date("d/m/y H:i:s"), "warn_id" => $count];
        $config->set($playerName, $result);
        $config->save();
    }

    /**
     * @param String $playerName
     */
    public static function deleteWarn(string $playerName, $player = null, $value = false, ?int $warnId = null) {
        if ($value) {
            $config = new Config(History::getInstance()->getDataFolder() . "WarnsData.yml", Config::YAML);
            $result = $config->get($playerName);
            if (self::isWarn($playerName)) {
                $count = count($result);
                $count--;
                unset($result[$count]);
                $config->set($playerName, $result);
                $config->save();
            } else {
                $player->sendMessage(A::RED . "There is no such player warned on the server");
            }
        }
    }
    public static function isWarn(String $playerName) : bool {
        $config = new Config(History::getInstance()->getDataFolder()."WarnsData.yml", Config::YAML);
        if($config->exists($playerName)){
            return true;
        }else{
            return false;
        }
        return false;
    }
}