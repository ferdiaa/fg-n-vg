<?php
/**
 *
 * Functions
 *
 * @package   Listingo
 * @author    themographics
 * @link      https://themeforest.net/user/themographics/portfolio
 * @since 1.0
 */


/**
 * @get settings
 * @return {}
 */
if (!function_exists('listingo_profile_backend_settings')) {
	function  listingo_profile_backend_settings(){
		$list	= array(
			'category'	 	=> 'category',
			'avatar'		=> 'avatar',
			'banner'	 	=> 'banner',
			'basics'	 	=> 'basics',
			'language'	 	=> 'language',
			'awards'	 	=> 'awards',
			'experience'	=> 'experience',
			'qualification'	=> 'qualification',
			'amenity'		=> 'amenity',
			'insurance'	 	=> 'insurance',
			'videos'	 	=> 'videos',
			'gallery'	 	=> 'gallery',
			'brochures'	 	=> 'brochures',
			'business-hours'=> 'business-hours',
			'services'	 	=> 'services',
			'privacy'	 	=> 'privacy',
			'locations'	 	=> 'locations',
			'payments'	 	=> 'payments',
		);
		return $list;
	}
}