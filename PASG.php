<?php
// Set maximum execution time to 900 seconds (15 minutes)
set_time_limit(900);

// Define the base URL of your website
$base_url = "https://yoursite.com/";

// Additional useful variables
$include_external_domains = false; // Whether to include external domains in the sitemap
$static_priority = true; // If true, all pages will have a priority of 0.5, otherwise priority will decrease with subdirectory level
$priority_base = 1.0; // Base priority for URLs
$priority_decrease = 0.1; // Decrease in priority for each subdirectory level
$change_freq = 'always'; // Change frequency for URLs - always, hourly, daily, weekly, monthly, yearly, never
$fetch_lastmod = true; // If true, use fetch_lastmod function, otherwise use today's date (false is faster)
$exclude_extensions = ['.html', '.jpg', '.png']; // File extensions to exclude from the sitemap
$exclude_patterns = ['something']; // URL patterns to exclude from the sitemap - useful for pages that shouldn't be indexed
$scan_level = 3; // Scanning level: 1 for simple scan, 2 for deeper recursive scan, 3 for aggressive scan


// Function to check if a URL should be excluded
function should_exclude($url, $exclude_extensions, $exclude_patterns)
{
    foreach ($exclude_extensions as $ext) {
        if (strpos($url, $ext) !== false) {
            return true;
        }
    }
    foreach ($exclude_patterns as $pattern) {
        if (strpos($url, $pattern) !== false) {
            return true;
        }
    }
    return false;
}

// Function to fetch the last modification date of a URL
function fetch_lastmod($url)
{
    $headers = @get_headers($url, 1);
    if ($headers && isset($headers['Last-Modified'])) {
        return date('Y-m-d', strtotime($headers['Last-Modified']));
    }
    return date('Y-m-d');
}

// Function to fetch URLs from a given page
function fetch_urls($url, $base_url, $include_external_domains, $exclude_extensions, $exclude_patterns, $scan_level, &$visited_urls = [])
{
    if (in_array($url, $visited_urls)) {
        return [];
    }
    $visited_urls[] = $url;

    echo "Fetching URLs from: $url<br>\n";
    flush();

    $html = file_get_contents($url);
    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    $hrefs = $xpath->evaluate("/html/body//a");
    $urls = [];
    $base_host = parse_url($base_url, PHP_URL_HOST);

    for ($i = 0; $i < $hrefs->length; $i++) {
        $href = $hrefs->item($i);
        $url = $href->getAttribute('href');

        if (strpos($url, 'http') === 0) {
            $url_host = parse_url($url, PHP_URL_HOST);
            if (($include_external_domains || $url_host === $base_host) && !should_exclude($url, $exclude_extensions, $exclude_patterns)) {
                $urls[] = $url;
                if ($scan_level > 1) {
                    $urls = array_merge($urls, fetch_urls($url, $base_url, $include_external_domains, $exclude_extensions, $exclude_patterns, $scan_level - 1, $visited_urls));
                }
            }
        } elseif (strpos($url, '/') === 0 && !should_exclude($url, $exclude_extensions, $exclude_patterns)) {
            $full_url = $base_url . ltrim($url, '/');
            $urls[] = $full_url;
            if ($scan_level > 1) {
                $urls = array_merge($urls, fetch_urls($full_url, $base_url, $include_external_domains, $exclude_extensions, $exclude_patterns, $scan_level - 1, $visited_urls));
            }
        }
    }

    // For scan_level 3, continue scanning until no new URLs are found
    if ($scan_level == 3) {
        $new_urls = array_diff($urls, $visited_urls);
        while (!empty($new_urls)) {
            foreach ($new_urls as $new_url) {
                $urls = array_merge($urls, fetch_urls($new_url, $base_url, $include_external_domains, $exclude_extensions, $exclude_patterns, $scan_level, $visited_urls));
            }
            $new_urls = array_diff($urls, $visited_urls);
        }
    }

    return array_unique($urls);
}

// Fetch URLs from the base URL
$urls = fetch_urls($base_url, $base_url, $include_external_domains, $exclude_extensions, $exclude_patterns, $scan_level);

// Create a new XML document
$xml = new DOMDocument('1.0', 'UTF-8');
$xml->formatOutput = true;

// Create the root <urlset> element
$urlset = $xml->createElement('urlset');
$urlset->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
$xml->appendChild($urlset);

// Track added URLs to avoid duplicates
$added_urls = [];

// Loop through the URLs and create <url> elements
foreach ($urls as $url) {
    $full_url = strpos($url, 'http') === 0 ? $url : $base_url . ltrim($url, '/');
    if (!in_array($full_url, $added_urls)) {
        $urlElement = $xml->createElement('url');

        $loc = $xml->createElement('loc', $full_url);
        $urlElement->appendChild($loc);

        $lastmod_date = $fetch_lastmod ? fetch_lastmod($full_url) : date('Y-m-d');
        $lastmod = $xml->createElement('lastmod', $lastmod_date);
        $urlElement->appendChild($lastmod);

        $changefreq = $xml->createElement('changefreq', $change_freq);
        $urlElement->appendChild($changefreq);

        if ($static_priority) {
            $priority = $xml->createElement('priority', '0.5');
        } else {
            $path = parse_url($full_url, PHP_URL_PATH);
            $subdir_count = substr_count(rtrim($path, '/'), '/');
            $priority = $priority_base - $subdir_count * $priority_decrease;
            $priority = number_format(max(0, min($priority, 1)), 1); // Ensure priority is between 0 and 1 and formatted as 1.0
            $priority = $xml->createElement('priority', $priority);
        }
        $urlElement->appendChild($priority);

        $urlset->appendChild($urlElement);
        $added_urls[] = $full_url;

        echo "Added URL: $full_url<br>\n";
        flush();
    }
}

// Save the XML to a file
$xml->save('sitemap.xml');

echo "Sitemap generated successfully!<br>\n";
flush();
?>
