<?php

/*
 *
 *  ____                           _   _  ___
 * |  _ \ _ __ ___  ___  ___ _ __ | |_| |/ (_)_ __ ___
 * | |_) | '__/ _ \/ __|/ _ \ '_ \| __| ' /| | '_ ` _ \
 * |  __/| | |  __/\__ \  __/ | | | |_| . \| | | | | | |
 * |_|   |_|  \___||___/\___|_| |_|\__|_|\_\_|_| |_| |_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author  PresentKim (debe3721@gmail.com)
 * @link    https://github.com/PresentKim
 * @license https://www.gnu.org/licenses/agpl-3.0.html AGPL-3.0.0
 *
 *   (\ /)
 *  ( . .) ♥
 *  c(")(")
 */

declare(strict_types=1);

namespace kim\present\lifespan\listener;

use kim\present\lifespan\Lifespan;
use pocketmine\entity\Entity;
use pocketmine\entity\object\ItemEntity;
use pocketmine\entity\projectile\Arrow;
use pocketmine\event\entity\EntitySpawnEvent;
use pocketmine\event\Listener;

class EntityEventListener implements Listener{
	/** @var Lifespan */
	private $plugin;

	/** @var \ReflectionProperty */
	private $property = null;

	/**
	 * EntityEventListener constructor.
	 *
	 * @param Lifespan $plugin
	 *
	 * @throws \ReflectionException
	 */
	public function __construct(Lifespan $plugin){
		$this->plugin = $plugin;

		$reflection = new \ReflectionClass(Entity::class);
		$this->property = $reflection->getProperty("age");
		$this->property->setAccessible(true);
	}

	/**
	 * @param EntitySpawnEvent $event
	 */
	public function onEntitySpawnEvent(EntitySpawnEvent $event) : void{
		$entity = $event->getEntity();
		if($entity instanceof ItemEntity){
			$this->property->setValue($entity, min(6000, max(-0x7fff, 6000 - $this->plugin->getItemLifespan())));
		}elseif($entity instanceof Arrow){
			$this->property->setValue($entity, min(1200, max(-0x7fff, 1200 - $this->plugin->getArrowLifespan())));
		}
	}
}