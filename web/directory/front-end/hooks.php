<?php
/**
 * @Check user role
 * @return 
 */
if (!function_exists('listingo_do_check_user_type')) {
    function listingo_do_check_user_type($user_identity) {
        if (isset($user_identity) && !empty($user_identity)) {
            $data = get_userdata($user_identity);

            if (isset($data->roles[0]) &&
                    !empty($data->roles[0]) &&
                    (( $data->roles[0] === 'business') ||
					 ( $data->roles[0] === 'administrator') ||
                    ($data->roles[0] === 'professional' ))) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }
    add_filter('listingo_do_check_user_type', 'listingo_do_check_user_type', 10, 1);
}

/**
 * @Check user role
 * @return 
 */
if (!function_exists('listingo_get_user_type')) {

    function listingo_get_user_type($user_identity) {
        if (!empty($user_identity)) {
            $data = get_userdata($user_identity);

            if (!empty($data->roles[0]) && $data->roles[0] === 'business') {
                return 'business';
            } else if (!empty($data->roles[0]) && $data->roles[0] === 'professional') {
                return 'professional';
            } else if (!empty($data->roles[0]) && $data->roles[0] === 'customer') {
				return 'customer';
			}else{
                return false;
            }
        }

        return false;
    }

    add_filter('listingo_get_user_type', 'listingo_get_user_type', 10, 1);
}

/**
 * Get Profile Image Urls
 */
if (!function_exists('listingo_get_profile_image_url')) {

    /**
     * Get thumbnail url based on attachment data
     *
     * @param $attach_data
     * @return string
     */
    function listingo_get_profile_image_url($attach_data, $type = 'avatar', $basename) {

        $upload_dir = wp_upload_dir();
        $image_path_data = explode('/', $attach_data['file']);
        $image_path_array = array_slice($image_path_data, 0, count($image_path_data) - 1);
        $image_path = implode('/', $image_path_array);

        $thumbnail_name = null;
        $json = array();
        $path = $upload_dir['baseurl'] . '/' . $image_path . '/';

        if ($type === 'avatar') {
            if (!empty($attach_data['sizes']['thumbnail']['file'])) {
                $json['thumbnail'] = $path . $attach_data['sizes']['thumbnail']['file'];
            } else {
                $json['thumbnail'] = $path . $basename;
            }

            if (!empty($attach_data['sizes']['large']['file'])) {
                $json['full'] = $path . $attach_data['sizes']['large']['file'];
            } else {
                $json['full'] = $path . $basename;
            }

            $json['banner'] = '';
        } elseif ($type === 'banner') {
            if (!empty($attach_data['sizes']['thumbnail']['file'])) {
                $json['thumbnail'] = $path . $attach_data['sizes']['thumbnail']['file'];
            } else {
                $json['thumbnail'] = $path . $basename;
            }

            if (!empty($attach_data['sizes']['large']['file'])) {
                $json['full'] = $path . $attach_data['sizes']['large']['file'];
            } else {
                $json['full'] = $path . $basename;
            }

            if (!empty($attach_data['sizes']['listingo_user_banner_profile']['file'])) {
                $json['banner'] = $path . $attach_data['sizes']['listingo_user_banner_profile']['file'];
            } else {
                $json['banner'] = $path . $basename;
            }
        } elseif ($type === 'award') {

            if (!empty($attach_data['sizes']['thumbnail']['file'])) {
                $json['thumbnail'] = $path . $attach_data['sizes']['thumbnail']['file'];
            } else {
                $json['thumbnail'] = $path . $basename;
            }

            if (!empty($attach_data['sizes']['large']['file'])) {
                $json['full'] = $path . $attach_data['sizes']['large']['file'];
            } else {
                $json['full'] = $path . $basename;
            }

            if (!empty($attach_data['sizes']['listingo_user_award_image']['file'])) {
                $json['banner'] = $path . $attach_data['sizes']['listingo_user_award_image']['file'];
            } else {
                $json['banner'] = $path . $basename;
            }
        } else {
            if (!empty($attach_data['sizes']['thumbnail']['file'])) {
                $json['thumbnail'] = $path . $attach_data['sizes']['thumbnail']['file'];
            } else {
                $json['thumbnail'] = $path . $basename;
            }

            if (!empty($attach_data['sizes']['large']['file'])) {
                $json['full'] = $path . $attach_data['sizes']['large']['file'];
            } else {
                $json['full'] = $path . $basename;
            }

            $json['banner'] = '';
        }

        return $json;
    }

}

/**
 * @Get User Avatar
 * @return {}
 */
if (!function_exists('listingo_get_user_avatar')) {

    function listingo_get_user_avatar($sizes = array(), $user_identity = '') {
        extract(shortcode_atts(array(
            "width" => '100',
            "height" => '100',
                        ), $sizes));
  
  		$db_privacy = listingo_get_privacy_settings($user_identity);

        if ($user_identity != '') {
            $thumb_id = get_user_meta($user_identity, 'profile_avatar', true);
   			$category_id = get_user_meta($user_identity, 'category', true);
   			
            if ( !empty($thumb_id['default_image']) && ( isset( $db_privacy['profile_photo'] ) && $db_privacy['profile_photo'] === 'on' ) ) {
                $thumb_url = wp_get_attachment_image_src($thumb_id['default_image'], array($width, $height), true);

                if ($thumb_url[1] == $width and $thumb_url[2] == $height) {
                    return $thumb_url[0];
                } else {
                    $thumb_url = wp_get_attachment_image_src($thumb_id['default_image'], "full", true);
                    return $thumb_url[0];
                }
            } else {
    
				if( !empty( $category_id ) ){
				 if (function_exists('fw_get_db_settings_option')) {
				  $default_avatar = fw_get_db_post_option($category_id, 'default_avatar', true);
				  if (empty($default_avatar['attachment_id'])) {
				   $default_avatar = fw_get_db_settings_option('default_avatar', $default_value = null);
				  }
				 }

				 if (!empty($default_avatar['attachment_id'])) {
				  $thumb_url = wp_get_attachment_image_src($default_avatar['attachment_id'], array($width, $height), true);

				  if ($thumb_url[1] == $width and $thumb_url[2] == $height) {
				   return $thumb_url[0];
				  } else {
				   $thumb_url = wp_get_attachment_image_src($default_avatar['attachment_id'], "full", true);
				   return $thumb_url[0];
				  }
				 } else {
				  return false;
				 }

				} else{
				 $user_info = get_userdata( $user_identity );

				 if( isset( $user_info->roles[0] ) && $user_info->roles[0] === 'customer' ){
				  $thumb_id = get_user_meta($user_identity, 'profile_avatar', true);

				  if ( !empty($thumb_id['default_image'])  ) {
				   $thumb_url = wp_get_attachment_image_src($thumb_id['default_image'], array($width, $height), true);

				   if ($thumb_url[1] == $width and $thumb_url[2] == $height) {
					return $thumb_url[0];
				   } else {
					$thumb_url = wp_get_attachment_image_src($thumb_id['default_image'], "full", true);
					return $thumb_url[0];
				   }
				  } else {
				   return false;
				  }
				 }


				 if (function_exists('fw_get_db_settings_option')) {
				  $default_avatar = fw_get_db_settings_option('default_avatar', $default_value = null);
				 }

				 if (!empty($default_avatar['attachment_id'])) {
				  $thumb_url = wp_get_attachment_image_src($default_avatar['attachment_id'], array($width, $height), true);

				  if ($thumb_url[1] == $width and $thumb_url[2] == $height) {
				   return $thumb_url[0];
				  } else {
				   $thumb_url = wp_get_attachment_image_src($default_avatar['attachment_id'], "full", true);
				   return $thumb_url[0];
				  }
				 } else {
				  return false;
				 }
				}   
            }
        }
        return false;
    }

}

/**
 * @Get User Avatar
 * @return {}
 */
if (!function_exists('listingo_get_user_banner')) {

    function listingo_get_user_banner($sizes = array(), $user_identity = '', $default = array()) {
        extract(shortcode_atts(array(
            "width" => '192',
            "height" => '380',
                        ), $sizes));
		
		$db_privacy = listingo_get_privacy_settings($user_identity);
		
        if ($user_identity != '') {
            $thumb_id = get_user_meta($user_identity, 'profile_banner_photos', true);
            $category_id = get_user_meta($user_identity, 'category', true);
			
            if ( !empty($thumb_id['default_image']) && ( isset( $db_privacy['profile_banner'] ) && $db_privacy['profile_banner'] === 'on' ) ) {
                $thumb_url = wp_get_attachment_image_src($thumb_id['default_image'], array($width, $height), true);

                if ($thumb_url[1] == $width and $thumb_url[2] == $height) {
                    return $thumb_url[0];
                } else {
                    $thumb_url = wp_get_attachment_image_src($thumb_id['default_image'], "full", true);
                    return $thumb_url[0];
                }
            } else {
                if (!empty($category_id)) {
                    if (function_exists('fw_get_db_settings_option')) {
                        $default_banner = fw_get_db_post_option($category_id, 'default_banner', true);
                        if (empty($default_banner['attachment_id'])) {
                            $default_banner = fw_get_db_settings_option('default_banner', $default_value = null);
                        }
                    }

                    if (!empty($default_banner['attachment_id'])) {
                        $thumb_url = wp_get_attachment_image_src($default_banner['attachment_id'], array($width, $height), true);

                        if ($thumb_url[1] == $width and $thumb_url[2] == $height) {
                            return $thumb_url[0];
                        } else {
                            $thumb_url = wp_get_attachment_image_src($default_banner['attachment_id'], "full", true);
                            return $thumb_url[0];
                        }
                    } else {
                        return false;
                    }
                } else {

                    if (function_exists('fw_get_db_settings_option')) {
                        $default_banner = fw_get_db_settings_option('default_banner', $default_value = null);
                    }
                    if (!empty($default_banner['attachment_id'])) {
                        $thumb_url = wp_get_attachment_image_src($default_banner['attachment_id'], array($width, $height), true);

                        if ($thumb_url[1] == $width and $thumb_url[2] == $height) {
                            return $thumb_url[0];
                        } else {
                            $thumb_url = wp_get_attachment_image_src($default_banner['attachment_id'], "full", true);
                            return $thumb_url[0];
                        }
                    } else {
                        return false;
                    }
                }
            }
        }
        return false;
    }

}


/**
 * @refine author base if username and base matched eg : anything/anything
 * @return {}
 */
if (!function_exists('listingo_get_username')) {

    function listingo_get_username($user_id = '') {
        if (empty($user_id)) {
            return esc_html__('unnamed', 'listingo');
        }

        $userdata = get_userdata($user_id);

        /* ============ Get The User Role ============= */

        $user_role = '';
        if (!empty($userdata->roles[0])) {
            $user_role = $userdata->roles[0];
        }


        if (!empty($user_role) && $user_role === 'business') {
            /* ==========Get the user Meta Info========= */
            $user_meta = get_user_meta($user_id);

            /* ======Get user meta company name===== */
            $user_company = '';
            if (!empty($user_meta['company_name'])) {
                $user_company = $user_meta['company_name'][0];
                return $user_company;
            }
        } else {
            if (!empty($userdata->first_name) && !empty($userdata->last_name)) {
                return $userdata->first_name . ' ' . $userdata->last_name;
            } else if (!empty($userdata->first_name) && empty($userdata->last_name)) {
                return $userdata->first_name;
            } else if (empty($userdata->first_name) && !empty($userdata->last_name)) {
                return $userdata->last_name;
            } else {
                return esc_html__('No Name', 'listingo');
            }
        }
    }

}

/**
 * Get the terms
 *
 * @param user_id, taxonomy
 * @return html
 */
if (!function_exists('listingo_get_term_options')) {

    function listingo_get_term_options($current = '', $taxonomyName = '',$return_type='echo') {
        //This gets top layer terms only.  This is done by setting parent to 0.  
		
		if( taxonomy_exists($taxonomyName) ){
        	$parent_terms = get_terms($taxonomyName, array('parent' => 0, 'orderby' => 'slug', 'hide_empty' => false));
			$options = '';
			if (isset($parent_terms) && !empty($parent_terms)) {
				foreach ($parent_terms as $pterm) {
					//Get the Child terms

					$terms = get_terms($taxonomyName, array('parent' => $pterm->term_id, 'orderby' => 'slug', 'hide_empty' => false));
					if (isset($terms) && !empty($terms)) {
						$options .= '<optgroup  label="' . $pterm->name . '">';
						foreach ($terms as $term) {
							$selected = '';

							if (!empty($current) && is_array($current) && in_array($term->slug, $current)
							) {
								$selected = 'selected';
							} else if (!empty($current) && !is_array($current) && $term->slug == $current
							) {
								$selected = 'selected';
							}

							$options .= '<option ' . $selected . ' value="' . $term->slug . '">' . $term->name . '</option>';
						}
						$options .= '</optgroup>';
					} else {
						$selected = '';

						if (!empty($current) && is_array($current) && in_array($pterm->slug, $current)
						) {
							$selected = 'selected';
						} else if (!empty($current) && !is_array($current) && $pterm->slug == $current
						) {

							$selected = 'selected';
						}

						$options .= '<option ' . $selected . ' value="' . $pterm->slug . '">' . $pterm->name . '</option>';
					}
				}
			}
			
			if( isset( $return_type ) && $return_type === 'return' ){
				return force_balance_tags($options);
			} else{
				echo force_balance_tags($options);
			}
			
		} else{
			if( isset( $return_type ) && $return_type === 'return' ){
				return '';
			} else{
				echo '';
			}
		}
    }

}


/**
 * @Profile Cities Find
 * @return {}
 */
if (!function_exists('listingo_get_cities_by_country')) {

    function listingo_get_cities_by_country($country,$current) {
        $options = '';

        if (!empty($country)) {
            $args = array(
                'hide_empty' => false,
                'meta_key' 	 => 'country',
                'meta_value' => $country
            );
            $terms = get_terms('cities', $args);

            if (!empty($terms)) {
                foreach ($terms as $key => $term) {
					$selected	= '';
					if( !empty($current) && $term->slug === $current ){
						$selected	= 'selected';
					}
					
                    $output .= '<option '.$selected.' value="' . esc_attr($term->slug) . '">' . esc_attr($term->name) . '</option>';
                }
               
                $options = $output;
            } else {
                $options = '';
            }
        }

        return $options;
    }

    add_filter('listingo_get_cities_by_country', 'listingo_get_cities_by_country',10,2);
}

/**
 * @Profile Cities Find
 * @return {}
 */
if (!function_exists('listingo_find_cities')) {

    function listingo_find_cities() {
        $json = array();
        $output = '';
        if (!empty($_POST['country'])) {
            $country = $_POST['country'];
            $args = array(
                'hide_empty' => false,
                'meta_key' 	 => 'country',
                'meta_value' => $country
            );
            $terms = get_terms('cities', $args);

            if (!empty($terms)) {
                foreach ($terms as $key => $term) {
                    $output .= '<option value="' . esc_attr($term->slug) . '">' . esc_attr($term->name) . '</option>';
                }
                $json['type'] = 'success';
                $json['message'] = esc_html('Cities Found Sucessfully.');
                $json['cities_data'] = $output;
            } else {
                $json['type'] = 'error';
                $json['message'] = esc_html('No cities or city found based on country selection.');
            }
        }

        echo json_encode($json);
        die;
    }

    add_action('wp_ajax_listingo_find_cities', 'listingo_find_cities');
    add_action('wp_ajax_nopriv_listingo_find_cities', 'listingo_find_cities');
}


/**
 * @Set Post Views
 * @return {}
 */
if (!function_exists('listingo_set_profile_views')) {

    function listingo_set_profile_views($user_id = '', $key = '') {
        if (!isset($_COOKIE[$key . $user_id])) {
            if ($key === 'set_profile_view') {
                setcookie("set_profile_view" . $user_id, 'profile_view_count', time() + 3600);
            } elseif ($key === 'set_job_view') {
                setcookie("set_job_view" . $user_id, 'jobs_view_count', time() + 3600);
            }
            $count = get_user_meta($user_id, $key, true);
            if ($count == '') {
                $count = 0;
                delete_user_meta($user_id, $key);
                add_user_meta($user_id, $key, '0');
            } else {
                $count++;
                update_user_meta($user_id, $key, $count);
            }
        }
    }

    add_action('sp_set_profile_views', 'listingo_set_profile_views', 2, 10);
}


/**
 * @Get Post Views
 * @return {}
 */
if (!function_exists('listingo_get_profile_views')) {

    function listingo_get_profile_views($user_id, $key = '') {

        $count = get_user_meta($user_id, $key, true);
        if ($count == '') {
            delete_user_meta($user_id, $key);
            add_user_meta($user_id, $key, '0');
            return "0 ";
        }
        return number_format($count);
    }

    add_filter('sp_get_profile_views', 'listingo_get_profile_views', 2, 20);
}

/**
 * @Get provider evaluation fields
 * @return {}
 */
if (!function_exists('listingo_get_reviews_evaluation')) {

    function listingo_get_reviews_evaluation($category_type, $reviews_type) {

        $reviews_evaluation = array();

        $reviews = '';
        if (function_exists('fw_get_db_settings_option')) {
            $reviews = fw_get_db_post_option($category_type, 'enable_reviews', true);
        }

        $reviews_check = !empty($reviews['gadget']) ? $reviews['gadget'] : '';

        if (!empty($reviews_check) && $reviews_check === 'enable' && $reviews_type === 'total_wait_time') {
            $reviews_evaluation = !empty($reviews['enable'][$reviews_type]) ? $reviews['enable'][$reviews_type] : array();
        } else if (!empty($reviews_check) && $reviews_check === 'enable' && $reviews_type === 'leave_rating') {
            $reviews_evaluation = !empty($reviews['enable'][$reviews_type]) ? $reviews['enable'][$reviews_type] : array();
        }

        $reviews_evaluation = array_filter($reviews_evaluation);
        $reviews_evaluation = array_combine(array_map('sanitize_title', $reviews_evaluation), $reviews_evaluation);
        return $reviews_evaluation;
    }

}


/**
 * @Authenticate user
 * @return 
 */
if (!function_exists('listingo_is_user_logged_in')) {

    function listingo_is_user_logged_in() {
        if (is_user_logged_in()) {
            return true;
        } else {
            return false;
        }
    }

    add_filter('sp_is_user_logged_in', 'listingo_is_user_logged_in');
}

/**
 * Get All user those are active.
 *
 * @param json
 * @return string
 */
if (!function_exists('listingo_prepare_user_list')) {

    function listingo_prepare_user_list() {
        $args = array(
            'orderby' => 'nicename',
            'order' => 'DESC',
        );

        $site_user = get_users($args);

        $user_list = array();
        foreach ($site_user as $user) {
            $user_list[$user->data->ID] = $user->data->display_name;
        }

        return $user_list;
    }

}

/**
 * Get user review meta data
 *
 * @param json
 * @return string
 */
if (!function_exists('listingo_get_review_data')) {

    function listingo_get_review_data($user_id, $review_key = '', $type = '') {
        $review_meta = get_user_meta($user_id, 'review_data', true);
        if ($type === 'value') {
            return !empty($review_meta[$review_key]) ? $review_meta[$review_key] : '';
        }
        return !empty($review_meta) ? $review_meta : array();
    }

}

/**
 * @Get Average Ratings
 * @return 
 */
if (!function_exists('listingo_get_everage_rating')) {

    function listingo_get_everage_rating($user_id = '') {

        $meta_query_args = array('relation' => 'AND');
        $meta_query_args[] = array(
            'key' 		=> 'user_to',
            'value' 	=> $user_id,
            'compare' 	=> '=',
            'type' 		=> 'NUMERIC'
        );

        $args = array('posts_per_page' => -1,
            'post_type' 		=> 'sp_reviews',
            'post_status' 		=> 'publish',
            'order' 			=> 'ASC',
        );

        $args['meta_query'] = $meta_query_args;

        $average_rating = 0;
        $total_rating   = 0;
		$total_recommended  = 0;
		
        $query = new WP_Query($args);

        if ($query->have_posts()) {
            while ($query->have_posts()) : $query->the_post();
                global $post;
                $user_rating = get_post_meta($post->ID, 'user_rating', true);
				$recommended = get_post_meta($post->ID, 'recommended', true);
				
				if( $recommended === 'yes' ){
					$total_recommended++;
				}
			
                $average_rating = $average_rating + $user_rating;
                $total_rating++;

            endwhile;
            wp_reset_postdata();
        }

        $data['sp_average_rating'] = 0;
        $data['sp_total_rating'] = 0;
        $data['sp_total_percentage'] = 0;
		$data['sp_total_recommendation'] = 0;
		
        if (isset($average_rating) && $average_rating > 0) {
            $data['sp_average_rating'] = $average_rating / $total_rating;
            $data['sp_total_rating'] = $total_rating;
			$data['sp_total_recommendation'] = $total_recommended;
            $data['sp_total_percentage'] = ( $average_rating / $total_rating) * 5;
        }

        return $data;
    }

}


/**
 * @Get Total ratings and votes
 * @return
 */
if (!function_exists('listingo_get_total_rating_votes')) {

    function listingo_get_total_rating_votes($author_id, $return_type = 'echo') {

        if (empty($author_id)) {
            return;
        }
		
		$category_type = get_user_meta($author_id,'category',true);

		$enable_reviews = '';
		if (function_exists('fw_get_db_settings_option')) {
			$enable_reviews = fw_get_db_post_option($category_type, 'enable_reviews', true);
		}
		
		
		if( !empty($enable_reviews) && $enable_reviews['gadget'] === 'enable' ){
			$recommendations = intval(0);
			$review_data = array();
			$total_votes = sprintf("%s %u %s%s", '(', intval(0), esc_html__('votes', 'listingo'), ')');

			$total_ratings = intval(0);
			if (isset($author_id)) {
				$review_data = get_user_meta($author_id, 'review_data', true);
				if (!empty($review_data)) {

					$recommended = !empty($review_data['sp_total_recommendation']) ? $review_data['sp_total_recommendation'] : '';
					$ratings = !empty($review_data['sp_total_rating']) ? $review_data['sp_total_rating'] : '';
					$average = !empty($review_data['sp_average_rating']) ? $review_data['sp_average_rating'] : '';

					if (empty($recommended)) {
						$recommended_value = 0;
					} else {
						$recommended_value = (intval($recommended) / intval($ratings)) * intval(100);
					}

					$recommendations 	= sprintf("%u%s", $recommended_value, esc_attr('%'));
					$total_votes 		= sprintf("%s %u %s%s", '(', $ratings, esc_html__('votes', 'listingo'), ')');
					$change_format 		= number_format((float) $average, 2);

					$total_ratings = $change_format * intval(20);
				}
			}
			ob_start();
			?>
			<ul class="tg-matadata">
				<li>
					<span class="tg-stars">
						<span style="width:<?php echo esc_attr($total_ratings) . esc_attr('%'); ?>;"></span>
					</span>
				</li>
				<li>
					<i class="fa fa-thumbs-o-up"></i>
					<em><?php echo esc_attr($recommendations); ?> <?php echo esc_attr($total_votes); ?></em>
				</li>
			</ul>
			<?php
			$output_data = ob_get_clean();
			if ($return_type === 'return') {
				return $output_data;
			} else {
				echo force_balance_tags($output_data);
			}
		}
    }

    add_action('sp_get_rating_and_votes', 'listingo_get_total_rating_votes', 10, 2);
}

/**
 * @Update add to favorites
 * @return 
 */
if (!function_exists('listingo_remove_wishlist')) {

    function listingo_remove_wishlist() {
        global $current_user;
        $json = array();
        $wishlist = array();
        $wishlist = get_user_meta($current_user->ID, 'wishlist', true);
        $wishlist = !empty($wishlist) && is_array($wishlist) ? $wishlist : array();

        $wl_id = array();
        $type = sanitize_text_field($_POST['type']);

        if (isset($type) && $type === 'all') {
            $wishlist = array();
            update_user_meta($current_user->ID, 'wishlist', $wishlist);

            $json['type'] = 'success';
            $json['message'] = esc_html__('Successfully! removed from your wishlist', 'listingo');
            echo json_encode($json);
            die();
        }

        $wl_id[] = sanitize_text_field($_POST['wl_id']);
        $posted_id = sanitize_text_field($_POST['wl_id']);

        if (!empty($posted_id)) {
            $wishlist = array_diff($wishlist, $wl_id);
            update_user_meta($current_user->ID, 'wishlist', $wishlist);

            $json['type'] = 'success';
            $json['message'] = esc_html__('Successfully! removed from your wishlist', 'listingo');
            echo json_encode($json);
            die();
        }

        $json['type'] = 'error';
        $json['message'] = esc_html__('Oops! something is going wrong.', 'listingo');
        echo json_encode($json);
        die();
    }

    add_action('wp_ajax_listingo_remove_wishlist', 'listingo_remove_wishlist');
    add_action('wp_ajax_nopriv_listingo_remove_wishlist', 'listingo_remove_wishlist');
}

/**
 * Get the terms by post ID
 *
 * @return html
 */
if (!function_exists('listingo_get_terms_by_post')) {

    function listingo_get_terms_by_post() {
        $post_id = !empty($_POST['id']) ? intval($_POST['id']) : '';
        $terms = get_the_terms($post_id, 'sub_category');
        $options = '';
        $json = array();

        $options .= '<option value="">' . esc_html__('Select sub category', 'listingo') . '</option>';

        if (!empty($terms)) {
            foreach ($terms as $pterm) {
                $options .= '<option value="' . $pterm->slug . '">' . $pterm->name . '</option>';
            }
        }

        $json['type'] = 'success';
        $json['options'] = $options;
        echo json_encode($json);
        die();
    }

    add_action('wp_ajax_listingo_get_terms_by_post', 'listingo_get_terms_by_post');
    add_action('wp_ajax_nopriv_listingo_get_terms_by_post', 'listingo_get_terms_by_post');
}



/* ------------------------------------------------
  /**
 * @Lost Password action
 * @return 
 */
if (!function_exists('listingo_ajax_lp')) {

    function listingo_ajax_lp() {
        global $wpdb;
        $json = array();

        if (function_exists('fw_get_db_settings_option')) {
            $captcha_settings = fw_get_db_settings_option('captcha_settings', $default_value = null);
        }

        $user_input = !empty($_POST['psemail']) ? $_POST['psemail'] : '';

        if (!wp_verify_nonce($_POST['tg_pwd_nonce'], "tg_pwd_nonce")) {
            $json['type'] = 'error';
            $json['message'] = esc_html__('No tricks please!', 'listingo');
            echo json_encode($json);
            die;
        }

        if (empty($user_input)) {
            $json['type'] = 'error';
            $json['message'] = esc_html__('Please add email address.', 'listingo');
            echo json_encode($json);
            die;
        } else if (!is_email($user_input)) {
            $json['type'] = "error";
            $json['message'] = esc_html__("Please add a valid email address.", 'listingo');
            echo json_encode($json);
            die;
        }

        //recaptcha check
        if (isset($captcha_settings) && $captcha_settings === 'enable') {
            if (isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) {
                $docReResult = listingo_get_recaptcha_response($_POST['g-recaptcha-response']);

                if ($docReResult == 1) {
                    $workdone = 1;
                } else if ($docReResult == 2) {
                    echo json_encode(array('type' => 'error',
                        'loggedin' => false,
                        'message' => esc_html__('Some error occur, please try again later.', 'listingo')
                            )
                    );
                    die;
                } else {
                    echo json_encode(array('type' => 'error',
                        'loggedin' => false,
                        'message' => esc_html__('Wrong reCaptcha. Please verify first.', 'listingo')
                            )
                    );
                    die;
                }
            } else {
                echo json_encode(array('type' => 'error',
                    'loggedin' => false,
                    'message' => esc_html__('Please enter reCaptcha!', 'listingo')
                        )
                );
                die;
            }
        }

        $user_data = get_user_by_email($user_input);
        if (empty($user_data) || $user_data->caps[administrator] == 1) {
            //the condition $user_data->caps[administrator] == 1 to prevent password change for admin users.
            //if you prefer to offer password change for admin users also, just delete that condition.
            $json['type'] = "error";
            $json['message'] = esc_html__("Invalid E-mail address!", 'listingo');
            echo json_encode($json);
            die;
        }

        $user_id = $user_data->ID;
        $user_login = $user_data->user_login;
        $user_email = $user_data->user_email;

        $key = $wpdb->get_var($wpdb->prepare("SELECT user_activation_key FROM $wpdb->users WHERE user_login = %s", $user_login));

        if (empty($key)) {
            //generate reset key
            $key = wp_generate_password(20, false);
            $wpdb->update($wpdb->users, array('user_activation_key' => $key), array('user_login' => $user_login));
        }

        $protocol = is_ssl() ? 'https' : 'http';
        $reset_link = esc_url(add_query_arg(array('action' => 'reset_pwd', 'key' => $key, 'login' => $user_login), home_url('/', $protocol)));

        if (class_exists('ListingoProcessEmail')) {
            $email_helper = new ListingoProcessEmail();

            //Get User Name with User ID
            $username = listingo_get_username($user_id);
            $emailData = array();
            $emailData['username'] = $username;
            $emailData['email'] = $user_email;
            $emailData['link'] = $reset_link;
            $email_helper->process_lostpassword_email($emailData);
        }


        $json['type'] = "success";
        $json['message'] = esc_html__("A link has been sent, please check your email.", 'listingo');
        echo json_encode($json);
        die;
    }

    add_action('wp_ajax_listingo_ajax_lp', 'listingo_ajax_lp');
    add_action('wp_ajax_nopriv_listingo_ajax_lp', 'listingo_ajax_lp');
}

/**
 * @Reset Password Form
 * @return 
 */
if (!function_exists('listingo_reset_password_form')) {

    function listingo_reset_password_form() {
        global $wpdb;
        $captcha_settings = '';
        if (function_exists('fw_get_db_settings_option')) {
            $captcha_settings = fw_get_db_settings_option('captcha_settings', $default_value = null);
        }

        if (!empty($_GET['key']) &&
                ( isset($_GET['action']) && $_GET['action'] == "reset_pwd" ) &&
                (!empty($_GET['login']) )
        ) {
            $reset_key = $_GET['key'];
            $user_login = $_GET['login'];
            $reset_action = $_GET['action'];

            $key = $wpdb->get_var($wpdb->prepare("SELECT user_activation_key FROM $wpdb->users WHERE user_login = %s", $user_login));

            if ($reset_key === $key) {
                $user_data = $wpdb->get_row($wpdb->prepare("SELECT ID, user_login, user_email FROM $wpdb->users WHERE user_activation_key = %s AND user_login = %s", $reset_key, $user_login));

                $user_login = $user_data->user_login;
                $user_email = $user_data->user_email;

                if (!empty($user_data)) {
                    ob_start();
                    ?>
                    <div class="modal fade tg-user-reset-model tg-modalmanageteam" tabindex="-1" role="dialog">
                        <div class="modal-dialog tg-modaldialog">
                            <div class="modal-content tg-modalcontent">
                                <div class="panel-lostps">
                                    <form class="tg-form-modal tg-form-signup do-reset-form">
                                        <div class="form-group">
                                            <div class="tg-modalhead">
                                                <h2><?php esc_html_e('Reset Password', 'listingo'); ?></h2>
                                            </div>
                                            <p><?php echo wp_get_password_hint(); ?></p>
                                            <div class="forgot-fields">
                                                <div class="form-group">
                                                    <label for="pass1"><?php esc_html_e('New password', 'listingo') ?></label>
                                                    <input type="password"  name="pass1" id="pass1" class="input" size="20" value="" autocomplete="off" />
                                                </div>
                                                <div class="form-group">
                                                    <label for="pass2"><?php esc_html_e('Repeat new password', 'listingo') ?></label>
                                                    <input type="password" name="pass2" id="pass2" class="input" size="20" value="" autocomplete="off" />

                                                    <input type="hidden" name="tg_pwd_nonce" value="<?php echo wp_create_nonce("tg_pwd_nonce"); ?>" />
                                                </div>
                                            </div>
                                            <?php if (isset($captcha_settings) && $captcha_settings === 'enable') { ?>
                                                <div class="domain-captcha">
                                                    <div id="recaptcha_reset"></div>
                                                </div>
                                            <?php } ?>
                                            <button class="tg-btn tg-btn-lg  do-reset-button" type="button"><?php esc_html_e('Submit', 'listingo'); ?></button>

                                            <input type="hidden" name="key" value="<?php echo esc_attr($reset_key); ?>" />
                                            <input type="hidden" name="reset_action" value="<?php echo esc_attr($reset_action); ?>" />
                                            <input type="hidden" name="login" value="<?php echo esc_attr($user_login); ?>" />
                                        </div>
                                    </form>    
                                </div>
                            </div>
                        </div>
                    </div>
                    <a href="javascript:;" class="open-reset-window" data-toggle="modal" data-target=".tg-user-reset-model"></a>
                    <?php
                    echo ob_get_clean();
					$script = "jQuery(document).ready(function ($) {setTimeout(function() {jQuery('.open-reset-window').trigger('click');},100);});";
            		wp_add_inline_script('listingo_callbacks', $script, 'after');
                }
            }
        }
    }

    add_action('listingo_reset_password_form', 'listingo_reset_password_form');
}

/**
 * @Reset Password Form
 * @return 
 */
if (!function_exists('listingo_verify_user_account')) {

    function listingo_verify_user_account() {
        global $wpdb;
        

        if ( !empty($_GET['key']) && !empty($_GET['verifyemail']) ) {
            $verify_key 	= esc_attr( $_GET['key'] );
            $user_email 	= esc_attr( $_GET['verifyemail'] );

            $user_identity = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->users WHERE user_email = %s", $user_email));
			if( !empty( $user_identity ) ){
				$confirmation_key = get_user_meta(intval( $user_identity ), 'confirmation_key', true);
				if ( $confirmation_key === $verify_key ) {
					update_user_meta( $user_identity, 'confirmation_key', '');
					update_user_meta( $user_identity, 'verify_user', 'on');
					
					$script = "jQuery(document).on('ready', function () { jQuery.sticky(scripts_vars.account_verification, {classList: 'success', speed: 200, autoclose: 5000 }); });";
            		wp_add_inline_script('listingo_callbacks', $script, 'after');
				}
			}
        }
    }

    add_action('listingo_verify_user_account', 'listingo_verify_user_account');
}

/**
 * @Reset Password action
 * @return 
 */
if (!function_exists('listingo_ajax_reset_password')) {

    function listingo_ajax_reset_password() {
        global $wpdb;
        $json = array();

        $captcha_settings = '';

        if (function_exists('fw_get_db_settings_option')) {
            $captcha_settings = fw_get_db_settings_option('captcha_settings', $default_value = null);
        }

        if (!wp_verify_nonce($_POST['tg_pwd_nonce'], "tg_pwd_nonce")) {
            $json['type'] = 'error';
            $json['message'] = esc_html__('No trick please.', 'listingo');
            echo json_encode($json);
            die;
        }


        if (isset($_POST['pass1'])) {
            if ($_POST['pass1'] != $_POST['pass2']) {
                // Passwords don't match
                $json['type'] = "error";
                $json['message'] = esc_html__("Oops! password is not matched", 'listingo');
                echo json_encode($json);
                die;
            }

            if (empty($_POST['pass1'])) {
                $json['type'] = "error";
                $json['message'] = esc_html__("Oops! password should not be empty", 'listingo');
                echo json_encode($json);
                die;
            }
        } else {
            $json['type'] = "error";
            $json['message'] = esc_html__("Oops! Invalid request", 'listingo');
            echo json_encode($json);
            die;
        }

        //recaptcha check
        if (isset($captcha_settings) && $captcha_settings === 'enable') {
            if (isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) {
                $docReResult = listingo_get_recaptcha_response($_POST['g-recaptcha-response']);

                if ($docReResult == 1) {
                    $workdone = 1;
                } else if ($docReResult == 2) {
                    echo json_encode(array('type' => 'error',
                        'loggedin' => false,
                        'message' => esc_html__('Some error occur, please try again later.', 'listingo')
                            )
                    );
                    die;
                } else {
                    echo json_encode(array('type' => 'error',
                        'loggedin' => false,
                        'message' => esc_html__('Wrong reCaptcha. Please verify first.', 'listingo')
                            )
                    );
                    die;
                }
            } else {
                echo json_encode(array('type' => 'error',
                    'loggedin' => false,
                    'message' => esc_html__('Please enter reCaptcha!', 'listingo')
                        )
                );
                die;
            }
        }


        if (!empty($_POST['key']) &&
                ( isset($_POST['reset_action']) && $_POST['reset_action'] == "reset_pwd" ) &&
                (!empty($_POST['login']) )
        ) {

            $reset_key = sanitize_text_field($_POST['key']);
            $user_login = sanitize_text_field($_POST['login']);

            $user_data = $wpdb->get_row($wpdb->prepare("SELECT ID, user_login, user_email FROM $wpdb->users WHERE user_activation_key = %s AND user_login = %s", $reset_key, $user_login));

            $user_login = $user_data->user_login;
            $user_email = $user_data->user_email;

            if (!empty($reset_key) && !empty($user_data)) {
                $new_password = sanitize_text_field( $_POST['pass1'] );

                wp_set_password($new_password, $user_data->ID);

                $json['redirect_url'] = home_url('/');
                $json['type'] = "success";
                $json['message'] = esc_html__("Congratulation! your password has been changed.", 'listingo');
                echo json_encode($json);
                die;
            } else {
                $json['type'] = "error";
                $json['message'] = esc_html__("Oops! Invalid request", 'listingo');
                echo json_encode($json);
                die;
            }
        }
    }

    add_action('wp_ajax_listingo_ajax_reset_password', 'listingo_ajax_reset_password');
    add_action('wp_ajax_nopriv_listingo_ajax_reset_password', 'listingo_ajax_reset_password');
}


/**
 * @Get Matched users
 * @return
 */
if (!function_exists('listingo_get_team_members')) {

    function listingo_get_team_members() {
		global $current_user;
        $s = sanitize_text_field(trim($_POST['email']));

        $json = array();
        $user_json = array();
        $output = '';
        $meta_query_args = array();

        $order = 'DESC';
        $orderby = 'ID';

        $query_args = array(
            'role__in' => array('professional', 'business'),
            'order' => $order,
            'orderby' => $orderby,
        );

        $search_args = array(
            'search' => trim($s),
            'search_columns' => array(
                'user_email',
                'user_login',
            )
        );

		$meta_keyword = array('relation' => 'OR',);
		$meta_keyword[] = array(
			'key' => 'first_name',
			'value' => $s,
			'compare' => 'LIKE',
		);

		$meta_keyword[] = array(
			'key' => 'last_name',
			'value' => $s,
			'compare' => 'LIKE',
		);

		$meta_keyword[] = array(
			'key' => 'nickname',
			'value' => $s,
			'compare' => 'LIKE',
		);

		$meta_keyword[] = array(
			'key' => 'username',
			'value' => $s,
			'compare' => 'LIKE',
		);

		$meta_keyword[] = array(
			'key' => 'full_name',
			'value' => $s,
			'compare' => 'LIKE',
		);

		$meta_keyword[] = array(
			'key' => 'company_name',
			'value' => $s,
			'compare' => 'LIKE',
		);

		if (!empty($meta_keyword)) {
			$meta_query_args[] = array_merge($meta_keyword, $meta_query_args);
		}
		
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
		
        $users_found = (int) count(get_users($query_args)); //Total Users
        $users_query = new WP_User_Query($query_args);
		
        if (!empty($users_query->results)) {
            $counter = 0;
            $total_found = $users_found;
			
            foreach ($users_query->results as $user) {
                $is_claimed = '';
                $full_name = listingo_get_username($user->ID);
                $user_email = $user->user_email;
                $username = $user->user_login;
                $avatar = apply_filters(
                        'listingo_get_media_filter', listingo_get_user_avatar(array('width' => 100, 'height' => 100), $user->ID), array('width' => 100, 'height' => 100)
                );
				
				if( intval( $current_user->ID ) !== intval( $user->ID ) ) {
					$user_json[$user->ID]['id'] = $user->ID;
					$user_json[$user->ID]['username'] = $full_name;
					$user_json[$user->ID]['full_name'] = $full_name;
					$user_json[$user->ID]['user_email'] = $user_email;
					$user_json[$user->ID]['photo'] = $avatar;
					$user_json[$user->ID]['user_link'] = get_author_posts_url($user->ID);

					$counter++;
				}
            }


            $json['type'] = 'success';
            $json['user_json'] = $user_json;

            $json['msg'] = esc_html__('Users found', 'listingo');
            echo json_encode($json);
            die;
        } else {
            $json['type'] = 'error';
            $json['user_json'] = $user_json;
            $json['msg'] = esc_html__('No user found', 'listingo');
            echo json_encode($json);
            die;
        }
    }

    add_action('wp_ajax_listingo_get_team_members', 'listingo_get_team_members');
    add_action('wp_ajax_nopriv_listingo_get_team_members', 'listingo_get_team_members');
}

/**
 * @update team member
 * @return {}
 */
if (!function_exists('listingo_update_team_members')) {

    function listingo_update_team_members() {
        global $current_user;

        $id = sanitize_text_field($_POST['id']);
		
		if( function_exists('listingo_is_demo_site') ) { 
			listingo_is_demo_site() ;
		}; //if demo site then prevent
		
        $teams = array();
        $teams = get_user_meta($current_user->ID, 'teams_data', true);
        $teams = !empty($teams) && is_array($teams) ? $teams : array();
        if (!empty($_POST['id'])) {
            $team_id = intval($_POST['id']);
            $teams[] = $team_id;
            $teams = array_unique($teams);
            update_user_meta($current_user->ID, 'teams_data', $teams);

            $json = array();
            $json['type'] = 'success';
            $json['message'] = esc_html__('Successfully! added to your team members', 'listingo');
            echo json_encode($json);
            die();
        }

        $json = array();
        $json['type'] = 'error';
        $json['message'] = esc_html__('Oops! something is going wrong.', 'listingo');
        echo json_encode($json);
        die();
    }

    add_action('wp_ajax_listingo_update_team_members', 'listingo_update_team_members');
    add_action('wp_ajax_nopriv_listingo_update_team_members', 'listingo_update_team_members');
}


/**
 * @invite users
 * @return 
 */
if (!function_exists('listingo_invite_users')) {

    function listingo_invite_users() {
        global $current_user;

        $email = sanitize_text_field($_POST['email']);
        $message = sanitize_text_field($_POST['message']);

        $json = array();

        if (empty($email)) {
            $json['type'] = 'error';
            $json['message'] = esc_html__('Please add email ID', 'listingo');
            echo json_encode($json);
            die;
        } else if (!is_email($email)) {
            $json['type'] = 'error';
            $json['message'] = esc_html__('Please add valid email ID', 'listingo');
            echo json_encode($json);
            die;
        } else if (empty($message)) {
            $json['type'] = 'error';
            $json['message'] = esc_html__('Please add message', 'listingo');
            echo json_encode($message);
            die;
        }

        $signup_page = '';
        if (function_exists('fw_get_db_settings_option')) {
            $signup_page = fw_get_db_settings_option('invitation_signup_page', true);
        }

        if (empty($signup_page[0])) {
            $json['type'] = 'error';
            $json['message'] = esc_html__('Please set the signup page first in directory settings.', 'listingo');
            echo json_encode($json);
            die;
        } else {
            $signup_page_slug = listingo_get_slug($signup_page[0]);
        }

        $protocol = is_ssl() ? 'https' : 'http';
        $signup_link = home_url('/' . esc_attr($signup_page_slug) . '/', $protocol);

        //Send ean email 
        if (class_exists('ListingoProcessEmail')) {
            $email_helper = new ListingoProcessEmail();
            $emailData = array();

            $emailData['email_to'] = esc_attr($email);
            $emailData['username'] = listingo_get_username($current_user->ID);
            $emailData['message'] = force_balance_tags($message);
            $emailData['link'] = esc_url($signup_link);

            $email_helper->process_invitation_email($emailData);
            $json['type'] = 'success';
            $json['message'] = esc_html__('Email has sent.', 'listingo');
        } else {
            $json['message'] = esc_html__('Some error occur please try again later.', 'listingo');
            $json['type'] = 'error';
        }

        echo json_encode($json);
        die;
    }

    add_action('wp_ajax_listingo_invite_users', 'listingo_invite_users');
    add_action('wp_ajax_nopriv_listingo_invite_users', 'listingo_invite_users');
}

/**
 * @delete team member
 * @return 
 */
if (!function_exists('listingo_remove_team_member')) {

    function listingo_remove_team_member() {
        global $current_user;
        $teams = array();
        $teams = get_user_meta($current_user->ID, 'teams_data', true);
        $teams = !empty($teams) && is_array($teams) ? $teams : array();
		
		if( function_exists('listingo_is_demo_site') ) { 
			listingo_is_demo_site() ;
		}; //if demo site then prevent
		
        if (!empty($_POST['id'])) {
            $team_id = array();
            $team_id[] = intval($_POST['id']);
            $teams = array_diff($teams, $team_id);
            update_user_meta($current_user->ID, 'teams_data', $teams);

            $json = array();
            $json['type'] = 'success';
            $json['message'] = esc_html__('Successfully! removed from your teams', 'listingo');
            echo json_encode($json);
            die();
        }

        $json = array();
        $json['type'] = 'error';
        $json['message'] = esc_html__('Oops! something is going wrong.', 'listingo');
        echo json_encode($json);
        die();
    }

    add_action('wp_ajax_listingo_remove_team_member', 'listingo_remove_team_member');
    add_action('wp_ajax_nopriv_listingo_remove_team_member', 'listingo_remove_team_member');
}

/**
 * @updaet cart
 * @return 
 */
if (!function_exists('listingo_update_cart')) {

    function listingo_update_cart() {
        global $current_user, $woocommerce;

		if( function_exists('listingo_is_demo_site') ) { 
			listingo_is_demo_site() ;
		}; //if demo site then prevent
		
        if (!empty($_POST['id'])) {
            $product_id = intval($_POST['id']);
			
			$woocommerce->cart->empty_cart(); //empty cart before update cart
			
			$is_cart_matched	= listingo_matched_cart_items($product_id);
			
            if ( isset( $is_cart_matched ) && $is_cart_matched > 0) {
                $json = array();
                $json['type'] = 'success';
                $json['message'] = esc_html__('You have already in cart, We are redirecting to checkout', 'listingo');
                $json['checkout_url'] = esc_url($woocommerce->cart->get_checkout_url());
                echo json_encode($json);
                die();
            }
			
			$cart_meta		= array();
			$package_meta	= listingo_get_package_features();
			
			$package_type 	= get_post_meta( $product_id, 'sp_package_type', true );
			$sp_duration 	= get_post_meta( $product_id, 'sp_duration', true );
			$sp_jobs 		= get_post_meta( $product_id, 'sp_jobs', true );
			$sp_articles 	= get_post_meta( $product_id, 'sp_articles', true );
			$sp_favorites 	= get_post_meta( $product_id, 'sp_favorites', true );

			$sp_articles = !empty( $sp_articles ) ? $sp_articles : 1;
			$sp_jobs 	 = !empty( $sp_jobs ) ? $sp_jobs : 1;
			
			$cart_meta['sp_duration']	= $sp_duration.'&nbsp;'.esc_html__( 'days','listingo' );
			$cart_meta['sp_jobs']		= $sp_jobs.'&nbsp;'.esc_html__( 'jobs','listingo' );
			
			//Check if favorite is enabled
			if( apply_filters('listingo_is_favorite_allowed',$current_user->ID) === true ){
				$cart_meta['sp_favorites']	= $sp_favorites;
			}
			
			
			if (!empty($package_type) && $package_type === 'provider' ) {
				$sp_featured		= get_post_meta( $product_id, 'sp_featured', true );
				$sp_appointments 	= get_post_meta( $product_id, 'sp_appointments', true );
				$sp_banner 			= get_post_meta( $product_id, 'sp_banner', true );
				$sp_insurance 		= get_post_meta( $product_id, 'sp_insurance', true );
				$sp_teams 			= get_post_meta( $product_id, 'sp_teams', true );
				$sp_hours 			= get_post_meta( $product_id, 'sp_hours', true );
				$sp_page_design 	= get_post_meta( $product_id, 'sp_page_design', true );
				$sp_gallery_photos 	= get_post_meta( $product_id, 'sp_gallery_photos', true );
				$sp_videos 			= get_post_meta( $product_id, 'sp_videos', true );
				$sp_contact_information	= get_post_meta( $product_id, 'sp_contact_information', true );
				
				$sp_photos_limit 	= get_post_meta( $product_id, 'sp_photos_limit', true );
				$sp_banners_limit 	= get_post_meta( $product_id, 'sp_banners_limit', true );
				
				$sp_gallery_photos 	= !empty( $sp_gallery_photos ) ? $sp_gallery_photos : 0;
				$sp_videos 			= !empty( $sp_videos ) ? $sp_videos : 0;
				$sp_photos_limit 	= !empty( $sp_photos_limit ) ? $sp_photos_limit : 0;
				$sp_banners_limit 	= !empty( $sp_banners_limit ) ? $sp_banners_limit : 0;
				
				$cart_meta['sp_articles']		= $sp_articles.'&nbsp;'.esc_html__( 'articles','listingo' );
				$cart_meta['sp_gallery_photos']	= $sp_gallery_photos.'&nbsp;'.esc_html__( 'Gallery photos','listingo' );
				$cart_meta['sp_videos']			= $sp_videos.'&nbsp;'.esc_html__( 'Video links','listingo' );
				$cart_meta['sp_photos_limit']	= $sp_photos_limit.'&nbsp;'.esc_html__( 'Profile photos','listingo' );
				$cart_meta['sp_banners_limit']	= $sp_banners_limit.'&nbsp;'.esc_html__( 'Banner photos','listingo' );
				$cart_meta['sp_appointments']	= $sp_appointments;
				$cart_meta['sp_banner']			= $sp_banner;
				$cart_meta['sp_insurance']		= $sp_insurance;
				$cart_meta['sp_teams']			= $sp_teams;
				$cart_meta['sp_hours']			= $sp_hours;
				$cart_meta['sp_page_design']	= $sp_page_design;
				$cart_meta['sp_contact_information']	= $sp_contact_information;
				
			}
			
			$provider_category	= listingo_get_provider_category($current_user->ID);
			$is_allowed			= check_allowed_packages_features();
			
			foreach( $is_allowed as $key => $meta ){
				$is_feature_allowed	= 'yes';
				
				if( isset( $meta['feature_check'] ) && $meta['feature_check'] === 'yes' ){
					if (apply_filters('listingo_is_feature_allowed', $provider_category, $meta['feature_check_key']) === true) {
						$is_feature_allowed	= 'yes';
					}else{
						$is_feature_allowed	= 'no';
					}
				}
				
				//jobs
				if( isset( $meta['key'] ) && $meta['key'] === 'jobs' ){
					if ( apply_filters('listingo_get_theme_settings', 'jobs') == 'yes') {
						$is_feature_allowed	= 'yes';
					}else{
						$is_feature_allowed	= 'no';
					}
				}
				
				//if contact informations are free or paid
				if( isset( $meta['key'] ) && $meta['key'] === 'sp_contact_information' ){
					if ( apply_filters('listingo_is_contact_informations_enabled', 'yes','','') === 'yes') {
						$is_feature_allowed	= 'yes';
					}else{
						$is_feature_allowed	= 'no';
					}
				}

				//Articles
				if( isset( $meta['key'] ) && $meta['key'] === 'articles' ){
					if ( function_exists('fw_get_db_settings_option') 
						 && fw_ext('articles') 
						 && apply_filters('listingo_is_feature_allowed', $provider_category, $meta['feature_check_key']) === true
					 ) {
						$is_feature_allowed	= 'yes';
					}else{
						$is_feature_allowed	= 'no';
					}
				}

				
				if( $is_feature_allowed === 'yes' ){
					//do nothing
				} else{
					unset($cart_meta[$key]);
				}
			}

            $cart_data = array(
                'product_id' 		=> $product_id,
				'cart_data'     	=> $cart_meta,
				'payment_type'     	=> 'subscription',
            );
			
            if (class_exists('WooCommerce')) {

                $woocommerce->cart->empty_cart();
                $cart_item_data = $cart_data;
                WC()->cart->add_to_cart($product_id, 1, null, null, $cart_item_data);

                $json = array();
                $json['type'] = 'success';
                $json['message'] = esc_html__('Please wait you are redirecting to checkout page.', 'listingo');
                $json['checkout_url'] = esc_url($woocommerce->cart->get_checkout_url());
                echo json_encode($json);
                die();
            } else {
                $json = array();
                $json['type'] = 'error';
                $json['message'] = esc_html__('Please install WooCommerce plugin to process this order', 'listingo');
            }
        }

        $json = array();
        $json['type'] = 'error';
        $json['message'] = esc_html__('Oops! something is going wrong.', 'listingo');
        echo json_encode($json);
        die();
    }

    add_action('wp_ajax_listingo_update_cart', 'listingo_update_cart');
    add_action('wp_ajax_nopriv_listingo_update_cart', 'listingo_update_cart');
}


/**
 * @check settings for packages options
 * @return 
 */
if (!function_exists('listingo_is_setting_enabled')) {

    function listingo_is_setting_enabled($user_id, $filter_type) {
        $current_date = date('Y-m-d H:i:s');
        $is_included = '';

        $package_expiry = listingo_get_subscription_meta('subscription_expiry', $user_id);
        $is_enabled 	= listingo_get_subscription_meta($filter_type, $user_id);

        if (isset($is_enabled) && $is_enabled === 'yes') {
            if (!empty($package_expiry) && $package_expiry > strtotime($current_date)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    add_filter('listingo_is_setting_enabled', 'listingo_is_setting_enabled', 10, 2);
}

/**
 * @check settings for directory type
 * @return 
 */
if (!function_exists('listingo_is_feature_allowed')) {

    function listingo_is_feature_allowed($category_id, $filter_type) {
        $current_date = date('Y-m-d H:i:s');
		$is_enabled	= 'disable';
		if (function_exists('fw_get_db_settings_option')) {
        	$is_enabled = fw_get_db_post_option($category_id, $filter_type, true);
		}

        if (isset($is_enabled) && $is_enabled === 'enable') {
            return true;
        } else {
            return false;
        }
    }

    add_filter('listingo_is_feature_allowed', 'listingo_is_feature_allowed', 10, 2);
}

/**
 * @check if favorite is enabled
 * @return 
 */
if (!function_exists('listingo_is_favorite_allowed')) {

    function listingo_is_favorite_allowed($user_id) {
        $current_date = date('Y-m-d H:i:s');
		$is_enabled	= 'disable';
		
		if ( ( apply_filters('listingo_get_user_type', $user_id) === 'business' 
			  || apply_filters('listingo_get_user_type', $user_id) === 'professional' ) 
			  && function_exists('fw_get_db_settings_option')
		) {
			
			$provider_category  = listingo_get_provider_category($user_id);
			if (function_exists('fw_get_db_settings_option')) {
				$is_enabled = fw_get_db_post_option($provider_category, 'favorite', true);
			}

			if (isset($is_enabled) && $is_enabled === 'enable') {
				return true;
			} else {
				return false;
			}
			
		}else{
			return true;
		}
    }

    add_filter('listingo_is_favorite_allowed', 'listingo_is_favorite_allowed', 10,1);
}

/**
 * @User manage columns 
 * @return 
 */
if (!function_exists('listingo_user_manage_user_columns')) {
    add_filter('manage_users_columns', 'listingo_user_manage_user_columns');

    function listingo_user_manage_user_columns($column) {
		$bk_settings	= listingo_get_booking_settings();
		
        $column['type'] = esc_html__('User Type', 'listingo');
        $column['verification'] = esc_html__('Verification', 'listingo');
        $column['status'] = esc_html__('Account Status', 'listingo');
		if( isset( $bk_settings['type'] ) && $bk_settings['type'] === 'woo'
			&& isset( $bk_settings['hide_wallet'] ) && $bk_settings['hide_wallet'] === 'no' ){
			$column['withdrawal'] = esc_html__('Withdrawal', 'listingo');
		}
		
        return $column;
    }

}


/**
 * @verify users status
 * @return 
 */
if (!function_exists('listingo_change_status')) {
    add_action('admin_action_listingo_change_status', 'listingo_change_status');

    function listingo_change_status() {

        if (isset($_REQUEST['users']) && isset($_REQUEST['nonce'])) {
            $nonce = !empty($_REQUEST['nonce']) ? $_REQUEST['nonce'] : '';
            $users = !empty($_REQUEST['users']) ? $_REQUEST['users'] : '';

            if (wp_verify_nonce($nonce, 'listingo_change_status_' . $users)) {
                $is_approved = get_user_meta($users, 'verify_user', true);
                if (isset($is_approved) && $is_approved === 'on') {
                    $new_status = 'off';
                    $message_param = 'unapproved';
                } else {
                    $new_status = 'on';
                    $message_param = 'approved';
                }
                update_user_meta($users, 'verify_user', $new_status);
                $redirect = admin_url('users.php?updated=' . $message_param);
            } else {
                $redirect = admin_url('users.php?updated=listingo_false');
            }
        } else {
            $redirect = admin_url('users.php?updated=listingo_false');
        }
        wp_redirect($redirect);
    }

}

/**
 * @activate users
 * @return 
 */
if (!function_exists('listingo_change_activation_status')) {
    add_action('admin_action_listingo_change_activation_status', 'listingo_change_activation_status');

    function listingo_change_activation_status() {

        if (isset($_REQUEST['users']) && isset($_REQUEST['nonce'])) {
            $nonce = !empty($_REQUEST['nonce']) ? $_REQUEST['nonce'] : '';
            $users = !empty($_REQUEST['users']) ? $_REQUEST['users'] : '';

            if (wp_verify_nonce($nonce, 'listingo_change_activation_status_' . $users)) {
                $is_activated = get_user_meta($users, 'activation_status', true); 
                if (isset($is_activated) && $is_activated === 'active') {
                    $new_status = 'deactive';
                    $message_param = 'De-acivated';
                } else {
                    $new_status = 'active';
                    $message_param = 'Activated';
                }
                update_user_meta($users, 'activation_status', $new_status);
                $redirect = admin_url('users.php?updated=' . $message_param);
            } else {
                $redirect = admin_url('users.php?updated=listingo_false');
            }
        } else {
            $redirect = admin_url('users.php?updated=listingo_false');
        }
        wp_redirect($redirect);
    }

}

/**
 * @User column data
 * @return 
 */
if (!function_exists('listingo_user_manage_user_column_row')) {
    add_filter('manage_users_custom_column', 'listingo_user_manage_user_column_row', 10, 3);

    function listingo_user_manage_user_column_row($val, $column_name, $user_id) {
        $user = get_userdata($user_id);
		
		$bk_settings	= listingo_get_booking_settings();
		
        $user_type = esc_html__('NILL', 'listingo');
        if (!empty($user->category)) {
            $user_type = get_the_title($user->category);
            if (!empty($user->sub_category)) {
                $sub_category = get_term_by('slug', $user->sub_category, 'sub_category');
				$sub_category	= !empty( $sub_category->name ) ?  $sub_category->name : ''; 
                $user_type = $user_type . '/' . $sub_category;
            }
        }

        switch ($column_name) {
            case 'verification' :
                $status = get_user_meta($user_id, 'verify_user', true);
                $val = '<span style="color:red;">' . esc_html__('Not Verified', 'listingo') . '</span>';
                if (isset($status) && $status === 'on') {
                    $val = '<span style="color:green;">' . esc_html__('Verified', 'listingo') . '</span>';
                }
                return $val;
                break;
            case 'status' :
                $activation_status = get_user_meta($user_id, 'activation_status', true);
                $val = '<span style="color:red;">' . esc_html__('Deactivated', 'listingo') . '</span>';
                if (isset($activation_status) && $activation_status === 'active') {
                    $val = '<span style="color:green;">' . esc_html__('Active', 'listingo') . '</span>';
                }
                return $val;
                break;
            case 'type' :
                return $user_type;
                break;
			case 'withdrawal' :
				if ( $user->roles[0] === 'business' || $user->roles[0] === 'professional') {
					if( isset( $bk_settings['type'] ) && $bk_settings['type'] === 'woo'
						&& isset( $bk_settings['hide_wallet'] ) && $bk_settings['hide_wallet'] === 'no' ){
						return  '<a class="button tips view view-withdrawal-detail cus-open-modal" data-id="'.$user_id.'" data-target="#cus-order-modal-'.$user_id.'" href="javascript:;">' . esc_html__('Withdrawal', 'listingo') . '</a>

						<div class="cus-modal" id="cus-order-modal-'.$user_id.'">
							<div class="cus-modal-dialog">
								<div class="cus-modal-content">
									<div class="cus-modal-header">
										<a href="javascript:;" data-target="#cus-order-modal-'.$user_id.'" class="cus-close-modal">×</a>
										<h4 class="cus-modal-title">'.esc_html__('Process Withdrawal','listingo').'</h4>
									</div>
									<div class="cus-modal-body">
										<div class="cus-form withdrawal-change-settings">
											<div class="edit-withdrawal-wrap">

											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						';
					}
				}
                break;
            default:
        }
    }

}

/**
 * @User Row action
 * @return 
 */
if (!function_exists('listingo_user_user_table_action_links')) {
    add_filter('user_row_actions', 'listingo_user_user_table_action_links', 10, 2);

    function listingo_user_user_table_action_links($actions, $user) {
        $is_approved = get_user_meta($user->ID, 'verify_user', true);
		$is_active = get_user_meta($user->ID, 'activation_status', true);

        $actions['listingo_activation_status'] = "<a style='color:" . ((isset($is_active) && $is_active === 'deactive') ? 'green' : 'red') . "' href='" . esc_url(admin_url("users.php?action=listingo_change_activation_status&users=" . $user->ID . "&nonce=" . wp_create_nonce('listingo_change_activation_status_' . $user->ID))) . "'>" . ((isset($is_active) && $is_active === 'deactive') ? esc_html__('Activate', 'listingo') : esc_html__('Deactivate', 'listingo')) . "</a>";
		
		$actions['listingo_status'] = "<a style='color:" . ((isset($is_approved) && $is_approved === 'off') ? 'green' : 'red') . "' href='" . esc_url(admin_url("users.php?action=listingo_change_status&users=" . $user->ID . "&nonce=" . wp_create_nonce('listingo_change_status_' . $user->ID))) . "'>" . ((isset($is_approved) && $is_approved === 'off') ? esc_html__('Verify', 'listingo') : esc_html__('Unverify', 'listingo')) . "</a>";
        return $actions;
    }

}

/**
 * @Admmin notices
 * @return 
 */
if (!function_exists('listingo_user_change_status_notices')) {
    add_action('admin_notices', 'listingo_user_change_status_notices');

    function listingo_user_change_status_notices() {
        global $pagenow;
        if ($pagenow == 'users.php') {
            if (isset($_REQUEST['updated'])) {
                $message = $_REQUEST['updated'];
                if ($message == 'listingo_false') {
                    print '<div class="updated notice error is-dismissible"><p>' . esc_html__('Something wrong. Please try again.', 'listingo') . '</p><button class="notice-dismiss" type="button"><span class="screen-reader-text">' . esc_html__('Dismiss this notice.', 'listingo') . '</span></button></div>';
                }
                if ($message == 'approved') {
                    print '<div class="updated notice is-dismissible"><p>' . esc_html__('User approved.', 'listingo') . '</p><button class="notice-dismiss" type="button"><span class="screen-reader-text">' . esc_html__('Dismiss this notice.', 'listingo') . '</span></button></div>';
                }
                if ($message == 'unapproved') {
                    print '<div class="updated notice is-dismissible"><p>' . esc_html__('User unapproved.', 'listingo') . '</p><button class="notice-dismiss" type="button"><span class="screen-reader-text">' . esc_html__('Dismiss this notice.', 'listingo') . '</span></button></div>';
                }
            }
        }
    }

}



/* * ***************************************************
 * Search Fields
 *
 * *************************************************** */

/**
 * @get search by keyword feild
 * @return html
 */
if (!function_exists('listingo_get_search_keyword')) {

    function listingo_get_search_keyword() {
        ob_start();
        $keyword = !empty($_GET['keyword']) ? esc_attr($_GET['keyword']) : '';
        ?>
        <input type="text" name="keyword" value="<?php echo esc_attr($keyword); ?>" class="form-control" placeholder="<?php esc_html_e('Keyword', 'listingo'); ?>">
        <?php
        echo ob_get_clean();
    }

    add_action('listingo_get_search_keyword', 'listingo_get_search_keyword');
}

/**
 * @get search by zip feild
 * @return html
 */
if (!function_exists('listingo_get_search_zip')) {

    function listingo_get_search_zip() {
        ob_start();
        $zip = !empty($_GET['zip']) ? esc_attr($_GET['zip']) : '';
        ?>
        <input type="text" name="zip" value="<?php echo esc_attr($zip); ?>" class="form-control" placeholder="<?php esc_html_e('Zip code', 'listingo'); ?>">
        <?php
        echo ob_get_clean();
    }

    add_action('listingo_get_search_zip', 'listingo_get_search_zip');
}

/**
 * @get search by geo location feild
 * @return html
 */
if (!function_exists('listingo_get_search_geolocation')) {

    function listingo_get_search_geolocation() {
        ob_start();
        if (function_exists('fw_get_db_settings_option')) {
            $dir_radius = fw_get_db_settings_option('dir_radius');
            $dir_default_radius = fw_get_db_settings_option('dir_default_radius');
            $dir_max_radius = fw_get_db_settings_option('dir_max_radius');
            $dir_distance_type = fw_get_db_settings_option('dir_distance_type');
            $dir_longitude = fw_get_db_settings_option('dir_longitude');
            $dir_latitude = fw_get_db_settings_option('dir_latitude');
        } else {
            $dir_radius = '';
            $dir_radius = '';
            $dir_default_radius = 50;
            $dir_max_radius = 300;
            $dir_distance_type = 'mi';
        }

        $dir_longitude = !empty($dir_longitude) ? $dir_longitude : '-0.1262362';
        $dir_latitude = !empty($dir_latitude) ? $dir_latitude : '51.5001524';
        $dir_default_radius = !empty($dir_default_radius) ? $dir_default_radius : 50;
        $dir_max_radius = !empty($dir_max_radius) ? $dir_max_radius : 300;
        $dir_distance_type = !empty($dir_distance_type) ? $dir_distance_type : 'mi';
		
		$lat 	= !empty( $_GET['lat'] ) ? $_GET['lat'] : '';
		$long 	= !empty( $_GET['long'] ) ? $_GET['long'] : '';

        $location = '';
        if (isset($_GET['geo']) && !empty($_GET['geo'])) {
            $location = $_GET['geo'];
        }

        $distance = $dir_default_radius;
        if (  !empty($_GET['geo_distance']) ) {
            $distance = $_GET['geo_distance'];
        }

        $distance_title = esc_html__('( Miles )', 'listingo');
        if ($dir_distance_type === 'km') {
            $distance_title = esc_html__('( Kilometers )', 'listingo');
        }
        ?>
        <div class="locate-me-wrap">
            <div id="location-pickr-map" class="elm-display-none"></div>
            <input type="text"  autocomplete="on" id="location-address" value="<?php echo esc_attr($location); ?>" name="geo" placeholder="<?php esc_html_e('Geo location', 'listingo'); ?>" class="form-control">
            <?php if (isset($dir_radius) && $dir_radius === 'enable') { ?>
                <a href="javascript:;" class="geolocate"><i class="fa fa-crosshairs geo-locate-me"></i></a>
                <a href="javascript:;" class="geodistance"><i class="fa fa-angle-down" aria-hidden="true"></i></a>
                <div class="geodistance_range elm-display-none">
                    <div class="distance-ml"><?php esc_html_e('Distance in', 'listingo'); ?>&nbsp;<?php echo esc_attr($distance_title); ?>&nbsp;<span><?php echo esc_js($distance); ?></span></div>
                    <input type="hidden" class="geo_distance" name="geo_distance" value="<?php echo esc_js($distance); ?>" />
                    <input type="hidden" id="location-latitude" name="lat" value="<?php echo esc_attr( $lat );?>" />
                    <input type="hidden" id="location-longitude" name="long" value="<?php echo esc_attr( $long );?>" />
                    <div class="geo_distance" id="geo_distance"></div>
                </div>
            <?php } ?>
            <?php
            $script = "jQuery.listingo_init_map(" . esc_js($dir_latitude) . "," . esc_js($dir_longitude) . ");";
            wp_add_inline_script('listingo_maps', $script, 'after');

            $geo_distance = 'jQuery( "#geo_distance" ).slider({
				   range: "min",
				   min:1,
				   max:' . esc_js($dir_max_radius) . ',
				   value:' . esc_js($distance) . ',
				   animate:"slow",
				   orientation: "horizontal",
				   slide: function( event, ui ) {
					  jQuery( ".distance-ml span" ).html( ui.value );
					  jQuery( ".geo_distance" ).val( ui.value );
				   }	
				});';
            wp_add_inline_script('listingo_maps', $geo_distance, 'after');
            ?>
        </div>
        <?php
        echo ob_get_clean();
    }

    add_action('listingo_get_search_geolocation', 'listingo_get_search_geolocation');
}


/**
 * @get search by category feild
 * @return html
 */
if (!function_exists('listingo_get_search_category')) {

    function listingo_get_search_category() {
		global $wp_query;
        ob_start();
		
		if( !class_exists( 'ListingoGlobalSettings' ) ) {return;}
		
        $args = array('posts_per_page' => '-1',
            'post_type' => 'sp_categories',
            'post_status' => 'publish',
            'suppress_filters' => false
        );
        $cust_query = get_posts($args);

		if( is_singular('sp_categories') ){
			$category = $wp_query->get_queried_object();
			if( !empty( $category->post_name ) ){
				$category = $category->post_name;
			}
		} else{
			$category = !empty($_GET['category']) ? esc_attr($_GET['category']) : '';
		}
		
        ?>
        <span class="tg-select sp-current-category">
            <select name="category" class="sp-caetgory-select">
                <option value=""><?php esc_html_e('Category', 'listingo'); ?></option>
                <?php
                if (!empty($cust_query)) {
                    $counter = 0;
                    $json = array();
                    foreach ($cust_query as $key => $dir) {
                        $selected = '';
                        if ($dir->post_name === $category) {
                            $selected = 'selected';
                        }

                        if (isset($dir->ID)) {
                            $sub_categories = wp_get_post_terms($dir->ID, 'sub_category', array("fields" => "all"));

                            $subarray = array();
                            if (!empty($sub_categories)) {
                                foreach ($sub_categories as $key => $sub_category) {
                                    if (!empty($sub_category)) {
                                        $subarray[$sub_category->slug] = htmlspecialchars_decode( $sub_category->name );
                                    }
                                }
                            }

                            $json[$dir->ID] = $subarray;
                        }

                        $categories['categories'] = $json;

                        echo '<option data-icon="fa fa-download" ' . $selected . ' data-id="' . $dir->ID . '" data-title="' . $dir->post_title . '"  value="' . $dir->post_name . '">' . $dir->post_title . '</option>';
                    }
                }
                ?>

            </select>
            <?php
            wp_add_inline_script('listingo_callbacks', "
					var SP_Editor = {};
					SP_Editor.elements = {};
					window.SP_Editor = SP_Editor;
					SP_Editor.elements = jQuery.parseJSON( '" . addslashes(json_encode($categories['categories'])) . "' );
				", 'after');
            ?>
            <script type="text/template" id="tmpl-load-subcategories">
                <#if( !_.isEmpty(data['childrens']) ) {#>
                <h4><?php esc_html_e('Filter By Sub Categories', 'listingo'); ?></h4>
                <div class="tg-checkboxgroup data-list">
                <#
                var _option	= '';
                var browser_cats = ListingoGetUrlParameter('sub_categories[]','yes');
                _.each( data['childrens'] , function(element, index, attr) {
                var _checked	= '';
                if(jQuery.inArray(index,browser_cats) !== -1){
                var _checked	= 'checked';
                }
                #>
                <div class="tg-checkbox sp-load-item">
                <input type="checkbox" name="sub_categories[]" {{_checked}} value="{{index}}" id="sub_categories-{{index}}">
                <label for="sub_categories-{{index}}">{{element}}</label>
                </div>
                <#	
                });
                #>
                </div>
                <a href="javascript:;" class="sp-loadMore"><?php esc_html_e('Load More', 'listingo'); ?></a>
                <# } #>
            </script>  
        </span>
        <?php
        echo ob_get_clean();
    }

    add_action('listingo_get_search_category', 'listingo_get_search_category');
}


/**
 * @get search by sub category feild
 * @return html
 */
if (!function_exists('listingo_get_search_sub_category')) {

    function listingo_get_search_sub_category() {
		global $wp_query;
        ob_start();
        ?>
        <span class="tg-select sub-cat-dp">
            <select name="sub_category[]" class="subcategories-wrap">
                <option value=""><?php esc_html_e('Sub Category', 'listingo'); ?></option>
            </select>
            <script type="text/template" id="tmpl-load-subcategories-dd">
                <#if( !_.isEmpty(data['childrens']) ) {
                var _option	= '';
                var browser_cats = ListingoGetUrlParameter('sub_categories[]','yes');
                _.each( data['childrens'] , function(element, index, attr) {
					#>
						<option value="{{index}}">{{element}}</option>
					<#	
					});
                } #>
            </script>  
        </span>
        <?php
        echo ob_get_clean();
    }

    add_action('listingo_get_search_sub_category', 'listingo_get_search_sub_category');
}

/**
 * @get search page map
 * @return html
 */
if (!function_exists('listingo_get_search_toggle_map')) {

    function listingo_get_search_toggle_map() {
		$dir_map_scroll = 'false';
		if (function_exists('fw_get_db_settings_option')) {
			$dir_map_scroll = fw_get_db_settings_option('dir_map_scroll');
		}

		$dir_map_scroll	= isset( $dir_map_scroll ) && $dir_map_scroll === 'true' ? 'unlock' : 'lock';

        ob_start();
        ?>
        <div id="sp-search-map" class="tg-map"></div>
        <div class="tg-mapcontrols">
            <span id="doc-mapplus"><i class="fa fa-plus"></i></span>
            <span id="doc-mapminus"><i class="fa fa-minus"></i></span>
            <span id="doc-lock"><i class="fa fa-<?php echo esc_attr( $dir_map_scroll );?>"></i></span>
        </div>
        <a id="tg-btnmapview" class="tg-btn tg-btnmapview" href="#">
            <span><?php esc_html_e('Map View', 'listingo'); ?></span>
            <i class="lnr lnr-chevron-down"></i>
        </a>
        <?php
        echo ob_get_clean();
    }

    add_action('listingo_get_search_toggle_map', 'listingo_get_search_toggle_map');
}

/**
 * @get search by category feild
 * @return html
 */
if (!function_exists('listingo_get_search_map_right')) {
	function listingo_get_search_map_right() {
		ob_start();
		$dir_map_lock	= 'fa-lock';
		if (function_exists('fw_get_db_settings_option')) {
            $dir_map_scroll = fw_get_db_settings_option('dir_map_scroll');
			$dir_map_lock	= !empty( $dir_map_scroll ) && $dir_map_scroll == 'true' ? 'fa-unlock' : 'fa-lock';
		}
		
		?>
		<div id="tg-mapclustring" class="tg-mapclustring">
			<div class="tg-mapclustringholder">
				<div id="sp-search-map" class="tg-map"></div>
				<div class="tg-mapcontrols">
					<span id="doc-mapplus"><i class="fa fa-plus"></i></span>
					<span id="doc-mapminus"><i class="fa fa-minus"></i></span>
					<span id="doc-lock"><i class="fa <?php echo esc_attr( $dir_map_lock );?>"></i></span>
				</div>
			</div>
		</div>
		<?php
		echo ob_get_clean();
	}
	add_action('listingo_get_search_map_right', 'listingo_get_search_map_right');
}


/**
 * @get sortby field
 * @return html
 */
if (!function_exists('listingo_get_sortby')) {

    function listingo_get_sortby() {
        ob_start();
        ?>
        <div class="tg-select">
            <select name="sortby" class="sp-sortby">
                <option value=""><?php esc_html_e('Sort By', 'listingo'); ?></option>
                <option value="recent" <?php echo isset($_GET['sortby']) && $_GET['sortby'] == 'recent' ? 'selected' : ''; ?>><?php esc_html_e('Most recent', 'listingo'); ?></option>
                <?php /*?><option value="featured" <?php echo isset($_GET['sortby']) && $_GET['sortby'] == 'featured' ? 'selected' : ''; ?>><?php esc_html_e('Featured', 'listingo'); ?></option><?php */?>
                <option value="distance" <?php echo isset($_GET['sortby']) && $_GET['sortby'] == 'distance' ? 'selected' : ''; ?>><?php esc_html_e('Distance', 'listingo'); ?></option>
            </select>
        </div>
        <?php
        echo ob_get_clean();
    }

    add_action('listingo_get_sortby', 'listingo_get_sortby');
}

/**
 * @get orderby field
 * @return html
 */
if (!function_exists('listingo_get_orderby')) {

    function listingo_get_orderby() {
        ob_start();
        ?>
        <div class="tg-select">
            <select name="orderby" class="sp-orderby">
                <option value=""><?php esc_html_e('Order By', 'listingo'); ?></option>
                <option value="DESC"  <?php echo isset($_GET['orderby']) && $_GET['orderby'] == 'DESC' ? 'selected' : ''; ?>><?php esc_html_e('DESC', 'listingo'); ?></option>
                <option value="ASC"  <?php echo isset($_GET['orderby']) && $_GET['orderby'] == 'ASC' ? 'selected' : ''; ?>><?php esc_html_e('ASC', 'listingo'); ?></option>
            </select>
        </div>
        <?php
        echo ob_get_clean();
    }

    add_action('listingo_get_orderby', 'listingo_get_orderby');
}

/**
 * @get show number of posts field
 * @return html
 */
if (!function_exists('listingo_get_showposts')) {

    function listingo_get_showposts() {
        ob_start();
        ?>
        <div class="tg-select">
            <select name="showposts" class="sp-showposts">
                <option value=""><?php esc_html_e('Show Posts', 'listingo'); ?></option>
                <option value="10" <?php echo isset($_GET['showposts']) && $_GET['showposts'] == 10 ? 'selected' : ''; ?>><?php esc_html_e('10', 'listingo'); ?></option>
                <option value="20" <?php echo isset($_GET['showposts']) && $_GET['showposts'] == 20 ? 'selected' : ''; ?>><?php esc_html_e('20', 'listingo'); ?></option>
                <option value="50" <?php echo isset($_GET['showposts']) && $_GET['showposts'] == 50 ? 'selected' : ''; ?>><?php esc_html_e('50', 'listingo'); ?></option>
                <option value="100" <?php echo isset($_GET['showposts']) && $_GET['showposts'] == 100 ? 'selected' : ''; ?>><?php esc_html_e('100', 'listingo'); ?></option>
            </select>
        </div>
        <?php
        echo ob_get_clean();
    }

    add_action('listingo_get_showposts', 'listingo_get_showposts');
}

/**
 * @get pending and approved appointments
 * @return html
 */
if (!function_exists('listingo_get_appointment_status')) {

    function listingo_get_appointment_status() {
        ob_start();
        ?>
        <div class="tg-select">
            <select name="appointment_status" class="sp-appointment-status">
                <option value=""><?php esc_html_e('Choose Status', 'listingo'); ?></option>
                <option value="publish" <?php echo isset($_GET['appointment_status']) && $_GET['appointment_status'] == 'publish' ? 'selected' : ''; ?>><?php esc_html_e('Approved', 'listingo'); ?></option>
                <option value="pending" <?php echo isset($_GET['appointment_status']) && $_GET['appointment_status'] == 'pending' ? 'selected' : ''; ?>><?php esc_html_e('Pending', 'listingo'); ?></option>
                <option value="trash" <?php echo isset($_GET['appointment_status']) && $_GET['appointment_status'] == 'trash' ? 'selected' : ''; ?>><?php esc_html_e('Trash', 'listingo'); ?></option>
            </select>
        </div>
        <?php
        echo ob_get_clean();
    }

    add_action('listingo_get_appointment_status', 'listingo_get_appointment_status');
}

/**
 * @get search filters
 * @return html
 */
if (!function_exists('listingo_get_search_filtrs')) {

    function listingo_get_search_filtrs() {
		global $wp_query;
        $languages_list = listingo_get_taxonomy_array('languages');
        $insurance_list = listingo_get_taxonomy_array('insurance');
        
        $appointment = !empty($_GET['appointment']) ? $_GET['appointment'] : '';
        $photo = !empty($_GET['photo']) ? $_GET['photo'] : '';
		
		//Country search
		if ( is_tax('countries') ) {
			$sub_cat = $wp_query->get_queried_object();
			if( !empty( $sub_cat->slug ) ){
				$country =	$sub_cat->slug;
			}
		} else{
			$country = !empty($_GET['country']) ? esc_attr( $_GET['country'] ) : '';
		}

		//city search
		if ( is_tax('cities') ) {
			$sub_cat = $wp_query->get_queried_object();
			if( !empty( $sub_cat->slug ) ){
				$city =	$sub_cat->slug;
			}
		} else{
			$city = !empty($_GET['city']) ? esc_attr( $_GET['city'] ) : '';
		}
		
		//insurance search
		if (!empty($_GET['insurance'])) {
		   $get_insurance = !empty($_GET['insurance']) ? $_GET['insurance'] : '';
		} else {
			if ( is_tax('insurance') ) {
				$sub_cat = $wp_query->get_queried_object();
				if( !empty( $sub_cat->slug ) ){
					$get_insurance = array( $sub_cat->slug );
				}
			} else{
				$get_insurance = array();
			}
		}
		
		//languages search
		if (!empty($_GET['languages'])) {
		   $get_languages = !empty($_GET['languages']) ? $_GET['languages'] : '';
		} else {
			if ( is_tax('languages') ) {
				$sub_cat = $wp_query->get_queried_object();
				if( !empty( $sub_cat->slug ) ){
					$get_languages = array( $sub_cat->slug );
				}
			} else{
				$get_languages = array();
			}
		}
		
		//hide/show some filters
		if (function_exists('fw_get_db_settings_option')) {
            $zip_search = fw_get_db_settings_option('zip_search');
            $misc_search = fw_get_db_settings_option('misc_search');
			$apt_search = fw_get_db_settings_option('apt_search');
			$dir_search_insurance = fw_get_db_settings_option('dir_search_insurance');
			$language_search = fw_get_db_settings_option('language_search');
			$country_cities = fw_get_db_settings_option('country_cities');
			$dir_gender = fw_get_db_settings_option('dir_gender');
			$dir_radius = fw_get_db_settings_option('dir_radius');
			$dir_location = fw_get_db_settings_option('dir_location');
			$dir_keywords = fw_get_db_settings_option('dir_keywords');
        } else {
            $dir_radius = '';
            $dir_location = '';
			$dir_keywords = '';
			$dir_gender = '';
			$misc_search = '';
			$zip_search = '';
			$dir_search_insurance = '';
            $language_search = '';
			$country_cities = '';
			$apt_search = '';
        }
		
        ob_start();
        ?>

        <fieldset class="subcat-search-wrap"></fieldset>
        <?php if (!empty($zip_search) && $zip_search === 'enable') { ?>
			<fieldset>
				<h4><?php esc_html_e('Filter By zip', 'listingo'); ?></h4>
				<div class="tg-checkboxgroup">
					<?php do_action('listingo_get_search_zip');?>
				</div>
			</fieldset>
        <?php } ?>
        <?php if (!empty($country_cities) && $country_cities === 'enable') { ?>
			<fieldset>
				<h4><?php esc_html_e('Filter By Location', 'listingo'); ?></h4>
				<div class="tg-checkboxgroup">
					<div class="form-group">
						<span class="tg-select">
							<select name="country" class="sp-country-select">
								<option value=""><?php esc_html_e('Choose Country', 'listingo'); ?></option>
								<?php listingo_get_term_options($country, 'countries'); ?>
							</select>
						</span>
					</div>
					<div class="form-group">
						<span class="tg-select">
							<select name="city" class="sp-city-select">
								<option value=""><?php esc_html_e('Choose city', 'listingo'); ?></option>
								<?php
									if (!empty($country)) {
										$country = esc_attr($country);
										$args = array(
											'hide_empty' => false,
											'meta_key'   => 'country',
											'meta_value' => $country
										);
										$terms = get_terms('cities', $args);
										if (!empty($terms)) {
											foreach ($terms as $key => $term) {
												$selected = '';
												if ( $city === $term->slug ) {
													$selected = 'selected';
												}
												echo '<option ' . esc_attr($selected) . ' value="' . esc_attr($term->slug) . '">' . esc_attr($term->name) . '</option>';
											}
										}
									}
								 ?>
							</select>
						</span>
					</div>
				</div>
			</fieldset>
        <?php } ?>
        <?php if (!empty($dir_gender) && $dir_gender === 'enable') { ?>
			<fieldset>
				<h4><?php esc_html_e('Filter By Gender', 'listingo'); ?></h4>
				<div class="tg-checkboxgroup">
					<div class="form-group">
						<span class="tg-select">
							<select name="gender">
								<option value=""><?php esc_html_e('All', 'listingo'); ?></option>
								<option value="male" <?php echo isset($_GET['gender']) && $_GET['gender'] === 'male' ? 'selected' : '';?>><?php esc_html_e('Male', 'listingo'); ?></option>
								<option value="female" <?php echo isset($_GET['gender']) && $_GET['gender'] === 'female' ? 'selected' : '';?>><?php esc_html_e('Female', 'listingo'); ?></option>
							</select>
						</span>
					</div>
				</div>
			</fieldset>
        <?php } ?>
        <?php if (!empty($language_search) && $language_search === 'enable') { ?>
			<?php if (!empty($languages_list)) { ?>
				<fieldset class="lang-search-wrap">
					<h4><?php esc_html_e('Filter By Language', 'listingo'); ?></h4>
					<div class="tg-checkboxgroup data-list">
						<?php
						foreach ($languages_list as $pterm) {
							$checked = '';
							if (!empty($get_languages) && in_array($pterm->slug, $get_languages)) {
								$checked = 'checked';
							}
							?>
							<span class="tg-checkbox sp-load-item">
								<input type="checkbox" <?php echo esc_attr($checked); ?> id="lang-<?php echo esc_attr($pterm->slug); ?>" name="languages[]" value="<?php echo esc_attr($pterm->slug); ?>">
								<label for="lang-<?php echo esc_attr($pterm->slug); ?>"><?php echo esc_attr($pterm->name); ?></label>
							</span>
							<?php
						}
						?>
						<?php if (count($languages_list) > 10) { ?>
							<a href="javascript:;" class="sp-loadMore"><?php esc_html_e('Load More', 'listingo'); ?></a>
						<?php } ?>
					</div>
					<?php
					wp_add_inline_script('listingo_callbacks', "_show_hide_list('.lang-search-wrap');", 'after');
					?>
				</fieldset>
			<?php } ?>
      	<?php } ?>
      	<?php if (!empty($dir_search_insurance) && $dir_search_insurance === 'enable') { ?>
			<?php if (!empty($insurance_list)) { ?>
				<fieldset class="insurance-search-wrap">
					<h4><?php esc_html_e('Filter By Insurance', 'listingo'); ?></h4>
					<div class="tg-checkboxgroup data-list">
						<?php
						foreach ($insurance_list as $pterm) {
							$checked = '';
							if (!empty($get_insurance) && in_array($pterm->slug, $get_insurance)) {
								$checked = 'checked';
							}
							?>
							<span class="tg-checkbox sp-load-item">
								<input type="checkbox" <?php echo esc_attr($checked); ?> id="lang-<?php echo esc_attr($pterm->slug); ?>" name="insurance[]" value="<?php echo esc_attr($pterm->slug); ?>">
								<label for="lang-<?php echo esc_attr($pterm->slug); ?>"><?php echo esc_attr($pterm->name); ?></label>
							</span>
							<?php
						}
						?>
						<?php if (count($insurance_list) > 10) { ?>
							<a href="javascript:;" class="sp-loadMore"><?php esc_html_e('Load More', 'listingo'); ?></a>
						<?php } ?>
					</div>
					<?php wp_add_inline_script('listingo_callbacks', "_show_hide_list('.insurance-search-wrap');", 'after');?>
				</fieldset>
			<?php } ?>
        <?php } ?>
        <?php do_action('listingo_get_custom_filters');?>
        <?php if ( ( !empty($misc_search) && $misc_search === 'enable') || (!empty($apt_search) && $apt_search === 'enable' )) { ?>
        <fieldset>
            <h4><?php esc_html_e('Misc', 'listingo'); ?></h4>
            <div class="tg-checkboxgroup">
                <?php if ( !empty($apt_search) && $apt_search === 'enable') { ?>
                <span class="tg-checkbox">
                    <input type="checkbox" id="onlineappointment" <?php echo isset($appointment) && $appointment === 'true' ? 'checked' : ''; ?> name="appointment" value="true">
                    <label for="onlineappointment"><?php esc_html_e('Online Appointment', 'listingo'); ?></label>
                </span>
                <?php } ?>
                <?php if ( !empty($misc_search) && $misc_search === 'enable') { ?>
                <span class="tg-checkbox">
                    <input type="checkbox" <?php echo isset($photo) && $photo === 'true' ? 'checked' : ''; ?> id="withprofilephoto" name="photo" value="true">
                    <label for="withprofilephoto"><?php esc_html_e('With Profile Photo', 'listingo'); ?></label>
                </span>
                <?php } ?>
            </div>
        </fieldset>
        <?php } ?>
        
        <fieldset>
            <input type="hidden" name="lang" value="<?php echo isset($_GET['lang']) ? esc_attr($_GET['lang']) : ''; ?>">
            <input type="hidden" name="view" value="<?php echo isset($_GET['view']) ? esc_attr($_GET['view']) : ''; ?>">
            <button class="tg-btn" type="submit"><?php esc_html_e('apply', 'listingo'); ?></button>
        </fieldset>
        <?php
        echo ob_get_clean();
    }

    add_action('listingo_get_search_filtrs', 'listingo_get_search_filtrs');
}

/**
 * @get user avatar
 * @return html
 */
if (!function_exists('listingo_result_avatar')) {

    function listingo_result_avatar($user_id, $media_type = 'avatar') {
        ob_start();
        global $current_user;

        $show_category  = '';
		$cat_url		= '';
		$cat_title		= '';
		
        if (function_exists('fw_get_db_settings_option')) {
			$show_category = fw_get_db_settings_option('show_category');
        }

        if (isset($media_type) && $media_type === 'banner') {
            $thumb = apply_filters(
                    'listingo_get_media_filter', listingo_get_user_banner(array('width' => 370, 'height' => 270), $user_id), array('width' => 370, 'height' => 270)
            );
        } else {
            $thumb = apply_filters(
                    'listingo_get_media_filter', listingo_get_user_avatar(array('width' => 370, 'height' => 270), $user_id), array('width' => 370, 'height' => 270)
            );
        }

		$bg_color = '';
		
		if( isset( $show_category ) && $show_category === 'child' ){
			$category_id = get_user_meta($user_id, 'sub_category', true);

			if( !empty( $category_id ) ){
				$sub_category_term = get_term_by('slug', $category_id, 'sub_category');
				if (!empty($sub_category_term)) {
					$cat_title 		= $sub_category_term->name;
					$cat_url		= get_term_link( $sub_category_term->term_id, 'sub_category' );
					
					if (function_exists('fw_get_db_term_option')) {
						$bg_color = fw_get_db_term_option($sub_category_term->term_id, 'sub_category');
						if (!empty($bg_color['subcat_color'])) {
							$bg_color = 'style=background:' . $bg_color['subcat_color'];
						} else{
							$bg_color = '';
						}
					}	
				}
			}
			
		} else{
			$category_id = get_user_meta($user_id, 'category', true);
			if (function_exists('fw_get_db_settings_option')) {
				$bg_color = fw_get_db_post_option($category_id, 'category_color', true);
				if (!empty($bg_color)) {
					$bg_color = 'style=background:' . $bg_color;
				}
			}
			
			$cat_url	= get_permalink($category_id);
			$cat_title	= get_the_title($category_id);
		}

        ?>
        <figure class="tg-featuredimg">
            <a href="<?php echo esc_url(get_author_posts_url($user_id)); ?>"><img class="searched-avatar" src="<?php echo esc_url($thumb); ?>" alt="<?php esc_html_e('Listingo', 'listingo'); ?>"></a>
			<?php if (!empty($category_id)) { ?>
				<a class="tg-themetag tg-categorytag sp-av-link" <?php echo esc_attr($bg_color); ?> href="<?php echo esc_url($cat_url); ?>">
					<?php echo esc_attr($cat_title); ?>
				</a>
			<?php } ?>
			<?php
				if (isset($user_id) && $user_id != $current_user->ID) {
					if( apply_filters('listingo_is_favorite_allowed',$current_user->ID) === true ){
						$wishlist = array();
						$wishlist = get_user_meta($current_user->ID, 'wishlist', true);
						$wishlist = !empty($wishlist) && is_array($wishlist) ? $wishlist : array();

						if (!empty($user_id) && in_array($user_id, $wishlist)) {
							echo '<a class="tg-heart tg-dislike sp-av-linkv2" href="javascript:;"><i class="fa fa-heart"></i></a>';
						} else {
							echo '<a class="tg-heart add-to-fav sp-av-linkv2" data-wl_id="' . $user_id . '" href="javascript:;"><i class="fa fa-heart"></i></a>';
						}
					}
				}
			?> 
        </figure>
        <?php
        echo ob_get_clean();
    }

    add_action('listingo_result_avatar', 'listingo_result_avatar', 10, 2);
}


/**
 * @get user tags(feature/verified)
 * @return html
 */
if (!function_exists('listingo_result_tags')) {

    function listingo_result_tags($user_id, $return_type = 'echo') {
        $verify_user = get_user_meta($user_id, 'verify_user', true);
        $featured_expiry = listingo_get_subscription_meta('subscription_featured_expiry', $user_id);
        $current_string = time();
		
		$tags_styling = '';
		if (function_exists('fw_get_db_settings_option')) {
            $tags_styling = fw_get_db_settings_option('tags_styling', $default_value = null);
        }

		$feature_style	 = '';
		$verified_style	 = '';
		$custom_styling	= 'tags-default-styling';
		
		if (isset($tags_styling['gadget']) && $tags_styling['gadget'] === 'custom') {
			$featured_text = !empty( $tags_styling['custom']['featured_text'] ) ? $tags_styling['custom']['featured_text'] : '#999';
			$featured_bg   = !empty( $tags_styling['custom']['featured_bg'] ) ? $tags_styling['custom']['featured_bg'] : '#FFF';
			$verified_text = !empty( $tags_styling['custom']['verified_text'] ) ? $tags_styling['custom']['verified_text'] : '#999';
			$verified_bg   = !empty( $tags_styling['custom']['verified_bg'] ) ? $tags_styling['custom']['verified_bg'] : '#FFF';
			$feature_style	= "style=background:".$featured_bg.";color:".$featured_text.";border:none;";
			$verified_style	= "style=background:".$verified_bg.";color:".$verified_text.";border:none;";
			$custom_styling	= 'tags-custom-styling';
		}

        ob_start();

        if (( isset($featured_expiry) && $featured_expiry > $current_string ) || (!empty($verify_user) && $verify_user === 'on' )
        ) {
            ?>
            <ul class="tg-tags <?php echo esc_attr( $custom_styling );;?>">
                <?php if (isset($featured_expiry) && $featured_expiry > $current_string) { ?>
                    <li><span <?php echo ( $feature_style );?> class="tg-tag tg-featuredtag"><i class="fa fa-star sp-tags-icon"></i><?php esc_html_e('featured', 'listingo'); ?></span></li>
                <?php } ?>
                <?php if (!empty($verify_user) && $verify_user === 'on') { ?>
                    <li><span <?php echo ( $verified_style );?> class="tg-tag tg-verifiedtag"><i class="fa fa-check sp-tags-icon"></i><?php esc_html_e('verified', 'listingo'); ?></span></li>
                <?php } ?>
            </ul>
            <?php
        }

        if ($return_type === 'return') {
            return ob_get_clean();
        } else {
            echo ob_get_clean();
        }
    }

    add_action('listingo_result_tags', 'listingo_result_tags', 10, 2);
}

/**
 * @Update add to favorites
 * @return 
 */
if (!function_exists('listingo_update_wishlist')) {

    function listingo_update_wishlist() {
        global $current_user;
        $wishlist = array();
        $wishlist = get_user_meta($current_user->ID, 'wishlist', true);
        $wishlist = !empty($wishlist) && is_array($wishlist) ? $wishlist : array();
        $wl_id = sanitize_text_field($_POST['wl_id']);

        if (!empty($wl_id)) {
            $wishlist[] = $wl_id;
            $wishlist = array_unique($wishlist);
            update_user_meta($current_user->ID, 'wishlist', $wishlist);

            $json = array();
            $json['type'] = 'success';
            $json['message'] = esc_html__('Successfully! added to your wishlist', 'listingo');
            echo json_encode($json);
            die();
        }

        $json = array();
        $json['type'] = 'error';
        $json['message'] = esc_html__('Oops! something is going wrong.', 'listingo');
        echo json_encode($json);
        die();
    }

    add_action('wp_ajax_listingo_update_wishlist', 'listingo_update_wishlist');
    add_action('wp_ajax_nopriv_listingo_update_wishlist', 'listingo_update_wishlist');
}

/**
 * @get author slugs
 * @return {}
 */
if ( ! function_exists( 'listingo_get_users_base_slug' ) ) {
	function listingo_get_users_base_slug(){
		$author_slug = 'author'; // change slug name
		
		$author_levels	= array($author_slug);
		$args = array( 'posts_per_page' 	=> '-1', 
					   'post_type' 			=> 'sp_categories', 
					   'post_status' 		=> 'publish',
					   'suppress_filters'   => false
				);
		
		$cust_query = get_posts($args);
		if( isset( $cust_query ) && !empty( $cust_query ) ) {
		  $author_levels	= array('');
		  $counter	= 0;
		  foreach ($cust_query as $key => $dir) {
			 $author_levels[]	= $dir->post_name;
		  }	
		}

		return $author_levels;
	}
}

/**
 * @prepare auhtor slugs
 * @return {}
 */
if ( ! function_exists( 'listingo_prepare_users_base' ) ) {	
	add_action( 'init', 'listingo_prepare_users_base' );
	function listingo_prepare_users_base(){
		global $wp_rewrite;
		$author_levels = listingo_get_users_base_slug();
		// Define the tag and use it in the rewrite rule
		add_rewrite_tag( '%author_level%', '(' . implode( '|', $author_levels ) . ')' );
		$wp_rewrite->author_base = '%author_level%';
		$wp_rewrite->flush_rules();
	}
}

/**
 * @refine author base if username and base matched eg : anything/anything
 * @return {}
 */
if ( ! function_exists( 'listingo_author_rewrite_rules' ) ) {	
	add_filter( 'author_rewrite_rules', 'listingo_author_rewrite_rules' );
	function listingo_author_rewrite_rules( $author_rewrite_rules ){
		foreach ( $author_rewrite_rules as $pattern => $substitution ) {
			if ( FALSE === strpos( $substitution, 'author_name' ) ) {
				unset( $author_rewrite_rules[$pattern] );
			}
		}
		return $author_rewrite_rules;
	}
}

/**
 * @refine author base if username and base matched eg : anything/anything
 * @return {}
 */
if ( ! function_exists( 'listingo_get_user_refined_link' ) ) {
	add_filter( 'author_link', 'listingo_get_user_refined_link', 10, 3 );
	function listingo_get_user_refined_link( $link, $author_id, $author_nicename ){
		$author_level = 'user';
		if ( 1 == $author_id ) {
			//return nothing
		} else {
			$db_category	 = get_user_meta( $author_id, 'category', true);
			if( !empty( $db_category ) ){
				$postdata = get_post($db_category); 
				if( !empty( $postdata->post_name ) ){
					$slug 	 = $postdata->post_name;
					$author_level = $slug;
				} else{
					$slug = 'author';
					$author_slug = $slug; // change slug name
					$author_level = $author_slug;
				}
				
			} else{
				$slug = 'author';
				$author_slug = $slug; // change slug name
				$author_level = $author_slug;
			}
		}
		
		$link = str_replace( '%author_level%', $author_level, $link );
		return $link;
	}
}

/**
 * @get sortby year
 * @return html
 */
if (!function_exists('listingo_get_sortby_year')) {

    function listingo_get_sortby_year($is_plus='no') {
        ob_start();
		$firstYear = 1990;
		$diff = (int)date('Y') - $firstYear;
		$is_plus	= $is_plus == 'yes' ? 5 : 0;
		$lastYear  = $firstYear + $diff + $is_plus;
        ?>
        <div class="tg-select">
            <select name="earning_year" class="sp-sortby">
               <option value=""><?php esc_html_e('Select year', 'listingo'); ?></option>
               <?php 
				for($i=$firstYear;$i<=$lastYear;$i++){
					$selected = !empty($_GET['earning_year']) && $_GET['earning_year'] == $i ? 'selected' : '';

					echo '<option '.$selected.' value='.$i.'>'.$i.'</option>';
				}
               ?>
            </select>
        </div>
        <?php
        echo ob_get_clean();
    }

    add_action('listingo_get_sortby_year', 'listingo_get_sortby_year',10,1);
}

/**
 * @get sortby month
 * @return html
 */
if (!function_exists('listingo_get_sortby_month')) {

    function listingo_get_sortby_month() {
        ob_start();
		$data	= listingo_get_month_array();
        ?>
        <div class="tg-select">
            <select name="earning_month" class="sp-sortby">
               <option value=""><?php esc_html_e('Select month', 'listingo'); ?></option>
               <?php 
				foreach($data as $key => $value){
					$selected = !empty($_GET['earning_month']) && $_GET['earning_month'] == $key ? 'selected' : '';
					echo '<option '.$selected.' value='.$key.'>'.$value.'</option>';
				}
               ?>
            </select>
        </div>
        <?php
        echo ob_get_clean();
    }

    add_action('listingo_get_sortby_month', 'listingo_get_sortby_month');
}

/**
 * @get month array
 * @return html
 */
if (!function_exists('listingo_get_month_array')) {

    function listingo_get_month_array() {
		return array(
			'01'	=> esc_html__('January','listingo'),
			'02'	=> esc_html__('February','listingo'),
			'03'	=> esc_html__('March','listingo'),
			'04'	=> esc_html__('April','listingo'),
			'05'	=> esc_html__('May','listingo'),
			'06'	=> esc_html__('June','listingo'),
			'07'	=> esc_html__('July','listingo'),
			'08'	=> esc_html__('August','listingo'),
			'09'	=> esc_html__('September','listingo'),
			'10'	=> esc_html__('October','listingo'),
			'11'	=> esc_html__('November','listingo'),
			'12'	=> esc_html__('December','listingo'),
		);
	}
}

/**
 * @Update gallery order
 * @return 
 */
if (!function_exists('listingo_update_gallery_order')) {

    function listingo_update_gallery_order() {
        global $current_user;
        $ids = !empty( $_POST['data'] ) ? explode(',',$_POST['data']) : array();
        
		if(!empty( $ids ) ){
			$profile_gallery_meta = get_user_meta($current_user->ID, 'profile_gallery_photos', true);
			$data_array = array();
			$attach_array = array();
			if (!empty($profile_gallery_meta['image_data'])) {
				
				foreach( $ids as $key => $value ){
					$attach_array[$value] = array(
						'full' 			=> $profile_gallery_meta['image_data'][$value]['full'],
						'thumb' 		=> $profile_gallery_meta['image_data'][$value]['thumb'],
						'banner' 		=> $profile_gallery_meta['image_data'][$value]['banner'],
						'image_id' 		=> $value
					);
				}	

				$is_replace = 'no';
				$profile_gallery_meta['image_data'] = $attach_array;
				
				update_user_meta($current_user->ID, 'profile_gallery_photos', $profile_gallery_meta);
			}
			
			$json = array();
			$json['type'] = 'success';
			$json['message'] = esc_html__('Images order updated.', 'listingo');
			echo json_encode($json);
			die();
		}
    }

    add_action('wp_ajax_listingo_update_gallery_order', 'listingo_update_gallery_order');
    add_action('wp_ajax_nopriv_listingo_update_gallery_order', 'listingo_update_gallery_order');
}

/**
 * @get search by keyword feild
 * @return html
 */
if (!function_exists('listingo_make_an_appointment_button')) {

    function listingo_make_an_appointment_button($user_id) {
        ob_start();
		
		if( empty( $user_id ) ){ return;}
        $provider_category	= listingo_get_provider_category($user_id);
		$db_privacy 		= listingo_get_privacy_settings($user_id);
		
		if( apply_filters('listingo_is_feature_allowed', $provider_category, 'appointments') === true
			&& ( isset( $db_privacy['profile_appointment'] ) && $db_privacy['profile_appointment'] === 'on' )
		){
			$title	= esc_html__('Make Appointment', 'listingo');
		} else{
			$title	= esc_html__('View Detail', 'listingo');
		}
        ?>
       	 <a class="tg-btn" href="<?php echo esc_url(get_author_posts_url($user_id)); ?>"><?php echo esc_attr( $title ); ?></a>
        <?php
        echo ob_get_clean();
    }

    add_action('listingo_make_an_appointment_button', 'listingo_make_an_appointment_button',10,1);
}

/**
 * @sort users by featured expiry dte
 * @return html
 */
if (!function_exists('listingo_sort_users_by_featured_expiry')) {
	function listingo_sort_users_by_featured_expiry($a, $b){  //The function to order our authors
	  $today = time();
	  if ($a->subscription_featured_expiry == $b->subscription_featured_expiry) {  //This is where the name of our custom meta key is entered, I named mine "order"
		return 0;  
	  }  
	  return ( $b->subscription_featured_expiry < $today ) ? -1 : 1;  //The actual sorting is done here. Change ">" to "<" to reverse order
	} 
}


/**
 * @Update provider page sorting
 * @return 
 */
if (!function_exists('listingo_update_provider_page_sorting')) {

    function listingo_update_provider_page_sorting() {
        global $current_user;
   		$json = array();
		$user_identity = $current_user->ID;
		
		if (apply_filters('listingo_is_setting_enabled', $user_identity, 'subscription_page_design') === true ){
			update_user_meta($user_identity, 'provider_page_sorting', $_POST['sort']);
			$json['type'] = 'success';
			$json['message'] = esc_html__('Page sorting updated.', 'listingo');
		} else{
			$json['type'] = 'error';
			$json['message'] = esc_html__('Please upgrade/buy package to get this feature.', 'listingo');
		}

        echo json_encode($json);
        die;
	}
	add_action('wp_ajax_listingo_update_provider_page_sorting', 'listingo_update_provider_page_sorting');
    add_action('wp_ajax_nopriv_listingo_update_provider_page_sorting', 'listingo_update_provider_page_sorting');
}


/**
 * @Update provider page sorting
 * @return 
 */
if (!function_exists('listingo_get_profile_sections')) {
	add_filter('listingo_get_profile_sections', 'listingo_get_profile_sections', 10, 2);
    function listingo_get_profile_sections($user_identity,$key) {
		$db_settings = get_user_meta($user_identity, 'provider_page_sorting', true);
		$cotent_list	= listingo_get_sortbale_list($key,$user_identity);
		if( empty( $db_settings ) ){
			$cotent_list	= $cotent_list;
		} else{
			$cotent_list	= listingo_get_final_section_array($db_settings[$key],$cotent_list);
		}
		
		return $cotent_list;
	}
}

/**
 * @Update provider page sorting
 * @return 
 */
if (!function_exists('listingo_get_final_section_array')) {
	function listingo_get_final_section_array($db_setting,$general_settings) {
		$final_settings = array();
		$upgraded_items	= array();
		foreach ($general_settings as $subKey => $subArray) {
			if( empty($db_setting[$subKey]) ){
				$upgraded_items[$subKey]	= $subKey;
			}
		}
		
		$db_setting	= array_merge( $upgraded_items, $db_setting);

		foreach($db_setting as $key => $sortId) {
			foreach ($general_settings as $subKey => $subArray) {
				if ($subKey == $sortId) {
					$final_settings[$subKey] = $general_settings[$subKey];
					break;
				}
			}
		}

		return $final_settings;
	}
}

/**
 * @Activate profile page design
 * @return 
 */
if (!function_exists('listingo_activate_profile_page')) {

    function listingo_activate_profile_page() {
        global $current_user;
		$user_identity = $current_user->ID;
        $json = array();
		
		if (apply_filters('listingo_is_setting_enabled', $user_identity, 'subscription_page_design') === true ){
			update_user_meta($user_identity, 'provider_page_style', $_POST['style']);
			$json['type'] = 'success';
			$json['message'] = esc_html__('Page design options updated.', 'listingo');
		} else{
			$json['type'] = 'error';
			$json['message'] = esc_html__('Please upgrade/buy package to get this feature.', 'listingo');
		}

        echo json_encode($json);
        die;
	}
	add_action('wp_ajax_listingo_activate_profile_page', 'listingo_activate_profile_page');
    add_action('wp_ajax_nopriv_listingo_activate_profile_page', 'listingo_activate_profile_page');
}


/**
 * Check if user is active user
 *
 * @param json
 * @return string
 */
if ( ! function_exists( 'listingo_is_user_active' ) ) {
	function listingo_is_user_active($user_id='') {
		global $current_user, $wp_roles,$userdata,$post;
		$user_identity	= $current_user->ID;
		
		if( isset( $user_id ) && !empty( $user_id ) ) {
			$profile_status 	= get_user_meta($user_id , 'activation_status' , true);
			if( $user_identity == $user_id && $profile_status === 'deactive' ){
				add_action( 'wp_footer', 'listingo_user_profile_status_message' );
			}
		}
	}
	add_action( 'listingo_is_user_active', 'listingo_is_user_active' );
}

/**
 * Check if user is verified user
 *
 * @param json
 * @return string
 */
if ( ! function_exists( 'listingo_is_user_verified' ) ) {
	function listingo_is_user_verified($user_id='') {
		global $current_user, $wp_roles,$userdata,$post;
		$user_identity	= $current_user->ID;
		
		if( isset( $user_id ) && !empty( $user_id ) ) {
			$verify_user = get_user_meta($user_id , 'verify_user' , true);
			
			if( $user_identity == $user_id && ( $verify_user == 'off' || empty( $verify_user ) )
			){
				add_action( 'wp_footer', 'listingo_is_user_verified_message' );
			}
		}
	}
	add_action( 'listingo_is_user_verified', 'listingo_is_user_verified' );
}


/**
 * check how number of photos, banners, videos or gallery photos allowed
 *
 * @param json
 * @return string
 */
if ( ! function_exists( 'listingo_get_allowed_number' ) ) {
	function listingo_get_allowed_number($paykey,$settingkey) {
		global $current_user;
		
		if( empty( $current_user->ID ) ){ return 0; }
		
		$is_pacakge_subscribed	= listingo_get_subscription_meta($paykey,$current_user->ID);
		
		if (function_exists('fw_get_db_settings_option')) {
			$default_number = fw_get_db_settings_option($settingkey, $default_value = null);
		}
		
		if( isset( $is_pacakge_subscribed ) && $is_pacakge_subscribed !='' ){
			return $is_pacakge_subscribed;
		} elseif( isset( $default_number ) && $default_number !='' ){
			return $default_number;
		} else{
			return 0;
		}

	}
	add_filter( 'listingo_get_allowed_number', 'listingo_get_allowed_number',10,2 );
}

/**
 * check if package wise limit of uploads
 *
 * @param json
 * @return string
 */
if ( ! function_exists( 'listingo_do_check_package_limit' ) ) {
	function listingo_do_check_package_limit($type) {
		global $current_user;
		$user_identity = $current_user->ID;
		
		if (!empty($type) && $type === 'profile_gallery') {
			$profile_gallery_meta = get_user_meta($user_identity, 'profile_gallery_photos', true);
			
			$allowed_photos	= apply_filters( 'listingo_get_allowed_number', 'subscription_gallery_photos','sp_gallery_photos' );
			$total_uploads	= 0;
			
			if (!empty($profile_gallery_meta['image_data'])) {
				$total_uploads	= count($profile_gallery_meta['image_data']);
			}
			
			
			if( intval($allowed_photos) === intval($total_uploads) ){
				$ajax_response = array(
					'type' 	  => 'error',
					'message' => esc_html__('Oops! you reached to maximum upload limit. Please upgrade your package or contact to your site administrator.', 'listingo'),
				);
				echo json_encode($ajax_response);
				die;
			}
		} else if (!empty($type) && $type === 'profile_photo') {
			$profile_meta = get_user_meta($user_identity, 'profile_avatar', true);
			
			$allowed_photos	= apply_filters( 'listingo_get_allowed_number', 'subscription_photos_limit','sp_photos_limit' );
		
			$total_uploads	= 0;
			
			if (!empty($profile_meta['image_data'])) {
				$total_uploads	= count($profile_meta['image_data']);
			}

			if( intval($allowed_photos) === intval($total_uploads) ){
				$ajax_response = array(
					'type' 	  => 'error',
					'message' => esc_html__('Oops! you reached to maximum upload limit. Please upgrade your package or contact to your site administrator.', 'listingo'),
				);
				echo json_encode($ajax_response);
				die;
			}
					
		} else if (!empty($type) && $type === 'profile_banner_photo') {
			$profile_banner_meta = get_user_meta($user_identity, 'profile_banner_photos', true);
			$allowed_banners_photos	= apply_filters( 'listingo_get_allowed_number', 'subscription_banners_limit','sp_banners_limit' );
			
			$total_uploads	= 0;
			
			if (!empty($profile_banner_meta['image_data'])) {
				$total_uploads	= count($profile_banner_meta['image_data']);
			}

			if( intval($allowed_banners_photos) === intval($total_uploads) ){
				$ajax_response = array(
					'type' 	  => 'error',
					'message' => esc_html__('Oops! you reached to maximum upload limit. Please upgrade your package or contact to your site administrator.', 'listingo'),
				);
				echo json_encode($ajax_response);
				die;
			}
		}

	}
	add_action( 'listingo_do_check_package_limit', 'listingo_do_check_package_limit',10,1 );
}


/**
 * @Activate profile page design
 * @return 
 */
if (!function_exists('listingo_update_gallery_meta')) {

    function listingo_update_gallery_meta() {
        global $current_user;
		$user_identity = $current_user->ID;
		$json = array();
		
		$id		= !empty( $_POST['id'] ) ? intval( $_POST['id'] ) : '';
		$title	= !empty( $_POST['title'] ) ? esc_attr( $_POST['title'] ) : '';
		$link	= !empty( $_POST['link'] ) ? esc_attr( $_POST['link'] ) : '';
		
		if( empty( $id ) ){
			$json['type'] = 'error';
			$json['message'] = esc_html__('Some error occur, please try again later.', 'listingo');
			echo json_encode($json);
        	die;
		} else if( empty( $title )){
			$json['type'] = 'error';
			$json['message'] = esc_html__('Title is required.', 'listingo');
			echo json_encode($json);
        	die;
		}
		
		$profile_gallery_meta = get_user_meta($user_identity, 'profile_gallery_photos', true);
		
		$current_gallery_meta	= !empty( $profile_gallery_meta['image_data'][$id] ) ? $profile_gallery_meta['image_data'][$id] : array();
		
		$current_gallery_meta['title']	= $title;
		$current_gallery_meta['link']	= $link;
		
		$profile_gallery_meta['image_data'][$id]	= $current_gallery_meta;

		update_user_meta($user_identity, 'profile_gallery_photos', $profile_gallery_meta);
		
		$json['type'] = 'success';
		$json['message'] = esc_html__('Meta has been updated.', 'listingo');

        echo json_encode($json);
        die;
	}
	add_action('wp_ajax_listingo_update_gallery_meta', 'listingo_update_gallery_meta');
    add_action('wp_ajax_nopriv_listingo_update_gallery_meta', 'listingo_update_gallery_meta');
}

/**
 * @update user location
 * @return 
 */
if (!function_exists('listingo_update_user_location')) {
	function listingo_update_user_location(){
		global $wpdb;
		$location	= !empty( $_POST['location'] ) ? $_POST['location'] : '';
		if( !empty( $location ) ){
			$location	= json_decode(stripslashes($location));

			if( !empty( $location ) ){
				$city			= !empty( $location->city ) ? esc_attr( $location->city ) : '';
				$state			= !empty( $location->state ) ? esc_attr( $location->state ) : '';
				$country		= !empty( $location->country ) ? esc_attr( $location->country ) : '';
				$code			= !empty( $location->code ) ? esc_attr( $location->code ) : '';
				$postal_town	= !empty( $location->postal_town ) ? esc_attr( $location->postal_town ) : '';
				
				
				if( !empty($country) && ( !empty($city) || !empty($postal_town) || !empty($state)  ) ){

					$country_term = term_exists( $country, 'countries' ); // array is returned if taxonomy is given

					//add country
					if( $country_term ){
						//do nothing
						$inserted_country	= $country_term['term_id'];
					} else{
						wp_insert_term( $country, 'countries', array( 'slug' => sanitize_title($country) ));
						$inserted_country	= $wpdb->insert_id;
					}

					//Add City
					if( !empty($city) ){
						$city_term = term_exists( $city, 'cities' ); // array is returned if taxonomy is given
						if( $city_term ){
							//do nothing
							$inserted_city	= $city_term['term_id'];
						} else{
							wp_insert_term( $city, 'cities', array( 'slug' => sanitize_title($city) ));
							$inserted_city	= $wpdb->insert_id;
							
							$country_object   	 = get_term( $inserted_country, 'countries' );
							add_term_meta($inserted_city, 'country', $country_object->slug, true);
							$new_values['country'][0] = $country_object->term_id;
							fw_set_db_term_option($inserted_city, 'cities', null, $new_values);
							
						}

					} else if(!empty($postal_town)){
						$city_term = term_exists( $postal_town, 'cities' ); // array is returned if taxonomy is given
						
						if( $city_term ){
							//do nothing
							$inserted_city	= $city_term['term_id'];
						} else{
							wp_insert_term( $postal_town, 'cities', array( 'slug' => sanitize_title($postal_town) ));
							$inserted_city	= $wpdb->insert_id;
							
							
							$country_object   	 = get_term( $inserted_country, 'countries' );
							add_term_meta($inserted_city, 'country', $country_object->slug, true);
							$new_values['country'][0] = $country_object->term_id;
							fw_set_db_term_option($inserted_city, 'cities', null, $new_values);
						}
					}  else if(!empty($state)){
						$city_term = term_exists( $state, 'cities' ); // array is returned if taxonomy is given
						
						if( $city_term ){
							//do nothing
							$inserted_city	= $city_term['term_id'];
						} else{
							wp_insert_term( $state, 'cities', array( 'slug' => sanitize_title($state) ));
							$inserted_city	= $wpdb->insert_id;
							
							
							$country_object   	 = get_term( $inserted_country, 'countries' );
							add_term_meta($inserted_city, 'country', $country_object->slug, true);
							$new_values['country'][0] = $country_object->term_id;
							fw_set_db_term_option($inserted_city, 'cities', null, $new_values);
						}
					}

					$country_object   	 = get_term( $inserted_country, 'countries' );
					$city_object 		 = get_term( $inserted_city, 'cities' );
					
					$json['countries'] 	 = listingo_get_term_options($country_object->slug, 'countries','return');
					$json['cities'] 	 = apply_filters('listingo_get_cities_by_country',$country_object->slug,$city_object->slug);
					$json['type'] 	 	 = 'success';
					$json['country_slug'] 	 = $country_object->slug;
					$json['city_slug'] 	 	 = $city_object->slug;
					$json['message'] 	 	 = esc_html__('Location added.', 'listingo');

					echo json_encode($json);
					die;
					
				}

			}

		}
	}

	add_action('wp_ajax_listingo_update_user_location', 'listingo_update_user_location');
    add_action('wp_ajax_nopriv_listingo_update_user_location', 'listingo_update_user_location');
}

/**
 * @update user location
 * @return 
 */
if (!function_exists('listingo_is_action_allow')) {
	function listingo_is_action_allow(){
		global $current_user;
		$user_identity = $current_user->ID;
		$json	= array();
		
		$activation_status = get_user_meta($user_identity, 'activation_status', true);
		$verify_user 	   = get_user_meta($user_identity, 'verify_user', true);
		
		if( isset( $activation_status ) && ( $activation_status === 'deactive' || $activation_status === '' )  ){
			$json['type'] 	 	 	 = 'error';
			$json['message'] 	 	 = esc_html__('You are not allowed to do this action. Please activate your account or contact to site administrator.', 'listingo');

			echo json_encode($json);
			die;
		} elseif( isset( $verify_user ) && ( $verify_user === 'off' || $verify_user === '' )  ){
			$json['type'] 	 	 	 = 'error';
			$json['message'] 	 	 = esc_html__('You are not a verified user, please contact to site administrator to get verify, then you will be able do this action.', 'listingo');

			echo json_encode($json);
			die;
		} else{
			//do nothing just process request
		}
	}
	add_action('listingo_is_action_allow', 'listingo_is_action_allow');
}

/**
 * @social media list
 * @return 
 */
if (!function_exists('listingo_get_social_media_icons_list')) {
    function listingo_get_social_media_icons_list($custom_list) {
        $list	= array(
			'facebook'	=> array(
				'title' 		=> esc_html__('Facebook Link?', 'listingo'),
				'placeholder' 		=> esc_html__('Facebook Link', 'listingo'),
				'is_url'   		=> true,
				'icon'			=> 'fa fa-facebook',
				'classses'		=> 'tg-facebook',
				'color'			=> '#3b5998',
			),
			'twitter'	=> array(
				'title' 	=> esc_html__('Twitter Link?', 'listingo'),
				'placeholder' 	=> esc_html__('Twitter Link', 'listingo'),
				'is_url'   	=> true,
				'icon'		=> 'fa fa-twitter',
				'classses'		=> 'tg-twitter',
				'color'			=> '#55acee',
			),
			'linkedin'	=> array(
				'title' 	=> esc_html__('Linkedin Link?', 'listingo'),
				'placeholder' 	=> esc_html__('Linkedin Link', 'listingo'),
				'is_url'   	=> true,
				'icon'		=> 'fa fa-linkedin',
				'classses'		=> 'tg-linkedin',
				'color'			=> '#0177b5',
			),
			'skype'	=> array(
				'title' 	=> esc_html__('Skype ID?', 'listingo'),
				'placeholder' 	=> esc_html__('Skype ID', 'listingo'),
				'is_url'   	=> false,
				'icon'		=> 'fa fa-skype',
				'classses'		=> 'tg-skype',
				'color'			=> '#00aff0',
			),
			'googleplus'	=> array(
				'title' 	=> esc_html__('Google Plus Link?', 'listingo'),
				'placeholder' 	=> esc_html__('Google Plus Link', 'listingo'),
				'is_url'   	=> true,
				'icon'		=> 'fa fa-google-plus',
				'classses'		=> 'tg-googleplus',
				'color'			=> '#dc4a38',
			),
			'pinterest'	=> array(
				'title' 	=> esc_html__('Pinterest Link?', 'listingo'),
				'placeholder' 	=> esc_html__('Pinterest Link', 'listingo'),
				'is_url'   	=> true,
				'icon'		=> 'fa fa-pinterest-p',
				'classses'		=> 'tg-pinterestp',
				'color'			=> '#bd081c',
			),
			'tumblr'	=> array(
				'title' 	=> esc_html__('Tumblr Link?', 'listingo'),
				'placeholder' 	=> esc_html__('Tumblr Link', 'listingo'),
				'is_url'   	=> true,
				'icon'		=> 'fa fa-tumblr',
				'classses'		=> 'tg-tumblr',
				'color'			=> '#36465d',
			),
			'instagram'	=> array(
				'title' 	=> esc_html__('Instagram Link?', 'listingo'),
				'placeholder' 	=> esc_html__('Instagram Link', 'listingo'),
				'is_url'   	=> true,
				'icon'		=> 'fa fa-instagram',
				'classses'		=> 'tg-instagram',
				'color'			=> '#c53081',
			),
			'flickr'	=> array(
				'title' 	=> esc_html__('Flickr Link?', 'listingo'),
				'placeholder' 	=> esc_html__('Flickr Link', 'listingo'),
				'is_url'   	=> true,
				'icon'		=> 'fa fa-flickr',
				'classses'		=> 'tg-flickr',
				'color'			=> '#ff0084',
			),
			'medium'	=> array(
				'title' 	=> esc_html__('Medium Link?', 'listingo'),
				'placeholder' 	=> esc_html__('Medium Link', 'listingo'),
				'is_url'   	=> true,
				'icon'		=> 'fa fa-medium',
				'classses'		=> 'tg-medium',
				'color'			=> '#02b875',
			),
			'tripadvisor'	=> array(
				'title' 	=> esc_html__('Tripadvisor Link?', 'listingo'),
				'placeholder' 	=> esc_html__('Tripadvisor Link', 'listingo'),
				'is_url'   	=> true,
				'icon'		=> 'fa fa-tripadvisor',
				'classses'		=> 'tg-tripadvisor',
				'color'			=> '#FF0000',
			),
			'wikipedia'	=> array(
				'title' 	=> esc_html__('Wikipedia Link?', 'listingo'),
				'placeholder' 	=> esc_html__('Wikipedia Link', 'listingo'),
				'is_url'   	=> true,
				'icon'		=> 'fa fa-wikipedia-w',
				'classses'		=> 'tg-wikipedia',
				'color'			=> '#5a5b5c',
			),
			'vimeo'	=> array(
				'title' 	=> esc_html__('Vimeo Link?', 'listingo'),
				'placeholder' 	=> esc_html__('Vimeo Link', 'listingo'),
				'is_url'  	 => true,
				'icon'		=> 'fa fa-vimeo',
				'classses'		=> 'tg-vimeo',
				'color'			=> '#00adef',
			),
			'youtube'	=> array(
				'title' 	=> esc_html__('Youtube Link?', 'listingo'),
				'placeholder' 	=> esc_html__('Youtube Link', 'listingo'),
				'is_url'   	=> true,
				'icon'		=> 'fa fa-youtube',
				'classses'		=> 'tg-youtube',
				'color'			=> '#cd201f',
			),
			'whatsapp'	=> array(
				'title' 	=> esc_html__('Whatsapp Number?', 'listingo'),
				'placeholder' 	=> esc_html__('Whatsapp Number', 'listingo'),
				'is_url'   	=> false,
				'icon'		=> 'fa fa-whatsapp',
				'classses'		=> 'tg-whatsapp',
				'color'			=> '#0dc143',
			),
			'vkontakte'	=> array(
				'title' 	=> esc_html__('Vkontakte Link?', 'listingo'),
				'placeholder' 	=> esc_html__('Vkontakte Link', 'listingo'),
				'is_url'   	=> false,
				'icon'		=> 'fa fa-vk',
				'classses'		=> 'tg-vkontakte',
				'color'			=> '#5A80A7',
			),
			'odnoklassniki'	=> array(
				'title' 	=> esc_html__('Odnoklassniki Link?', 'listingo'),
				'placeholder' 	=> esc_html__('Odnoklassniki Link', 'listingo'),
				'is_url'    => true,
				'icon'		=> 'fa fa-odnoklassniki',
				'classses'		=> 'tg-odnoklassniki',
				'color'			=> '#f58220',
			),
		);
		
		$list	= array_merge($list,$custom_list);
		$list	= apply_filters('listingo_exclude_social_media_icons',$list);
		return $list;
    }
    add_filter('listingo_get_social_media_icons_list', 'listingo_get_social_media_icons_list', 10,1);
}


/**
 * @Chat api settings
 * @return 
 */
if (!function_exists('listingo_get_social_api_settings')) {
    function listingo_get_social_api_settings($custom_list,$key) {
        global $current_user;
        $list   = array(
            'facebook'  => array(
                'page_id'   => array(
                    'title'     => esc_html__('Facebook Page ID (Required)', 'listingo'),
                    'value'     => listingo_get_social_settings_value('facebook','chat','page_id',$current_user->ID)
                ),
                'app_id'   => array(
                    'title'     => esc_html__('Facebook App ID (optional)', 'listingo'),
                    'value'     => listingo_get_social_settings_value('facebook','chat','app_id',$current_user->ID)
                ),
                'theme_color'   => array(
                    'title'     => esc_html__('Facebook theme color (optional)', 'listingo'),
                    'value'     => listingo_get_social_settings_value('facebook','chat','theme_color',$current_user->ID)
                ),
                'loggedin_message'   => array(
                    'title'     => esc_html__('Welcome message for loggedin user (optional)', 'listingo'),
                    'value'     => listingo_get_social_settings_value('facebook','chat','loggedin_message',$current_user->ID)
                ),
                'loggedout_message'   => array(
                    'title'     => esc_html__('Welcome message for loggedout user (optional)', 'listingo'),
                    'value'     => listingo_get_social_settings_value('facebook','chat','loggedout_message',$current_user->ID)
                ),
            ),
        );
        
        $list   = array_merge($list,$custom_list);
        return !empty( $list[$key] ) ? $list[$key] : array();
    }
    add_filter('listingo_get_social_api_settings', 'listingo_get_social_api_settings', 10,2);
}

/**
 * @get tooltip settings
 * @return 
 */
if (!function_exists('listingo_get_tooltip')) {
	function listingo_get_tooltip($type,$element){
		if( empty( $element ) ){return;}
		$type	= !empty( $type ) ? $type : 'element';
		if (is_page_template('directory/dashboard.php')) {
			if (function_exists('fw_get_db_settings_option')) {
				$data = fw_get_db_settings_option('tip_'.$element, $default_value = null);

				if( !empty( $data[0]['content'] ) ){
					$title		= !empty( $data[0]['title'] ) ?  $data[0]['title'] : '';
					$content	= !empty( $data[0]['content'] ) ?  $data[0]['content'] : '';

					if( !empty( $content ) ){?>
						<span class="sp-<?php echo esc_attr( $type );?>-hint sp-tipso-tooltip"><i data-tipso-title="<?php echo esc_attr( $title );?>" data-tipso="<?php echo esc_attr( $content );?>" class="fa fa-question-circle tipso_style sp-data-tipso"></i></span>
					<?php 
					}

				}
			}
		}
	}
	add_action('listingo_get_tooltip', 'listingo_get_tooltip',10,2);
}

/**
 * @get settings
 * @return {}
 */
if (!function_exists('listingo_get_theme_settings')) {
	function  listingo_get_theme_settings($key){
		$sp_theme_settings = get_option( 'sp_theme_settings' );
		$setting	= !empty( $sp_theme_settings[$key] ) ? $sp_theme_settings[$key] : '';
		return $setting;
	}
	add_filter('listingo_get_theme_settings', 'listingo_get_theme_settings', 10, 1);
}

/**
 * @is contact infomations included in packages
 * @return {}
 */
if (!function_exists('listingo_is_contact_informations_enabled')) {
	function  listingo_is_contact_informations_enabled($key,$check_expiry,$user_id){
		
		if (function_exists('fw_get_db_settings_option')) {
			$sp_contact_information = fw_get_db_settings_option('sp_contact_information', $default_value = null);
			
			if( isset( $sp_contact_information ) && $sp_contact_information === 'no' ){
				if( isset( $check_expiry ) &&  $check_expiry === 'yes' ){
					$key	=  'yes';
				} else{
					$key	=  'no';
				}
			}else{
				
				if( isset( $check_expiry ) &&  $check_expiry === 'yes' ){
					if( apply_filters('listingo_is_setting_enabled', $user_id, 'subscription_contact_information') === true  ){
						$key	=  'yes';
						
					} else{
						$key	=  'no';
					}
				} else{
					$key	=  'yes';
				}
				
			}
		}
		
		return $key;
	}
	add_filter('listingo_is_contact_informations_enabled', 'listingo_is_contact_informations_enabled', 10, 3);
}

/**
 * @is contact infomations included in packages
 * @return {}
 */
if (!function_exists('listingo_get_user_meta')) {
	function  listingo_get_user_meta($key,$user){
		
		if( !empty($user) ){
			if( isset( $key ) && $key === 'phone' ){
				if ( apply_filters('listingo_is_contact_informations_enabled', 'yes','yes',$user->ID) === 'yes') {
					
					$userphone = !empty( $user->phone ) ? $user->phone : '';
					if (!empty($userphone)) {?>
						<li>
							<i class="lnr lnr-phone-handset"></i>
							<span><a href="tel:<?php echo esc_attr($userphone); ?>"><?php echo esc_attr($userphone); ?></a></span>
						</li>
					<?php }
				}
			} else if( isset( $key ) && $key === 'email' ){
				$useremail = !empty( $user->user_email ) ? $user->user_email : '';
				if ( apply_filters('listingo_is_contact_informations_enabled', 'yes','yes',$user->ID) === 'yes') {
					if( !empty( $user->user_email ) ){
						$email = explode('@', $user->user_email);
					}

					if( !empty( $email ) ){?>
						<li>
							<i class="lnr lnr-envelope"></i>
							<span data-toggle="tooltip" data-placement="top" title="<?php esc_html_e('Click to view email', 'listingo'); ?>" data-last="<?php echo esc_attr($email[1]); ?>"><a href="mailto:<?php echo esc_attr($useremail); ?>"><?php echo esc_attr($email[0]); ?>@<em>XXXX.XXXX</em></a></span>
						</li>
					<?php }
				}
			}
		}
	}
	add_action('listingo_get_user_meta', 'listingo_get_user_meta', 10, 2);
}
