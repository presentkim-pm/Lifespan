<?php

/**
 *  ____                           _   _  ___
 * |  _ \ _ __ ___  ___  ___ _ __ | |_| |/ (_)_ __ ___
 * | |_) | '__/ _ \/ __|/ _ \ '_ \| __| ' /| | '_ ` _ \
 * |  __/| | |  __/\__ \  __/ | | | |_| . \| | | | | | |
 * |_|   |_|  \___||___/\___|_| |_|\__|_|\_\_|_| |_| |_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author       PresentKim (debe3721@gmail.com)
 * @link         https://github.com/PresentKim
 * @license      https://www.gnu.org/licenses/lgpl-3.0 LGPL-3.0 License
 *
 *   (\ /)
 *  ( . .) ♥
 *  c(")(")
 *
 * @noinspection PhpUnused
 */

declare(strict_types=1);

namespace kim\present\lifespan;

use InvalidArgumentException;
use pocketmine\entity\object\ItemEntity;
use pocketmine\entity\projectile\Arrow;
use pocketmine\event\entity\EntitySpawnEvent;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;

use function is_finite;
use function is_int;
use function is_numeric;

final class Main extends PluginBase implements Listener{

    public const MAX_LIFESPAN = 0x7fff;

    /** Ticks that an arrow stuck in a block stays before despawning, hardcoded by the server */
    private const ARROW_DEFAULT_LIFESPAN = 1200;

    private int $itemLifespan = 6000;
    private int $arrowLifespan = self::ARROW_DEFAULT_LIFESPAN;

    protected function onEnable() : void{
        $config = $this->getConfig();
        try{
            $this->setItemLifespan(self::secondsToTicks($config->get("item-lifespan", 300)));
        }catch(InvalidArgumentException $e){
            $this->getLogger()->warning("Invalid item-lifespan in config.yml, using the default of 300 seconds: " . $e->getMessage());
            $this->setItemLifespan(300 * 20);
        }
        try{
            $this->setArrowLifespan(self::secondsToTicks($config->get("arrow-lifespan", 60)));
        }catch(InvalidArgumentException $e){
            $this->getLogger()->warning("Invalid arrow-lifespan in config.yml, using the default of 60 seconds: " . $e->getMessage());
            $this->setArrowLifespan(60 * 20);
        }

        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    /** @priority MONITOR */
    public function onEntitySpawnEvent(EntitySpawnEvent $event) : void{
        $entity = $event->getEntity();
        if($entity instanceof ItemEntity){
            $entity->setDespawnDelay($this->getItemLifespan());
        }
    }

    /** @priority MONITOR */
    public function onProjectileHitEvent(ProjectileHitEvent $event) : void{
        $entity = $event->getEntity();
        if($entity instanceof Arrow){
            $collideTicks = self::ARROW_DEFAULT_LIFESPAN - $this->getArrowLifespan();
            //HACK: run in the scope of Arrow to access its protected property
            (function() use ($collideTicks) : void{
                $this->collideTicks = $collideTicks;
            })->call($entity);
        }
    }

    public function getItemLifespan() : int{
        return $this->itemLifespan;
    }

    public function setItemLifespan(int $value) : void{
        self::assertValidLifespan($value);
        $this->itemLifespan = $value;
    }

    public function getArrowLifespan() : int{
        return $this->arrowLifespan;
    }

    public function setArrowLifespan(int $value) : void{
        self::assertValidLifespan($value);
        $this->arrowLifespan = $value;
    }

    /**
     * Converts a lifespan configured in seconds (int or float) to ticks.
     *
     * @throws InvalidArgumentException if the value is not a number
     */
    public static function secondsToTicks(mixed $seconds) : int{
        if(!is_int($seconds) && !is_numeric($seconds)){
            throw new InvalidArgumentException("Value must be a number of seconds");
        }
        $ticks = $seconds * 20;
        if(!is_finite((float) $ticks)){
            throw new InvalidArgumentException("Value must be a finite number of seconds");
        }
        return (int) $ticks;
    }

    private static function assertValidLifespan(int $value) : void{
        if($value < 0){
            throw new InvalidArgumentException("Value $value is too small, it must be at least 0");
        }

        if($value > self::MAX_LIFESPAN){
            throw new InvalidArgumentException("Value $value is too big, it must be at most 0x7fff");
        }
    }
}
