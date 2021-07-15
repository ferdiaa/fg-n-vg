<?php
if (!defined('FW'))
    die('Forbidden');

/**
 * @var $atts
 */
global $paged;
$pg_page = get_query_var('page') ? get_query_var('page') : 1; //rewrite the global var
$pg_paged = get_query_var('paged') ? get_query_var('paged') : 1; //rewrite the global var
//paged works on single pages, page - works on homepage
$paged = max($pg_page, $pg_paged);

$posts_in['post__in'] = !empty($atts['posts']) ? $atts['posts'] : array();

$order 			= !empty($atts['order']) ? $atts['order'] : 'DESC';
$orderby 		= !empty($atts['orderby']) ? $atts['orderby'] : 'ID';
$show_posts 	= !empty($atts['show_posts']) ? $atts['show_posts'] : '-1';
$show_buttons 	= !empty($atts['cat_view']['view1']['show_buttons']) ? $atts['cat_view']['view1']['show_buttons'] : array();

//total posts Query 
$query_args = array(
    'posts_per_page' => -1,
    'post_type' => 'sp_categories',
    'order' => $order,
    'orderby' => $orderby,
    'post_status' => 'publish',
    'ignore_sticky_posts' => 1);

//By Posts 
if (!empty($posts_in)) {
    $query_args = array_merge($query_args, $posts_in);
}
$query = new WP_Query($query_args);
$count_post = $query->post_count;

//Main Query 
$query_args = array(
    'posts_per_page' => $show_posts,
    'post_type' => 'sp_categories',
    'paged' => $paged,
    'order' => $order,
    'orderby' => $orderby,
    'post_status' => 'publish',
    'ignore_sticky_posts' => 1);

//By Posts 
if (!empty($posts_in)) {
    $query_args = array_merge($query_args, $posts_in);
}
$query = new WP_Query($query_args);
?>

<div class="sc-home-category-3 tg-haslayout">
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-10 col-md-push-1 col-lg-8 col-lg-push-2">
			<?php if ( !empty( $atts['category_heading'] ) || !empty( $atts['category_description'] ) ) { ?>
				<div class="tg-sectionhead">
					<?php if (!empty($atts['category_heading'])) { ?>
                    <div class="tg-sectiontitle">
                        <h2><?php echo esc_attr($atts['category_heading']); ?></h2>
                    </div>
	                <?php } ?>
	                <?php if (!empty($atts['category_description'])) { ?>
	                    <div class="tg-description">
	                        <?php echo wp_kses_post(wpautop(do_shortcode($atts['category_description']))); ?>
	                    </div>
	                <?php } ?>
				</div>
			<?php } ?>
		</div>
		<?php if ($query->have_posts()) { ?>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<div class="tg-searchbycatagory">
			<?php
			while ($query->have_posts()) {
				$query->the_post();
				global $post;

				$category_icon = '';
				$category_color = '';
				if (function_exists('fw_get_db_post_option')) {
					$category_icon = fw_get_db_post_option($post->ID, 'category_icon', true);
					$category_color = fw_get_db_post_option($post->ID, 'category_color', true);
				}

				$icon_class = '';
				if ( !empty( $category_icon ) ) {
					$icon_class = !empty( $category_icon['icon-class'] ) ? $category_icon['icon-class'] : '';
				}			

				$icon_style = '#000000';
				if ( !empty( $category_color ) ) {
					$icon_style = 'style="color: '.$category_color.'"';
				}										
										
			?>				
				<div class="tg-catagory">
					<a href="<?php the_permalink(); ?>">
						<?php if ( !empty( $icon_class ) ) { ?>
							<span <?php echo ( $icon_style ); ?> class="tg-categoryicon"><i class="<?php echo esc_attr( $icon_class ); ?>"></i></span>								
						<?php } ?>
						<span><?php the_title(); ?></span>
					</a>
				</div>													
			<?php } wp_reset_postdata(); ?>
			<?php 
				if ( !empty( $show_buttons ) ) { 
				foreach ($show_buttons as $key => $value) {
					$btn_text = !empty( $value['button_text'] ) ? $value['button_text'] : '';
					$btn_link = !empty( $value['button_link'] ) ? $value['button_link'] : '#';
					if ( !empty( $btn_text ) ) {
				?>
				<div class="tg-catagory">
					<a href="<?php echo esc_attr( $btn_link ); ?>">
						<img src="<?php echo get_template_directory_uri(); ?>/images/img-07.png" alt="<?php echo esc_attr( $btn_text ); ?>">
						<span><?php echo esc_attr( $btn_text ); ?></span>
					</a>
				</div>
			<?php } } ?>
			<?php } ?>
			</div>
		</div>
		<?php } ?>
	</div>
</div>