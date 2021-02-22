<?php

/**
 * Class ilSrUIUIHookGUI
 * @author studer + raimann ag - Team Core 1 <support-core1@studer-raimann.ch>
 */
class ilSrUIConfigGUI extends ilPluginConfigGUI
{

    /**
     * @inheritDoc
     */
    public function executeCommand()
    {
        global $DIC;
        parent::executeCommand();
        switch ($DIC->ctrl()->getNextClass()) {
            case strtolower(ilSrUIMainGUI::class):
                $h = new ilSrUIMainGUI($DIC, $this->plugin_object);
                $DIC->ctrl()->forwardCommand($h);

                return;
        }
        $DIC->ctrl()->redirectByClass([ilSrUIMainGUI::class]);
    }

    /**
     * @param string $cmd
     */
    public function performCommand($cmd)
    {
        // noting to to here
    }
}
