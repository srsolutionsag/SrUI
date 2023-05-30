<?php

namespace srag\Plugins\SrUI\Config\CloneInfo;

use srag\Plugins\SrUI\Config\BaseNestedConfig;

/**
 * Class ProdServerConfig
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class ProdServerConfig extends BaseNestedConfig
{
    public const NS = 'prod_system';
    public const F_PROD_HOSTNAMES = 'prod_hostname';
    public const F_NON_PROD_MESSAGE = 'non_prod_message';
    public const F_NON_PROD_MESSAGE_TITLE = 'non_prod_message_title';
    public const F_SEND_NON_INDEX_HEADERS = 'send_non_index_headers';

    public function __construct()
    {
        parent::__construct(self::NS);
    }

    public function getHostnames(): array
    {
        static $hostnames;
        if (!is_array($hostnames)) {
            $hostnames = explode(",", $this->data[self::F_PROD_HOSTNAMES] ?? '');
        }
        return $hostnames;
    }

    public function sendNonIdexHeader(): bool
    {
        return $this->data[self::F_SEND_NON_INDEX_HEADERS] ?? false;
    }

    public function getNonProdMessage(): string
    {
        return $this->data[self::F_NON_PROD_MESSAGE] ?? '';
    }

    public function getNonProdMessageTitle(): string
    {
        return $this->data[self::F_NON_PROD_MESSAGE_TITLE] ?? '';
    }

    public function onProdSystem(): bool
    {
        return in_array(gethostname(), $this->getHostnames());
    }

    public function getFields(): array
    {
        return [
            self::F_PROD_HOSTNAMES,
            self::F_NON_PROD_MESSAGE,
            self::F_NON_PROD_MESSAGE_TITLE,
            self::F_SEND_NON_INDEX_HEADERS
        ];
    }

    public function isNamespaceActive(): bool
    {
        return isset($this->data['prod_hostname']) && !empty($this->data['prod_hostname']);
    }
}
