<?php

declare(strict_types=1);

namespace kim\present\lifetime\listener;

use kim\present\lifetime\Lifetime;
use pocketmine\entity\Entity;
use pocketmine\entity\object\ItemEntity;
use pocketmine\entity\projectile\Arrow;
use pocketmine\event\entity\EntitySpawnEvent;
use pocketmine\event\Listener;

class EntityEventListener implements Listener{
	/** @var Lifetime */
	private $owner = null;

	/** @var \ReflectionProperty */
	private $property = null;

	/**
	 * EntityEventListener constructor.
	 *
	 * @param Lifetime $owner
	 *
	 * @throws \ReflectionException
	 */
	public function __construct(Lifetime $owner){
		$this->owner = $owner;

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
			$this->property->setValue($entity, (6000 - $this->owner->getItemLifetime()));
		}elseif($entity instanceof Arrow){
			$this->property->setValue($entity, (1200 - $this->owner->getArrowLifetime()));
		}
	}
}