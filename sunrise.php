<?php

/**
 * Class MultilanguageSubsiteMapper
 */
class MultilanguageSubsiteMapper {

	/**
	 * @var array
	 */
	private $sites_language_urls = [];

	/**
	 * MultilanguageSubsiteMapper constructor
	 */
	public function __construct() {
		add_filter( 'pre_get_site_by_path', [ $this, 'allow_language_subdomains' ], 10, 3 );
	}

	/**
	 * Associate non-default language domains with site IDs
	 */
	private function setup_sites_and_languages() {
		global $wpdb;

		foreach ( get_sites() as $site ) {
			$prefix = DB_TABLE_PREFIX; // 'wp_'

			if ( ! is_main_site( $site->blog_id ) ) {
				$prefix = $prefix . $site->blog_id . '_';
			}

			$polylang_options = $wpdb->get_row( $wpdb->prepare( "SELECT option_value FROM {$prefix}options WHERE option_name = %s LIMIT 1", 'polylang' ) );

			if ( ! empty( $polylang_options->option_value ) ) {
				$polylang_options = unserialize( $polylang_options->option_value );

				if ( ! empty( $polylang_options['domains'] ) ) {
					unset( $polylang_options['domains'][ $polylang_options['default_lang'] ] );

					foreach ( $polylang_options['domains'] as $domain ) {
						$this->sites_language_urls[ str_replace( [ 'https://', 'http://' ], '', $domain ) ] = intval( $site->blog_id );
					}
				}
			}
		}
	}

	/**
	 * Associate language domains with multisite domain
	 *
	 * The multisite uses the default language domain.
	 * If the request is for a non-default language-specific domain,
	 * get the site object and overwrite the default domain with the language specific one
	 *
	 * @param $site
	 * @param string $domain
	 * @param string $path
	 *
	 * @return false|WP_Site
	 */
	public function allow_language_subdomains( $site, string $domain, string $path ) {
		if ( empty( $this->sites_language_urls ) ) {
			$this->setup_sites_and_languages();
		}

		if ( ( ! is_admin() || $path === '/wp/' ) && ! empty( $this->sites_language_urls[ $domain ] ) ) {
			$site         = WP_Site::get_instance( $this->sites_language_urls[ $domain ] );
			$site->domain = $domain;
		}

		return $site;
	}

}

new MultilanguageSubsiteMapper();
