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
use srag\Plugins\SrUI\Version;
use srag\Plugins\SrUI\Config\Footer\PermanentLinkConfig;

/**
 * Class ConfigurationStorage
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class Configuration
{
    protected \srag\Plugins\SrUI\Config\ConfigCollection $collection;
    protected string $version;
    protected \srag\Plugins\SrUI\Config\ConfigurationStorage $storage;
    protected \srag\Plugins\SrUI\ResourceStorage\Factory $resource_storage_factory;

    /**
     * @param string $version
     */
    public function __construct(string $version)
    {
        $this->version = $version;
        $this->resource_storage_factory = new Factory();
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
            new PermanentLinkConfig()
        ]);
        $this->storage = new ConfigurationStorage($this->collection, $this->version);
        $this->storage->read();
    }

    public function isUpToDate(): bool
    {
        return !$this->storage->needsUpdate();
    }

    public function getStoredConfigVersion(): Version
    {
        return new Version($this->storage->getStoredVersion());
    }

    public function get(string $namespace): Config
    {
        return $this->collection->get($namespace);
    }

    public function save(): void
    {
        $this->storage->write();
    }

    public function export(): string
    {
        $resource_storage = $this->resource_storage_factory->storage();

        $zip = new \ZipArchive();
        $filename = \ilFileUtils::ilTempnam();
        $zip->open($filename, \ZipArchive::CREATE);
        $zip->addFromString('version', $this->storage->getStoredVersion());
        $zip->addFromString('config', json_encode($this->storage->getRawData(), JSON_THROW_ON_ERROR));
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

    public function import(\ZipArchive $zip): void
    {
        $resource_storage = $this->resource_storage_factory->storage();

        function array_map_recursive(callable $func, array $arr)
        {
            array_walk_recursive($arr, function (&$v) use ($func): void {
                $v = $func($v);
            });
            return $arr;
        }

        $version = $zip->getFromName('version');
        $config = $zip->getFromName('config');
        $config_array = json_decode($config, true, 512, JSON_THROW_ON_ERROR);

        $finfo_possible = class_exists('finfo');

        $config_array = array_map_recursive(function ($value) use ($zip, $resource_storage, $finfo_possible) {
            if (!empty($value) && $file_content = $zip->getFromName($value)) {
                $mime_type = null;
                if ($finfo_possible) {
                    $finfo = new \finfo(FILEINFO_MIME_TYPE);
                    $mime_type = $finfo->buffer($file_content);
                }
                return $resource_storage->fromString($file_content, $mime_type);
            }
            return $value;
        }, $config_array);

        $this->storage->readFromArray($config_array);
        $this->storage->write();
    }

    public function getStorage(): ConfigurationStorage
    {
        return $this->storage;
    }

    public function prodServerConfig(): ProdServerConfig
    {
        return $this->get(ProdServerConfig::NS);
    }

    public function contentConfig(): SpecialContentFeaturesConfig
    {
        return $this->get(SpecialContentFeaturesConfig::NS);
    }

    public function headerColorConfig(): HeaderColorsConfiguration
    {
        return $this->get(HeaderColorsConfiguration::NS);
    }

    public function headerLogoConfig(): HeaderLogoConfiguration
    {
        return $this->get(HeaderLogoConfiguration::NS);
    }

    public function responsiveHeaderLogoConfig(): ResponsiveHeaderLogoConfiguration
    {
        return $this->get(ResponsiveHeaderLogoConfiguration::NS);
    }

    public function titleConfig(): HeaderTitleConfiguration
    {
        return $this->get(HeaderTitleConfiguration::NS);
    }

    public function footerColorConfig(): FooterColorsConfig
    {
        return $this->get(FooterColorsConfig::NS);
    }

    public function menuColorConfig(): MenuColorConfig
    {
        return $this->get(MenuColorConfig::NS);
    }

    public function footerAdditionalInfosConfig(): AdditionalInfoConfig
    {
        return $this->get(AdditionalInfoConfig::NS);
    }

    public function footerPermanentLinkConfig(): PermanentLinkConfig
    {
        return $this->get(PermanentLinkConfig::NS);
    }

    public function buttonColorConfig(): ButtonColorConfig
    {
        return $this->get(ButtonColorConfig::NS);
    }

    public function delete(): void
    {
        $this->storage->delete();
    }
}
