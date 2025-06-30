<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

namespace srag\Plugins\SrUI\Artifact;

/**
 * An array as an artifact.
 */
class ArtifactSaver
{
    public const SUFFIX = '.php';
    protected ArrayArtifact $artifact;
    protected string $base_path;

    public function __construct(protected string $file_name)
    {
        $this->artifact = new ArrayArtifact([]);
        $this->base_path = rtrim(CLIENT_DATA_DIR, "/");
    }

    public function getPath(): string
    {
        return $this->base_path . "/" . $this->file_name . self::SUFFIX;
    }

    public function save(ArrayArtifact $artifact): void
    {
        $this->artifact = $artifact;
        if (!file_exists($this->getPath())) {
            touch($this->getPath());
        }
        file_put_contents($this->getPath(), $this->artifact->serialize());
        if (function_exists('opcache_invalidate')) {
            opcache_invalidate($this->getPath(), true);
        }
    }

    public function read(): array
    {
        if (!file_exists($this->getPath())) {
            $data = []; // TODO reset to default
        } else {
            $data = include($this->getPath());
        }

        $this->artifact = new ArrayArtifact(is_array($data) ? $data : []);
        return $data;
    }

    public function delete(): void
    {
        if (file_exists($this->getPath())) {
            unlink($this->getPath());
        }
    }
}
