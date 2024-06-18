<?php

namespace ZaaDCraft\DoubleJump;

use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\event\player\PlayerJumpEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\world\particle\FlameParticle;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;

class DoubleJump extends PluginBase implements Listener {

    private $doubleJumpPlayers = [];

    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onJoin(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();
        $this->doubleJumpPlayers[$player->getName()] = true; // Enable double jump on join
        $player->sendMessage("Double Jump activated!");
    }

    public function onJump(PlayerJumpEvent $event): void {
        $player = $event->getPlayer();
        if(isset($this->doubleJumpPlayers[$player->getName()])) {
            $player->setMotion(new Vector3(0, 0.8, 0)); // Set the jump motion

            // Add flame particles
            $level = $player->getWorld();
            $pos = $player->getPosition();
            for($i = -0.3; $i <= 0.3; $i += 0.3) {
                if($i != 0) {
                    $level->addParticle(new Vector3($pos->x + $i, $pos->y, $pos->z), new FlameParticle());
                    $level->addParticle(new Vector3($pos->x, $pos->y, $pos->z + $i), new FlameParticle());
                }
            }

            // Play ghast sound
            $pk = new PlaySoundPacket();
            $pk->soundName = "mob.ghast.scream";
            $pk->x = (int) $player->getPosition()->x;
            $pk->y = (int) $player->getPosition()->y;
            $pk->z = (int) $player->getPosition()->z;
            $pk->volume = 1;
            $pk->pitch = 1;
            $player->getNetworkSession()->sendDataPacket($pk);
        }
    }
}