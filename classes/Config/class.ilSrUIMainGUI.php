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
    const TAB_FOOTER = 'tab_footer';
    const TAB_CONTENT = 'tab_content';
    const TAB_CLONE_INFO = 'tab_clone_info';

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
            case strtolower(ilSrUIFooterConfigGUI::class):
                $this->tabs->activateTab(self::TAB_FOOTER);
                $this->ctrl->forwardCommand(new ilSrUIFooterConfigGUI($this->dic, $this->plugin));
                break;
            case strtolower(ilSrUIContentConfigGUI::class):
                $this->tabs->activateTab(self::TAB_CONTENT);
                $this->ctrl->forwardCommand(new ilSrUIContentConfigGUI($this->dic, $this->plugin));
                break;
            case strtolower(ilSrUICloneInfoConfigGUI::class):
                $this->tabs->activateTab(self::TAB_CLONE_INFO);
                $this->ctrl->forwardCommand(new ilSrUICloneInfoConfigGUI($this->dic, $this->plugin));
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
        $this->tabs->addTab(
            self::TAB_FOOTER,
            $this->plugin->txt(self::TAB_FOOTER),
            $this->ctrl->getLinkTargetByClass(ilSrUIFooterConfigGUI::class)
        );
        $this->tabs->addTab(
            self::TAB_CONTENT,
            $this->plugin->txt(self::TAB_CONTENT),
            $this->ctrl->getLinkTargetByClass(ilSrUIContentConfigGUI::class)
        );
        $this->tabs->addTab(
            self::TAB_CLONE_INFO,
            $this->plugin->txt(self::TAB_CLONE_INFO),
            $this->ctrl->getLinkTargetByClass(ilSrUICloneInfoConfigGUI::class)
        );
    }

    public function index() : void
    {
        $this->ctrl->redirectByClass(ilSrUILoginPageConfigGUI::class);
    }

}
