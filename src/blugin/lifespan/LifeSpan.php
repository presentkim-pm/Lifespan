<?php

namespace blugin\lifespan;

use pocketmine\command\{
  Command, PluginCommand, CommandExecutor, CommandSender
};
use pocketmine\plugin\PluginBase;
use blugin\lifespan\lang\PluginLang;
use blugin\lifespan\listener\EntityEventListener;

class LifeSpan extends PluginBase implements CommandExecutor{

    public const INVALID_TYPE = -1;
    public const ITEM_TYPE = 0;
    public const ARROW_TYPE = 1;

    /** @var LifeSpan */
    private static $instance = null;

    /** @return LifeSpan */
    public static function getInstance() : LifeSpan{
        return self::$instance;
    }

    /** @var PluginCommand */
    private $command;

    /** @var PluginLang */
    private $language;

    /** @var string[] */
    private $typeMap = [];

    public function onLoad() : void{
        self::$instance = $this;
    }

    public function onEnable() : void{
        $dataFolder = $this->getDataFolder();
        if (!file_exists($dataFolder)) {
            mkdir($dataFolder, 0777, true);
        }
        $this->reloadConfig();
        $this->language = new PluginLang($this);

        $this->typeMap = [];
        $this->typeMap[strtolower($this->language->translate('commands.lifespan.item'))] = self::ITEM_TYPE;
        foreach ($this->language->getArray('commands.lifespan.item.aliases') as $key => $aliases) {
            $this->typeMap[strtolower($aliases)] = self::ITEM_TYPE;
        }
        $this->typeMap[strtolower($this->language->translate('commands.lifespan.arrow'))] = self::ARROW_TYPE;
        foreach ($this->language->getArray('commands.lifespan.arrow.aliases') as $key => $aliases) {
            $this->typeMap[strtolower($aliases)] = self::ARROW_TYPE;
        }

        if ($this->command !== null) {
            $this->getServer()->getCommandMap()->unregister($this->command);
        }
        $this->command = new PluginCommand($this->language->translate('commands.lifespan'), $this);
        $this->command->setPermission('lifespan.cmd');
        $this->command->setDescription($this->language->translate('commands.lifespan.description'));
        $this->command->setUsage($this->language->translate('commands.lifespan.usage'));
        if (is_array($aliases = $this->language->getArray('commands.lifespan.aliases'))) {
            $this->command->setAliases($aliases);
        }
        $this->getServer()->getCommandMap()->register('lifespan', $this->command);

        $this->getServer()->getPluginManager()->registerEvents(new EntityEventListener(), $this);
    }

    public function onDisable(){
        $dataFolder = $this->getDataFolder();
        if (!file_exists($dataFolder)) {
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
        if (isset($args[1])) {
            if (!is_numeric($args[1])) {
                $sender->sendMessage($this->language->translate('commands.generic.num.notNumber', [$args[1]]));
            } else {
                $lifespan = (float) $args[1];
                if ($lifespan < 0) {
                    $sender->sendMessage($this->language->translate('commands.generic.num.tooSmall', [
                      $lifespan,
                      0,
                    ]));
                } elseif ($lifespan > 9999) {
                    $sender->sendMessage($this->language->translate('commands.generic.num.tooBig', [
                      $lifespan,
                      9999,
                    ]));
                } else {
                    $type = $this->typeMap[strtolower($args[0])] ?? self::INVALID_TYPE;
                    if ($type === self::INVALID_TYPE) {
                        $sender->sendMessage($this->language->translate('commands.lifespan.failure.invalid', [$args[0]]));
                    } else {
                        $this->getConfig()->set(($type ? 'arrow' : 'item') . '-lifespan', $lifespan);
                        $sender->sendMessage($this->language->translate('commands.lifespan.success', [
                          $this->language->translate('commands.lifespan.' . ($type ? 'arrow' : 'item')),
                          $lifespan,
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
        if (empty($pharPath)) {
            return dirname(__FILE__, 4) . DIRECTORY_SEPARATOR;
        } else {
            return $pharPath . DIRECTORY_SEPARATOR;
        }
    }
}
