<?php

if (!defined('FW')) {
    die('Forbidden');
}
$options = array(
	'page_settings' => array(
		'title'   => esc_html__( 'Title bar Settings', 'listingo' ),
		'type'    => 'box',
		'options' => array(
			'titlebar_type' => array(
				'type'         => 'multi-picker',
				'label'        => false,
				'desc'         => false,
				'picker'       => array(
					'gadget' => array(
						'label'   => esc_html__( 'Title bar Type', 'listingo' ),
						'desc'   => esc_html__( 'Select title bar type', 'listingo' ),
						'type'    => 'select',
						'value'    => 'default',
						'choices' => array(
							'default' => esc_html__('Default', 'listingo'),	
							'custom' => esc_html__('Custom Setttings', 'listingo'),	
							'rev_slider' => esc_html__('Revolution Slider', 'listingo'),
							'custom_shortcode' => esc_html__('Custom Shortcode', 'listingo'),
							'none' => esc_html__('None, hide it', 'listingo'),	
							
						)
					)
				),
				'choices'      => array(
					'default'  => array(
					),
					'custom'  => array(
						'titlebar_bg_image' => array (
							'type'        => 'upload' ,
							'label'       => esc_html__('Background?' , 'listingo') ,
							'desc'        => esc_html__('Upload background image' , 'listingo') ,
							'images_only' => true ,
						) ,
						'titlebar_bg'     => array (
							'type'  => 'rgba-color-picker' ,
							'value' => 'rgba(54, 59, 77, 0.40)' ,
							'label' => esc_html__('Background color' , 'listingo') ,
							'desc'  => esc_html__('RGBA color will be over image and solid color will override image' , 'listingo') ,
						) ,
					),
					'rev_slider'  => array(
						'rev_slider' => array(
							'type'  => 'select',
							'value' => '',
							'label' => esc_html__('Revolution Slider', 'listingo'),
							'desc'  => esc_html__('Please Select Revolution slider.', 'listingo'),
							'help' => esc_html__('Please install revolution slider first.', 'listingo'),
							'choices' => listingo_prepare_rev_slider(),
						),
					),
					'custom_shortcode'  => array(
						'custom_shortcode' => array(
							'type'  => 'textarea',
							'value' => '',
							'desc' => esc_html__('Custom Shortcode, You can add any shortcode here.', 'listingo'),
							'label'  => esc_html__('Custom Shortcode', 'listingo'),
						),
					),
				)
			),
		)
	),
);

