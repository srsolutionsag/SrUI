<?php

namespace srag\Plugins\SrUI\Config;

/**
 * Class BaseNestedConfig
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
abstract class BaseNestedConfig implements Config
{
    protected $namespace;

    protected $data = null;

    public function __construct(string $namespace)
    {
        $this->namespace = $namespace;
    }

    public function getNamespace() : string
    {
        return $this->namespace;
    }

    public function has(string $key) : bool
    {
        return isset($this->data[$key]);
    }

    public function get(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    public function loadData($data) : void
    {
        $this->data = $data;
    }

    public function getData()
    {
        foreach ($this->getFields() as $field_name) {
            if (!isset($this->data[$field_name])) {
                return null;
            }
        }

        return $this->data;
    }

    public function isNamespaceActive() : bool
    {
        return $this->data !== null;
    }

    abstract public function getFields() : array;

}
