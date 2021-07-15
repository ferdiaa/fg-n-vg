<?php
if (!defined('FW'))
    die('Forbidden');
/**
 * @var $atts
 */

$today = time();
$pg_page = get_query_var('page') ? get_query_var('page') : 1; //rewrite the global var
$pg_paged = get_query_var('paged') ? get_query_var('paged') : 1; //rewrite the global var
//paged works on single pages, page - works on homepage
$paged = max($pg_page, $pg_paged);

if (isset($atts['get_mehtod']['gadget']) && $atts['get_mehtod']['gadget'] === 'by_users' && !empty($atts['get_mehtod']['by_users']['users'])) {
    $users_list = !empty($atts['get_mehtod']['by_users']['users']) ? $atts['get_mehtod']['by_users']['users'] : array();
} else {
    $cat_sepration  = array();
    $cat_sepration  = $atts['get_mehtod']['by_cats']['categories'];
}


$show_users	 = !empty( $atts['show_posts'] ) ? $atts['show_posts'] : 9;
$order		 = !empty( $atts['order'] ) ? $atts['order'] : 'DESC';
$show_pagination		 = !empty( $atts['show_pagination'] ) ? $atts['show_pagination'] : '';

$limit  = (int) $show_users;
$offset = ($paged - 1) * $limit;

$uniq_flag = fw_unique_increment();

$query_args	= array(
					'role__in' => array('professional', 'business'),
					'order'    => $order,
				 );
if( !empty( $cat_sepration ) ) {
	foreach( $cat_sepration as $key => $value ){
		$meta_category[] = array(
						'key'     => 'category',
						'value'   => $value,
						'compare' => '='
					);
	}
	
}

$meta_query_args	= array();
//Verify user
$meta_query_args[] = array(
    'key' 		=> 'verify_user',
    'value' 	=> 'on',
    'compare'   => '='
);
//active users filter
$meta_query_args[] = array(
    'key' 		=> 'activation_status',
    'value' 	=> 'active',
    'compare' 	=> '='
);

if (!empty($meta_query_args)) {
    $query_relation = array('relation' => 'AND',);
    $meta_query_args = array_merge($query_relation, $meta_query_args);
    $query_args['meta_query'] = $meta_query_args;
}

//By Categories
if( !empty( $meta_category ) ) {
	$query_relations = array( 'relation' => 'OR',);
	$meta_query_args	= array_merge( $query_relations, $meta_category );
	$query_args['meta_query'][] = $meta_query_args;
}

//Featured
$expiry_args = array(
					'key'     => 'subscription_featured_expiry',
					'value'   => $today,
					'type'    => 'numeric',
					'compare' => '>'
				);
$query_args['meta_query'][] = $expiry_args;

$query_args['meta_key']	   = 'subscription_featured_expiry';
$query_args['orderby']	   = 'meta_value';
$query_args['number'] 		= $limit;
$query_args['offset'] 		= $offset;

//By users
if( !empty( $users_list ) ) {
	$query_args['include']	= $users_list;
}

?>
<div class="sp-featured-providers tg-haslayout">
	<div class="row">
		<?php if (!empty($atts['provider_heading']) || !empty($atts['provider_description'])) { ?>
			<div class="col-xs-12 col-sm-12 col-md-10 col-md-push-1 col-lg-8 col-lg-push-2">
				<div class="tg-sectionhead">
					<?php if (!empty($atts['provider_heading'])) { ?>
						<div class="tg-sectiontitle">
							<h2><?php echo esc_attr($atts['provider_heading']); ?></h2>
						</div>
					<?php } ?>
					<?php if (!empty($atts['provider_description'])) { ?>
						<div class="tg-description">
							<?php echo wp_kses_post(wpautop(do_shortcode($atts['provider_description']))); ?>
						</div>
					<?php } ?>
				</div>
			</div>
		<?php } ?>
    	<div class="tg-latestserviceproviders">
			<div class="row">
				<div class="tg-serviceproviders">
				 <?php
					$user_query = new WP_User_Query($query_args);
					$total_users	= $user_query->total_users;

					if (!empty($user_query->results)) {

						foreach ($user_query->results as $user) {
							$username = listingo_get_username($user->ID);
							$useremail = $user->user_email;
							$userphone = $user->phone;
							$email = explode('@', $user->user_email);

							$category = get_user_meta($user->ID, 'category', true);
							$map_marker = fw_get_db_post_option($category, 'dir_map_marker', true);
							$avatar = apply_filters(
									'listingo_get_media_filter', 
									listingo_get_user_avatar(array('width' => 92, 'height' => 92), $user->ID), 
									array('width' => 92, 'height' => 92)
							);


							?>
							<div class="col-xs-6 col-sm-12 col-md-6 col-lg-4 tg-verticaltop">
								<div class="tg-serviceprovider">
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
				</div>
				<?php if (!empty($total_users) && !empty($limit) && $total_users > $limit && ( !empty( $atts['show_pagination'] ) && $atts['show_pagination'] === 'yes' )) { ?>
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<?php listingo_prepare_pagination($total_users, $limit); ?>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
