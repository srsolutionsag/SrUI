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
 * Interface Config
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
interface Config
{
    public function getNamespace(): string;

    public function has(string $key): bool;

    /**
     * @param string     $key
     * @param mixed|null $default
     * @return mixed
     */
    public function get(string $key, $default = null);

    public function loadData($data): void;

    public function getData();

    public function getRawData();

    public function isNamespaceActive(): bool;
}
