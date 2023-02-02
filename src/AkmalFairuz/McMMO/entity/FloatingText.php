<?php

/*
 *
 *              _                             _        ______             _
 *     /\      | |                           | |      |  ____|           (_)
 *    /  \     | | __   _ __ ___      __ _   | |      | |__       __ _    _    _ __    _   _    ____
 *   / /\ \    | |/ /  | '_ ` _ \    / _` |  | |      |  __|     / _` |  | |  | '__|  | | | |  |_  /
 *  / ____ \   |   <   | | | | | |  | (_| |  | |      | |       | (_| |  | |  | |     | |_| |   / /
 * /_/    \_\  |_|\_\  |_| |_| |_|   \__,_|  |_|      |_|        \__,_|  |_|  |_|      \__,_|  /___|
 *
 * Discord: akmal#7191
 * GitHub: https://github.com/AkmalFairuz
 *
 */

namespace AkmalFairuz\McMMO\entity;

use pocketmine\entity\Human;

use pocketmine\player\Player;

use pocketmine\nbt\tag\CompoundTag;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\network\mcpe\protocol\SetActorDataPacket;
use pocketmine\utils\TextFormat;

use AkmalFairuz\McMMO\Main;

class FloatingText extends Human {

    public $updateTick = 0;

    public $type = 0;

    public function getName() : string {
		return "FloatingText";
	}

	public function initEntity(CompoundTag $nbt) : void {
		parent::initEntity($nbt);
		$this->setNameTagAlwaysVisible(true);
		$this->setScale(0.0000000000000000000000000000000001);
		$this->updateTick = 0;
        $this->type = $this->namedtag->getInt("type");
	}

	public function onUpdate(int $currentTick) : bool {
        parent::onUpdate($currentTick);
        $this->setImmobile(true);
		$this->updateTick++;
        if($this->updateTick == 20) {
            $this->updateTick = 0;
            $a = ["Lumberjack", "Farmer", "Excavation", "Miner", "Killer", "Combat", "Builder", "Consumer", "Archer", "Lawn Mower"];
            $l = "";
            $i = 0;
            $lead = Main::getInstance()->getAll($this->type);
            arsort($lead);
            foreach($lead as $k => $o) {
                if($i == 20) break;
                $i++;
                $l .= TextFormat::RED. $i . ") " . TextFormat::GREEN . $k . TextFormat::RED . " : " . TextFormat::BLUE . "Lv. " . $o . "\n";
            }
            $this->setNameTag(TextFormat::BOLD . TextFormat::AQUA . "MCMMO Leaderboard\n" . TextFormat::RESET . TextFormat::YELLOW . $a[$this->type] . TextFormat::RESET . "\n\n".$l);
            foreach ($this->getViewers() as $player) {
                $this->sendNameTag($player);
            }
        }
        return true; 
    }

	public function sendNameTag(Player $player): void {
        $pk = new SetActorDataPacket();
        $pk->entityRuntimeId = $this->getId();
        $pk->metadata = [self::DATA_NAMETAG => [self::DATA_TYPE_STRING, $this->getNameTag()]];
        $player->getNetworkSession()->sendDataPacket($pk);
    }

	public function attack(EntityDamageEvent $source) : void {
		$source->cancel();
	}
}
