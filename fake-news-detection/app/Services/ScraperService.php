<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class ScraperService
{
    /**
     * Scrape the main content text from a given URL.
     *
     * @param string $url
     * @return string
     * @throws \Exception
     */
    public function scrape(string $url): string
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            ])->get($url);

            if ($response->failed()) {
                throw new \Exception("Failed to fetch URL: " . $response->status());
            }

            $html = $response->body();
            $crawler = new Crawler($html);

            // Remove scripts, styles, and other non-content elements
            $crawler->filter('script, style, nav, footer, header, aside, .ad, .advertisement, .social-share')->each(function (Crawler $crawler) {
                foreach ($crawler as $node) {
                    $node->parentNode->removeChild($node);
                }
            });

            // Try to find the main article content
            // We look for common article tags or classes
            $content = '';
            
            // Strategy 1: <article> tag
            if ($crawler->filter('article')->count() > 0) {
                $content = $crawler->filter('article')->text();
            } 
            // Strategy 2: Common class names
            elseif ($crawler->filter('.post-content, .article-body, .entry-content, .story-body, #content')->count() > 0) {
                $content = $crawler->filter('.post-content, .article-body, .entry-content, .story-body, #content')->text();
            }
            // Strategy 3: Fallback to all paragraphs
            else {
                $content = implode("\n\n", $crawler->filter('p')->each(function (Crawler $node) {
                    return $node->text();
                }));
            }

            $cleanContent = trim(preg_replace('/\s+/', ' ', $content));

            if (strlen($cleanContent) < 50) {
                throw new \Exception("Could not extract sufficient text from this URL. Please paste the text manually.");
            }

            return $cleanContent;

        } catch (\Exception $e) {
            throw new \Exception("Scraping failed: " . $e->getMessage());
        }
    }
}
