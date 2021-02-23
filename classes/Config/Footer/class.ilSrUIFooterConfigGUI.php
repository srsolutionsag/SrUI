<?php

use ILIAS\UI\Component\Input\Container\Form\Form;
use srag\Plugins\SrUI\Artifact\Artifact;

/**
 * Class ilSrUIFooterConfigGUI
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 * @ilCtrl_IsCalledBy ilSrUIFooterConfigGUI: ilSrUIMainGUI
 */
class ilSrUIFooterConfigGUI implements ilSrUIGUI
{
    use ilSrUIHelperGUI;
    use ilSrUITrafos;

    const CMD_INDEX = 'index';
    const CMD_SAVE = 'save';
    const F_SHOW_RENDERED_BY = 'show_rendered_by';

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
        $artifact = new Artifact('sr_ui_setting');

        // Fields
        $show_rendered_by = $this->ui_factory->input()->field()->checkbox($this->plugin->txt(self::F_SHOW_RENDERED_BY))
                                             ->withValue((bool) $artifact[self::F_SHOW_RENDERED_BY])
                                             ->withAdditionalTransformation($this->getArtifactSafer($this->refinery, $artifact)(self::F_SHOW_RENDERED_BY));

        return $this->ui_factory->input()->container()->form()->standard(
            $this->ctrl->getLinkTarget($this, self::CMD_SAVE),
            [
                self::F_SHOW_RENDERED_BY => $show_rendered_by,
            ]
        )->withAdditionalTransformation($this->getCustomTrafo($this->refinery)(function ($v) use (&$artifact) {
            return $artifact;
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
