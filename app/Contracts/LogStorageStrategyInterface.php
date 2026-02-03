<?php

namespace App\Contracts;

/**
 * Log Storage Strategy Interface
 * 
 * Defines the contract for log storage implementations (VictoriaLogs, File, etc.)
 * Part of Victoria Logs Migration - Strategy Pattern
 */
interface LogStorageStrategyInterface
{
    /**
     * Store a log entry
     * 
     * @param array $logData Complete log data array
     * @return void
     * @throws \Exception if storage fails
     */
    public function store(array $logData): void;

    /**
     * Query logs with filters
     * 
     * @param array $params Query parameters (filters, pagination, etc.)
     * @return array Array of log entries
     * @throws \Exception if query fails
     */
    public function query(array $params): array;

    /**
     * Get table name for a module
     * 
     * @param string $module Module/table name
     * @return string|null Table name or null if not found
     */
    public function getAuditTables(string $module): ?string;

    /**
     * Get all audit table names
     * 
     * @return array List of all audit table names
     */
    public function getAllAuditTables(): array;
}
