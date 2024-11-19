<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

use ILIAS\UI\Component\Input\Container\Form\Form;
use srag\Plugins\SrUI\Config\Menu\MenuColorConfig;

/**
 * Class ilSrUIMenuConfigGUI
 *
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 * @ilCtrl_IsCalledBy ilSrUIMenuConfigGUI: ilSrUIMainGUI
 */
class ilSrUIMenuConfigGUI implements ilSrUIGUI
{
    use ilSrUIHelperGUI;
    use ilSrUITrafos;

    private function getForm(): Form
    {
        $factory = $this->ui_factory->input();
        // Menu Colors
        $menu_colors = $factory->field()->optionalGroup([
            MenuColorConfig::F_MAIN_COLOR => $factory->field()->text(
                $this->plugin->txt(MenuColorConfig::F_MAIN_COLOR)
            )->withRequired(true),
            MenuColorConfig::F_MAIN_HOVER_COLOR => $factory->field()->text(
                $this->plugin->txt(MenuColorConfig::F_MAIN_HOVER_COLOR)
            )->withRequired(true),
            MenuColorConfig::F_SUB_COLOR => $factory->field()->text(
                $this->plugin->txt(MenuColorConfig::F_SUB_COLOR)
            )->withRequired(true),
            MenuColorConfig::F_SUB_HOVER_COLOR => $factory->field()->text(
                $this->plugin->txt(MenuColorConfig::F_SUB_HOVER_COLOR)
            )->withRequired(true),
            MenuColorConfig::F_TOOLS_COLOR => $factory->field()->text(
                $this->plugin->txt(MenuColorConfig::F_TOOLS_COLOR)
            )->withRequired(true),
            MenuColorConfig::F_TOOLS_HOVER_COLOR => $factory->field()->text(
                $this->plugin->txt(MenuColorConfig::F_TOOLS_HOVER_COLOR)
            )->withRequired(true),

        ], $this->plugin->txt(MenuColorConfig::NS))
                               ->withValue($this->config->get(MenuColorConfig::NS)->getData())
                               ->withAdditionalTransformation(
                                   $this->getCustomTrafo($this->refinery)(function (
                                       $v
                                   ): bool {
                                       $this->config->get(MenuColorConfig::NS)->loadData($v);
                                       return true;
                                   })
                               );

        return $factory->container()->form()->standard(
            $this->ctrl->getLinkTarget($this, self::CMD_SAVE),
            [
                MenuColorConfig::NS => $menu_colors,
            ]
        )->withAdditionalTransformation(
            $this->getCustomTrafo($this->refinery)(fn($v): bool => true)
        );
    }
}
