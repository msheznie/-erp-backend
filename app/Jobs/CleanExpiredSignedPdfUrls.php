<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CleanExpiredSignedPdfUrls implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        if(env('QUEUE_DRIVER_CHANGE','database') == 'database'){
            if(env('IS_MULTI_TENANCY',false)){
                 self::onConnection('database_main');
            }else{
                 self::onConnection('database');
            }
        }else{
            self::onConnection(env('QUEUE_DRIVER_CHANGE','database'));
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $cleanupCount = 0;
            $currentTime = time();

            // Get cache driver to determine cleanup strategy
            $cacheDriver = config('cache.default');

            switch ($cacheDriver) {
                case 'redis':
                    $cleanupCount = $this->cleanupRedisCache($currentTime);
                    break;
                
                case 'memcached':
                    $cleanupCount = $this->cleanupMemcachedCache($currentTime);
                    break;
                
                case 'file':
                case 'database':
                default:
                    $cleanupCount = $this->cleanupGenericCache($currentTime);
                    break;
            }

            Log::info("Signed PDF URL cache cleanup completed. Cleaned up {$cleanupCount} expired entries.");

        } catch (\Exception $e) {
            Log::error('Signed PDF URL cache cleanup failed: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
        }
    }

    /**
     * Cleanup Redis cache entries
     *
     * @param int $currentTime
     * @return int
     */
    private function cleanupRedisCache($currentTime)
    {
        $cleanupCount = 0;
        
        try {
            $redis = \Illuminate\Support\Facades\Redis::connection();
            $keys = $redis->keys(config('cache.prefix', 'laravel_cache') . ':signed_pdf_url:*');

            foreach ($keys as $key) {
                $data = $redis->get($key);
                if ($data) {
                    $urlData = unserialize($data);
                    if ($this->isExpired($urlData, $currentTime)) {
                        $redis->del($key);
                        $cleanupCount++;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning('Redis cleanup failed, falling back to generic cleanup: ' . $e->getMessage());
            return $this->cleanupGenericCache($currentTime);
        }

        // Also clean up the active tokens tracking list for Redis
        $this->cleanupActiveTokensList($currentTime);

        return $cleanupCount;
    }

    /**
     * Cleanup Memcached cache entries
     *
     * @param int $currentTime
     * @return int
     */
    private function cleanupMemcachedCache($currentTime)
    {
        // Memcached doesn't support key patterns, so we'll rely on TTL
        // But we still need to clean up the active tokens list
        $this->cleanupActiveTokensList($currentTime);
        
        Log::info('Memcached cleanup: Relying on TTL for automatic cleanup, cleaned active tokens list');
        return 0;
    }

    /**
     * Generic cleanup for file/database cache
     *
     * @param int $currentTime
     * @return int
     */
    private function cleanupGenericCache($currentTime)
    {
        $cleanupCount = 0;

        // For generic cache drivers, we'll use a different approach
        // Store a list of active tokens and check them individually
        $activeTokens = Cache::get('signed_pdf_active_tokens', []);
        
        foreach ($activeTokens as $index => $token) {
            $cacheKey = "signed_pdf_url:{$token}";
            $urlData = Cache::get($cacheKey);
            
            if (!$urlData || $this->isExpired($urlData, $currentTime)) {
                Cache::forget($cacheKey);
                unset($activeTokens[$index]);
                $cleanupCount++;
            }
        }

        // Update the active tokens list or remove it if empty
        if (empty($activeTokens)) {
            Cache::forget('signed_pdf_active_tokens');
            Log::info('Removed empty active tokens list from cache');
        } else {
            Cache::put('signed_pdf_active_tokens', array_values($activeTokens), 60 * 24); // 24 hours
        }

        return $cleanupCount;
    }

    /**
     * Clean up the active tokens tracking list
     * This method works across all cache drivers
     *
     * @param int $currentTime
     * @return void
     */
    private function cleanupActiveTokensList($currentTime)
    {
        try {
            $activeTokens = Cache::get('signed_pdf_active_tokens', []);
            
            if (empty($activeTokens)) {
                return; // Nothing to clean
            }

            $validTokens = [];
            $cleanedCount = 0;

            foreach ($activeTokens as $token) {
                $cacheKey = "signed_pdf_url:{$token}";
                $urlData = Cache::get($cacheKey);
                
                // Keep token if it still exists and is not expired
                if ($urlData && !$this->isExpired($urlData, $currentTime)) {
                    $validTokens[] = $token;
                } else {
                    $cleanedCount++;
                }
            }

            // Update or remove the active tokens list
            if (empty($validTokens)) {
                Cache::forget('signed_pdf_active_tokens');
                Log::info("Removed empty active tokens list after cleaning {$cleanedCount} expired tokens");
            } else if ($cleanedCount > 0) {
                Cache::put('signed_pdf_active_tokens', $validTokens, 60 * 24); // 24 hours
                Log::info("Cleaned {$cleanedCount} expired tokens from active tokens list, " . count($validTokens) . " remaining");
            }

        } catch (\Exception $e) {
            Log::warning('Failed to cleanup active tokens list: ' . $e->getMessage());
        }
    }

    /**
     * Check if a URL data entry is expired
     *
     * @param array $urlData
     * @param int $currentTime
     * @return bool
     */
    private function isExpired($urlData, $currentTime)
    {
        if (!is_array($urlData) || !isset($urlData['created_at'])) {
            return true; // Invalid data, should be cleaned
        }

        $createdAt = $urlData['created_at'];
        $expiresInMinutes = env('SIGNED_PDF_EXPIRES_IN_MINUTES', 3);
        $expirationTime = $createdAt + ($expiresInMinutes * 60);

        // Also check if it was used and grace period expired
        if (isset($urlData['used_at']) && $urlData['used_at']) {
            $gracePeriodExpiration = $urlData['used_at'] + 120; // 2 minutes grace period
            return $currentTime > $gracePeriodExpiration;
        }

        return $currentTime > $expirationTime;
    }

    /**
     * The job failed to process.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function failed(\Exception $exception)
    {
        Log::error('CleanExpiredSignedPdfUrls job failed: ' . $exception->getMessage());
    }
}
