<?php

if (!defined('FW'))
    die('Forbidden');



$options = array(
    'heading' => array(
        'label' => esc_html__('Heading', 'listingo'),
        'desc' => esc_html__('Add news section heading. leave it empty to hide.', 'listingo'),
        'type' => 'text',
    ),
    'description' => array(
        'type' => 'wp-editor',
        'label' => esc_html__('Description', 'listingo'),
        'desc' => esc_html__('Add section description. leave it empty to hide.', 'listingo'),
        'tinymce' => true,
        'media_buttons' => false,
        'teeny' => true,
        'wpautop' => false,
        'editor_css' => '',
        'reinit' => true,
        'size' => 'small', // small | large
        'editor_type' => 'tinymce',
        'editor_height' => 200
    ),
    'get_mehtod' => array(
        'type' => 'multi-picker',
        'label' => false,
        'desc' => false,
        'value' => array('gadget' => 'normal'),
        'picker' => array(
            'gadget' => array(
                'type' => 'select',
                'value' => 'by_cats',
                'desc' => esc_html__('Select question by category or item', 'listingo'),
                'label' => esc_html__('Questions By', 'listingo'),
                'choices' => array(
                    'by_cats' => esc_html__('By Categories', 'listingo'),
                    'by_posts' => esc_html__('By item', 'listingo'),
                ),
            )
        ),
        'choices' => array(
            'by_cats' => array(
                'categories' => array(
                    'type' => 'multi-select',
                    'label' => esc_html__('Select category', 'listingo'),
                    'population' => 'posts',
                    'source' => 'sp_categories',
                    'prepopulate' => 500,
                    'desc' => esc_html__('Show question by category selection. Leave it empty to get from all categories.', 'listingo'),
                ),
                'order' => array(
                    'type' => 'select',
                    'value' => 'DESC',
                    'desc' => esc_html__('Post Order', 'listingo'),
                    'label' => esc_html__('Posts By', 'listingo'),
                    'choices' => array(
                        'ASC' => esc_html__('ASC', 'listingo'),
                        'DESC' => esc_html__('DESC', 'listingo'),
                    ),
                ),
                'orderby' => array(
                    'type' => 'select',
                    'value' => 'ID',
                    'desc' => esc_html__('Post Order', 'listingo'),
                    'label' => esc_html__('Posts By', 'listingo'),
                    'choices' => array(
                        'ID' => esc_html__('Order by post id', 'listingo'),
                        'author' => esc_html__('Order by author', 'listingo'),
                        'title' => esc_html__('Order by title', 'listingo'),
                        'name' => esc_html__('Order by post name', 'listingo'),
                        'date' => esc_html__('Order by date', 'listingo'),
                        'rand' => esc_html__('Random order', 'listingo'),
                    ),
                ),
                'show_posts' => array(
                    'type' => 'slider',
                    'value' => 9,
                    'properties' => array(
                        'min' => 1,
                        'max' => 100,
                        'sep' => 1,
                    ),
                    'label' => esc_html__('Show no of posts', 'listingo'),
                ),
            ),
            'by_posts' => array(
                'posts' => array(
                    'type' => 'multi-select',
                    'label' => esc_html__('Select questions', 'listingo'),
                    'population' => 'posts',
                    'source' => 'sp_questions',
                    'prepopulate' => 500,
                    'desc' => esc_html__('Show questions item selection.', 'listingo'),
                ),
                'show_posts' => array(
                    'type' => 'slider',
                    'value' => 9,
                    'properties' => array(
                        'min' => 1,
                        'max' => 100,
                        'sep' => 1,
                    ),
                    'label' => esc_html__('Show no of posts', 'listingo'),
                ),
            )
        ),
        'show_borders' => true,
    ),
    'show_pagination' => array(
        'type' => 'select',
        'value' => 'no',
        'label' => esc_html__('Show Pagination', 'listingo'),
        'desc' => esc_html__('', 'listingo'),
        'choices' => array(
            'yes' => esc_html__('Yes', 'listingo'),
            'no' => esc_html__('No', 'listingo'),
        ),
        'no-validate' => false,
    ),
);
