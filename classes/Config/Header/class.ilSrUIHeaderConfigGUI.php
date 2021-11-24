<?php

use ILIAS\UI\Component\Input\Container\Form\Form;
use srag\Plugins\SrUI\Artifact\Artifact;

/**
 * Class ilSrUIHeaderConfigGUI
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 * @ilCtrl_IsCalledBy ilSrUIHeaderConfigGUI: ilSrUIMainGUI
 */
class ilSrUIHeaderConfigGUI implements ilSrUIGUI
{
    use ilSrUIHelperGUI;
    use ilSrUITrafos;

    const CMD_INDEX = 'index';
    const CMD_SAVE = 'save';
    const F_ICON = 'icon';
    const F_ICON_WIDTH = 'icon_width';
    const F_DARKMODE = 'darkmode';
    const F_HEADER_TITLE = 'header_title';
    const F_HEADER_COLORS = 'header_colors';
    const F_HEADER_BG_COLOR = 'header_bg_color';
    const F_HEADER_BC_BG_COLOR = 'header_bc_bg_color';
    const F_HEADER_BC_TEXT_COLOR = 'header_bc_text_color';
    /**
     * @var Artifact
     */
    protected $artifact;

    public function executeCommand() : void
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

    private function getForm() : Form
    {
        $this->artifact = Artifact::getInstance('sr_ui_setting');

        // Fields
        $factory = $this->ui_factory->input();
        $icon = $factory->field()->file(new ilSrUIFileHandlerGUI(), $this->plugin->txt(self::F_ICON))
                        ->withAcceptedMimeTypes([
                            ilMimeTypeUtil::IMAGE__SVG_XML,
                            ilMimeTypeUtil::IMAGE__JPEG,
                            ilMimeTypeUtil::IMAGE__GIF,
                            ilMimeTypeUtil::IMAGE__PNG
                        ])
                        ->withValue($this->artifact[self::F_ICON] ?? [])
                        ->withAdditionalTransformation($this->getArtifactSafer($this->refinery,
                            $this->artifact)(self::F_ICON));

        $icon_with = $factory->field()->text($this->plugin->txt(self::F_ICON_WIDTH))
                             ->withValue((string) ($this->artifact[self::F_ICON_WIDTH] ?? '45px'))
                             ->withAdditionalTransformation($this->getArtifactSafer($this->refinery,
                                 $this->artifact)(self::F_ICON_WIDTH));

        $header_title = $factory->field()->text($this->plugin->txt(self::F_HEADER_TITLE))
                                ->withValue((string) ($this->artifact[self::F_HEADER_TITLE] ?? 'ILIAS+'))
                                ->withAdditionalTransformation($this->getArtifactSafer($this->refinery,
                                    $this->artifact)(self::F_HEADER_TITLE));

        $darkmode = $factory->field()->checkbox($this->plugin->txt(self::F_DARKMODE))
                            ->withValue((bool) ($this->artifact[self::F_DARKMODE] ?? false))
                            ->withAdditionalTransformation($this->getArtifactSafer($this->refinery,
                                $this->artifact)(self::F_DARKMODE));

        $header_color_inputs = [
            self::F_HEADER_BG_COLOR => $factory->field()->text($this->plugin->txt(self::F_HEADER_BG_COLOR))
                                               ->withValue(($this->artifact[self::F_HEADER_BG_COLOR] ?? ''))
                                               ->withAdditionalTransformation($this->getArtifactSafer($this->refinery,
                                                   $this->artifact)(self::F_HEADER_BG_COLOR)),
            self::F_HEADER_BC_BG_COLOR => $factory->field()->text($this->plugin->txt(self::F_HEADER_BC_BG_COLOR))
                                                  ->withValue(($this->artifact[self::F_HEADER_BC_BG_COLOR] ?? ''))
                                                  ->withAdditionalTransformation($this->getArtifactSafer($this->refinery,
                                                      $this->artifact)(self::F_HEADER_BC_BG_COLOR)),
            self::F_HEADER_BC_TEXT_COLOR => $factory->field()->text($this->plugin->txt(self::F_HEADER_BC_TEXT_COLOR))
                                                    ->withValue(($this->artifact[self::F_HEADER_BC_TEXT_COLOR] ?? ''))
                                                    ->withAdditionalTransformation($this->getArtifactSafer($this->refinery,
                                                        $this->artifact)(self::F_HEADER_BC_TEXT_COLOR))
        ];
        $header_colors = $factory->field()->optionalGroup($header_color_inputs,
            $this->plugin->txt(self::F_HEADER_COLORS))
                                 ->withValue(($this->artifact[self::F_HEADER_COLORS] ?? null))
                                 ->withAdditionalTransformation($this->getArtifactSafer($this->refinery,
                                     $this->artifact)(self::F_HEADER_COLORS));

        return $factory->container()->form()->standard(
            $this->ctrl->getLinkTarget($this, self::CMD_SAVE),
            [
                self::F_ICON => $icon,
                self::F_ICON_WIDTH => $icon_with,
                self::F_HEADER_TITLE => $header_title,
                self::F_DARKMODE => $darkmode,
                self::F_HEADER_COLORS => $header_colors,
            ]
        )->withAdditionalTransformation($this->getCustomTrafo($this->refinery)(function ($v) {
            return $this->artifact;
        }));
    }

    private function index() : void
    {
        $this->tpl->setContent($this->ui_renderer->render($this->getForm()));
    }

    private function save() : void
    {
        $form = $this->getForm()->withRequest($this->http->request());

        $artifact = $form->getData();
        if ($artifact instanceof Artifact) {
            $artifact->save();
        }
        $this->ctrl->redirect($this);
    }
}
