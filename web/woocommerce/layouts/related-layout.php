<?php
/**
 * The template for displaying product content within loops.
 *
 * Override this template by copying it to yourtheme/woocommerce/content-product.php
 *
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.4.0
 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

global $product, $woocommerce_loop;

// Store loop count we're currently on
if (empty($woocommerce_loop['loop']))
    $woocommerce_loop['loop'] = 0;

// Store column count for displaying the grid
if (empty($woocommerce_loop['columns']))
    $woocommerce_loop['columns'] = apply_filters('loop_shop_columns', 4);

// Ensure visibility
if (!$product || !$product->is_visible())
    return;

// Increase loop count
$woocommerce_loop['loop'] ++;

// Extra post classes
$classes = array();
if (0 == ( $woocommerce_loop['loop'] - 1 ) % $woocommerce_loop['columns'] || 1 == $woocommerce_loop['columns'])
    $classes[] = 'first';
if (0 == $woocommerce_loop['loop'] % $woocommerce_loop['columns'])
    $classes[] = 'last';

$bootstrapColumn = round(12 / $woocommerce_loop['columns']);

?>
<div class="item">
	<div class="tg-product">
        <figure>
            <?php 
				echo apply_filters('listingo_prepare_post_thumbnail', 
					get_the_post_thumbnail($post->ID, 'listingo_blog_grid'),
				array('width'=>280,'height'=>200)
			); ?>
            <?php wc_get_template('loop/sale-flash.php'); ?>
            <figcaption>
                <?php do_action('listingo_woocommerce_add_to_cart_button'); ?>
            </figcaption>
        </figure>
        <div class="tg-productinfo">
            <div class="tg-booknameandtitle">
                <h3><a href="<?php echo esc_url( get_the_permalink() ); ?>"><?php the_title(); ?></a></h3>
            </div>
        </div>
    </div>
</div>