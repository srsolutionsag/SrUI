<?php

use ILIAS\UI\Component\Input\Container\Form\Form;
use srag\Plugins\SrUI\Config\Footer\FooterColorsConfig;
use srag\Plugins\SrUI\Config\Footer\AdditionalInfoConfig;
use srag\Plugins\SrUI\ResourceStorage\Factory;

/**
 * Class ilSrUIExchangeGUI
 *
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 * @ilCtrl_IsCalledBy ilSrUIExchangeGUI: ilSrUIMainGUI
 */
class ilSrUIExchangeGUI implements ilSrUIGUI
{
    use ilSrUIHelperGUI;
    use ilSrUITrafos;
    
    const CMD_DOWNLOAD = 'download';
    const F_IMPORT_FILE = 'import_file';
    
    public function executeCommand() : void
    {
        switch ($this->ctrl->getCmd(self::CMD_INDEX)) {
            case self::CMD_INDEX:
                $this->index();
                break;
            case self::CMD_SAVE:
                $this->save();
                break;
            case self::CMD_DOWNLOAD:
                $this->download();
                break;
        }
    }
    
    private function index() : void
    {
        ilUtil::sendInfo('Importing Logos and other files currently not always possible');
        
        $import = $this->ui_factory->panel()->standard(
            $this->plugin->txt('panel_import'),
            $this->getForm()
        );
        $export = $this->ui_factory->panel()->standard(
            $this->plugin->txt('panel_export'),
            $this->ui_factory->button()->standard(
                $this->plugin->txt('download_configuration'),
                $this->ctrl->getLinkTarget($this, self::CMD_DOWNLOAD)
            )
        );
        
        $this->tpl->setContent($this->ui_renderer->render([$export, $import]));
    }
    
    private function download() : void
    {
        $zip_file = $this->config->export();
        ilFileDelivery::deliverFileAttached($zip_file, 'sr_ui_config.zip');
    }
    
    private function save() : void
    {
        $form = $this->getForm()->withRequest($this->http->request());
        
        if ($rid = $form->getData()) {
            $storage = $this->storage_factory->storage();
            $path = $storage->getPath($rid);
            $zip = new ZipArchive();
            $zip->open($path);
            $this->config->import($zip);
            $this->ctrl->redirect($this);
        } else {
            $this->tpl->setContent($this->ui_renderer->render($form));
        }
    }
    
    private function getForm() : Form
    {
        $factory = $this->ui_factory->input();
        
        return $factory->container()->form()->standard(
            $this->ctrl->getLinkTarget($this, self::CMD_SAVE),
            [
                self::F_IMPORT_FILE => $factory->field()->file(
                    new ilSrUIFileHandlerGUI(),
                    $this->plugin->txt(
                        self::F_IMPORT_FILE
                    )
                )
            ]
        )->withAdditionalTransformation(
            $this->getCustomTrafo($this->refinery)(function ($v) {
                return $v[self::F_IMPORT_FILE][0] ?? null;
            })
        );
    }
    
}
