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
 * @author  PresentKim (debe3721@gmail.com)
 * @link    https://github.com/PresentKim
 * @license https://www.gnu.org/licenses/lgpl-3.0 LGPL-3.0 License
 *
 *   (\ /)
 *  ( . .) ♥
 *  c(")(")
 *
 * @noinspection PhpUnused
 */

declare(strict_types=1);

namespace kim\present\lifespan;

use Closure;
use InvalidArgumentException;
use pocketmine\entity\object\ItemEntity;
use pocketmine\entity\projectile\Arrow;
use pocketmine\event\entity\EntitySpawnEvent;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener{
    private int $itemLifespan = 6000;
    private int $arrowLifespan = 1200;

    public function onEnable() : void{
        $config = $this->getConfig();
        $this->setItemLifespan($config->getNested("item-lifespan", 300) * 20);
        $this->setArrowLifespan($config->getNested("arrow-lifespan", 60) * 20);

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
            Closure::bind( //HACK: Closure bind hack to access inaccessible members
                closure: function() use ($entity) : void{
                    $entity->collideTicks = 1200 - $this->getArrowLifespan();
                },
                newThis: $this,
                newScope: Arrow::class
            )();
        }
    }

    public function getItemLifespan() : int{
        return $this->itemLifespan;
    }

    public function setItemLifespan(int $value) : void{
        if($value < 0){
            throw new InvalidArgumentException("Value $value is too small, it must be at least 0");
        }elseif($value > 0x7fff){
            throw new InvalidArgumentException("Value $value is too big, it must be at most 0x7fff");
        }
        $this->itemLifespan = $value;
    }

    public function getArrowLifespan() : int{
        return $this->arrowLifespan;
    }

    public function setArrowLifespan(int $value) : void{
        if($value < 0){
            throw new InvalidArgumentException("Value $value is too small, it must be at least 0");
        }elseif($value > 0x7fff){
            throw new InvalidArgumentException("Value $value is too big, it must be at most 0x7fff");
        }
        $this->arrowLifespan = $value;
    }
}
