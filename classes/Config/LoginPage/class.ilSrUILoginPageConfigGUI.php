<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

use ILIAS\UI\Component\Input\Container\Form\Form;
use srag\Plugins\SrUI\Config\LoginPage\CenteredPageConfig as CenterPage;
use srag\Plugins\SrUI\Config\LoginPage\ShowComponentsConfig as ShowComponents;
use ILIAS\FileUpload\MimeType;
use ILIAS\UI\Component\Input\Container\Form\FormInput;
use ILIAS\DI\Container;

/**
 * Class ilSrUILoginPageConfigGUI
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 * @ilCtrl_IsCalledBy ilSrUILoginPageConfigGUI: ilSrUIMainGUI
 */
class ilSrUILoginPageConfigGUI implements ilSrUIGUI
{
    use ilSrUIHelperGUI;
    use ilSrUITrafos;

    public const MIME_TYPES = [
        MimeType::IMAGE__SVG_XML,
        MimeType::IMAGE__JPEG,
        MimeType::IMAGE__GIF,
        MimeType::IMAGE__PNG
    ];


    protected function makeInputHTMLAware(FormInput $input): void
    {
        $reflection = new ReflectionClass(\ILIAS\UI\Implementation\Component\Input\Field\FormInput::class);
        $operations_property = $reflection->getProperty('operations');
        $operations_property->setAccessible(true);
        $operations_property->setValue($input, []);
    }

    private function getForm(): Form
    {
        $this->initAssets();

        $factory = $this->ui_factory->input();

        // we must remove all constraints for the HTML input, otherwise it will not work
        $make_editor = function (string $id): string {
            return "CodeMirror.fromTextArea(document.getElementById('$id'), {
                    lineNumbers: true,
                });";
        };

        $html_textarea = $factory->field()->textarea(
            $this->plugin->txt(CenterPage::F_ADDITIONAL_HTML)
        )->withAdditionalOnLoadCode($make_editor);

        $this->makeInputHTMLAware($html_textarea);

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
            )->withAdditionalOnLoadCode($make_editor),
            // Additional HTML
            CenterPage::F_ADDITIONAL_HTML => $html_textarea,
            // Show Logo in Container
            CenterPage::F_SHOW_HEADER_LOGO => $factory->field()->checkbox(
                $this->plugin->txt(CenterPage::F_SHOW_HEADER_LOGO)
            )
        ];
        $centered_login_page = $factory->field()->optionalGroup($arr, $this->plugin->txt(CenterPage::NS))
                                       ->withValue($this->config->get(CenterPage::NS)->getData())
                                       ->withAdditionalTransformation(
                                           $this->getCustomTrafo($this->refinery)(function (
                                               $v
                                           ): bool {
                                               $this->config->get(CenterPage::NS)->loadData($v);
                                               return true;
                                           })
                                       );

        // SHOW COMPONENTS
        $fields = [
            ShowComponents::F_SHOW_HEADER => $this->ui_factory->input()->field()->checkbox(
                $this->plugin->txt(ShowComponents::F_SHOW_HEADER)
            ),
            ShowComponents::F_SHOW_MAIN_BAR => $this->ui_factory->input()->field()->checkbox(
                $this->plugin->txt(ShowComponents::F_SHOW_MAIN_BAR)
            ),
            ShowComponents::F_SHOW_FOOTER => $this->ui_factory->input()->field()->checkbox(
                $this->plugin->txt(ShowComponents::F_SHOW_FOOTER)
            ),
        ];
        $show_components = $factory->field()->optionalGroup($fields, $this->plugin->txt(ShowComponents::NS))
                                   ->withValue($this->config->get(ShowComponents::NS)->getData())
                                   ->withAdditionalTransformation(
                                       $this->getCustomTrafo($this->refinery)(function (
                                           $v
                                       ): bool {
                                           $this->config->get(ShowComponents::NS)->loadData($v);
                                           return true;
                                       })
                                   );

        return $factory->container()->form()->standard(
            $this->ctrl->getLinkTarget($this, self::CMD_SAVE),
            [
                CenterPage::NS => $centered_login_page,
                ShowComponents::NS => $show_components
                //                Title::NS => $header_title,
                //                Colors::NS => $header_colors,
            ]
        )->withAdditionalTransformation(
            $this->getCustomTrafo($this->refinery)(fn($v): bool => true)
        );
    }

    protected function initAssets(): void
    {
        $this->tpl->addCss(
            './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/SrUI/node_modules/codemirror/lib/codemirror.css'
        );
        $this->tpl->addJavaScript(
            './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/SrUI/node_modules/codemirror/lib/codemirror.js'
        );
        $this->tpl->addJavaScript(
            './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/SrUI/node_modules/codemirror/mode/css/css.js'
        );
        $this->tpl->addJavaScript(
            './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/SrUI/node_modules/codemirror/mode/htmlmixed/htmlmixed.js'
        );

        $this->tpl->addInlineCss(".CodeMirror {border: 1px solid #ddd;}");
    }
}
