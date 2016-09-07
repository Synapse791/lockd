<?php

namespace Lockd\Http\Controllers\Api\Install;

use Lockd\Http\Controllers\Api\BaseApiController;
use Lockd\Services\Install\CheckManager;

/**
 * Class CheckController
 *
 * @package Lockd\Http\Controllers\Api\Install
 * @author Iain Earl <synapse791@gmail.com>
 * @codeCoverageIgnore
 */
class CheckController extends BaseApiController
{
    private $checkManager;

    public function __construct(CheckManager $checkManager)
    {
        $this->checkManager = $checkManager;
    }

    public function check($check)
    {
        switch($check) {
            case 'php_version':
                $result = $this->checkManager->isPhpVersionOk();
                break;
            case 'openssl_module':
                $result = $this->checkManager->isModuleLoaded('openssl');
                break;
            case 'mysql_module':
                $result = $this->checkManager->isModuleLoaded('mysql');
                break;
            case 'pdo_module':
                $result = $this->checkManager->isModuleLoaded('PDO');
                break;
            case 'mbstring_module':
                $result = $this->checkManager->isModuleLoaded('mbstring');
                break;
            case 'tokenizer_module':
                $result = $this->checkManager->isModuleLoaded('tokenizer');
                break;
            case 'storage_writable':
                $result = $this->checkManager->isStorageWritable();
                break;
            default:
                return $this->jsonResponse([], 400, 'bad_request', "Unknown check {$check}");
        }

        return $this->jsonResponse($result);
    }
}