<?php
class Cache {
    private $cache_dir;
    private $cache_time;

    public function __construct($cache_dir = null, $cache_time = 300) {
        $this->cache_dir = $cache_dir ?? dirname(__DIR__) . '/cache';
        $this->cache_time = $cache_time;

        // Create cache directory if it doesn't exist
        if (!file_exists($this->cache_dir)) {
            mkdir($this->cache_dir, 0777, true);
        }
    }

    public function get($key) {
        $filename = $this->getCacheFilename($key);
        
        if (!file_exists($filename)) {
            return false;
        }

        $data = file_get_contents($filename);
        $cache = unserialize($data);

        if ($cache['expires'] < time()) {
            unlink($filename);
            return false;
        }

        return $cache['data'];
    }

    public function set($key, $data, $time = null) {
        $filename = $this->getCacheFilename($key);
        $time = $time ?? $this->cache_time;

        $cache = [
            'expires' => time() + $time,
            'data' => $data
        ];

        return file_put_contents($filename, serialize($cache));
    }

    public function delete($key) {
        $filename = $this->getCacheFilename($key);
        if (file_exists($filename)) {
            return unlink($filename);
        }
        return true;
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
?> 