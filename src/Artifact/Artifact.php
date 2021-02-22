<?php

namespace srag\Plugins\SrUI\Artifact;

use ILIAS\Filesystem\Filesystem;
use ILIAS\Setup\ArrayArtifact;

/**
 * Class Artifact
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class Artifact implements \ArrayAccess
{
    const SUFFIX = '.php';
    protected $file_name = 'artifact';
    /**
     * @var Filesystem
     */
    protected $fs;
    /**
     * @var array
     */
    protected $data;

    /**
     * Artifact constructor.
     * @param string     $file_name
     * @param Filesystem $fs
     */
    public function __construct(string $file_name)
    {
        $this->file_name = $file_name;
        $this->data      = $this->read();
    }

    /**
     * @return string
     */
    private function getPath() : string
    {
        return rtrim(CLIENT_DATA_DIR, "/") . "/" . $this->file_name . self::SUFFIX;
        //return './Customizing/' . $this->file_name . self::SUFFIX;
    }

    public function save() : void
    {
        $a = new ArrayArtifact($this->data);
        if (!file_exists($this->getPath())) {
            touch($this->getPath());
        }
        file_put_contents($this->getPath(), $a->serialize());
    }

    protected function read() : array
    {
        $data = null;
        if (file_exists($this->getPath())) {
            $data = include($this->getPath());
        }
        return is_array($data) ? $data : [];
    }

    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

}
