<?php

namespace presentkim\lifespan\command\subcommands;

namespace presentkim\lifespan\command;

use pocketmine\command\{
  Command, CommandExecutor, CommandSender
};
use presentkim\lifespan\LifeSpanMain as Plugin;
use presentkim\lifespan\command\subcommands\{
  ArrowSubCommand, ItemSubCommand, LangSubCommand, ReloadSubCommand, SaveSubCommand
};

class CommandListener implements CommandExecutor{

    /** @var Plugin */
    protected $owner;

    /**
     * SubComamnd[] $subcommands
     */
    protected $subcommands = [];

    /** @param Plugin $owner */
    public function __construct(Plugin $owner){
        $this->owner = $owner;

        $this->subcommands = [
          new ItemSubCommand($this->owner),
          new ArrowSubCommand($this->owner),
          new LangSubCommand($this->owner),
          new ReloadSubCommand($this->owner),
          new SaveSubCommand($this->owner),
        ];
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
        if (!isset($args[0])) {
            return false;
        } else {
            $label = array_shift($args);
            foreach ($this->subcommands as $key => $value) {
                if ($value->checkLabel($label)) {
                    $value->execute($sender, $args);
                    return true;
                }
            }
            return false;
        }
    }
}