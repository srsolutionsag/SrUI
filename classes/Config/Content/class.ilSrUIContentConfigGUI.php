<?php

use ILIAS\UI\Component\Input\Container\Form\Form;
use srag\Plugins\SrUI\Config\Content\Config;

/**
 * Class ilSrUIContentConfigGUI
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 * @ilCtrl_IsCalledBy ilSrUIContentConfigGUI: ilSrUIMainGUI
 */
class ilSrUIContentConfigGUI implements ilSrUIGUI
{
    use ilSrUIHelperGUI;
    use ilSrUITrafos;

    private function getForm() : Form
    {
        $config = $this->config->get(Config::NS);
        // Fields
        $cards = $this->ui_factory->input()->field()->checkbox($this->plugin->txt(Config::F_FANCY_CARDS))
                                  ->withValue($config->get(Config::F_FANCY_CARDS, false));

        $tabs = $this->ui_factory->input()->field()->checkbox($this->plugin->txt(Config::F_FANCY_TABS))
                                 ->withValue($config->get(Config::F_FANCY_TABS, false));

        return $this->ui_factory->input()->container()->form()->standard(
            $this->ctrl->getLinkTarget($this, self::CMD_SAVE),
            [
                Config::F_FANCY_CARDS => $cards,
                Config::F_FANCY_TABS => $tabs,
            ]
        )->withAdditionalTransformation($this->getCustomTrafo($this->refinery)(function ($v) {
            $this->config->get(Config::NS)->loadData($v);
            return true;
        }));
    }

}
