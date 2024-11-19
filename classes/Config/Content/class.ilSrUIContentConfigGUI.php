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

        return $this->ui_factory->input()->container()->form()->standard(
            $this->ctrl->getLinkTarget($this, self::CMD_SAVE),
            [
                Content::F_FANCY_CARDS => $cards,
                Content::F_FANCY_TABS => $tabs,
                Content::F_VIEW_PORT => $view_port,
            ]
        )->withAdditionalTransformation(
            $this->getCustomTrafo($this->refinery)(function ($v): bool {
                $this->config->get(Content::NS)->loadData($v);
                return true;
            })
        );
    }
}
