<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

use ILIAS\UI\Component\Input\Container\Form\Form;
use srag\Plugins\SrUI\Config\Buttons\ButtonColorConfig;

/**
 * Class ilSrUIButtonConfigGUI
 *
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 * @ilCtrl_IsCalledBy ilSrUIButtonConfigGUI: ilSrUIMainGUI
 */
class ilSrUIButtonConfigGUI implements ilSrUIGUI
{
    use ilSrUIHelperGUI;
    use ilSrUITrafos;

    private function getForm(): Form
    {
        $factory = $this->ui_factory->input();
        // Button Colors
        $button_colors = $factory->field()->optionalGroup([
            ButtonColorConfig::F_PRIMARY_BUTTON_COLOR => $factory->field()->text(
                $this->plugin->txt(ButtonColorConfig::F_PRIMARY_BUTTON_COLOR)
            )->withRequired(true),
            ButtonColorConfig::F_PRIMARY_BUTTON_COLOR_TEXT => $factory->field()->text(
                $this->plugin->txt(ButtonColorConfig::F_PRIMARY_BUTTON_COLOR_TEXT)
            )->withRequired(true),
            ButtonColorConfig::F_PRIMARY_BUTTON_COLOR_HOVER => $factory->field()->text(
                $this->plugin->txt(ButtonColorConfig::F_PRIMARY_BUTTON_COLOR_HOVER)
            )->withRequired(true),
            ButtonColorConfig::F_PRIMARY_BUTTON_COLOR_TEXT_HOVER => $factory->field()->text(
                $this->plugin->txt(ButtonColorConfig::F_PRIMARY_BUTTON_COLOR_TEXT_HOVER)
            )->withRequired(true),

            ButtonColorConfig::F_GENERAL_BUTTON_COLOR => $factory->field()->text(
                $this->plugin->txt(ButtonColorConfig::F_GENERAL_BUTTON_COLOR)
            )->withRequired(true),
            ButtonColorConfig::F_GENERAL_BUTTON_COLOR_TEXT => $factory->field()->text(
                $this->plugin->txt(ButtonColorConfig::F_GENERAL_BUTTON_COLOR_TEXT)
            )->withRequired(true),
            ButtonColorConfig::F_GENERAL_BUTTON_COLOR_HOVER => $factory->field()->text(
                $this->plugin->txt(ButtonColorConfig::F_GENERAL_BUTTON_COLOR_HOVER)
            )->withRequired(true),
            ButtonColorConfig::F_GENERAL_BUTTON_COLOR_TEXT_HOVER => $factory->field()->text(
                $this->plugin->txt(ButtonColorConfig::F_GENERAL_BUTTON_COLOR_TEXT_HOVER)
            )->withRequired(true),
        ], $this->plugin->txt(ButtonColorConfig::NS))
                                 ->withValue($this->config->get(ButtonColorConfig::NS)->getData())
                                 ->withAdditionalTransformation(
                                     $this->getCustomTrafo($this->refinery)(function (
                                         $v
                                     ): bool {
                                         $this->config->get(ButtonColorConfig::NS)->loadData($v);
                                         return true;
                                     })
                                 );

        return $factory->container()->form()->standard(
            $this->ctrl->getLinkTarget($this, self::CMD_SAVE),
            [
                ButtonColorConfig::NS => $button_colors,
            ]
        )->withAdditionalTransformation(
            $this->getCustomTrafo($this->refinery)(fn($v): bool => true)
        );
    }
}
