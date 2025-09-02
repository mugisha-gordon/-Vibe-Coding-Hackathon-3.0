<?php
class Cache {
    private $cache_dir;
    private $stats = [
        'hits' => 0,
        'misses' => 0
    ];

    public function __construct() {
        $this->cache_dir = __DIR__ . '/../cache/';
        if (!file_exists($this->cache_dir)) {
            mkdir($this->cache_dir, 0755, true);
        }
    }

    public function get($key) {
        $filename = $this->getCacheFilename($key);
        if (file_exists($filename) && (time() - filemtime($filename) < 3600)) {
            $this->stats['hits']++;
            return unserialize(file_get_contents($filename));
        }
        $this->stats['misses']++;
        return false;
    }

    public function set($key, $value) {
        $filename = $this->getCacheFilename($key);
        return file_put_contents($filename, serialize($value));
    }

    public function delete($key) {
        $filename = $this->getCacheFilename($key);
        if (file_exists($filename)) {
            unlink($filename);
            return true;
        }
        return false;
    }

    public function clear() {
        $files = glob($this->cache_dir . '*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        return true;
    }

    public function getStats() {
        return $this->stats;
    }

    private function getCacheFilename($key) {
        return $this->cache_dir . md5($key) . '.cache';
    }
}
?> 