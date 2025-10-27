Descrição
Effortlessly conceal your WordPress site from detection! With over 99.99% of hacks targeting specific plugin and theme vulnerabilities, this plugin significantly boosts site security by making it invisible to hackers’ web scanners.

By removing all traces of WordPress, including themes and plugins, potential exploits are rendered harmless. This method ensures that your site is safe without affecting SEO; in fact, it can enhance certain SEO aspects when used strategically.

WP-Hide has launched the easiest way to completely hide your WordPress core files, login page, theme and plugins paths from being shown on front side. This is a huge improvement over Site Security, since no one will know whether you are running or not a WordPress. It also provides a simple way to clean up html by removing all WordPress fingerprints.

No file and directory change!
No file and directory will be changed anywhere. Everything is processed virtually. The plugin code uses URL rewrite techniques and WordPress filters to apply all internal functionality and features. Everything is done automatically without user intervention required at all.

Real hide of WordPress core files and plugins
The plugin not only allows you to change default URLs of you WordPress, but it also hides/blocks such defaults. Other similar plugins, just change the slugs, but the defaults are still accessible, obviously revealing WordPress as CMS.

You can change the default WordPress login URL from wp-admin and wp-login.php to something totally arbitrary. No one will ever know where to try to guess a login and hack into your site. It becomes totally invisible.

Full plugin documentation available at WordPress Hide and Security Enhancer Documentation

When testing with WordPress theme and plugins detector services/sites, any setting change may not reflect right away on their reports, since they use cache. So, you may want to check again later, or try a different inner URL. Homepage URL usage is not mandatory.

Being the best content management system, widely used, WordPress is susceptible to a large range of hacking attacks including brute-force, SQL injections, XSS, XSRF etc. Despite the fact the WordPress core is a very secure code maintained by a team of professional enthusiast, the additional plugins and themes make ita vulnerable spot for every website. In many cases, those are created by pseudo-developers who do not follow the best coding practices or simply do not own the experience to create a secure plugin.
Statistics reveal that every day new vulnerabilities are discovered, many affecting hundreds of thousands of WordPress websites.
Over 99,9% of hacked WordPress websites are target of automated malware scripts, which search for certain WordPress fingerprints. This plugin hides or replaces those traces, making the hacking bots attacks useless.

It works well with custom WordPress directory structures,e.g. custom plugins, themes, and upload folders.

Once configured, you need to clear server cache data and/or any cache plugins (e.g. W3 Cache), for a new html data to be created. If you use CDN this should be cache clear as well.

Main plugin functionality:

Customizes Admin URL
Blocks default admin URL
Blocks any direct folder access to completely hide the structure
Customize wp-login.php filename
2FA – Two-factor Authentication
2FA – Two-factor Authentication – Email Verification Code
2FA – Two-factor Authentication – Authenticator App
2FA – Two-factor Authentication – Recovery Codes
2FA – Two-factor Authentication – Shortcode for front-side user settings interface
2FA – Two-factor Authentication – My Account > Account Details – area for 2FA user settings interface
Google Captcha
Blocks default wp-login.php
Blocks default wp-signup.php
Blocks XML-RPC API
Creates New XML-RPC paths
Adjusts theme URL
Creates New child Theme URL
Changes theme style file name
Cleans any headers for theme style file
Customizes wp-include
Blocks default wp-include paths
Blocks default wp-content
Customizes plugins URL
Changes Individual plugin URL
Blocks default plugins paths
Creates New upload URL
Blocks default upload URL
Removes WordPress version
Blocks Meta Generator
Disables the emoji and required javascript code
Removes pingback tag
Removes wlwmanifest Meta
Removes rsd_link Meta
Removes wpemoji
Minifies Html, Css, JavaScript

Security Headers

and many more.

No other plugin functionality will be blocked or interfered in any way by WP-Hide

This plugin allows to change the default Admin URL from wp-login.php and wp-admin to something else. All original links turn the default theme to “404 Not Found” page, as if nothing exists there. Besides the huge security advantage, the WP-Hide plugin saves lots of server processing time by reducing php code and MySQL usage since brute-force attacks target the weakURL.

Important: Compared to all other similar plugins which mainly use redirects, this plugin turns a default theme to“404 error” page for all blocked URL functionalities, without revealing the link existence at all.

Since version 1.2, WP-Hide change individual plugin URLs and made them unrecognizable. For example,the change of the default WooCommerce plugin URL and its dependencies from domain.com/wp-content/plugins/woocommerce/ into domain.com/ecommerce/cdn/ or anything customized.

Plugin Sections
**Hide -> Scan

Exhaustive system security examination with analysis and improvements guidance and fixes
Hide -> Rewrite > Theme

New Theme Path – Changes default theme path
New Style File Path – Changes default style file name and path
Remove description header from Style file – Replaces any WordPress metadata information (like theme name, version etc.,) from style file
Child – New Theme Path – Changes default child theme path
Child – New Style File Path – Changes child theme style-sheet file path and name
Child – Remove description header from Style file – Replaces any WordPress metadata information (like theme name, version etc.,) from style file
Hide -> Rewrite > WP includes

New Include Path – Changes default wp-include path/URL
Block wp-include URL – Blocks default wp-include URL
Hide -> Rewrite > WP content

New Content Path – Change default wp-content path/URL
Block wp-content URL – Blocks the default content URL
Hide -> Rewrite > Plugins

New Plugin Path – Changes default wp-content/plugins path/URL
Block plugin URL – Blocks default wp-content/plugins URL
New path / URL for Every Active Plugin
Customize path and name for any active plugins
Hide -> Rewrite > Uploads

New Upload Path – Changes default media files path/URL
Block upload URL – Blocks default media files URL
Hide -> Rewrite > Comments

New wp-comments-post.php Path
Block wp-comments-post.php
Hide -> Rewrite > Author

New Author Path
Prevent Access to Author Archives
Block default path
Hide -> Rewrite > Search

New Search Path
Block default path
Hide -> Rewrite > XML-RPC

New XML-RPC Path – Changes default XML-RPC path / URL
Block default xmlrpc.php – Blocks default XML-RPC URL
Disable XML-RPC authentication – Filters whether XML-RPC methods require authentication
Remove pingback – Removes pingback link tag from theme
Hide -> Rewrite > JSON REST

Clean the REST API response
Disable JSON REST V1 service – Disables an API service for WordPress which is active by default
Disable JSON REST V2 service – Disables an API service for WordPress which is active by default
Block any JSON REST calls – Any call for JSON REST API service will be blocked
Disable output the REST API link tag into page header
Disable JSON REST WP RSD endpoint from XML-RPC responses
Disable Sends a Link header for the REST API
Hide -> Rewrite > Root Files

Block license.txt – Blocks access to license.txt root file
Block readme.html – Blocks access to readme.html root file
Block wp-activate.php – Blocks access to wp-activate.php file
Block wp-cron.php – Blocks outside access to wp-cron.php file
Block wp-signup.php – Blocks default wp-signup.php file
Block other wp-*.php files – Blocks other wp-.php files within WordPress Root
Hide -> Rewrite > URL Slash

URL’s add Slash – Add a slash to any links without it. This disguisesthe existence of a file, folder or a wrong URL, which will all be slashed.
Hide -> General / Html > Core

Disabling Directory Listing
Hide -> General / Html > Meta

Remove WordPress Generator Meta
Remove Other Generator Meta
Remove Shortlink Meta
Remove DNS Prefetch
Remove Resource Hints
Remove wlwmanifest Meta
Remove feed_links Meta
Disable output the REST API link tag into page header
Remove rsd_link Meta
Remove adjacent_posts_rel Meta
Remove profile link
Remove canonical link
Hide -> General / Block Detectors

Block Detectors
Hide -> General / Emulate CMS

Emulate CMS
Hide -> General / Html > Admin Bar

Remove WordPress Admin Bar for specified urser roles
Hide -> General / Feed

Remove feed|rdf|rss|rss2|atom links
Hide -> General / Robots.txt

Disable admin URL within Robots.txt
Hide -> General / Html > Emoji

Disable Emoji
Disable TinyMC Emoji
Hide -> General / Html > Styles

Remove Version
Remove ID from link tags
Hide -> General / Html > Scripts

Remove Version
Hide -> General / Html > Oembed

Remove Oembed
Hide -> General / Html > Headers

Remove Link Header
Remove X-Powered-By Header
Remove Server Header
Remove X-Pingback Header
Hide -> General / Html > HTML

Remove HTML Comments
Minify Html, CSS, JavaScript
Remove general classes from body tag
Remove ID from Menu items
Remove class from Menu items
Remove general classes from post
Remove general classes from images
Hide -> General / Html > User Interactions

Disable Mouse right click
Disable Text Selection
Disable Copy
Disable Cut
Disable Paste
Disable Print
Disable Print Screen
Disable Developer Tools
Disable View Source
Disable Drag / Drop
Hide -> Admin > wp-login.php

New wp-login.php – Maps a new wp-login.php instead of the default one
Block default wp-login.php – Blocks default wp-login.php file from being accessible
Customize the default login page Logo image
Hide -> Admin > Admin URL

New Admin URL – Creates a new admin URL instead of the default ”/wp-admin”. This also applies for admin-ajax.php calls
Disable customized Admin Url redirect to the Login page
Block default Admin Url – Blocks default admin URL and files from being accessible
Security -> 2FA

Enable 2FA
Enable the 2FA for specific roles
Enforce User to Configure 2FA
Primary option for Two-Factor
Disable 2FA when using Temporary Login
Security -> 2FA Email

Activate 2FA Email
Security -> 2FA Auth App

Activate Authenticator app (TOTP)
Security -> 2FA Recovery Codes

Activate 2FA Recovery Codes
Security -> Captcha

Google Captcha V2
Google Captcha V3
CloudFlare Turnstile ( PRO )
Settings -> CDN

CDN Url – Sets-up CDN if applied. Some providers replace site assets with custom URLs.
Security -> Headers

HTTP Response Headers are a powerful tool to Harden Your Website Security.
* Cross-Origin-Embedder-Policy (COEP)
* Cross-Origin-Opener-Policy (COOP)
* Cross-Origin-Resource-Policy (CORP)
* Referrer-Policy
* X-Content-Type-Options
* X-Download-Options
* X-Frame-Options (XFO)
* X-Permitted-Cross-Domain-Policies
* X-XSS-Protection

This free version works with Apache and IIS server types. For all server types, check with WP Hide PRO

This is a basic version that can hide everything for basic sites, example https://demo.wp-hide.com/. When using complex plugins and themes, the WP Hide PRO may be required. We provide free assistance to hide everything on your site, along with the commercial product.

Anything wrong with this plugin on your site? Just use the forum or get in touch with us at Contact and we’ll check it out.

A website example can be found at https://demo.wp-hide.com/ or our website WP Hide and Security Enhancer

Plugin homepage at WordPress Hide and Security Enhancer

This plugin is developed by Nsp-Code

Localization
Please help and translate this plugin to your language at https://translate.wordpress.org/projects/wp-plugins/wp-hide-security-enhancer

You are kindly asked to promote this plugin if it comes up to your expectations via an article on your site or any other place. If you liked this code/WP-Hide or if it helped with your project, why not leave a 5 star review on this board.