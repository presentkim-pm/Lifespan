<?php

namespace presentkim\lifespan\command\subcommands;

use pocketmine\command\CommandSender;
use presentkim\lifespan\{
  LifeSpanMain as Plugin, util\Translation, command\SubCommand
};
use function presentkim\lifespan\util\toInt;

class ArrowSubCommand extends SubCommand{

    public function __construct(Plugin $owner){
        parent::__construct($owner, Translation::translate('prefix'), 'command-lifespan-arrow', 'lifespan.arrow.cmd');
    }

    /**
     * @param CommandSender $sender
     * @param array         $args
     *
     * @return bool
     */
    public function onCommand(CommandSender $sender, array $args){
        if (isset($args[0])) {
            $lifespan = toInt($args[0], null, function (int $i){
                return $i >= 0;
            });
            if ($lifespan === null) {
                $sender->sendMessage($this->prefix . Translation::translate('command-generic-failure@invalid', $args[0]));
            } else {
                $this->owner->getConfig()->set('arrow-lifespan', $lifespan);
                $sender->sendMessage($this->prefix . Translation::translate($this->getFullId('success'), $lifespan));
            }
            return true;
        }
        return false;
    }
}