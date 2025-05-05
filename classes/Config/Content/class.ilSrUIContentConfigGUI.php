<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

use ILIAS\UI\Component\Input\Container\Form\Form;
use srag\Plugins\SrUI\Config\Content\SpecialContentFeaturesConfig as Content;

/**
 * Class ilSrUIContentConfigGUI
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 * @ilCtrl_IsCalledBy ilSrUIContentConfigGUI: ilSrUIMainGUI
 */
class ilSrUIContentConfigGUI implements ilSrUIGUI
{
    use ilSrUIHelperGUI;
    use ilSrUITrafos;

    private function getForm(): Form
    {
        $config = $this->config->get(Content::NS);
        // Fields
        $cards = $this->ui_factory->input()->field()->checkbox($this->plugin->txt(Content::F_FANCY_CARDS))
                                  ->withValue($config->get(Content::F_FANCY_CARDS, false));

        $tabs = $this->ui_factory->input()->field()->checkbox($this->plugin->txt(Content::F_FANCY_TABS))
                                 ->withValue($config->get(Content::F_FANCY_TABS, false));

        $view_port = $this->ui_factory->input()->field()->checkbox($this->plugin->txt(Content::F_VIEW_PORT))
                                      ->withValue($config->get(Content::F_VIEW_PORT, false));

        $tab_link_color = $this->ui_factory->input()->field()->text($this->plugin->txt(Content::F_TAB_LINK_COLOR))
                                      ->withValue($config->get(Content::F_TAB_LINK_COLOR, ''));

        return $this->ui_factory->input()->container()->form()->standard(
            $this->ctrl->getLinkTarget($this, self::CMD_SAVE),
            [
                Content::F_FANCY_CARDS => $cards,
                Content::F_FANCY_TABS => $tabs,
                Content::F_VIEW_PORT => $view_port,
                Content::F_TAB_LINK_COLOR => $tab_link_color,
            ]
        )->withAdditionalTransformation(
            $this->getCustomTrafo($this->refinery)(function ($v): bool {
                $this->config->get(Content::NS)->loadData($v);
                return true;
            })
        );
    }
}
