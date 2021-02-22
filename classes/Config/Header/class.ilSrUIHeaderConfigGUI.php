<?php

use ILIAS\Refinery\Transformation;
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

    const CMD_INDEX = 'index';
    const CMD_SAVE = 'save';
    const F_ICON = 'icon';
    const F_ICON_WIDTH = 'icon_width';
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
        $this->artifact = new Artifact('sr_ui_setting');

        $custom_trafo = function (callable $c) : Transformation {
            return $this->refinery->custom()->transformation($c);
        };

        $artifact_safer = function (string $key) use ($custom_trafo) : Transformation {
            return $custom_trafo(function ($va) use ($key) {
                return $this->artifact[$key] = $va;
            });
        };

        // Fields
        $icon = $this->ui_factory->input()->field()->file(new ilSrUIFileHandlerGUI(), $this->plugin->txt(self::F_ICON))
                                 ->withAcceptedMimeTypes([ilMimeTypeUtil::IMAGE__SVG_XML])
                                 ->withValue($this->artifact[self::F_ICON] ?? [])
                                 ->withAdditionalTransformation($artifact_safer(self::F_ICON));

        $icon_with = $this->ui_factory->input()->field()->text($this->plugin->txt(self::F_ICON_WIDTH))
                                      ->withValue((string) ($this->artifact[self::F_ICON_WIDTH] ?? '45px'))
                                      ->withAdditionalTransformation($artifact_safer(self::F_ICON_WIDTH));

        return $this->ui_factory->input()->container()->form()->standard(
            $this->ctrl->getLinkTarget($this, self::CMD_SAVE),
            [
                self::F_ICON       => $icon,
                self::F_ICON_WIDTH => $icon_with,
            ]
        )->withAdditionalTransformation($custom_trafo(function ($v) {
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
