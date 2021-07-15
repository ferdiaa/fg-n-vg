<?php
if (!defined('FW'))
    die('Forbidden');

global $paged;

$per_page = intval(6);
if (!empty($atts['show_posts'])) {
    $per_page = $atts['show_posts'];
}

$limit = (int) $per_page;

$pg_page = get_query_var('page') ? get_query_var('page') : 1; //rewrite the global var
$pg_paged = get_query_var('paged') ? get_query_var('paged') : 1; //rewrite the global var
//paged works on single pages, page - works on homepage
$paged = max($pg_page, $pg_paged);

$offset = ($paged - 1) * $limit;

$meta_query_args = array();

$query_args = array(
	'role__in' => array('professional', 'business'),
	'order' => 'DESC',
	'orderby' => 'ID',
);
//Verify user
$meta_query_args[] = array(
	'key' => 'verify_user',
	'value' => 'on',
	'compare' => '='
);
//active users filter
$meta_query_args[] = array(
	'key' => 'activation_status',
	'value' => 'active',
	'compare' => '='
);

if (!empty($meta_query_args)) {
	$query_relation = array('relation' => 'AND',);
	$meta_query_args = array_merge($query_relation, $meta_query_args);
	$query_args['meta_query'] = $meta_query_args;
}

$total_query = new WP_User_Query($query_args);
$total_users = $total_query->total_users;

$query_args['number'] = $limit;
$query_args['offset'] = $offset;
?>
<div class="sp-sc-categories tg-haslayout">
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
			<?php
			$user_query = new WP_User_Query($query_args);
			if (!empty($user_query->results)) {
				foreach ($user_query->results as $user) {
					$username = listingo_get_username($user->ID);
					$category = get_user_meta($user->ID, 'category', true);
					$avatar = apply_filters(
							'listingo_get_media_filter', listingo_get_user_avatar(array('width' => 92, 'height' => 92), $user->ID), array('width' => 92, 'height' => 92)
					);
					?>
					<div class="col-xs-6 col-sm-6 col-md-4 col-lg-4 tg-verticaltop">
						<div class="tg-serviceprovider tg-automotive">
							<?php do_action('listingo_result_avatar', $user->ID, 'banner'); ?>
							<div class="tg-serviceprovidercontent">
								<?php if (!empty($avatar)) { ?>
									<div class="tg-companylogo">
										<a href="<?php echo get_author_posts_url($user->ID); ?>">
											<img src="<?php echo esc_url($avatar); ?>" alt="<?php esc_html_e('Avatar', 'listingo'); ?>">
										</a>
									</div>
								<?php } ?>
								<div class="tg-companycontent">
									<?php listingo_result_tags($user->ID, 'echo'); ?>
									<div class="tg-title">
										<h3>
											<a href="<?php echo get_author_posts_url($user->ID); ?>"><?php echo esc_attr($username); ?></a>
										</h3>
									</div>
									<?php listingo_get_total_rating_votes($user->ID, 'echo'); ?>
								</div>
							</div>
						</div>
					</div>
					<?php
				}
			}
			?>
		</div>
	</div>
	<?php if (!empty($total_users) && !empty($limit) && $total_users > $limit && isset($atts['show_pagination']) && $atts['show_pagination'] == 'yes') { ?>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<?php listingo_prepare_pagination($total_users, $limit); ?>
		</div>
	<?php } ?>
</div>