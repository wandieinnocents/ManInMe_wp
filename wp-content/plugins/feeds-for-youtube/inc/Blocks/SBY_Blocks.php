<?php

namespace SmashBalloon\YouTubeFeed\Blocks;

use SmashBalloon\YouTubeFeed\Services\AssetsService;
use Smashballoon\Customizer\Feed_Builder;

/**
 * Instagram Feed block with live preview.
 *
 * @since 1.7.1
 */
class SBY_Blocks {

	protected $feed_builder;

	public function __construct( Feed_Builder $feed_builder ) {
		$this->feed_builder = $feed_builder;
	}

	/**
	 * Indicates if current integration is allowed to load.
	 *
	 * @since 1.8
	 *
	 * @return bool
	 */
	public function allow_load() {
		return function_exists( 'register_block_type' );
	}

	/**
	 * Loads an integration.
	 *
	 * @since 1.7.1
	 */
	public function load() {
		$this->hooks();
	}

	/**
	 * Integration hooks.
	 *
	 * @since 1.7.1
	 */
	protected function hooks() {
		add_action( 'init', array( $this, 'register_block' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) );
	}

	/**
	 * Register Instagram Feed Gutenberg block on the backend.
	 *
	 * @since 1.7.1
	 */
	public function register_block() {

		wp_register_style(
			'sby-blocks-styles',
			trailingslashit( SBY_PLUGIN_URL ) . 'css/sby-blocks.css',
			array( 'wp-edit-blocks' ),
			SBYVER
		);

		$attributes = array(
			'shortcodeSettings' => array(
				'type' => 'string',
			),
			'noNewChanges' => array(
				'type' => 'boolean',
			),
			'executed' => array(
				'type' => 'boolean',
			)
		);

		register_block_type(
			'sby/sby-feed-block',
			array(
				'attributes'      => $attributes,
				'render_callback' => array( $this, 'get_feed_html' ),
			)
		);
	}

	/**
	 * Load Instagram Feed Gutenberg block scripts.
	 *
	 * @since 1.7.1
	 */
	public function enqueue_block_editor_assets() {
		do_action('sby_enqueue_scripts', true);

		wp_enqueue_style( 'sby-blocks-styles' );
		wp_enqueue_script(
			'sby-feed-block',
			trailingslashit( SBY_PLUGIN_URL ) . 'js/sby-blocks.js',
			array( 'wp-blocks', 'wp-i18n', 'wp-element' ),
			SBYVER,
			true
		);

		$shortcode_settings = '';

		$i18n = array(
			'addSettings'         => esc_html__( 'Add Settings', SBY_TEXT_DOMAIN ),
			'shortcodeSettings'   => esc_html__( 'Shortcode Settings', SBY_TEXT_DOMAIN ),
			'example'             => esc_html__( 'Example', SBY_TEXT_DOMAIN ),
			'preview'             => esc_html__( 'Apply Changes', SBY_TEXT_DOMAIN ),

		);

		if ( ! empty( $_GET['sby_wizard'] ) ) {
			$shortcode_settings = 'feed="' . (int) $_GET['sby_wizard'] . '"';
		}

		wp_localize_script(
			'sby-feed-block',
			'sby_block_editor',
			array(
				'wpnonce'  => wp_create_nonce( 'sby-blocks' ),
				'canShowFeed' => true,
				'shortcodeSettings'    => $shortcode_settings,
				'i18n'     => $i18n,
			)
		);
	}

	/**
	 * Get form HTML to display in a Instagram Feed Gutenberg block.
	 *
	 * @param array $attr Attributes passed by Instagram Feed Gutenberg block.
	 *
	 * @since 1.7.1
	 *
	 * @return string
	 */
	public function get_feed_html( $attr ) {

		$return = '';

		$shortcode_settings = isset( $attr['shortcodeSettings'] ) ? $attr['shortcodeSettings'] : '';

		$statuses = get_option( 'sby_statuses', array() );

		if ( empty( $statuses['support_legacy_shortcode'] ) ) {
			if ( empty( $shortcode_settings ) || strpos( $shortcode_settings, 'feed=' ) === false ) {
				$feeds = $this->feed_builder->get_feed_list();
				if ( ! empty( $feeds[0]['id'] ) ) {
					$shortcode_settings = 'feed="' . (int) $feeds[0]['id'] . '"';
				}
			}
		}

		$shortcode_settings = str_replace(array( '[youtube-feed', ']' ), '', $shortcode_settings );

		$return .= do_shortcode( '[youtube-feed '.$shortcode_settings.']' );

		return $return;

	}

	/**
	 * Checking if is Gutenberg REST API call.
	 *
	 * @since 1.7.1
	 *
	 * @return bool True if is Gutenberg REST API call.
	 */
	public static function is_gb_editor() {

		// TODO: Find a better way to check if is GB editor API call.
		return defined( 'REST_REQUEST' ) && REST_REQUEST && ! empty( $_REQUEST['context'] ) && 'edit' === $_REQUEST['context']; // phpcs:ignore
	}

}
