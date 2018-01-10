<?php

namespace presentkim\lifespan\command\subcommands;

use pocketmine\command\CommandSender;
use presentkim\lifespan\{
  LifeSpanMain as Plugin, util\Translation, command\SubCommand
};

class SaveSubCommand extends SubCommand{

    public function __construct(Plugin $owner){
        parent::__construct($owner, Translation::translate('prefix'), 'command-lifespan-save', 'lifespan.save.cmd');
    }

    /**
     * @param CommandSender $sender
     * @param array         $args
     *
     * @return bool
     */
    public function onCommand(CommandSender $sender, array $args) {
        $this->owner->save();
        $sender->sendMessage($this->prefix . Translation::translate($this->getFullId('success')));

        return true;
    }
}