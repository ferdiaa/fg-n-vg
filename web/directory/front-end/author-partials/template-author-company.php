<?php
/**
 *
 * Author Company Template.
 *
 * @package   Listingo
 * @author    themographics
 * @link      https://themeforest.net/user/themographics/portfolio
 * @since 1.0
 */
/**
 * Get User Queried Object Data
 */
$author_profile = $wp_query->get_queried_object();

/**
 * Get the Professional Statement
 */
$professional_statements = '';
$description = '';

if (!empty($author_profile->professional_statements)) {
    $professional_statements = $author_profile->professional_statements;
}

if (!empty($author_profile->description)) {
    $description = $author_profile->description;
}
?>
<?php if (!empty($professional_statements)) { ?>
    <div class="tg-companyfeaturebox tg-introduction">
        <div class="tg-companyfeaturetitle">
            <h3><?php esc_html_e('Introduction', 'listingo'); ?></h3>
        </div>
        <?php if( !empty( $description ) ){?>
			<div class="tg-description">                
				<?php echo wp_kses_post(wpautop(do_shortcode( $description ))); ?>                
			</div>
        <?php }?>
        <?php if( !empty( $professional_statements ) ){?>
			<div class="tg-description">
				<?php echo wp_kses_post(wpautop(do_shortcode($professional_statements ))); ?>
			</div>
        <?php }?>
    </div>
    <?php
    $script = "
    jQuery(document).ready(function(){
    var _readmore = jQuery('.tg-introduction .tg-description');
	_readmore.readmore({
		speed: 500,
		collapsedHeight: 140,
		moreLink: '<a class=\"tg-btntext\" href=\"#\">more...</a>',
		lessLink: '<a class=\"tg-btntext\" href=\"#\">less...</a>',
	});
    });";
    wp_add_inline_script('listingo_callbacks', $script, 'after');
    ?>
<?php } ?>
