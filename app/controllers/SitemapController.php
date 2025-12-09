<?php

namespace App\Controllers;

use App\Core\Controller;

class SitemapController extends Controller
{
    private $campaignModel;
    
    public function __construct()
    {
        $this->campaignModel = $this->model('Campaign');
    }
    
    public function index()
    {
        header('Content-Type: application/xml; charset=utf-8');
        
        $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
        
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        // Homepage
        echo $this->generateUrl($baseUrl . '/public', '1.0', 'daily', date('Y-m-d'));
        
        // Static pages
        echo $this->generateUrl($baseUrl . '/public/howToPlay', '0.8', 'monthly', date('Y-m-d'));
        echo $this->generateUrl($baseUrl . '/public/winners', '0.9', 'daily', date('Y-m-d'));
        echo $this->generateUrl($baseUrl . '/public/buyTicket', '0.9', 'daily', date('Y-m-d'));
        
        // Active campaigns
        $campaigns = $this->campaignModel->getActive();
        foreach ($campaigns as $campaign) {
            $lastmod = $campaign->updated_at ?? $campaign->created_at ?? date('Y-m-d');
            echo $this->generateUrl(
                $baseUrl . '/public/campaign/' . $campaign->id,
                '0.9',
                'daily',
                date('Y-m-d', strtotime($lastmod))
            );
        }
        
        echo '</urlset>';
    }
    
    private function generateUrl($loc, $priority = '0.5', $changefreq = 'weekly', $lastmod = null)
    {
        $xml = "  <url>\n";
        $xml .= "    <loc>" . htmlspecialchars($loc) . "</loc>\n";
        if ($lastmod) {
            $xml .= "    <lastmod>" . $lastmod . "</lastmod>\n";
        }
        $xml .= "    <changefreq>" . $changefreq . "</changefreq>\n";
        $xml .= "    <priority>" . $priority . "</priority>\n";
        $xml .= "  </url>\n";
        return $xml;
    }
}
