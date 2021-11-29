<?php

use ILIAS\UI\Component\Input\Container\Form\Form;
use srag\Plugins\SrUI\Config\Footer\ColorConfig;
use srag\Plugins\SrUI\Config\Footer\AdditionalInfoConfig;

/**
 * Class ilSrUIFooterConfigGUI
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 * @ilCtrl_IsCalledBy ilSrUIFooterConfigGUI: ilSrUIMainGUI
 */
class ilSrUIFooterConfigGUI implements ilSrUIGUI
{
    use ilSrUIHelperGUI;
    use ilSrUITrafos;

    private function getForm() : Form
    {
        $factory = $this->ui_factory->input();

        // ADDITIONAL INFOS
        $additional_infos_confog = $this->config->get(AdditionalInfoConfig::NS);
        $additional_infos = $factory->field()->optionalGroup([
            AdditionalInfoConfig::F_SHOW_RENDERED_BY => $factory->field()->checkbox($this->plugin->txt(AdditionalInfoConfig::F_SHOW_RENDERED_BY)),

        ], $this->plugin->txt(AdditionalInfoConfig::NS))
                                    ->withValue($additional_infos_confog->getData())
                                    ->withAdditionalTransformation($this->getCustomTrafo($this->refinery)(function ($v
                                    ) {
                                        $this->config->get(AdditionalInfoConfig::NS)->loadData($v);
                                        return true;
                                    }));

        // FOOTER COLORS
        $footer_color_config = $this->config->get(ColorConfig::F_FOOTER_COLORS);
        $footer_colors = $factory->field()->optionalGroup([
            ColorConfig::F_FOOTER_BG_COLOR => $factory->field()->text($this->plugin->txt(ColorConfig::F_FOOTER_BG_COLOR)),
            ColorConfig::F_FOOTER_TEXT_COLOR => $factory->field()->text($this->plugin->txt(ColorConfig::F_FOOTER_TEXT_COLOR))

        ], $this->plugin->txt(ColorConfig::F_FOOTER_COLORS))
                                 ->withValue($footer_color_config->getData())
                                 ->withAdditionalTransformation($this->getCustomTrafo($this->refinery)(function ($v) {
                                     $this->config->get(ColorConfig::F_FOOTER_COLORS)->loadData($v);
                                     return true;
                                 }));

        return $factory->container()->form()->standard(
            $this->ctrl->getLinkTarget($this, self::CMD_SAVE),
            [
                AdditionalInfoConfig::NS => $additional_infos,
                ColorConfig::F_FOOTER_COLORS => $footer_colors,
            ]
        )->withAdditionalTransformation($this->getCustomTrafo($this->refinery)(function ($v) {
            return true;
        }));
    }

}
