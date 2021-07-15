<?php
if (!defined('FW'))
    die('Forbidden');

$options = array(
    'heading' => array(
        'label' => esc_html__('Heading', 'listingo'),
        'desc' => esc_html__('Search the forum for previous questions or ask a new question.', 'listingo'),
        'type' => 'text',
    ),
	'show_recent' => array(
        'type' => 'select',
        'value' => 'no',
        'label' => esc_html__('Show recent question', 'listingo'),
        'desc' => esc_html__('', 'listingo'),
        'choices' => array(
            'yes' => esc_html__('Yes', 'listingo'),
            'no' => esc_html__('No', 'listingo'),
        ),
        'no-validate' => false,
    ),
);
