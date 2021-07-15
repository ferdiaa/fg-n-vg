<?php
/**
 *
 * Search template, list view
 *
 * @package   Listingo
 * @author    Themographics
 * @link      http://themographics.com/
 * @since 1.0
 */
global $paged, $wp_query, $total_users, $query_args, $limit;

get_header();

if (!empty($_GET['category'])) {
    $sp_category = listingo_get_page_by_slug($_GET['category'], 'sp_categories', 'id');
} else {
    $sp_category = '';
}

if (function_exists('fw_get_db_settings_option')) {
    $dir_map_marker_default = fw_get_db_settings_option('dir_map_marker');
    $search_page_map = fw_get_db_settings_option('search_page_map');
	
	$zip_search = fw_get_db_settings_option('zip_search');
	$misc_search = fw_get_db_settings_option('misc_search');
	$dir_search_insurance = fw_get_db_settings_option('dir_search_insurance');
	$language_search = fw_get_db_settings_option('language_search');
	$country_cities = fw_get_db_settings_option('country_cities');
	$dir_radius = fw_get_db_settings_option('dir_radius');
	$dir_location = fw_get_db_settings_option('dir_location');
	$dir_keywords = fw_get_db_settings_option('dir_keywords');
} else {
    $dir_map_marker_default = '';
    $search_page_map = '';
	
	$dir_radius = '';
	$dir_location = '';
	$dir_keywords = '';
	$misc_search = '';
	$zip_search = '';
	$dir_search_insurance = '';
	$language_search = '';
	$country_cities = '';
}

//Search center point
$direction	= listingo_get_location_lat_long();

?>
<div class="tg-listing tg-listview tg-listingvtwo sp-search-left">
	<?php do_action('listingo_get_search_map_right'); ?>

	<div class="tg-serviceproviders">
		<form class="sp-form-search" action="" method="get">
			<div class="tg-themeform tg-formsearch">
				<fieldset>
					<?php if (!empty($dir_keywords) && $dir_keywords === 'enable') { ?>
						<div class="form-group">
							<?php do_action('listingo_get_search_keyword'); ?>
						</div>
					<?php }?>
					<?php if (!empty($dir_location) && $dir_location === 'enable') { ?>
						<div class="form-group">
							<div class="tg-inputwithicon">
								<?php do_action('listingo_get_search_geolocation'); ?>
							</div>
						</div>
					<?php }?>
					<div class="form-group"> 
						<?php do_action('listingo_get_search_category'); ?>
					</div>
					<div class="form-group">
						<button class="tg-btn" type="submit"><i class="lnr lnr-magnifier"></i></button>
					</div>
				</fieldset>
				<a id="tg-btnadvancefilters" class="tg-btnadvancefilters" href="javascript:;"> <span><?php esc_html_e('Advanced Filters', 'listingo'); ?></span> <i class="fa fa-angle-right"></i> </a>
			</div>
			<div class="tg-advancedfilters">
				<div class="tg-themeform tg-formrefinesearch">
					<div class="tg-themeform tg-formrefinesearch">
						<?php do_action('listingo_get_search_filtrs'); ?>
					</div>
				</div>
			</div>
			<div class="tg-filters">
				<span class="tg-totallistingfound"><?php echo intval($total_users); ?>&nbsp;<?php esc_html_e('Results Found', 'listingo'); ?></span>
				<div class="tg-sortfilters">
					<div class="tg-sortfilter tg-sortby"> 
						<?php do_action('listingo_get_sortby'); ?>
					</div>
					<div class="tg-sortfilter tg-arrange">
						<?php do_action('listingo_get_orderby'); ?>
					</div>
					<div class="tg-sortfilter tg-show">
						<?php do_action('listingo_get_showposts'); ?>
					</div>
				</div>
			</div>
			<div class="row">
				<div id="container" class="transitions-enabled infinite-scroll clearfix">
					<?php
						$user_query = get_users($query_args);
						if( empty( $_GET['sortby'] ) ){usort($user_query,'listingo_sort_users_by_featured_expiry');}
						$sp_usersdata	=  array();
						$sp_userslist	=  array();
						$sp_userslist['status'] = 'none';
						$sp_userslist['lat']  = floatval ( $direction['lat'] );
						$sp_userslist['long'] = floatval ( $direction['long'] );

						if (!empty($user_query)) {
							$sp_userslist['status'] = 'found';

							if (!empty($sp_category)) {
								$title = get_the_title($sp_category);
								$postdata = get_post($sp_category);
								$slug = $postdata->post_name;
							} else {
								$title = '';
								$slug = '';
							}

							foreach ($user_query as $user) {
								$username = listingo_get_username($user->ID);
								$useremail = $user->user_email;
								$userphone = $user->phone;
								if( !empty( $user->user_email ) ){
									$email = explode('@', $user->user_email);
								}

								$category = get_user_meta($user->ID, 'category', true);
								$map_marker = fw_get_db_post_option($category, 'dir_map_marker', true);
								$avatar = apply_filters(
										'listingo_get_media_filter', listingo_get_user_avatar(array('width' => 92, 'height' => 92), $user->ID), array('width' => 92, 'height' => 92)
								);

								$sp_usersdata['latitude'] = $user->latitude;
								$sp_usersdata['longitude'] = $user->longitude;
								$sp_usersdata['username'] = $username;

								$infoBox = '';
								$infoBox .= '<div class="tg-infoBox">';
								$infoBox .= '<div class="tg-serviceprovider">';
								$infoBox .= '<figure class="tg-featuredimg"><img src="' . esc_url($avatar) . '" alt="' . $username . '"></figure>';
								$infoBox .= '<div class="tg-companycontent">';
								$infoBox .= listingo_result_tags($user->ID, 'return');
								$infoBox .= '<div class="tg-title">';
								$infoBox .= '<h3><a href="' . get_author_posts_url($user->ID) . '">' . $username . '</a></h3>';
								$infoBox .= '</div>';
								$infoBox .= listingo_get_total_rating_votes($user->ID, 'return');
								$infoBox .= '</div>';
								$infoBox .= '</div>';
								$infoBox .= '</div>';

								if (isset($map_marker['url']) && !empty($map_marker['url'])) {
									$sp_usersdata['icon'] = $map_marker['url'];
								} else {
									if (!empty($dir_map_marker_default['url'])) {
										$sp_usersdata['icon'] = $dir_map_marker_default['url'];
									} else {
										$sp_usersdata['icon'] = get_template_directory_uri() . '/images/map-marker.png';
									}
								}

								$sp_usersdata['html']['content'] = $infoBox;
								$sp_userslist['users_list'][] = $sp_usersdata;
								?>
							<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
								<div class="tg-serviceprovider sp-vlist-item">
									<?php do_action('listingo_result_avatar', $user->ID); ?>
									<div class="tg-companycontent">
										<?php do_action('listingo_result_tags', $user->ID); ?>
										<div class="tg-title">
											<h3><a href="<?php echo esc_url(get_author_posts_url($user->ID)); ?>"><?php echo esc_attr($username); ?></a></h3>
										</div>
										<?php do_action('sp_get_rating_and_votes', $user->ID); ?>
										<ul class="tg-companycontactinfo">
											<?php do_action('listingo_get_user_meta','phone',$user);?>
                                            <?php do_action('listingo_get_user_meta','email',$user);?>
											<?php 
											if( !empty( $user->latitude ) && !empty( $user->longitude ) ){	
												$unit	= listingo_get_distance_scale();
												$unit 	= !empty( $unit ) && $unit === 'Mi' ? 'M' : 'K';
												
												if( !empty( $_GET['geo'] ) ) {
													$args = array(
														'timeout'     => 15,
														'headers' 	  => array('Accept-Encoding' => ''),
														'sslverify'   => false
													);

													$address	 = !empty($_GET['geo']) ?  $_GET['geo'] : '';
													$prepAddr	 = str_replace(' ','+',$address);

													$url	 = 'http://maps.google.com/maps/api/geocode/json?address='.$prepAddr.'&sensor=false';
													$response   = wp_remote_get( $url, $args );
													$geocode	= wp_remote_retrieve_body($response);
													$output	  = json_decode($geocode);

													if( isset( $output->results ) && !empty( $output->results ) ) {
														$Latitude	= $output->results[0]->geometry->location->lat;
														$Longitude  = $output->results[0]->geometry->location->lng;
														$distance	= listingo_GetDistanceBetweenPoints($Latitude,$Longitude,$user->latitude,$user->longitude);
													}
												}
												?>
												<?php if( !empty( $distance ) ) {?>
													<li class="dynamic-locations"><i class='lnr lnr-location'></i><span><?php esc_html_e('within','listingo');?>&nbsp;<?php echo esc_attr($distance);?></span></li>
												<?php } else{?>
													<li class="dynamic-location-<?php echo intval($user->ID);?>"></li>
													<?php  
															wp_add_inline_script( 'listingo_callbacks', 'if ( window.navigator.geolocation ) {
																window.navigator.geolocation.getCurrentPosition(
																	function(pos) {
																		jQuery.cookie("geo_location", pos.coords.latitude+"|"+pos.coords.longitude, { expires : 365 });
																		var with_in	= _get_distance(pos.coords.latitude, pos.coords.longitude, '.esc_js($user->latitude).','. esc_js($user->longitude).',"'.$unit.'");
																		jQuery(".dynamic-location-'.intval($user->ID).'").html("<i class=\'lnr lnr-location\'></i><span>"+scripts_vars.with_in+"&nbsp;"+_get_round(with_in, 2)+"&nbsp;"+scripts_vars.kilometer+"</i></span>");

																	}
																);
															}
														' );
														}
													?>
											<?php }?>
										</ul>
										<?php do_action('listingo_make_an_appointment_button',$user->ID);?>
									</div>
								</div>
							</div>
							<?php
						}
					} else{?>
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
							<?php Listingo_Prepare_Notification::listingo_info(esc_html__('Sorry!', 'listingo'), esc_html__('Nothing found.', 'listingo')); ?>
						</div>
					<?php }?>
					<?php if (!empty($total_users) && !empty($limit) && $total_users > $limit) { ?>
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
							<?php listingo_prepare_pagination($total_users, $limit); ?>
						</div>
					<?php } ?>
					<?php if (isset($search_page_map) && $search_page_map === 'enable') { 
							$script	= "jQuery(document).ready(function ($) {listingo_init_map_script(".json_encode($sp_userslist)."); });";
							wp_add_inline_script('listingo_gmaps', $script,'after');
					} ?> 
				</div>
			</div>
		</form>
	</div>

</div>
<?php get_footer(); ?>

