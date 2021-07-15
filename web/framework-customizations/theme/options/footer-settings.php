<?php

if (!defined('FW')) {
    die('Forbidden');
}
$options = array(
    'footer' => array(
        'title' => esc_html__('Footer Settings', 'listingo'),
        'type' => 'tab',
        'options' => array(
            'footer_settings' => array(
                'title' => esc_html__('Footer Settings', 'listingo'),
                'type' => 'box',
                'options' => array(
                    'feature_section' => array(
                        'type' => 'multi-picker',
                        'label' => false,
                        'desc' => '',
                        'picker' => array(
                            'gadget' => array(
                                'type' => 'switch',
                                'value' => 'disable',
                                'attr' => array(),
                                'label' => esc_html__('Add Features?', 'listingo'),
                                'desc' => esc_html__('Enable/Disable feature section.', 'listingo'),
                                'left-choice' => array(
                                    'value' => 'disable',
                                    'label' => esc_html__('Disable', 'listingo'),
                                ),
                                'right-choice' => array(
                                    'value' => 'enable',
                                    'label' => esc_html__('Enable', 'listingo'),
                                ),
                            )
                        ),
                        'choices' => array(
                            'enable' => array(
                                'features' => array(
                                    'label' => esc_html__('Add Feature.', 'listingo'),
                                    'type' => 'addable-popup',
                                    'value' => array(),
                                    'desc' => esc_html__('Add your feature.', 'listingo'),
                                    'popup-options' => array(
                                        'feature_title' => array(
                                            'label' => esc_html__('Feature Title', 'listingo'),
                                            'type' => 'text',
                                            'value' => 'Best Sevices Award',
                                            'desc' => esc_html__('Add feature title here.', 'listingo')
                                        ),
                                        'feature_desc' => array(
                                            'label' => esc_html__('Feature Description', 'listingo'),
                                            'type' => 'text',
                                            'value' => 'Earned best service award in 2016',
                                            'desc' => esc_html__('Add feature short description here.', 'listingo')
                                        ),
                                        'feature_icon' => array(
                                            'type' => 'icon-v2',
                                            'preview_size' => 'small',
                                            'modal_size' => 'medium',
                                            'attr' => array('class' => 'custom-class', 'data-foo' => 'bar'),
                                            'label' => esc_html__('Feature Icon', 'listingo'),
                                            'desc' => esc_html__('Choose your feature icon.', 'listingo'),
                                        ),
                                        'feature_color' => array(
                                            'type' => 'color-picker',
                                            'value' => '',
                                            'attr' => array('class' => 'custom-class', 'data-foo' => 'bar'),
                                            // palette colors array
                                            'palettes' => array('#ec407a', '#42a5f5', '#66bb6a'),
                                            'label' => esc_html__('Icon Background Color', 'listingo'),
                                            'desc' => esc_html__('This color will effect the icon background color and the title color.', 'listingo'),
                                        ),
                                    ),
                                    'template' => '{{- feature_title }}',
                                ),
                            ),
                            'default' => array(),
                        ),
                        'show_borders' => false,
                    ),
                    'enable_widget_area' => array(
                        'type' => 'switch',
                        'value' => 'disable',
                        'attr' => array(),
                        'label' => esc_html__('Widget Area', 'listingo'),
                        'desc' => esc_html__('Enable/Disable Widget Area', 'listingo'),
                        'left-choice' => array(
                            'value' => 'disable',
                            'label' => esc_html__('Disable', 'listingo'),
                        ),
                        'right-choice' => array(
                            'value' => 'enable',
                            'label' => esc_html__('Enable', 'listingo'),
                        ),
                    ),
                    'enable_footer_menu' => array(
                        'type' => 'switch',
                        'value' => 'disable',
                        'attr' => array(),
                        'label' => esc_html__('Footer Menu', 'listingo'),
                        'desc' => esc_html__('Enable/Disable Footer Menu', 'listingo'),
                        'left-choice' => array(
                            'value' => 'disable',
                            'label' => esc_html__('Disable', 'listingo'),
                        ),
                        'right-choice' => array(
                            'value' => 'enable',
                            'label' => esc_html__('Enable', 'listingo'),
                        ),
                    ),
                    'footer_copyright' => array(
                        'type' => 'text',
                        'value' => 'Copyright &copy; 2017 The Listingo, All Right Reserved themographics',
                        'label' => esc_html__('Footer Copyright', 'listingo'),
                    ),
                )
            ),
        )
    )
);
