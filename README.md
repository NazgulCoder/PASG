# PASG
PASG - PHP Advanced Sitemap Generator is a powerful PHP script to automatically generate comprehensive sitemaps for your website!

## Features ✨
- **Automatic URL Discovery**: Recursively crawls your website to find all pages
- **Customizable Scanning**: Choose from 3 scan levels (simple, deep, aggressive)
- **Flexible Configuration**:
  - Exclude specific file extensions and URL patterns
  - Set custom priorities and change frequencies
  - Option to include/exclude external domains
- **Dynamic Priority Calculation**:
  - Static priority mode (all pages 0.5)
  - Dynamic priority based on directory depth
- **Last Modified Tracking**: Fetches last modified dates from headers
- **Efficient Crawling**:
  - Avoids duplicate URLs
  - Progress tracking with real-time output

## Configuration ⚙️
Set these variables at the top of the script:
```php
$base_url = "https://yoursite.com/";
$include_external_domains = false;
$static_priority = true;
$change_freq = 'always';
$scan_level = 3; // 1-3
$exclude_extensions = ['.html', '.jpg', '.png'];
$exclude_patterns = ['something'];
```

## Usage 🚀
1. Set your base URL and configuration options.
2. Upload to your web server.
3. Run via:
   - CLI: `php PASG.php`
   - Web browser: visit the script URL.

Generated sitemap saved as `sitemap.xml`.

## Requirements
- PHP 5.6+
- `allow_url_fopen` enabled
- Write permissions to create `sitemap.xml`

Note: The script includes automatic execution time extension (15 minutes) to handle large websites.


## LICENSE

[![MIT License](https://img.shields.io/badge/License-MIT-green.svg)](https://choosealicense.com/licenses/mit/)

MIT License

Copyright (c) [2025] [NazgulCoder]

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
