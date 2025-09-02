<?php
class Cache {
    private $cache_dir;
    private $cache_time;

    public function __construct($cache_dir = '../cache', $cache_time = 3600) {
        $this->cache_dir = $cache_dir;
        $this->cache_time = $cache_time;
        
        // Create cache directory if it doesn't exist
        if (!file_exists($this->cache_dir)) {
            mkdir($this->cache_dir, 0755, true);
        }
    }

    public function get($key) {
        $filename = $this->getCacheFilename($key);
        
        if (file_exists($filename) && (time() - filemtime($filename) < $this->cache_time)) {
            return unserialize(file_get_contents($filename));
        }
        
        return false;
    }

    public function set($key, $data) {
        $filename = $this->getCacheFilename($key);
        return file_put_contents($filename, serialize($data));
    }

    public function delete($key) {
        $filename = $this->getCacheFilename($key);
        if (file_exists($filename)) {
            return unlink($filename);
        }
        return false;
    }

    public function clear() {
        $files = glob($this->cache_dir . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        return true;
    }

    private function getCacheFilename($key) {
        return $this->cache_dir . '/' . md5($key) . '.cache';
    }
} 