<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

use srag\Plugins\SrUI\ResourceStorage\ResourceStorage;
use ILIAS\FileUpload\DTO\UploadResult;
use ILIAS\FileUpload\Handler\AbstractCtrlAwareUploadHandler;
use ILIAS\FileUpload\Handler\BasicFileInfoResult;
use ILIAS\FileUpload\Handler\BasicHandlerResult;
use ILIAS\FileUpload\Handler\FileInfoResult;
use ILIAS\FileUpload\Handler\HandlerResult as HandlerResultInterface;
use srag\Plugins\SrUI\ResourceStorage\Factory;
use srag\Plugins\SrUI\ResourceStorage\Stakeholder;

/**
 * Class ilSrUIFileHandlerGUI
 *
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 * @ilCtrl_IsCalledBy ilSrUIFileHandlerGUI: ilSrUIMainGUI
 */
class ilSrUIFileHandlerGUI extends AbstractCtrlAwareUploadHandler implements ilSrUIGUI
{
    protected ResourceStorage $storage;
    /**
     * @var Stakeholder
     */
    protected $stakeholder;

    /**
     * ilUIDemoFileUploadHandlerGUI constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $factory = new Factory();
        $this->storage = $factory->storage();
        $this->stakeholder = $factory->stakeholder();
    }

    /**
     * @inheritDoc
     */
    protected function getUploadResult(): HandlerResultInterface
    {
        $this->upload->process();
        /**
         * @var $result UploadResult
         */
        $array = $this->upload->getResults();
        $result = end($array);
        if ($result instanceof UploadResult && $result->isOK()) {
            $identifier = $this->storage->fromUpload($result);
            $status = HandlerResultInterface::STATUS_OK;
            $message = 'Upload ok';
        } else {
            $status = HandlerResultInterface::STATUS_FAILED;
            $identifier = '';
            $message = $result->getStatus()->getMessage();
        }

        return new BasicHandlerResult($this->getFileIdentifierParameterName(), $status, $identifier, $message);
    }

    protected function getRemoveResult(string $identifier): HandlerResultInterface
    {
        if ($this->storage->remove($identifier)) {
            return new BasicHandlerResult(
                $this->getFileIdentifierParameterName(),
                HandlerResultInterface::STATUS_OK,
                $identifier,
                'file deleted'
            );
        }

        return new BasicHandlerResult(
            $this->getFileIdentifierParameterName(),
            HandlerResultInterface::STATUS_FAILED,
            $identifier,
            'file not found'
        );
    }

    public function getInfoResult(string $identifier): ?FileInfoResult
    {
        $info = $this->storage->getRevisionInfo($identifier);

        return new BasicFileInfoResult(
            $this->getFileIdentifierParameterName(),
            $identifier,
            (string) $info['title'],
            (int) $info['size'],
            (string) $info['mime_type']
        );
    }

    public function getInfoForExistingFiles(array $file_ids): array
    {
        return [];
    }
}
