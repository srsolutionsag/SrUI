<?php

namespace srag\Plugins\SrUI\Config;

use srag\Plugins\SrUI\Config\Footer\AdditionalInfoConfig;
use srag\Plugins\SrUI\Config\Header\HeaderLogoConfiguration;
use srag\Plugins\SrUI\Config\Header\HeaderTitleConfiguration;
use srag\Plugins\SrUI\Config\Header\HeaderColorsConfiguration;
use srag\Plugins\SrUI\Config\LoginPage\CenteredPageConfig;
use srag\Plugins\SrUI\Config\LoginPage\ShowComponentsConfig;
use srag\Plugins\SrUI\Config\CloneInfo\ProdServerConfig;

/**
 * Class SavableArtifact
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class Configuration
{
    protected $collection;
    protected $version = '1.0.0';
    protected $artifact;

    /**
     * @param string $version
     */
    public function __construct(string $version)
    {
        $this->version = $version;
        $this->collection = new ConfigCollection([
            new ProdServerConfig(),
            new \srag\Plugins\SrUI\Config\Content\Config(),
            new \srag\Plugins\SrUI\Config\Footer\ColorConfig(),
            new AdditionalInfoConfig(),
            new HeaderLogoConfiguration(),
            new HeaderTitleConfiguration(),
            new HeaderColorsConfiguration(),
            new CenteredPageConfig(),
            new ShowComponentsConfig()
        ]);
        $this->artifact = new SavableArtifact($this->collection, 'sr_ui_setting', $this->version);
        $this->artifact->read();
    }

    public function get(string $namespace) : Config
    {
        return $this->collection->get($namespace);
    }

    public function save()
    {
        $this->artifact->write();
    }

    public function getArtifact() : SavableArtifact
    {
        return $this->artifact;
    }

}
