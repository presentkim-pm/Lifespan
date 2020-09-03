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

namespace blugin\lifespan\command;

use blugin\lib\command\Subcommand;
use blugin\lib\command\validator\defaults\NumberArgumentValidator;
use pocketmine\command\CommandSender;

abstract class LifespanSubcommand extends Subcommand{
    /**
     * @param string[] $args = []
     */
    public function execute(CommandSender $sender, array $args = []) : bool{
        if(!isset($args[0]))
            return false;

        $lifespan = (int) NumberArgumentValidator::validateRange($args[0], 0, 0x7fff);

        $this->setLifespan($lifespan);
        $this->sendMessage($sender, "success", [(string) $lifespan]);
        return true;
    }

    abstract protected function setLifespan(int $lifespan) : void;
}