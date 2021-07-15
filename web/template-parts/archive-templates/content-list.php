<?php
/**
 *
 * The template used for displaying audio post formate
 *
 * @package   Listingo
 * @author    themographics
 * @link      https://themeforest.net/user/themographics/portfolio
 * @since 1.0
 */

global $paged,$query;
if (function_exists('fw_get_db_settings_option')) {
	$enable_author    = fw_get_db_settings_option('enable_author' , $default_value = null);
	$enable_comments    = fw_get_db_settings_option('enable_comments' , $default_value = null);
	$enable_categories   = fw_get_db_settings_option('enable_categories' , $default_value = null);
	$enable_sharing    = fw_get_db_settings_option('enable_sharing' , $default_value = null);
	
	$archive_show_posts    = fw_get_db_settings_option('archive_show_posts' , $default_value = null);
	$archive_order    = fw_get_db_settings_option('archive_order' , $default_value = null);
	$archive_orderby    = fw_get_db_settings_option('archive_orderby' , $default_value = null);
	$archive_meta_information    = fw_get_db_settings_option('archive_meta_information' , $default_value = null);
} else{
	$enable_author    = '';
	$enable_comments    = '';
	$enable_categories    = '';
	$enable_sharing    = '';
	$archive_show_posts    = get_option('posts_per_page');
	$archive_order    = 'DESC';
	$archive_orderby    = 'ID';
	$archive_meta_information    = 'enable';
}

?>
<div class="blog-list-view-template">
	<?php 
	get_option('posts_per_page');
	
	if (empty($paged)) {
		$paged = 1;
	}
	
	if (!isset($_GET["s"])) {
		$_GET["s"] = '';
	}
	$counter_no = 0;

	while (have_posts()) : the_post();
		global $post;
		$user_ID    = get_the_author_meta('ID');
		$width      = 231;
		$height     = 183;
		$thumbnail = listingo_prepare_thumbnail($post->ID , $width , $height);
		
		if (!function_exists('fw_get_db_post_option')) {
			$enable_author = 'enable';
			$enable_date = 'enable';
		} else {
			$enable_author = fw_get_db_post_option($post->ID, 'enable_author', true);
			$enable_date = fw_get_db_post_option($post->ID, 'enable_date', true);
		}
		
		$stickyClass = '';
		if (is_sticky()) {
			$stickyClass = 'sticky';
		}
		?>                         
		<article class="tg-post">
			<?php if( !empty( $thumbnail ) ){?>
				<figure class="tg-classimg">
					<?php listingo_get_post_thumbnail($thumbnail,$post->ID,'linked');?>
				</figure>
			<?php }?>
			<div class="tg-postcontent">
				<div class="tg-title"><h3><?php listingo_get_post_title($post->ID);?></h3></div>
				<div class="tg-description">
					<p><?php echo listingo_prepare_excerpt(350); ?></p>
				</div>
				<a class="tg-btn" href="<?php echo esc_url(get_permalink());?>"><?php esc_html_e('read more', 'listingo'); ?></a>
			</div>
			<?php if (is_sticky()) {?>
				<span class="sticky-wrap tg-themetag tg-tagclose"><i class="fa fa-bolt" aria-hidden="true"></i>&nbsp;<?php esc_html_e('Featured','listingo');?></span>
			<?php }?>
		</article>
		<?php
		endwhile;
		wp_reset_postdata();
		$qrystr = '';
		if ($wp_query->found_posts > $archive_show_posts) {
			?>
			<div class="theme-nav">
				<?php 
					if (function_exists('listingo_prepare_pagination')) {
						echo listingo_prepare_pagination($wp_query->found_posts , $archive_show_posts);
					}
				?>
			</div>
		<?php }?>
</div>