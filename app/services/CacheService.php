<?php

namespace App\Services;

class CacheService
{
    private $cacheDir;
    private $defaultTTL = 3600; // 1 hour

    public function __construct()
    {
        $this->cacheDir = __DIR__ . '/../../storage/cache/';
        
        // Create cache directory if it doesn't exist
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }

    /**
     * Get cached data
     */
    public function get($key, $default = null)
    {
        $filename = $this->getCacheFilename($key);
        
        if (!file_exists($filename)) {
            return $default;
        }

        $data = unserialize(file_get_contents($filename));
        
        // Check if expired
        if ($data['expires_at'] < time()) {
            $this->delete($key);
            return $default;
        }

        return $data['value'];
    }

    /**
     * Set cached data
     */
    public function set($key, $value, $ttl = null)
    {
        $ttl = $ttl ?? $this->defaultTTL;
        $filename = $this->getCacheFilename($key);
        
        $data = [
            'value' => $value,
            'expires_at' => time() + $ttl
        ];

        return file_put_contents($filename, serialize($data)) !== false;
    }

    /**
     * Check if key exists and is not expired
     */
    public function has($key)
    {
        $filename = $this->getCacheFilename($key);
        
        if (!file_exists($filename)) {
            return false;
        }

        $data = unserialize(file_get_contents($filename));
        
        if ($data['expires_at'] < time()) {
            $this->delete($key);
            return false;
        }

        return true;
    }

    /**
     * Delete cached data
     */
    public function delete($key)
    {
        $filename = $this->getCacheFilename($key);
        
        if (file_exists($filename)) {
            return unlink($filename);
        }

        return true;
    }

    /**
     * Clear all cache
     */
    public function clear()
    {
        $files = glob($this->cacheDir . '*.cache');
        
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        return true;
    }

    /**
     * Remember: Get from cache or execute callback and cache result
     */
    public function remember($key, $callback, $ttl = null)
    {
        if ($this->has($key)) {
            return $this->get($key);
        }

        $value = $callback();
        $this->set($key, $value, $ttl);

        return $value;
    }

    /**
     * Get cache filename
     */
    private function getCacheFilename($key)
    {
        return $this->cacheDir . md5($key) . '.cache';
    }

    /**
     * Clean expired cache entries
     */
    public function cleanExpired()
    {
        $files = glob($this->cacheDir . '*.cache');
        $cleaned = 0;

        foreach ($files as $file) {
            if (is_file($file)) {
                $data = unserialize(file_get_contents($file));
                
                if ($data['expires_at'] < time()) {
                    unlink($file);
                    $cleaned++;
                }
            }
        }

        return $cleaned;
    }

    /**
     * Get cache statistics
     */
    public function getStats()
    {
        $files = glob($this->cacheDir . '*.cache');
        $totalSize = 0;
        $expired = 0;
        $active = 0;

        foreach ($files as $file) {
            if (is_file($file)) {
                $totalSize += filesize($file);
                $data = unserialize(file_get_contents($file));
                
                if ($data['expires_at'] < time()) {
                    $expired++;
                } else {
                    $active++;
                }
            }
        }

        return [
            'total_entries' => count($files),
            'active_entries' => $active,
            'expired_entries' => $expired,
            'total_size' => $totalSize,
            'total_size_formatted' => $this->formatBytes($totalSize)
        ];
    }

    /**
     * Format bytes to human readable
     */
    private function formatBytes($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Tag-based cache invalidation
     */
    public function tags($tags)
    {
        return new TaggedCache($this, (array)$tags);
    }
}

/**
 * Tagged cache for group invalidation
 */
class TaggedCache
{
    private $cache;
    private $tags;

    public function __construct(CacheService $cache, array $tags)
    {
        $this->cache = $cache;
        $this->tags = $tags;
    }

    public function get($key, $default = null)
    {
        return $this->cache->get($this->taggedKey($key), $default);
    }

    public function set($key, $value, $ttl = null)
    {
        return $this->cache->set($this->taggedKey($key), $value, $ttl);
    }

    public function flush()
    {
        foreach ($this->tags as $tag) {
            $this->cache->delete('tag:' . $tag);
        }
    }

    private function taggedKey($key)
    {
        return implode(':', $this->tags) . ':' . $key;
    }
}
