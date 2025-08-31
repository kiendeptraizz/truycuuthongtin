<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PerformanceMonitor
{
    public static function logSlowQueries(): void
    {
        DB::listen(function ($query) {
            if ($query->time > 1000) { // Queries slower than 1 second
                Log::warning('Slow Query Detected', [
                    'sql' => $query->sql,
                    'bindings' => $query->bindings,
                    'time' => $query->time . 'ms',
                    'connection' => $query->connectionName,
                ]);
            }
        });
    }

    public static function getSystemStats(): array
    {
        return [
            'memory_usage' => memory_get_usage(true),
            'memory_peak' => memory_get_peak_usage(true),
            'execution_time' => microtime(true) - LARAVEL_START,
            'queries_count' => count(DB::getQueryLog()),
        ];
    }

    public static function optimizeDatabase(): array
    {
        $results = [];

        // Get table sizes
        $tables = DB::select("
            SELECT 
                table_name,
                ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'size_mb'
            FROM information_schema.tables 
            WHERE table_schema = DATABASE()
            ORDER BY (data_length + index_length) DESC
        ");

        $results['table_sizes'] = $tables;

        // Check for missing indexes
        $slowQueries = DB::select("
            SELECT table_name, column_name
            FROM information_schema.columns 
            WHERE table_schema = DATABASE() 
            AND column_name IN ('created_at', 'updated_at', 'status', 'email')
            AND table_name NOT IN (
                SELECT DISTINCT table_name 
                FROM information_schema.statistics 
                WHERE table_schema = DATABASE()
                AND column_name IN ('created_at', 'updated_at', 'status', 'email')
            )
        ");

        $results['suggested_indexes'] = $slowQueries;

        return $results;
    }
}
