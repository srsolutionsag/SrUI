<?php

use ILIAS\DI\Container;

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
     * ilSrUIMainGUI constructor.
     * @param Container    $dic
     * @param ilSrUIPlugin $plugin
     */
    public function __construct(Container $dic, ilSrUIPlugin $plugin)
    {
        $this->dic         = $dic;
        $this->ctrl        = $dic->ctrl();
        $this->tabs        = $dic->tabs();
        $this->plugin      = $plugin;
        $this->ui_factory  = $dic->ui()->factory();
        $this->ui_renderer = $dic->ui()->renderer();
        $this->tpl         = $dic->ui()->mainTemplate();
        $this->refinery    = $dic->refinery();
        $this->filesystems = $dic->filesystem();
        $this->http        = $dic->http();
    }
}
