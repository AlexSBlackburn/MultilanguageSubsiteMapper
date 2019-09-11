# MultilanguageSubsiteMapper
Associate Polylang language domains with WordPress multisite domains

## What does this script do?
Imagine you have a WordPress multisite with two sites using different domains - example-site.com & test-site.com.
Both of these sites is also multilanguage using the plugin Polylang. In the Polylang settings the different languages are set to work with different URLs.
The Dutch version of example-site.com uses example-site.nl and the Spanish version of test-site.com uses test-site.es. This won't work in WordPress because before Polylang runs, WordPress checks the request URL against the registered site domains, sees that example-site.nl doesn't exist and redirects to the sign-up page (or whatever is set in the wp-config.php NOBLOGREDIRECT define).
This script runs before the WordPress multisite functionality. It grabs all the non-default language domains from each site from Polylang and checks if one is being requested. If a request is made for one of the domains it loads the site where it found that domain in Polylang but switches out the domain property in the global site object for the requested one.

## Installation
1. Download this repo and place the sunrise.php file in the wp-content/ directory of your WordPress project.
2. Add `define( 'SUNRISE', true );` to your wp-config.php
3. sunrise.php will now be loaded on every request

## Bugs
I've yet to come across any bugs caused by this script, but if you use it and find any please feel free to open an issue or make a pull request.
