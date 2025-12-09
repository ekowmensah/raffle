<?php

namespace App\Helpers;

class SeoHelper
{
    /**
     * Generate SEO meta tags for campaigns
     */
    public static function generateCampaignMeta($campaign, $baseUrl = '')
    {
        if (empty($baseUrl)) {
            $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
        }
        
        // Clean and prepare data
        $title = self::cleanText($campaign->name ?? 'Campaign');
        $description = self::cleanText($campaign->description ?? '');
        $siteName = 'eTickets Raffle';
        
        // Generate description if not provided
        if (empty($description)) {
            if ($campaign->campaign_type === 'item') {
                $description = "Win {$campaign->item_name} worth GHS " . number_format($campaign->item_value ?? 0, 2) . "! Buy tickets now for {$title}. Easy entry, transparent draws, instant winners!";
            } else {
                $description = "Win cash prizes in {$title}! Buy tickets starting from GHS " . number_format($campaign->ticket_price ?? 0, 2) . ". Transparent draws, instant winners, secure payments!";
            }
        }
        
        // Limit description length
        $description = self::truncateText($description, 160);
        
        // Generate image URL
        $imageUrl = self::getCampaignImageUrl($campaign, $baseUrl);
        
        // Generate keywords
        $keywords = self::generateKeywords($campaign);
        
        // Current page URL
        $currentUrl = $baseUrl . '/public/buy-ticket/' . ($campaign->id ?? '');
        
        return [
            'title' => $title,
            'description' => $description,
            'keywords' => $keywords,
            'image' => $imageUrl,
            'url' => $currentUrl,
            'site_name' => $siteName,
            'type' => 'website',
            'campaign' => $campaign
        ];
    }
    
    /**
     * Render meta tags HTML
     */
    public static function renderMetaTags($meta)
    {
        $html = '';
        
        // Basic meta tags
        $html .= '<title>' . htmlspecialchars($meta['title']) . ' | ' . htmlspecialchars($meta['site_name']) . '</title>' . "\n";
        $html .= '<meta name="description" content="' . htmlspecialchars($meta['description']) . '">' . "\n";
        $html .= '<meta name="keywords" content="' . htmlspecialchars($meta['keywords']) . '">' . "\n";
        
        // Open Graph meta tags (Facebook, LinkedIn)
        $html .= '<meta property="og:title" content="' . htmlspecialchars($meta['title']) . '">' . "\n";
        $html .= '<meta property="og:description" content="' . htmlspecialchars($meta['description']) . '">' . "\n";
        $html .= '<meta property="og:image" content="' . htmlspecialchars($meta['image']) . '">' . "\n";
        $html .= '<meta property="og:url" content="' . htmlspecialchars($meta['url']) . '">' . "\n";
        $html .= '<meta property="og:type" content="' . htmlspecialchars($meta['type']) . '">' . "\n";
        $html .= '<meta property="og:site_name" content="' . htmlspecialchars($meta['site_name']) . '">' . "\n";
        
        // Twitter Card meta tags
        $html .= '<meta name="twitter:card" content="summary_large_image">' . "\n";
        $html .= '<meta name="twitter:title" content="' . htmlspecialchars($meta['title']) . '">' . "\n";
        $html .= '<meta name="twitter:description" content="' . htmlspecialchars($meta['description']) . '">' . "\n";
        $html .= '<meta name="twitter:image" content="' . htmlspecialchars($meta['image']) . '">' . "\n";
        
        // Additional SEO tags
        $html .= '<link rel="canonical" href="' . htmlspecialchars($meta['url']) . '">' . "\n";
        $html .= '<meta name="robots" content="index, follow">' . "\n";
        $html .= '<meta name="author" content="' . htmlspecialchars($meta['site_name']) . '">' . "\n";
        
        return $html;
    }
    
    /**
     * Generate JSON-LD structured data
     */
    public static function generateStructuredData($campaign, $baseUrl = '')
    {
        if (empty($baseUrl)) {
            $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
        }
        
        $imageUrl = self::getCampaignImageUrl($campaign, $baseUrl);
        
        $structuredData = [
            "@context" => "https://schema.org",
            "@type" => "Product",
            "name" => $campaign->name ?? 'Campaign',
            "description" => self::cleanText($campaign->description ?? ''),
            "image" => $imageUrl,
            "brand" => [
                "@type" => "Brand",
                "name" => "eTickets Raffle"
            ],
            "offers" => [
                "@type" => "Offer",
                "url" => $baseUrl . '/public/buy-ticket/' . ($campaign->id ?? ''),
                "priceCurrency" => "GHS",
                "price" => $campaign->ticket_price ?? 0,
                "availability" => $campaign->status === 'active' ? "https://schema.org/InStock" : "https://schema.org/OutOfStock",
                "priceValidUntil" => $campaign->end_date ?? date('Y-m-d', strtotime('+30 days'))
            ]
        ];
        
        if ($campaign->campaign_type === 'item') {
            $structuredData['category'] = 'Prize Draw';
            $structuredData['award'] = $campaign->item_name ?? 'Prize';
        }
        
        return '<script type="application/ld+json">' . json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>';
    }
    
    /**
     * Get campaign image URL
     */
    private static function getCampaignImageUrl($campaign, $baseUrl)
    {
        // Check if campaign has an image
        if (!empty($campaign->image)) {
            return $baseUrl . '/uploads/campaigns/' . $campaign->image;
        }
        
        // Default image based on campaign type
        if ($campaign->campaign_type === 'item') {
            return $baseUrl . '/assets/images/default-item-campaign.jpg';
        }
        
        return $baseUrl . '/assets/images/default-campaign.jpg';
    }
    
    /**
     * Generate keywords from campaign data
     */
    private static function generateKeywords($campaign)
    {
        $keywords = [];
        
        // Base keywords
        $keywords[] = 'raffle';
        $keywords[] = 'lottery';
        $keywords[] = 'win prizes';
        $keywords[] = 'Ghana raffle';
        $keywords[] = 'online raffle';
        
        // Campaign specific
        if (!empty($campaign->name)) {
            $keywords[] = self::cleanText($campaign->name);
        }
        
        if ($campaign->campaign_type === 'item' && !empty($campaign->item_name)) {
            $keywords[] = 'win ' . self::cleanText($campaign->item_name);
            $keywords[] = self::cleanText($campaign->item_name) . ' giveaway';
        } else {
            $keywords[] = 'cash prizes';
            $keywords[] = 'win money';
        }
        
        // Station/Programme keywords
        if (!empty($campaign->station_name)) {
            $keywords[] = self::cleanText($campaign->station_name);
        }
        
        return implode(', ', array_unique($keywords));
    }
    
    /**
     * Clean text for meta tags
     */
    private static function cleanText($text)
    {
        // Remove HTML tags
        $text = strip_tags($text);
        
        // Remove extra whitespace
        $text = preg_replace('/\s+/', ' ', $text);
        
        // Trim
        $text = trim($text);
        
        return $text;
    }
    
    /**
     * Truncate text to specified length
     */
    private static function truncateText($text, $length = 160, $suffix = '...')
    {
        if (strlen($text) <= $length) {
            return $text;
        }
        
        return substr($text, 0, $length - strlen($suffix)) . $suffix;
    }
}
