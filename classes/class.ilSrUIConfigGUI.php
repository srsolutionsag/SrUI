<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

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

    public function performCommand(string $cmd): void
    {
        // noting to to here
    }
}
