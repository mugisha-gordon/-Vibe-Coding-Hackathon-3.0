<?php
require_once __DIR__ . "/cache.php";

class PerformanceMonitor {
    private $start_time;
    private $memory_usage;
    private $queries = [];
    private $cache;

    public function __construct() {
        $this->start_time = microtime(true);
        $this->memory_usage = memory_get_usage();
        $this->cache = new Cache();
    }

    public function start() {
        $this->start_time = microtime(true);
        $this->memory_usage = memory_get_usage();
    }

    public function logQuery($sql, $time) {
        $this->queries[] = [
            'sql' => $sql,
            'time' => $time
        ];
    }

    public function getPerformanceMetrics() {
        $end_time = microtime(true);
        $execution_time = $end_time - $this->start_time;
        $memory_used = memory_get_usage() - $this->memory_usage;
        
        return [
            'execution_time' => $execution_time,
            'memory_used' => $this->formatBytes($memory_used),
            'queries' => $this->queries,
            'cache_hits' => $this->cache->getStats()['hits'],
            'cache_misses' => $this->cache->getStats()['misses']
        ];
    }

    public function optimizePage() {
        // Enable output buffering
        ob_start();
        
        // Enable gzip compression
        if (!ob_start("ob_gzhandler")) {
            ob_start();
        }
        
        // Set appropriate headers
        header('Content-Type: text/html; charset=utf-8');
        header('Cache-Control: public, max-age=3600');
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 3600) . ' GMT');
    }

    public function minifyOutput($buffer) {
        // Remove comments
        $buffer = preg_replace('/<!--[^\[>](.*?)-->/s', '', $buffer);
        
        // Remove whitespace
        $buffer = preg_replace('/\s+/', ' ', $buffer);
        
        // Remove whitespace between tags
        $buffer = preg_replace('/>\s+</', '><', $buffer);
        
        return $buffer;
    }

    private function formatBytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
?> 