<?php

namespace srag\Plugins\SrUI\Config;

/**
 * Class SavableArtifact
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class SavableArtifact
{
    const SUFFIX = '.php';
    const CONFIG_VERSION = 2;
    const FIELD_CONFIG_VERSION = 'config_version';
    protected $file_name = 'artifact';
    protected $config_collection;
    protected $base_path;
    protected $data = [];
    protected $config_version;

    /**
     * @param $config_collection
     */
    public function __construct(ConfigCollection $config_collection, string $file_name, string $config_version)
    {
        $this->config_version = $config_version;
        $this->base_path = rtrim(CLIENT_DATA_DIR, "/");
        $this->file_name = $file_name;
        $this->config_collection = $config_collection;
    }

    private function getPath() : string
    {
        return $this->base_path . "/" . $this->file_name . self::SUFFIX;
    }

    private function fromConfigsToArray() : void
    {
        foreach ($this->config_collection->getAll() as $config) {
            $this->data[$config->getNamespace()] = $config->getData();
        }
    }

    private function fromArrayToConfigs() : void
    {
        foreach ($this->data as $namespace => $data) {
            if ($this->config_collection->has($namespace)) {
                $this->config_collection->get($namespace)->loadData($data);
            }
        }
    }

    public function read() : void
    {
        if (!file_exists($this->getPath())) {
            $data = []; // TODO reset to default
        } else {
            $data = include($this->getPath());
        }

        $this->data = is_array($data) ? $data : [];
        $this->data[self::FIELD_CONFIG_VERSION] = $this->data[self::FIELD_CONFIG_VERSION];
        $this->fromArrayToConfigs();
    }

    public function write() : void
    {
        $this->fromConfigsToArray();
        if (!file_exists($this->getPath())) {
            touch($this->getPath());
        }
        file_put_contents($this->getPath(), $this->serialize());
        if (function_exists('opcache_invalidate')) {
            opcache_invalidate($this->getPath(), true);
        }
    }

    final public function serialize() : string
    {
        return "<?" . "php return " . var_export($this->data, true) . ";";
    }

    private function check(array $a)
    {
        foreach ($a as $item) {
            if (is_string($item) || is_int($item) || is_float($item) || is_bool($item) || is_null($item)) {
                continue;
            }
            if (is_array($item)) {
                $this->check($item);
                continue;
            }
            throw new \InvalidArgumentException(
                "Array data for artifact may only contain ints, strings, floats, bools or " .
                "other arrays with this content. Found: " . gettype($item)
            );
        }
    }

    public function needsUpdate() : bool
    {
        return version_compare($this->config_version, $this->getStoredVersion());
    }

    public function getStoredVersion() : string
    {
        return $this->data[self::FIELD_CONFIG_VERSION] ?? '';
    }
}
