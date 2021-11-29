<?php

use ILIAS\UI\Component\Input\Container\Form\Form;
use srag\Plugins\SrUI\Config\CloneInfo\Config;
use srag\Plugins\SrUI\Config\CloneInfo\ProdServerConfig;

/**
 * Class ilSrUICloneInfoConfigGUI
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 * @ilCtrl_IsCalledBy ilSrUICloneInfoConfigGUI: ilSrUIMainGUI
 */
class ilSrUICloneInfoConfigGUI implements ilSrUIGUI
{
    use ilSrUIHelperGUI;
    use ilSrUITrafos;

    private function getForm() : Form
    {
        $config = $this->config->get(ProdServerConfig::NS);
        // Fields
        $factory = $this->ui_factory->input()->field();

        $prod_system = $factory->optionalGroup(
            [
                ProdServerConfig::F_PROD_HOSTNAMES => $factory->text(
                    $this->plugin->txt(ProdServerConfig::F_PROD_HOSTNAMES),
                    $this->plugin->txt('prod_hostname_byline') . ' ' . gethostname()),
                ProdServerConfig::F_NON_PROD_MESSAGE => $factory->text(
                    $this->plugin->txt(ProdServerConfig::F_NON_PROD_MESSAGE)
                ),
                ProdServerConfig::F_SEND_NON_INDEX_HEADERS => $factory->checkbox(
                    $this->plugin->txt(ProdServerConfig::F_SEND_NON_INDEX_HEADERS)
                )
            ],
            $this->plugin->txt(ProdServerConfig::NS)
        )->withValue($config->isNamespaceActive() ? $config->getData() : null);

        return $this->ui_factory->input()->container()->form()->standard(
            $this->ctrl->getLinkTarget($this, self::CMD_SAVE),
            [
                ProdServerConfig::NS => $prod_system
            ]
        )->withAdditionalTransformation($this->getCustomTrafo($this->refinery)(function ($v) use ($config) {
            $config->loadData($v[ProdServerConfig::NS]);
            return true;
        }));
    }
}
