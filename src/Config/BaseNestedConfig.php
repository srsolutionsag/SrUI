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
 * Class BaseNestedConfig
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
abstract class BaseNestedConfig implements Config
{
    protected $data;

    public function __construct(protected string $namespace)
    {
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function has(string $key): bool
    {
        return isset($this->data[$key]);
    }

    public function get(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    public function loadData($data): void
    {
        $this->data = $data;
    }

    public function getData()
    {
        $fields = $this->getFields();
        foreach ($fields as $field_name) {
            if (!isset($this->data[$field_name])) {
                return null;
            }
        }

        // filter extra values
        foreach ($this->data as $key => $value) {
            if (!in_array($key, $fields, true)) {
                unset($this->data[$key]);
            }
        }

        // fill missing values
        foreach ($fields as $field_name) {
            if (!isset($this->data[$field_name])) {
                $this->data[$field_name] = null;
            }
        }

        return $this->data;
    }

    public function getRawData()
    {
        return $this->data;
    }

    public function isNamespaceActive(): bool
    {
        return $this->data !== null;
    }

    abstract public function getFields(): array;
}
