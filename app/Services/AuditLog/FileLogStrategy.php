<?php

namespace App\Services\AuditLog;

use App\Contracts\LogStorageStrategyInterface;
use Illuminate\Support\Facades\Log;

/**
 * File Log Storage Strategy
 * 
 */
class FileLogStrategy implements LogStorageStrategyInterface
{
    protected $logPath;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->logPath = storage_path('logs/audit.log');
    }

    /**
     * Store a log entry to file
     * 
     * @param array $logData
     * @return void
     */
    public function store(array $logData): void
    {
        try {
            Log::useFiles($this->logPath);
            Log::info('data:', $logData);
        } catch (\Exception $e) {
            Log::error('Failed to write to audit log file: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Query logs (not implemented for file strategy)
     * 
     * @param array $params
     * @return array
     */
    public function query(array $params): array
    {
        throw new \Exception('Query not supported for file-based log storage. Use VictoriaLogs instead.');
    }

    /**
     * Get table name for module
     * 
     * @param string $module
     * @return string|null
     */
    public function getAuditTables(string $module): ?string
    {
        return null; // Not applicable for file-based storage
    }

    /**
     * Get all audit table names
     * 
     * @return array
     */
    public function getAllAuditTables(): array
    {
        return []; // Not applicable for file-based storage
    }
}
