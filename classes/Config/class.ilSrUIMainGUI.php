<?php

/**
 * Class ilSrUIMainGUI
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 * @ilCtrl_IsCalledBy ilSrUIMainGUI: ilSrUIConfigGUI
 * @ilCtrl_calls      ilSrUIMainGUI: hub2ConfigOriginsGUI
 */
class ilSrUIMainGUI implements ilSrUIGUI
{
    use ilSrUIHelperGUI;

    const TAB_LOGIN_PAGE = 'tab_login_page';
    const TAB_HEADER = 'tab_header';
    const CMD_INDEX = 'index';

    public function executeCommand() : void
    {
        $this->initTabs();
        $next_class = $this->ctrl->getNextClass();
        switch ($next_class) {
            case strtolower(ilSrUILoginPageConfigGUI::class):
                $this->tabs->activateTab(self::TAB_LOGIN_PAGE);
                $this->ctrl->forwardCommand(new ilSrUILoginPageConfigGUI($this->dic, $this->plugin));
                break;
            case strtolower(ilSrUIHeaderConfigGUI::class):
                $this->tabs->activateTab(self::TAB_HEADER);
                $this->ctrl->forwardCommand(new ilSrUIHeaderConfigGUI($this->dic, $this->plugin));
                break;
            case strtolower(ilSrUIFileHandlerGUI::class):
                $this->ctrl->forwardCommand(new ilSrUIFileHandlerGUI());
                break;
            case '':
            default:
                // we are in this GUI class (cmdClass), therefore we execute the command here
                switch ($this->ctrl->getCmd(self::CMD_INDEX)) {
                    case self::CMD_INDEX:
                        $this->index();
                        break;
                }
                break;

        }

    }

    private function initTabs() : void
    {
        $this->tabs->addTab(
            self::TAB_LOGIN_PAGE,
            $this->plugin->txt(self::TAB_LOGIN_PAGE),
            $this->ctrl->getLinkTargetByClass(ilSrUILoginPageConfigGUI::class)
        );
        $this->tabs->addTab(
            self::TAB_HEADER,
            $this->plugin->txt(self::TAB_HEADER),
            $this->ctrl->getLinkTargetByClass(ilSrUIHeaderConfigGUI::class)
        );
    }

    public function index() : void
    {
        $this->dic->ui()->mainTemplate()->setContent('here we are in the main GUI');
    }

}
