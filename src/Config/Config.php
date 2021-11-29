<?php

namespace srag\Plugins\SrUI\Config;

/**
 * Interface Config
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
interface Config
{
    public function getNamespace() : string;

    public function has(string $key) : bool;

    /**
     * @param string     $key
     * @param mixed|null $default
     * @return mixed
     */
    public function get(string $key, $default = null);

    public function loadData($data) : void;

    public function getData();

    public function isNamespaceActive() : bool;
}
