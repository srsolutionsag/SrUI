<?php

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
