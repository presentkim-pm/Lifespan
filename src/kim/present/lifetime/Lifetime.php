<?php

declare(strict_types=1);

namespace kim\present\lifetime;

use kim\present\lifetime\lang\PluginLang;
use kim\present\lifetime\listener\EntityEventListener;
use pocketmine\command\{
	Command, CommandExecutor, CommandSender, PluginCommand
};
use pocketmine\plugin\PluginBase;

class Lifetime extends PluginBase implements CommandExecutor{

	public const INVALID_TYPE = -1;
	public const ITEM_TYPE = 0;
	public const ARROW_TYPE = 1;

	/** @var Lifetime */
	private static $instance = null;

	/** @return Lifetime */
	public static function getInstance() : Lifetime{
		return self::$instance;
	}

	/** @var PluginCommand */
	private $command;

	/** @var PluginLang */
	private $language;

	/** @var string[] */
	private $typeMap = [];

	/**
	 * Called when the plugin is loaded, before calling onEnable()
	 */
	protected function onLoad() : void{
		self::$instance = $this;
	}

	/**
	 * Called when the plugin is enabled
	 */
	protected function onEnable() : void{
		$dataFolder = $this->getDataFolder();
		if(!file_exists($dataFolder)){
			mkdir($dataFolder, 0777, true);
		}
		$this->reloadConfig();
		$this->language = new PluginLang($this);

		$this->typeMap = [];
		$this->typeMap[strtolower($this->language->translate('commands.lifetime.item'))] = self::ITEM_TYPE;
		foreach($this->language->getArray('commands.lifetime.item.aliases') as $key => $aliases){
			$this->typeMap[strtolower($aliases)] = self::ITEM_TYPE;
		}
		$this->typeMap[strtolower($this->language->translate('commands.lifetime.arrow'))] = self::ARROW_TYPE;
		foreach($this->language->getArray('commands.lifetime.arrow.aliases') as $key => $aliases){
			$this->typeMap[strtolower($aliases)] = self::ARROW_TYPE;
		}

		if($this->command !== null){
			$this->getServer()->getCommandMap()->unregister($this->command);
		}
		$this->command = new PluginCommand($this->language->translate('commands.lifetime'), $this);
		$this->command->setPermission('lifetime.cmd');
		$this->command->setDescription($this->language->translate('commands.lifetime.description'));
		$this->command->setUsage($this->language->translate('commands.lifetime.usage'));
		if(is_array($aliases = $this->language->getArray('commands.lifetime.aliases'))){
			$this->command->setAliases($aliases);
		}
		$this->getServer()->getCommandMap()->register('lifetime', $this->command);

		$this->getServer()->getPluginManager()->registerEvents(new EntityEventListener($this), $this);
	}

	public function onDisable(){
		$dataFolder = $this->getDataFolder();
		if(!file_exists($dataFolder)){
			mkdir($dataFolder, 0777, true);
		}

		$this->saveConfig();
	}

	/**
	 * @param CommandSender $sender
	 * @param Command       $command
	 * @param string        $label
	 * @param string[]      $args
	 *
	 * @return bool
	 */
	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
		if(isset($args[1])){
			if(!is_numeric($args[1])){
				$sender->sendMessage($this->language->translate('commands.generic.num.notNumber', [$args[1]]));
			}else{
				$lifetime = (float) $args[1];
				if($lifetime < 0){
					$sender->sendMessage($this->language->translate('commands.generic.num.tooSmall', [
						$lifetime,
						0,
					]));
				}elseif($lifetime > 9999){
					$sender->sendMessage($this->language->translate('commands.generic.num.tooBig', [
						$lifetime,
						9999,
					]));
				}else{
					$type = $this->typeMap[strtolower($args[0])] ?? self::INVALID_TYPE;
					if($type === self::INVALID_TYPE){
						$sender->sendMessage($this->language->translate('commands.lifetime.failure.invalid', [$args[0]]));
					}else{
						$this->getConfig()->set(($type ? 'arrow' : 'item') . '-lifetime', $lifetime);
						$sender->sendMessage($this->language->translate('commands.lifetime.success', [
							$this->language->translate('commands.lifetime.' . ($type ? 'arrow' : 'item')),
							$lifetime,
						]));
					}
				}
			}
			return true;
		}
		return false;
	}

	/**
	 * @param string $name = ''
	 *
	 * @return PluginCommand
	 */
	public function getCommand(string $name = '') : PluginCommand{
		return $this->command;
	}

	/**
	 * @return PluginLang
	 */
	public function getLanguage() : PluginLang{
		return $this->language;
	}

	/**
	 * @return string
	 */
	public function getSourceFolder() : string{
		$pharPath = \Phar::running();
		if(empty($pharPath)){
			return dirname(__FILE__, 4) . DIRECTORY_SEPARATOR;
		}else{
			return $pharPath . DIRECTORY_SEPARATOR;
		}
	}
}
