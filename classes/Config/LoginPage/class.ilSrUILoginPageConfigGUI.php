<?php

use ILIAS\UI\Component\Input\Container\Form\Form;
use srag\Plugins\SrUI\Config\LoginPage\CenteredPageConfig as CenterPage;
use srag\Plugins\SrUI\Config\LoginPage\ShowComponentsConfig as ShowComponents;

/**
 * Class ilSrUILoginPageConfigGUI
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 * @ilCtrl_IsCalledBy ilSrUILoginPageConfigGUI: ilSrUIMainGUI
 */
class ilSrUILoginPageConfigGUI implements ilSrUIGUI
{
    use ilSrUIHelperGUI;
    use ilSrUITrafos;

    const MIME_TYPES = [
        ilMimeTypeUtil::IMAGE__SVG_XML,
        ilMimeTypeUtil::IMAGE__JPEG,
        ilMimeTypeUtil::IMAGE__GIF,
        ilMimeTypeUtil::IMAGE__PNG
    ];

    private function getForm() : Form
    {
        $factory = $this->ui_factory->input();

        // CENTERED LOGIN PAGE
        $arr = [
            // Background
            CenterPage::F_BACKGROUND_IMAGE => $factory->field()->file(
                new ilSrUIFileHandlerGUI(),
                $this->plugin->txt(CenterPage::F_BACKGROUND_IMAGE)
            )->withAcceptedMimeTypes(self::MIME_TYPES),
            // Additional CSS
            CenterPage::F_CENTERED_LOGIN_PAGE_CSS => $factory->field()->textarea(
                $this->plugin->txt(CenterPage::F_CENTERED_LOGIN_PAGE_CSS)
            ),
            // Show Logo in Container
            CenterPage::F_SHOW_HEADER_LOGO => $factory->field()->checkbox(
                $this->plugin->txt(CenterPage::F_SHOW_HEADER_LOGO)
            )
        ];
        $centered_login_page = $factory->field()->optionalGroup($arr, $this->plugin->txt(CenterPage::NS))
                                       ->withValue($this->config->get(CenterPage::NS)->getData())
                                       ->withAdditionalTransformation($this->getCustomTrafo($this->refinery)(function (
                                           $v
                                       ) {
                                           $this->config->get(CenterPage::NS)->loadData($v);
                                           return true;
                                       }));

        // SHOW COMPONENTS
        $fields = [
            ShowComponents::F_SHOW_HEADER => $this->ui_factory->input()->field()->checkbox($this->plugin->txt(ShowComponents::F_SHOW_HEADER)),
            ShowComponents::F_SHOW_MAIN_BAR => $this->ui_factory->input()->field()->checkbox($this->plugin->txt(ShowComponents::F_SHOW_MAIN_BAR)),
            ShowComponents::F_SHOW_FOOTER => $this->ui_factory->input()->field()->checkbox($this->plugin->txt(ShowComponents::F_SHOW_FOOTER)),
        ];
        $show_components = $factory->field()->optionalGroup($fields, $this->plugin->txt(ShowComponents::NS))
                                   ->withValue($this->config->get(ShowComponents::NS)->getData())
                                   ->withAdditionalTransformation($this->getCustomTrafo($this->refinery)(function (
                                       $v
                                   ) {
                                       $this->config->get(ShowComponents::NS)->loadData($v);
                                       return true;
                                   }));

        return $factory->container()->form()->standard(
            $this->ctrl->getLinkTarget($this, self::CMD_SAVE),
            [
                CenterPage::NS => $centered_login_page,
                ShowComponents::NS => $show_components
                //                Title::NS => $header_title,
                //                Colors::NS => $header_colors,
            ]
        )->withAdditionalTransformation($this->getCustomTrafo($this->refinery)(function ($v) {
            return true;
        }));
    }

}
