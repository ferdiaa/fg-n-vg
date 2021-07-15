<?php
/**
 *
 * The template part for displaying the dashboard menu
 *
 * @package   Listingo
 * @author    Themographics
 * @link      http://themographics.com/
 * @since 1.0
 */

global $current_user, $wp_roles, $userdata, $post;

$reference 		 = (isset($_GET['ref']) && $_GET['ref'] <> '') ? $_GET['ref'] : '';
$mode 			 = (isset($_GET['mode']) && $_GET['mode'] <> '') ? $_GET['mode'] : '';
$user_identity 	 = $current_user->ID;
$bk_settings	 = listingo_get_booking_settings();

$url_identity = $user_identity;
if (isset($_GET['identity']) && !empty($_GET['identity'])) {
	$url_identity = $_GET['identity'];
}

$dir_profile_page = '';
$insight_page = '';
if (function_exists('fw_get_db_settings_option')) {
	$dir_profile_page = fw_get_db_settings_option('dir_profile_page', $default_value = null);
	$insight_page = fw_get_db_settings_option('insight_page', $default_value = null);
}

$profile_page = isset($dir_profile_page[0]) ? $dir_profile_page[0] : '';
$provider_category = listingo_get_provider_category($user_identity);

if( ( isset( $bk_settings['type'] ) && $bk_settings['type'] === 'woo' && $bk_settings['hide_wallet'] === 'no'  ) 
	&& apply_filters('listingo_is_setting_enabled', $user_identity, 'subscription_appointments') === true 
	&& apply_filters('listingo_do_check_user_type', $user_identity) === true
	&& apply_filters('listingo_is_feature_allowed', $provider_category, 'appointments') === true
){?>
	<li class="tg-privatemessages tg-hasdropdown <?php echo ( $reference === 'statement' ? 'tg-active tg-openmenu' : ''); ?>">
		<a id="tg-btntoggle" class="tg-btntoggle" href="javascript:">
			<i class="lnr lnr-sort-amount-asc"></i>
			<span><?php esc_html_e('Earnings/Withdrawals', 'listingo'); ?></span>
			<?php do_action('listingo_get_tooltip','menu','menu_earnings');?>
		</a>
		<ul class="tg-emailmenu">
			<li class="<?php echo ( $mode === 'earnings' ? 'tg-active' : ''); ?>">
				<a href="<?php Listingo_Profile_Menu::listingo_profile_menu_link($profile_page, 'statement', $user_identity, '', 'earnings'); ?>">
					<span><?php esc_html_e('Earnings', 'listingo'); ?></span>
				</a>
			</li>
			<li class="<?php echo ( $mode === 'withdrawals' ? 'tg-active' : ''); ?>">
				<a href="<?php Listingo_Profile_Menu::listingo_profile_menu_link($profile_page, 'statement', $user_identity, '', 'withdrawals'); ?>">
					<span><?php esc_html_e('Withdrawals', 'listingo'); ?></span>
				</a>
			</li>
		</ul>
	</li>
<?php }?>

<?php 
if (apply_filters('listingo_is_setting_enabled', $user_identity, 'subscription_appointments') === true 
	&& apply_filters('listingo_do_check_user_type', $user_identity) === true
	&& apply_filters('listingo_is_feature_allowed', $provider_category, 'appointments') === true
	&& isset( $bk_settings['type'] ) && $bk_settings['type'] === 'adaptive'
		 ) { ?>
	<li class="<?php echo ( $reference === 'withdrawal' ? 'tg-active' : ''); ?>">
		<a href="<?php Listingo_Profile_Menu::listingo_profile_menu_link($profile_page, 'withdrawal', $user_identity); ?>">
			<i class="lnr lnr-sort-amount-asc"></i>
			<span><?php esc_html_e('Earning Settings', 'listingo'); ?></span>
			<?php do_action('listingo_get_tooltip','menu','menu_earnings');?>
		</a>
	</li>
<?php }