<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

namespace srag\Plugins\SrUI\Config;

/**
 * Class ConfigCollection
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class ConfigCollection
{
    protected $configs = [];
    protected $artifact;

    /**
     * @param Config[] $configs
     */
    public function __construct(array $configs)
    {
        foreach ($configs as $config) {
            if (!$config instanceof Config) {
                throw new \InvalidArgumentException('all configs MUST be of type ' . Config::class);
            }
            $this->configs[$config->getNamespace()] = $config;
        }
    }

    public function has(string $namespace): bool
    {
        return isset($this->configs[$namespace]);
    }

    public function get(string $namespace): Config
    {
        if (!$this->has($namespace)) {
            throw new \InvalidArgumentException('unknown $namespace ' . $namespace);
        }
        return $this->configs[$namespace];
    }

    /**
     * @return Config[]
     */
    public function getAll(): array
    {
        return $this->configs;
    }
}
