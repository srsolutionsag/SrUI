<?php

use ILIAS\UI\Component\Input\Container\Form\Form;
use srag\Plugins\SrUI\Config\Header\HeaderLogoConfiguration as Logo;
use srag\Plugins\SrUI\Config\Header\ResponsiveHeaderLogoConfiguration as ResponsiveLogo;
use srag\Plugins\SrUI\Config\Header\HeaderTitleConfiguration as Title;
use srag\Plugins\SrUI\Config\Header\HeaderColorsConfiguration as Colors;

/**
 * Class ilSrUIHeaderConfigGUI
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 * @ilCtrl_IsCalledBy ilSrUIHeaderConfigGUI: ilSrUIMainGUI
 */
class ilSrUIHeaderConfigGUI implements ilSrUIGUI
{
    use ilSrUIHelperGUI;
    use ilSrUITrafos;

    const F_DARKMODE = 'darkmode'; // not yet migrated
    const MIME_TYPES = [
        ilMimeTypeUtil::IMAGE__SVG_XML,
        ilMimeTypeUtil::IMAGE__JPEG,
        ilMimeTypeUtil::IMAGE__GIF,
        ilMimeTypeUtil::IMAGE__PNG
    ];

    private function getForm() : Form
    {
        $factory = $this->ui_factory->input();

        // HEADER ICON
        $header_logo = $factory->field()->optionalGroup(
            [
                // Icon
                Logo::F_ICON => $factory->field()->file(
                    new ilSrUIFileHandlerGUI(),
                    $this->plugin->txt(Logo::F_ICON)
                )->withAcceptedMimeTypes(self::MIME_TYPES),
                // Width
                Logo::F_ICON_WIDTH => $factory->field()->text($this->plugin->txt(Logo::F_ICON_WIDTH)),
                Logo::F_ICON_LINK => $factory->field()->text($this->plugin->txt(Logo::F_ICON_LINK)),
                
            ],
            $this->plugin->txt(Logo::NS))
                               ->withValue($this->config->get(Logo::NS)->getData())
                               ->withAdditionalTransformation($this->getCustomTrafo($this->refinery)(function ($v) {
                                   $this->config->get(Logo::NS)->loadData($v);
                                   return true;
                               }));

        // RESPONSIVE HEADER ICON
        $responsive_header_logo = $factory->field()->optionalGroup(
            [
                // Icon
                ResponsiveLogo::F_ICON => $factory->field()->file(
                    new ilSrUIFileHandlerGUI(),
                    $this->plugin->txt(ResponsiveLogo::F_ICON)
                )->withAcceptedMimeTypes(self::MIME_TYPES),
                // Width
                ResponsiveLogo::F_ICON_WIDTH => $factory->field()->text($this->plugin->txt(ResponsiveLogo::F_ICON_WIDTH))
            ],
            $this->plugin->txt(ResponsiveLogo::NS))
                               ->withValue($this->config->get(ResponsiveLogo::NS)->getData())
                               ->withAdditionalTransformation($this->getCustomTrafo($this->refinery)(function ($v) {
                                   $this->config->get(ResponsiveLogo::NS)->loadData($v);
                                   return true;
                               }));

        // Header Title
        $header_title = $factory->field()->optionalGroup([
            Title::NS => $factory->field()->text(
                $this->plugin->txt(Title::F_HEADER_TITLE)
            )->withRequired(true)
        ], $this->plugin->txt(Title::NS))
                                ->withValue($this->config->get(Title::NS)->getData())
                                ->withAdditionalTransformation($this->getCustomTrafo($this->refinery)(function ($v
                                ) {
                                    $this->config->get(Title::NS)->loadData($v);
                                    return true;
                                }));
        
        // Header Colors
        $header_colors = $factory->field()->optionalGroup([
            Colors::F_HEADER_BG_COLOR => $factory->field()->text(
                $this->plugin->txt(Colors::F_HEADER_BG_COLOR)
            )->withRequired(true),
            Colors::F_HEADER_BC_BG_COLOR => $factory->field()->text(
                $this->plugin->txt(Colors::F_HEADER_BC_BG_COLOR)
            )->withRequired(true),
            Colors::F_HEADER_BC_TEXT_COLOR => $factory->field()->text(
                $this->plugin->txt(Colors::F_HEADER_BC_TEXT_COLOR)
            )->withRequired(true),
            Colors::F_TITLE_COLOR => $factory->field()->text(
                $this->plugin->txt(Colors::F_TITLE_COLOR)
            )->withRequired(true),

        ], $this->plugin->txt(Colors::NS))
                                 ->withValue($this->config->get(Colors::NS)->getData())
                                 ->withAdditionalTransformation($this->getCustomTrafo($this->refinery)(function ($v
                                 ) {
                                     $this->config->get(Colors::NS)->loadData($v);
                                     return true;
                                 }));

        return $factory->container()->form()->standard(
            $this->ctrl->getLinkTarget($this, self::CMD_SAVE),
            [
                Logo::NS => $header_logo,
                ResponsiveLogo::NS => $responsive_header_logo,
                Title::NS => $header_title,
                Colors::NS => $header_colors,
            ]
        )->withAdditionalTransformation($this->getCustomTrafo($this->refinery)(function ($v) {
            return true;
        }));
    }
}
