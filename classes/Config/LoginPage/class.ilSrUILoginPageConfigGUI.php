<?php

use ILIAS\UI\Component\Input\Container\Form\Form;
use srag\Plugins\SrUI\Artifact\Artifact;

/**
 * Class ilSrUILoginPageConfigGUI
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 * @ilCtrl_IsCalledBy ilSrUILoginPageConfigGUI: ilSrUIMainGUI
 */
class ilSrUILoginPageConfigGUI implements ilSrUIGUI
{
    use ilSrUIHelperGUI;
    use ilSrUITrafos;

    const CMD_INDEX = 'index';
    const CMD_SAVE = 'save';
    const F_CENTERED_LOGIN_PAGE = 'centered_login_page';
    const F_SHOW_HEADER = 'show_header';
    const F_SHOW_FOOTER = 'show_footer';
    const F_BACKGROUND_IMAGE = 'background_image';
    const F_CENTERED_LOGIN_PAGE_CSS = 'centered_login_page_css';
    const F_SHOW_MAIN_BAR = 'show_main_bar';
    const F_SHOW_HEADER_LOGO = 'show_header_logo';
    /**
     * @var Artifact
     */
    protected $artifact;

    public function executeCommand(): void
    {
        switch ($this->ctrl->getCmd(self::CMD_INDEX)) {
            case self::CMD_INDEX:
                $this->index();
                break;
            case self::CMD_SAVE:
                $this->save();
                break;
        }
    }

    private function getForm(): Form
    {
        $this->artifact = Artifact::getInstance('sr_ui_setting');
        $artifact_safer = $this->getArtifactSafer($this->refinery, $this->artifact);

        // Fields
        $value_of_group = $this->artifact[self::F_CENTERED_LOGIN_PAGE] ? [
            self::F_BACKGROUND_IMAGE => $this->artifact[self::F_BACKGROUND_IMAGE] ?? [],
            self::F_CENTERED_LOGIN_PAGE_CSS => (string)($this->artifact[self::F_CENTERED_LOGIN_PAGE_CSS] ?? file_get_contents(ilSrUIPlugin::TEMPLATES_BASE . 'login.css'))
        ] : null;

        $inputs_of_group = [
            self::F_BACKGROUND_IMAGE => $this->ui_factory->input()->field()->file(new ilSrUIFileHandlerGUI(), $this->plugin->txt(self::F_BACKGROUND_IMAGE))
                ->withAcceptedMimeTypes([
                    ilMimeTypeUtil::IMAGE__JPEG,
                    ilMimeTypeUtil::IMAGE__GIF,
                    ilMimeTypeUtil::IMAGE__SVG_XML
                ])
                ->withValue($this->artifact[self::F_BACKGROUND_IMAGE] ?? [])
                ->withAdditionalTransformation($artifact_safer(self::F_BACKGROUND_IMAGE)),
            self::F_CENTERED_LOGIN_PAGE_CSS => $this->ui_factory->input()->field()->textarea($this->plugin->txt(self::F_CENTERED_LOGIN_PAGE_CSS))
                ->withValue((string)($this->artifact[self::F_CENTERED_LOGIN_PAGE_CSS] ?? file_get_contents(ilSrUIPlugin::TEMPLATES_BASE . 'login.css')))
                ->withAdditionalTransformation($artifact_safer(self::F_CENTERED_LOGIN_PAGE_CSS)),
        ];
        $centered_login_page = $this->ui_factory->input()->field()->optionalGroup($inputs_of_group, $this->plugin->txt(self::F_CENTERED_LOGIN_PAGE))
            ->withValue($value_of_group)
            ->withAdditionalTransformation($this->getCustomTrafo($this->refinery)(static function ($v) {
                return !is_null($v);
            }))
            ->withAdditionalTransformation($artifact_safer(self::F_CENTERED_LOGIN_PAGE));
        $show_header = $this->ui_factory->input()->field()->checkbox($this->plugin->txt(self::F_SHOW_HEADER))
            ->withValue((bool)$this->artifact[self::F_SHOW_HEADER])
            ->withAdditionalTransformation($artifact_safer(self::F_SHOW_HEADER));
        $show_footer = $this->ui_factory->input()->field()->checkbox($this->plugin->txt(self::F_SHOW_FOOTER))
            ->withValue((bool)$this->artifact[self::F_SHOW_FOOTER])
            ->withAdditionalTransformation($artifact_safer(self::F_SHOW_FOOTER));
        $show_main_bar = $this->ui_factory->input()->field()->checkbox($this->plugin->txt(self::F_SHOW_MAIN_BAR))
            ->withValue((bool)$this->artifact[self::F_SHOW_MAIN_BAR])
            ->withAdditionalTransformation($artifact_safer(self::F_SHOW_MAIN_BAR));
        $show_header_logo = $this->ui_factory->input()->field()->checkbox($this->plugin->txt(self::F_SHOW_HEADER_LOGO))
            ->withValue((bool)$this->artifact[self::F_SHOW_HEADER_LOGO])
            ->withAdditionalTransformation($artifact_safer(self::F_SHOW_HEADER_LOGO));

        return $this->ui_factory->input()->container()->form()->standard(
            $this->ctrl->getLinkTarget($this, self::CMD_SAVE),
            [
                self::F_CENTERED_LOGIN_PAGE => $centered_login_page,
                self::F_SHOW_HEADER => $show_header, // currently no solution
                self::F_SHOW_FOOTER => $show_footer,
                self::F_SHOW_MAIN_BAR => $show_main_bar,
                self::F_SHOW_HEADER_LOGO => $show_header_logo,
            ]
        )->withAdditionalTransformation($this->getCustomTrafo($this->refinery)(function ($v) {
            return $this->artifact;
        }));
    }

    private function index(): void
    {
        $this->tpl->setContent($this->ui_renderer->render($this->getForm()));
    }

    private function save(): void
    {
        $form = $this->getForm()->withRequest($this->http->request());

        $artifact = $form->getData();
        if ($artifact instanceof Artifact) {
            $artifact->save();
        }
        $this->ctrl->redirect($this);
    }

}
