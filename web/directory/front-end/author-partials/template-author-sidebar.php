<?php
/**
 *
 * Author Sidebar Template.
 *
 * @package   Listingo
 * @author    themographics
 * @link      https://themeforest.net/user/themographics/portfolio
 * @since 1.0
 */
global $wp_query, $current_user;
/**
 * Get User Queried Object Data
 */
$author_profile = $wp_query->get_queried_object();

$category_type = $author_profile->category;
$country 		= !empty($author_profile->country) ? $author_profile->country : '';
$city 			= !empty($author_profile->city) ? $author_profile->city : '';
$longitude 		= $author_profile->longitude;
$latitude 		= $author_profile->latitude;
$address 				= $author_profile->address;
$schedule_time_format 	= isset($author_profile->business_hours_format) ? $author_profile->business_hours_format : '12hour';
$provider_category 		= listingo_get_provider_category($author_profile->ID);
$db_privacy 			= listingo_get_privacy_settings($author_profile->ID);

$business_hours 	= !empty($author_profile->business_hours) ? $author_profile->business_hours : array();
$business_days 		= listingo_prepare_business_hours_settings();
$profile_brochure 	= !empty($author_profile->profile_brochure) ? $author_profile->profile_brochure : array();



if (!empty($dir_map_marker['url'])) {
    $dir_map_marker = $dir_map_marker['url'];
} else {
	if (function_exists('fw_get_db_settings_option')) {
    	$dir_map_marker = fw_get_db_settings_option('dir_map_marker');
	}
    $dir_map_marker = !empty($dir_map_marker['url']) ? $dir_map_marker['url'] : '';
}

if (empty($dir_map_marker)) {
    $dir_map_marker = get_template_directory_uri() . '/images/map-marker.png';
}
$sp_usersdata = array();
$sp_userinfo['userinfo']	=  array();

$sp_usersdata['marker'] 	= $dir_map_marker;
$sp_usersdata['longitude'] 	= $longitude;
$sp_usersdata['latitude'] 	= $latitude;
$sp_usersdata['address'] 	= $address;
$sp_userinfo['userinfo'][]  = $sp_usersdata;

$profile_section = apply_filters('listingo_get_profile_sections',$author_profile->ID,'sidebar',$author_profile->ID);
?>
<div class="col-xs-12 col-sm-5 col-md-4 col-lg-4 pull-right">
    <aside id="tg-sidebar" class="tg-sidebar">
        <div class="tg-widget tg-widgetlocationandcontactinfo">
            <div class="tg-mapbox">
                <?php if (!empty($latitude) && !empty($longitude)) { ?>
                    <div id="tg-locationmap" class="tg-locationmap"></div>
                    <?php
						$script = "jQuery(document).ready(function () {listingo_init_detail_map_script(" . json_encode($sp_userinfo) . ");});";
						wp_add_inline_script('listingo_gmaps', $script, 'after');
                    ?>
                <?php } ?>
            </div>
			<!--Author contatc info-->
			<?php get_template_part('directory/front-end/author-partials/template-author-sidebar', 'contactinfo');?>
		</div>
       	
       	<?php
			foreach( $profile_section as $key => $value  ){
				get_template_part('directory/front-end/author-partials/template-author-sidebar', $key);
			}
		?>
       <?php if (is_active_sidebar('user-page-sidebar')) {?>
		  <div class="tg-advertisement">
			<?php dynamic_sidebar('user-page-sidebar'); ?>
		  </div>
	   <?php }?>
    </aside>
</div>