<?php
/**
 *
 * Author Hits View Template.
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

/* ===============Get Total Profile Views================== */
$profile_view = apply_filters('sp_get_profile_views', $author_profile->ID, 'set_profile_view');

/* =============Get the Category ID From User Object============= */
$post_id = '';
$category_name = '';
if (!empty($author_profile->category)) {
    $post_id = $author_profile->category;
    $category_name = get_the_title($post_id);
}
if (!empty($category_name) || !empty($profile_view)) {
    ?>
    <div class="tg-companynameandviews">
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8 pull-left">
                    <?php if (!empty($category_name)) { ?>
                        <h2><?php echo esc_attr($category_name); ?></h2>
                    <?php } ?>
                    <?php if (!empty($profile_view)) { ?>
                        <span class="tg-totalsviews">
                            <i class="fa fa-eye"></i>
                            <i><?php echo intval($profile_view); ?></i>
                        </span>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <?php
}
