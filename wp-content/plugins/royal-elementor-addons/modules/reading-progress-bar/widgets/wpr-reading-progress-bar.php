<?php

namespace WprAddons\Modules\ReadingProgressBar\Widgets;

// Elementor classes
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Responsive\Responsive;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Image_Size;
use Elementor\Core\Schemes\Color;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Repeater;
use Elementor\Core\Schemes\Typography;
use Elementor\Widget_Base;
use Elementor\Icons;
use Elementor\Utils;
use WprAddons\Classes\Utilities;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Wpr_Reading_Progress_Bar extends Widget_Base {
    public function get_name() {
        return 'wpr-reading-progress-bar';
    }

    public function get_title() {
        return esc_html__( 'Reading Progress Bar', 'wpr-addons' );
    }

    public function get_icon() {
        return 'wpr-icon eicon-skill-bar';
    }

    public function get_categories() {
        return [ 'wpr-widgets'];
    }

    public function get_keywords() {
        return [ 'royal', 'reading progress bar', 'skills bar', 'percentage bar', 'scroll' ];
    }

    public function get_custom_help_url() {
        if ( empty(get_option('wpr_wl_plugin_links')) )
        // return 'https://royal-elementor-addons.com/contact/?ref=rea-plugin-panel-progress-bar-help-btn';
            return 'https://wordpress.org/support/plugin/royal-elementor-addons/';
    }
    
    public function register_controls() {
        
		$this->start_controls_section(
			'wpr_reading_progress_bar',
			[
                'tab' => Controls_Manager::TAB_CONTENT,
				'label' => __( 'Reading Progress Bar - Royal Addons', 'wpr-addons' ),
			]
        );

		$this->add_control(
			'wpr_particles_apply_changes',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw' => '<div class="elementor-update-preview editor-wpr-preview-update"><span>Update changes to Preview</span><button class="elementor-button elementor-button-success" onclick="elementor.reloadPreview();">Apply</button>',
				'separator' => 'after'
			]
		);

		$this->add_control(
			'wpr_progress_bar_position',
			[
				'label' => __( 'Position', 'wpr-addons' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'top',
				'render_type' => 'template',
				'options' => [
					'top' => __( 'Top', 'wpr-addons' ),
					'bottom' => __( 'Bottom', 'wpr-addons' ),
				],
				'selectors_dictionary' => [
					'top' => 'top: 0px; bottom: auto;',
					'bottom' => 'bottom: 0px; top: auto;',
				],
				'selectors' => [
					'{{WRAPPER}} .wpr-reading-progress-bar-container' => '{{VALUE}}',
				]
			]
		);

		$this->add_control(
			'wpr_height',
			[
				'label' => __( 'Height', 'wpr-addons' ),
				'type' => Controls_Manager::SLIDER,
                'render_type' => 'template',
				'range' => [
					'px' => [
						'min'  => 1,
						'max'  => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors' => [
					'.wpr-reading-progress-bar-container' => 'height: {{SIZE}}{{UNIT}} !important',
					'.wpr-reading-progress-bar-container .wpr-reading-progress-bar' => 'height: {{SIZE}}{{UNIT}} !important',
				],
			]
		);

		$this->add_control(
			'background_type',
			[
				'label' => __( 'Background Type', 'wpr-addons' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'transparent',
				'render_type' => 'template',
				'options' => [
					'transparent' => __( 'Transparent', 'wpr-addons' ),
					'colored' => __( 'Colored', 'wpr-addons' ),
				]
			]
		);

		$this->add_control(
			'wpr_background_color',
			[
				'label' => __( 'Background Color', 'wpr-addons' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#C5C5C6',
				'selectors' => [
					'.wpr-reading-progress-bar-container' => 'background-color: {{VALUE}};'
				],
				'condition' => [
					'background_type' => 'colored'
				]
			]
		);

		$this->add_control(
			'wpr_fill_color',
			[
				'label' => __( 'Fill Color', 'wpr-addons' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#6A63DA',
				'selectors' => [
					'.wpr-reading-progress-bar-container .wpr-reading-progress-bar' => 'background-color: {{VALUE}};'
				]
			]
		);

        $this->end_controls_section();
        
	}

	public function render() {
    	$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'wpr-rpb-attrs', [
			'class' => 'wpr-reading-progress-bar-container',
			'data-background-type' => $settings['background_type'],
		] );

        echo '<div '. $this->get_render_attribute_string('wpr-rpb-attrs') .'><div class="wpr-reading-progress-bar"></div></div>';
	}
}