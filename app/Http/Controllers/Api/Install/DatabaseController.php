<?php

namespace Lockd\Http\Controllers\Api\Install;

use Illuminate\Http\Request;
use Lockd\Http\Controllers\Api\BaseApiController;
use Illuminate\Database\DatabaseManager;
use Illuminate\Log\Writer;
use Illuminate\Support\Facades\Artisan;

/**
 * Class DatabaseController
 *
 * @package Lockd\Http\Controllers\Api\Install
 * @author Iain Earl <synapse791@gmail.com>
 * @codeCoverageIgnore
 */
class DatabaseController extends BaseApiController
{
    private $logger;

    private $databaseManager;

    public function __construct(Writer $logger, DatabaseManager $databaseManager)
    {
        $this->logger = $logger;
        $this->databaseManager = $databaseManager;
    }

    public function task($task)
    {
        try {
            switch ($task) {
                case 'check':
                    return $this->jsonResponse(
                        $this->databaseManager->connection()->getDatabaseName() ? true : false
                    );
                case 'install':
                    $exitCode = Artisan::call('migrate:install');
                    return $this->jsonResponse($exitCode == 0);
                case 'migrate':
                    $exitCode = Artisan::call('migrate', ['--force' => true]);
                    return $this->jsonResponse($exitCode == 0);
                case 'seed':
                    $exitCode = Artisan::call('db:seed', ['--force' => true]);
                    return $this->jsonResponse($exitCode == 0);
                default:
                    return $this->jsonBadRequest("Unknown task: {$task}");
            }
        } catch (\PDOException $e) {
            $this->logger->error($e->getMessage() . $e->getTraceAsString());
            return $this->jsonResponse(false);
        }
    }
}