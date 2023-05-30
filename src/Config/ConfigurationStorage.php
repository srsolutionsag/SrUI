<?php

namespace srag\Plugins\SrUI\Config;

use srag\Plugins\SrUI\Artifact\ArrayArtifact;
use srag\Plugins\SrUI\Artifact\ArtifactSaver;

/**
 * Class ConfigurationStorage
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class ConfigurationStorage
{
    public const FIELD_CONFIG_VERSION = 'config_version';
    protected \srag\Plugins\SrUI\Config\ConfigCollection $config_collection;
    protected string $config_version;
    protected $data = [];
    protected \srag\Plugins\SrUI\Artifact\ArtifactSaver $artifact_storage;

    /**
     * @param $config_collection
     */
    public function __construct(ConfigCollection $config_collection, string $config_version)
    {
        $this->config_version = $config_version;
        $this->config_collection = $config_collection;
        $this->artifact_storage = new ArtifactSaver('sr_ui_setting');
    }

    private function fromConfigsToArray(): void
    {
        foreach ($this->config_collection->getAll() as $config) {
            $this->data[$config->getNamespace()] = $config->getRawData();
        }
    }

    private function fromArrayToConfigs(): void
    {
        foreach ($this->data as $namespace => $data) {
            if ($this->config_collection->has($namespace)) {
                $this->config_collection->get($namespace)->loadData($data);
            }
        }
    }

    public function readFromArray(array $raw_data): void
    {
        $this->data = $raw_data;
        $this->fromArrayToConfigs();
    }

    public function read(): void
    {
        $this->data = $this->getRawData();
        $this->fromArrayToConfigs();
    }

    public function write(): void
    {
        $this->fromConfigsToArray();
        $this->data[self::FIELD_CONFIG_VERSION] = $this->config_version;
        $this->artifact_storage->save(new ArrayArtifact($this->data));
    }

    public function needsUpdate(): bool
    {
        return version_compare($this->config_version, $this->getStoredVersion());
    }

    public function getStoredVersion(): string
    {
        return $this->data[self::FIELD_CONFIG_VERSION] ?? '';
    }

    public function getArtifactPath(): string
    {
        return $this->artifact_storage->getPath();
    }

    public function getRawData(): array
    {
        return $this->artifact_storage->read();
    }
}
