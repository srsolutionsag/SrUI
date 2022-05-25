<?php

use ILIAS\DI\Container;
use srag\Plugins\SrUI\ILIASVersion;
use srag\Plugins\SrUI\ResourceStorage\Factory;

/**
 * Trait ilSrUIHelperGUI
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 */
trait ilSrUIHelperGUI
{
    /**
     * @var ilCtrl
     */
    protected $ctrl;
    /**
     * @var ilTabsGUI
     */
    protected $tabs;
    /**
     * @var ilSrUIPlugin
     */
    protected $plugin;
    /**
     * @var Container
     */
    protected $dic;
    /**
     * @var \ILIAS\UI\Factory
     */
    protected $ui_factory;
    /**
     * @var \ILIAS\UI\Renderer
     */
    protected $ui_renderer;
    /**
     * @var ilTemplate
     */
    protected $tpl;
    /**
     * @var \ILIAS\Refinery\Factory
     */
    protected $refinery;
    /**
     * @var \ILIAS\Filesystem\Filesystems
     */
    protected $filesystems;
    /**
     * @var \ILIAS\DI\HTTPServices
     */
    protected $http;
    /**
     * @var \srag\Plugins\SrUI\Config\Configuration
     */
    protected $config;
    /**
     * @var ILIASVersion
     */
    protected $ilias_version;
    /**
     * @var Factory
     */
    protected $storage_factory;
    
    /**
     * ilSrUIMainGUI constructor.
     * @param Container    $dic
     * @param ilSrUIPlugin $plugin
     */
    public function __construct(Container $dic, ilSrUIPlugin $plugin)
    {
        $this->dic = $dic;
        $this->ctrl = $dic->ctrl();
        $this->tabs = $dic->tabs();
        $this->plugin = $plugin;
        $this->ui_factory = $dic->ui()->factory();
        $this->ui_renderer = $dic->ui()->renderer();
        $this->tpl = $dic->ui()->mainTemplate();
        $this->refinery = $dic->refinery();
        $this->filesystems = $dic->filesystem();
        $this->http = $dic->http();
        $this->config = $this->plugin->getConfiguration();
        $this->ilias_version = new ILIASVersion(ILIAS_VERSION_NUMERIC);
        $this->storage_factory = new Factory();
    }

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

    private function index() : void
    {
        $this->tpl->setContent($this->ui_renderer->render($this->getForm()));
    }

    private function save() : void
    {
        $form = $this->getForm()->withRequest($this->http->request());

        if ($form->getData()) {
            $this->config->save();
            $this->ctrl->redirect($this);
        }else {
            $this->tpl->setContent($this->ui_renderer->render($form));
        }
    }
}
