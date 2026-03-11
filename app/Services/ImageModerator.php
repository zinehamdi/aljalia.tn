<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImageModerator
{
    protected $apiUser;
    protected $apiSecret;

    public function __construct()
    {
        // Sign up for free at https://sightengine.com/ to get your keys
        $this->apiUser = env('SIGHTENGINE_API_USER');
        $this->apiSecret = env('SIGHTENGINE_API_SECRET');
    }

    /**
     * Check if the image contains nudity or pornography using Sightengine.
     * 
     * @param string $filePath Absolute path to the image
     * @return bool True if the image is SAFE, False if it's NOT safe or error
     */
    public function isSafe($filePath)
    {
        if (empty($this->apiUser) || empty($this->apiSecret)) {
            Log::warning('Sightengine API keys not set. Skipping image moderation.');
            return true; // Bypass for development if no keys are set
        }

        try {
            $response = Http::attach(
                'media', file_get_contents($filePath), basename($filePath)
            )->post("https://api.sightengine.com/1.0/check.json", [
                'models' => 'nudity-2.1', // More accurate model for nudity
                'api_user' => $this->apiUser,
                'api_secret' => $this->apiSecret,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Detailed check for nudity
                // Sightengine gives scores for: sexual_activity, sexual_display, erotica, etc.
                if (isset($data['nudity'])) {
                    $scores = $data['nudity'];
                    
                    // If sexual activity or explicit displays are detected with high confidence (> 0.5)
                    if ($scores['sexual_activity'] > 0.5 || $scores['sexual_display'] > 0.5 || $scores['erotica'] > 0.7) {
                        Log::info('Nudity detected in image: ' . $filePath, $scores);
                        return false;
                    }
                    return true;
                }
            }

            Log::error('Sightengine API error: ' . $response->body());
            return true; // Safety default: allow if API is down but log error

        } catch (\Exception $e) {
            Log::error('Image moderation failed: ' . $e->getMessage());
            return true;
        }
    }
}
