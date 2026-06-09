<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class NewsApiService
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = env('NEWS_API_KEY');
        $this->baseUrl = env('NEWS_API_URL');
    }

    public function fetchTopHeadlines($category = null, $country = 'us', $pageSize = 20)
    {
        // Cache results for 10 minutes to avoid hitting rate limits
        $cacheKey = 'news_' . $category . '_' . $country;
        return Cache::remember($cacheKey, 600, function () use ($category, $country, $pageSize) {
            $params = [
                'country' => $country,
                'apiKey' => $this->apiKey,
                'pageSize' => $pageSize,
            ];
            if ($category) {
                $params['category'] = $category;
            }

            $response = Http::get($this->baseUrl, $params);

            if ($response->failed()) {
                throw new \Exception('News API error: ' . $response->body());
            }

            return $response->json()['articles'] ?? [];
        });
    }
}