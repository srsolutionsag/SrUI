<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

use ILIAS\UI\Component\Input\Container\Form\Form;
use srag\Plugins\SrUI\Config\Footer\FooterColorsConfig;
use srag\Plugins\SrUI\Config\Footer\AdditionalInfoConfig;
use srag\Plugins\SrUI\Config\Footer\PermanentLinkConfig;

/**
 * Class ilSrUIFooterConfigGUI
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 * @ilCtrl_IsCalledBy ilSrUIFooterConfigGUI: ilSrUIMainGUI
 */
class ilSrUIFooterConfigGUI implements ilSrUIGUI
{
    use ilSrUIHelperGUI;
    use ilSrUITrafos;

    private function getForm(): Form
    {
        $factory = $this->ui_factory->input();

        // ADDITIONAL INFOS
        $additional_infos_confog = $this->config->get(AdditionalInfoConfig::NS);
        $additional_infos = $factory->field()->optionalGroup([
            AdditionalInfoConfig::F_SHOW_RENDERED_BY => $factory->field()->checkbox(
                $this->plugin->txt(AdditionalInfoConfig::F_SHOW_RENDERED_BY)
            ),

        ], $this->plugin->txt(AdditionalInfoConfig::NS))
                                    ->withValue($additional_infos_confog->getData())
                                    ->withAdditionalTransformation(
                                        $this->getCustomTrafo($this->refinery)(function (
                                            $v
                                        ): bool {
                                            $this->config->get(AdditionalInfoConfig::NS)->loadData($v);
                                            return true;
                                        })
                                    );

        // PERMANANT LINK
        $permanent_link_config = $this->config->get(PermanentLinkConfig::NS);

        $permanent_link = $factory->field()->optionalGroup([
            PermanentLinkConfig::F_SHOW_ENHACED_PERMANANT_LINK => $factory->field()->checkbox(
                $this->plugin->txt(PermanentLinkConfig::F_SHOW_ENHACED_PERMANANT_LINK)
            ),

        ], $this->plugin->txt(PermanentLinkConfig::NS))
                                  ->withValue($permanent_link_config->getData())
                                  ->withAdditionalTransformation(
                                      $this->getCustomTrafo($this->refinery)(function (
                                          $v
                                      ): bool {
                                          $this->config->get(PermanentLinkConfig::NS)->loadData($v);
                                          return true;
                                      })
                                  );

        // FOOTER COLORS
        $footer_color_config = $this->config->get(FooterColorsConfig::NS);
        $footer_colors = $factory->field()->optionalGroup([
            FooterColorsConfig::F_FOOTER_BG_COLOR => $factory->field()->text(
                $this->plugin->txt(FooterColorsConfig::F_FOOTER_BG_COLOR)
            )->withRequired(true),
            FooterColorsConfig::F_FOOTER_TEXT_COLOR => $factory->field()->text(
                $this->plugin->txt(FooterColorsConfig::F_FOOTER_TEXT_COLOR)
            )->withRequired(true)

        ], $this->plugin->txt(FooterColorsConfig::NS))
                                 ->withValue($footer_color_config->getData())
                                 ->withAdditionalTransformation(
                                     $this->getCustomTrafo($this->refinery)(function ($v): bool {
                                         $this->config->get(FooterColorsConfig::NS)->loadData($v);
                                         return true;
                                     })
                                 );

        return $factory->container()->form()->standard(
            $this->ctrl->getLinkTarget($this, self::CMD_SAVE),
            [
                AdditionalInfoConfig::NS => $additional_infos,
                FooterColorsConfig::NS => $footer_colors,
                PermanentLinkConfig::NS => $permanent_link
            ]
        )->withAdditionalTransformation(
            $this->getCustomTrafo($this->refinery)(function ($v): bool {
                return true;
            })
        );
    }
}
