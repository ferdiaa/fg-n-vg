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

$social_links = apply_filters('listingo_get_social_media_icons_list',array());

?>
<div class="tg-contactinfobox">
	<ul class="tg-contactinfo">
		<?php if (!empty($author_profile->address)) { ?>
			<li>
				<i class="lnr lnr-location"></i>
				<address><?php echo esc_attr($author_profile->address); ?></address>
			</li>
		<?php } ?>
		<?php do_action('listingo_get_user_meta','phone',$author_profile);?>
        <?php do_action('listingo_get_user_meta','email',$author_profile);?>
		<?php if (!empty($author_profile->fax)) { ?>
			<li>
				<i class="lnr lnr-printer"></i>
				<span><?php echo esc_attr($author_profile->fax); ?></span>
			</li>
		<?php } ?>
		<?php if (!empty($author_profile->user_url)) { ?>
			<li>
				<i class="lnr lnr-screen"></i>
				<span><a href="<?php echo esc_url($author_profile->user_url); ?>" target="_blank"><?php echo esc_attr($author_profile->user_url); ?></a></span>
			</li>
		<?php } ?>
	</ul>
	<ul class="tg-socialicons">
		<?php 
		if( !empty( $social_links ) ){
			foreach( $social_links as $key => $social ){
				$item 		= get_user_meta($author_profile->ID,$key,true);
				$icon		= !empty( $social['icon'] ) ? $social['icon'] : '';
				$classes	= !empty( $social['classses'] ) ? $social['classses'] : '';
				$title		= !empty( $social['title'] ) ? $social['title'] : '';
				$color		= !empty( $social['color'] ) ? $social['color'] : '#484848';
				if(!empty($item)) {?>
					<li class="<?php echo esc_attr($classes); ?>"><a href="<?php echo esc_attr($item); ?>"><i style="background:<?php echo esc_attr( $color );?>" class="<?php echo esc_attr($icon); ?>"></i></a></li>
				<?php } ?>
		<?php }} ?>
	</ul>
	<?php if (!empty($author_profile->address)) { ?>
		<a class="tg-btn tg-btn-lg" href="//maps.google.com/maps?saddr=&amp;daddr=<?php echo esc_attr($author_profile->address); ?>" target="_blank"><?php esc_html_e('Get Directions', 'listingo'); ?></a>
	<?php } ?>
</div>
