<?php

/**
 * Interface ilSrUIGUI
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 */
interface ilSrUIGUI
{

    const CMD_INDEX = 'index';
    const CMD_SAVE = 'save';

    public function executeCommand() : void;

}
