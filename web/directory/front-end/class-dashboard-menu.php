<?php
/**
 * @ Listingo Functions
 * @ return {}
 * @ Version 1.0.0
 */
if (!class_exists('Listingo_Profile_Menu')) {

    class Listingo_Profile_Menu {

        protected static $instance = null;

        public function __construct() {
            //Do something
        }

        /**
         * @Returns the *Singleton* instance of this class.
         * @return Singleton The *Singleton* instance.
         */
        public static function getInstance() {
            if (is_null(self::$instance)) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        /**
         * @profile Menu
         * @Returns Menu Top
         */
        public static function listingo_profile_menu_top() {
            global $current_user, $wp_roles, $userdata, $post;

            ob_start();
            $username = listingo_get_username($current_user->ID);
            $avatar = apply_filters(
                    'listingo_get_media_filter', listingo_get_user_avatar(array('width' => 100, 'height' => 100), $current_user->ID), array('width' => 100, 'height' => 100)
            );
            ?>
            <div class="tg-useradminbox sp-top-menu">
                <div class="tg-themedropdown tg-userdropdown">
                    <a href="javascript:;" id="tg-usermenu" class="tg-btndropdown">
                        <em><img src="<?php echo esc_url($avatar); ?>" alt="<?php esc_html_e('Profile Avatar', 'listingo'); ?>"></em>
                        <span><?php echo esc_attr($username); ?></span>
                    </a>
                    <div class="tg-dropdownmenu tg-usermenu" aria-labelledby="tg-usermenu">
                        <nav id="tg-dashboardnav" class="tg-dashboardnav">
                            <?php self::listingo_profile_menu('dashboard-menu-top'); ?>
                        </nav>
                    </div>
                </div>
            </div>
            <?php
            echo ob_get_clean();
        }

        /**
         * @profile Menu
         * @Returns Menu Top
         */
        public static function listingo_profile_menu_left() {
            global $current_user, $wp_roles, $userdata, $post;

            ob_start();
            ?>

            <?php self::listingo_do_process_userinfo(); ?>
            <div class="tg-widgetdashboard">
                <nav id="tg-dashboardnav" class="tg-dashboardnav">
                    <?php self::listingo_profile_menu('dashboard-menu-left'); ?>
                </nav>
            </div>

            <?php
            echo ob_get_clean();
        }

        /** 	
         * @Profile Menu
         * @Returns Dashoboard Menu
         */
        public static function listingo_profile_menu($menu_type = "dashboard-menu-left") {
            global $current_user, $wp_roles, $userdata, $post;
			$reference 		 = (isset($_GET['ref']) && $_GET['ref'] <> '') ? $_GET['ref'] : '';
			$mode 			 = (isset($_GET['mode']) && $_GET['mode'] <> '') ? $_GET['mode'] : '';
			$user_identity 	 = $current_user->ID;

			$url_identity = $user_identity;
			if (isset($_GET['identity']) && !empty($_GET['identity'])) {
				$url_identity = $_GET['identity'];
			}
			
			$menu_list 	= apply_filters('listingo_get_dashboard_menu','default');
            ob_start();
            ?>
            <ul class="<?php echo esc_attr($menu_type); ?>">
                <?php 
					if ($url_identity == $user_identity) {
						if( !empty( $menu_list ) ){
							foreach($menu_list as $key => $value){
								get_template_part('directory/front-end/dashboard-menu-templates/profile-menu', $key);
							}
						}
					} 
                ?>
            </ul>
            <?php
            echo ob_get_clean();
        }

        /**
         * @Generate Menu Link
         * @Returns 
         */
        public static function listingo_profile_menu_link($profile_page = '', $slug = '', $user_identity = '', $return = false, $mode = '', $id = '') {
            if ( empty( $profile_page ) ) {
                $permalink = home_url('/') . '?author=' . $user_identity;
            } else {
                $query_arg['ref'] = urlencode($slug);

                //mode
                if (!empty($mode)) {
                    $query_arg['mode'] = urlencode($mode);
                }
				
                //id for edit record
                if (!empty($id)) {
                    $query_arg['id'] = urlencode($id);
                }

                $query_arg['identity'] = urlencode($user_identity);

                $permalink = add_query_arg(
                        $query_arg, esc_url(get_permalink($profile_page)
                        )
                );
            }
			
            if ($return) {
                return esc_url($permalink);
            } else {
                echo esc_url($permalink);
            }
        }

        /**
         * @Generate Profile Avatar Image Link
         * @Returns HTML
         */
        public static function listingo_get_avatar() {
            global $current_user, $wp_roles, $userdata, $post;

            $user_identity = $current_user->ID;
            $dir_profile_page = '';
            if (function_exists('fw_get_db_settings_option')) {
                $dir_profile_page = fw_get_db_settings_option('dir_profile_page', $default_value = null);
            }

            $profile_page = isset($dir_profile_page[0]) ? $dir_profile_page[0] : '';

            $avatar = apply_filters(
                    'listingo_get_media_filter', listingo_get_user_avatar(array('width' => 100, 'height' => 100), $user_identity), array('width' => 100, 'height' => 100)
            );
            ?>
            <figure class="sp-user-profile-img">
                <img src="<?php echo esc_url($avatar); ?>" alt="<?php esc_html_e('Profile Avatar', 'listingo'); ?>">
                <a class="tg-btnedite sp-profile-edit" href="<?php Listingo_Profile_Menu::listingo_profile_menu_link($profile_page, 'settings', $user_identity); ?>">
                    <i class="lnr lnr-pencil"></i>
                </a>
            </figure>
            <?php
        }

        /**
         * @Generate Profile Banner Image Link
         * @Returns HTML
         */
        public static function listingo_get_banner() {
            global $current_user, $wp_roles, $userdata, $post;

            $user_identity = $current_user->ID;

            $user_identity = $user_identity;
            if (isset($_GET['identity']) && !empty($_GET['identity'])) {
                $user_identity = $_GET['identity'];
            }

            $avatar = apply_filters(
                    'listingo_get_media_filter', listingo_get_user_banner(array('width' => 270, 'height' => 120), $user_identity), array('width' => 270, 'height' => 120)//size width,height
            );
            ?>
            <figure class="tg-profilebannerimg sp-profile-banner-img">
                <img src="<?php echo esc_url($avatar); ?>" alt="<?php esc_html_e('Profile Banner', 'listingo'); ?>">
                <?php if(apply_filters('listingo_do_check_user_type', $user_identity) === true ) {?>
               	 	<a target="_blank" class="sp-view-profile" href="<?php echo esc_url(get_author_posts_url($user_identity));?>"><span class="lnr lnr-eye"></span></a>
                <?php }?>
            </figure>
            <?php
        }

        /**
         * @Generate Profile Information
         * @Returns HTML
         */
        public static function listingo_get_user_info() {
            global $current_user;


            $user_identity = $current_user->ID;

            $user_identity = $user_identity;
            if (isset($_GET['identity']) && !empty($_GET['identity'])) {
                $user_identity = $_GET['identity'];
            }
            $get_username = listingo_get_username($user_identity);

            $tag_line = get_user_meta($user_identity, 'tag_line', true);
            ?>
            <div class="tg-admininfo">
                <?php if (!empty($get_username)) { ?>
                    <h3><?php echo esc_attr($get_username); ?></h3>
                <?php } ?>
                <?php if (apply_filters('listingo_do_check_user_type', $user_identity) === true) { ?>
                    <?php if (!empty($tag_line)) { ?>
                        <h4><?php echo esc_attr($tag_line); ?></h4>
                    <?php } ?>
                    <?php do_action('sp_get_rating_and_votes', $user_identity, 'echo'); ?>
                <?php } ?>
            </div>
            <?php if(apply_filters('listingo_do_check_user_type', $user_identity) === true ) {?>
               	 	<a target="_blank" class="sp-view-profile-btn tg-btn" href="<?php echo esc_url(get_author_posts_url($user_identity));?>"><?php esc_html_e('View Profile', 'listingo'); ?></a>
                <?php }?>
            <?php
        }

        /**
         * @get user info
         * @return 
         */
        public static function listingo_do_process_userinfo() {
            global $current_user, $wp_roles, $userdata, $post;
            $reference = (isset($_GET['ref']) && $_GET['ref'] <> '') ? $_GET['ref'] : $reference = '';
            $user_identity = $current_user->ID;
            ?>
            <div class="tg-widgetprofile">
                <?php self::listingo_get_banner(); ?>
                <div class="tg-widgetcontent">
                    <?php self::listingo_get_avatar(); ?>
                    <?php self::listingo_get_user_info(); ?>
                </div>
            </div>
            <?php
        }

    }

    new Listingo_Profile_Menu();
}
