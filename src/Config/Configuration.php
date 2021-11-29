<?php

namespace srag\Plugins\SrUI\Config;

use srag\Plugins\SrUI\Config\Footer\AdditionalInfoConfig;
use srag\Plugins\SrUI\Config\Header\HeaderLogoConfiguration;
use srag\Plugins\SrUI\Config\Header\HeaderTitleConfiguration;
use srag\Plugins\SrUI\Config\Header\HeaderColorsConfiguration;
use srag\Plugins\SrUI\Config\LoginPage\CenteredPageConfig;
use srag\Plugins\SrUI\Config\LoginPage\ShowComponentsConfig;
use srag\Plugins\SrUI\Config\CloneInfo\ProdServerConfig;
use srag\Plugins\SrUI\Config\Content\SpecialContentFeaturesConfig;
use srag\Plugins\SrUI\Config\Footer\FooterColorsConfig;

/**
 * Class ConfigurationStorage
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class Configuration
{
    protected $collection;
    protected $version = '1.0.0';
    protected $storage;

    /**
     * @param string $version
     */
    public function __construct(string $version)
    {
        $this->version = $version;
        $this->collection = new ConfigCollection([
            new ProdServerConfig(),
            new SpecialContentFeaturesConfig(),
            new FooterColorsConfig(),
            new AdditionalInfoConfig(),
            new HeaderLogoConfiguration(),
            new HeaderTitleConfiguration(),
            new HeaderColorsConfiguration(),
            new CenteredPageConfig(),
            new ShowComponentsConfig()
        ]);
        $this->storage = new ConfigurationStorage($this->collection, $this->version);
        $this->storage->read();
    }

    public function get(string $namespace) : Config
    {
        return $this->collection->get($namespace);
    }

    public function save()
    {
        $this->storage->write();
    }

    public function getStorage() : ConfigurationStorage
    {
        return $this->storage;
    }

    public function prodServerConfig() : ProdServerConfig
    {
        return $this->get(ProdServerConfig::NS);
    }

    public function contentConfig() : SpecialContentFeaturesConfig
    {
        return $this->get(SpecialContentFeaturesConfig::NS);
    }

    public function headerColorConfig() : HeaderColorsConfiguration
    {
        return $this->get(HeaderColorsConfiguration::NS);
    }

    public function headerLogoConfig() : HeaderLogoConfiguration
    {
        return $this->get(HeaderLogoConfiguration::NS);
    }

    public function titleConfig() : HeaderTitleConfiguration
    {
        return $this->get(HeaderTitleConfiguration::NS);
    }

    public function footerColorConfig() : FooterColorsConfig
    {
        return $this->get(FooterColorsConfig::NS);
    }

    public function footerAdditionalInfosConfig() : AdditionalInfoConfig
    {
        return $this->get(AdditionalInfoConfig::NS);
    }

}
