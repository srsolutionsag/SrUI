<?php

/**
 * @author            Fabian Schmid <fabian@sr.solutions>
 *
 * @ilCtrl_isCalledBy ilSrUIConfigGUI: ilObjComponentSettingsGUI
 */
class ilSrUIConfigGUI extends ilPluginConfigGUI
{
    /**
     * @inheritDoc
     */
    public function executeCommand(): void
    {
        global $DIC;
        parent::executeCommand();
        if ($DIC->ctrl()->getNextClass() === strtolower(ilSrUIMainGUI::class)) {
            $h = new ilSrUIMainGUI($DIC, $this->plugin_object);
            $DIC->ctrl()->forwardCommand($h);
            return;
        }
        $DIC->ctrl()->redirectByClass([ilSrUIMainGUI::class]);
    }

    /**
     * @param string $cmd
     */
    public function performCommand(string $cmd): void
    {
        // noting to to here
    }
}
