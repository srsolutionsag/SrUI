<?php

use ILIAS\UI\Component\Input\Container\Form\Form;
use srag\Plugins\SrUI\Artifact\Artifact;

/**
 * Class ilSrUIContentConfigGUI
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 * @ilCtrl_IsCalledBy ilSrUIContentConfigGUI: ilSrUIMainGUI
 */
class ilSrUIContentConfigGUI implements ilSrUIGUI
{
    use ilSrUIHelperGUI;
    use ilSrUITrafos;
    
    const CMD_INDEX = 'index';
    const CMD_SAVE = 'save';
    const F_FANCY_CARDS = 'fancy_cards';
    const F_FANCY_TABS = 'fancy_tabs';
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
        
        // Fields
        $cards = $this->ui_factory->input()->field()->checkbox($this->plugin->txt(self::F_FANCY_CARDS))
                                  ->withValue((bool) ($this->artifact[self::F_FANCY_CARDS] ?? false))
                                  ->withAdditionalTransformation($this->getArtifactSafer($this->refinery,
                                      $this->artifact)(self::F_FANCY_CARDS));
        
        $tabs = $this->ui_factory->input()->field()->checkbox($this->plugin->txt(self::F_FANCY_TABS))
                                 ->withValue((bool) ($this->artifact[self::F_FANCY_TABS] ?? false))
                                 ->withAdditionalTransformation($this->getArtifactSafer($this->refinery,
                                     $this->artifact)(self::F_FANCY_TABS));
        
        return $this->ui_factory->input()->container()->form()->standard(
            $this->ctrl->getLinkTarget($this, self::CMD_SAVE),
            [
                self::F_FANCY_CARDS => $cards,
                self::F_FANCY_TABS  => $tabs,
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
