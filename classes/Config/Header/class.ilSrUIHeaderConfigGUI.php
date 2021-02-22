<?php

/**
 * Class ilSrUIHeaderConfigGUI
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 * @ilCtrl_IsCalledBy ilSrUIHeaderConfigGUI: ilSrUIMainGUI
 */
class ilSrUIHeaderConfigGUI implements ilSrUIGUI
{
    use ilSrUIHelperGUI;

    const CMD_INDEX = 'index';

    public function executeCommand() : void
    {
        switch ($this->ctrl->getCmd(self::CMD_INDEX)) {
            case self::CMD_INDEX:
                $this->index();
                break;
        }
    }

    private function index() : void
    {

    }
}
