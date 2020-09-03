<?php

/*
 *
 *  ____  _             _         _____
 * | __ )| |_   _  __ _(_)_ __   |_   _|__  __ _ _ __ ___
 * |  _ \| | | | |/ _` | | '_ \    | |/ _ \/ _` | '_ ` _ \
 * | |_) | | |_| | (_| | | | | |   | |  __/ (_| | | | | | |
 * |____/|_|\__,_|\__, |_|_| |_|   |_|\___|\__,_|_| |_| |_|
 *                |___/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author  Blugin team
 * @link    https://github.com/Blugin
 * @license https://www.gnu.org/licenses/lgpl-3.0 LGPL-3.0 License
 *
 *   (\ /)
 *  ( . .) ♥
 *  c(")(")
 */

declare(strict_types=1);

namespace blugin\lifespan;

use blugin\lib\command\SubcommandTrait;
use blugin\lib\translator\MultilingualConfigTrait;
use blugin\lib\translator\TranslatorHolder;
use blugin\lib\translator\TranslatorHolderTrait;
use blugin\lifespan\command\ArrowSubcommand;
use blugin\lifespan\command\ItemSubcommand;
use pocketmine\entity\object\ItemEntity;
use pocketmine\entity\projectile\Arrow;
use pocketmine\event\entity\EntitySpawnEvent;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

class Lifespan extends PluginBase implements Listener, TranslatorHolder{
    use SingletonTrait, TranslatorHolderTrait, MultilingualConfigTrait, SubcommandTrait;

    public const TYPE_ITEM = 0;
    public const TYPE_ARROW = 1;

    public const TAG_ITEM = "Item";
    public const TAG_ARROW = "Arrow";

    /** @var \ReflectionProperty */
    private $property = null;

    /** @var int[] */
    private $typeMap;

    /** @var string[] */
    private $typeTagMap = [
        self::TYPE_ITEM => "item",
        self::TYPE_ARROW => "arrow"
    ];

    /** @var int (short) */
    private $itemLifespan = 6000;

    /** @var int (short) */
    private $arrowLifespan = 1200;

    public function onLoad() : void{
        self::setInstance($this);

        $this->loadLanguage($this->getConfig()->getNested("settings.language"));

        $reflection = new \ReflectionClass(Arrow::class);
        $this->property = $reflection->getProperty("collideTicks");
        $this->property->setAccessible(true);
    }

    public function onEnable() : void{
        //Register main command with subcommands
        $command = $this->getMainCommand();
        $command->registerSubcommand(new ItemSubcommand($command));
        $command->registerSubcommand(new ArrowSubcommand($command));
        $this->recalculatePermissions();
        $this->getServer()->getCommandMap()->register($this->getName(), $command);

        //Load lifespan data
        $dataPath = "{$this->getDataFolder()}lifespan.json";
        if(!file_exists($dataPath)){
            $this->itemLifespan = 6000;  //default:  5 minutes
            $this->arrowLifespan = 1200; //default: 60 seconds
            return;
        }

        $content = file_get_contents($dataPath);
        if($content === false)
            throw new \RuntimeException("Unable to load lifespan.json file");

        $data = json_decode($content, true);
        if(!is_array($data) || count($data) < 2 || !isset($data[self::TAG_ITEM]) || !is_numeric($data[self::TAG_ITEM]) || !isset($data[self::TAG_ARROW]) || !is_numeric($data[self::TAG_ARROW])){
            throw new \RuntimeException("Invalid data in lifespan.json file. Must be int array");
        }
        $this->setItemLifespan((int) $data[self::TAG_ITEM]);
        $this->setArrowLifespan((int) $data[self::TAG_ARROW]);

        //Register event listeners
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onDisable() : void{
        //Unregister main command with subcommands
        $this->getServer()->getCommandMap()->unregister($this->getMainCommand());

        //Save lifespan data
        $dataPath = "{$this->getDataFolder()}lifespan.json";
        file_put_contents($dataPath, json_encode([
            self::TAG_ITEM => $this->itemLifespan,
            self::TAG_ARROW => $this->arrowLifespan
        ], JSON_PRETTY_PRINT));
    }

    /**
     * @priority MONITOR
     */
    public function onEntitySpawnEvent(EntitySpawnEvent $event) : void{
        $entity = $event->getEntity();
        if($entity instanceof ItemEntity){
            $entity->setDespawnDelay(min(0x7fff, max(0, $this->getItemLifespan())));
        }elseif($entity instanceof Arrow){
            $this->property->setValue($entity, min(0x7fff, max(0, $this->getArrowLifespan())));
        }
    }

    public function getItemLifespan() : int{
        return $this->itemLifespan;
    }

    public function setItemLifespan(int $value) : void{
        if($value < 0){
            throw new \InvalidArgumentException("Value {$value} is too small, it must be at least 0");
        }elseif($value > 0x7fff){
            throw new \InvalidArgumentException("Value {$value} is too big, it must be at most 0x7fff");
        }
        $this->itemLifespan = $value;
    }

    public function getArrowLifespan() : int{
        return $this->arrowLifespan;
    }

    public function setArrowLifespan(int $value) : void{
        if($value < 0){
            throw new \InvalidArgumentException("Value {$value} is too small, it must be at least 0");
        }elseif($value > 0x7fff){
            throw new \InvalidArgumentException("Value {$value} is too big, it must be at most 0x7fff");
        }
        $this->arrowLifespan = $value;
    }
}
