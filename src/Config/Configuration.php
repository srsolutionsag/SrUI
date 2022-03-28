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
use srag\Plugins\SrUI\Config\Menu\MenuColorConfig;
use srag\Plugins\SrUI\Config\Buttons\ButtonColorConfig;
use srag\Plugins\SrUI\ResourceStorage\Factory;
use srag\Plugins\SrUI\Config\Header\ResponsiveHeaderLogoConfiguration;

/**
 * Class ConfigurationStorage
 *
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
            new ResponsiveHeaderLogoConfiguration(),
            new HeaderTitleConfiguration(),
            new HeaderColorsConfiguration(),
            new CenteredPageConfig(),
            new ShowComponentsConfig(),
            new MenuColorConfig(),
            new ButtonColorConfig(),
        ]);
        $this->storage = new ConfigurationStorage($this->collection, $this->version);
        $this->storage->read();
    }
    
    public function isUpToDate() : bool
    {
        return !$this->storage->needsUpdate();
    }
    
    public function get(string $namespace) : Config
    {
        return $this->collection->get($namespace);
    }
    
    public function save()
    {
        $this->storage->write();
    }
    
    public function export() : string
    {
        $resource_storage = Factory::storage();
        
        $zip = new \ZipArchive();
        $filename = \ilUtil::ilTempnam();
        $zip->open($filename, \ZipArchive::CREATE);
        $zip->addFromString('version', $this->storage->getStoredVersion());
        $zip->addFromString('config', json_encode($this->storage->getRawData()));
        foreach ($this->collection->getAll() as $config) {
            $data = $config->getRawData();
            if (!is_array($data)) {
                continue;
            }
            foreach ($data as $datum) {
                if (is_array($datum) && is_string($datum[0]) && $resource_storage->has($datum[0])) {
                    $zip->addFromString($datum[0], $resource_storage->getString($datum[0]));
                }
            }
        }
        $zip->close();
        
        return $filename;
    }
    
    public function import(\ZipArchive $zip) : void
    {
        $zip = $zip;
        $resource_storage = Factory::storage();
        
        function array_map_recursive(callable $func, array $arr)
        {
            array_walk_recursive($arr, function (&$v) use ($func) {
                $v = $func($v);
            });
            return $arr;
        }
        
        $version = $zip->getFromName('version');
        $config = $zip->getFromName('config');
        $config_array = json_decode($config, true);
        
        $config_array = array_map_recursive(function ($value) use ($zip, $resource_storage) {
            if ($file_content = $zip->getFromName($value)) {
                $new_rid = $resource_storage->fromString($file_content);
                return $new_rid;
            }
            return $value;
        }, $config_array);
        
        $this->storage->readFromArray($config_array);
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
    
    public function responsiveHeaderLogoConfig() : ResponsiveHeaderLogoConfiguration
    {
        return $this->get(ResponsiveHeaderLogoConfiguration::NS);
    }
    
    public function titleConfig() : HeaderTitleConfiguration
    {
        return $this->get(HeaderTitleConfiguration::NS);
    }
    
    public function footerColorConfig() : FooterColorsConfig
    {
        return $this->get(FooterColorsConfig::NS);
    }
    
    public function menuColorConfig() : MenuColorConfig
    {
        return $this->get(MenuColorConfig::NS);
    }
    
    public function footerAdditionalInfosConfig() : AdditionalInfoConfig
    {
        return $this->get(AdditionalInfoConfig::NS);
    }
    
    public function buttonColorConfig() : ButtonColorConfig
    {
        return $this->get(ButtonColorConfig::NS);
    }
    
}
