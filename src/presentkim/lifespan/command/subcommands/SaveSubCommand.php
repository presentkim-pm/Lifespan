<?php

namespace presentkim\lifespan\command\subcommands;

use pocketmine\command\CommandSender;
use presentkim\lifespan\LifeSpanMain as Plugin;
use presentkim\lifespan\command\{
  PoolCommand, SubCommand
};

class SaveSubCommand extends SubCommand{

    public function __construct(PoolCommand $owner){
        parent::__construct($owner, 'save');
    }

    /**
     * @param CommandSender $sender
     * @param String[]      $args
     *
     * @return bool
     */
    public function onCommand(CommandSender $sender, array $args) : bool{
        $this->plugin->save();
        $sender->sendMessage(Plugin::$prefix . $this->translate('success'));

        return true;
    }
}