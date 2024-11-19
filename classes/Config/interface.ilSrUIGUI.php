<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

/**
 * Interface ilSrUIGUI
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 */
interface ilSrUIGUI
{
    public const CMD_INDEX = 'index';
    public const CMD_SAVE = 'save';

    public function executeCommand(): void;
}
