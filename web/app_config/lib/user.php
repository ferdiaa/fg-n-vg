<?php

if (!class_exists('ListingoApp_User_Route')) {

    class ListingoApp_User_Route extends WP_REST_Controller
    {

        /**
         * Register the routes for the objects of the controller.
         */
        public function register_routes() {
            $version = '1';
            $namespace = 'api/v' . $version;
            $base = 'user';

            register_rest_route($namespace, '/' . $base . '/login',
                    array(
                array(
                    'methods' => WP_REST_Server::READABLE,
                    'callback' => array($this, 'get_items'),
                    'args' => array(
                    ),
                ),
                array(
                    'methods' => WP_REST_Server::CREATABLE,
                    'callback' => array($this, 'user_login'),
                    'args' => array(),
                ),
                    )
            );

             register_rest_route($namespace, '/' . $base . '/token',
                    array(
                array(
                    'methods' => WP_REST_Server::READABLE,
                    'callback' => array($this, 'get_items'),
                    'args' => array(
                    ),
                ),
                array(
                    'methods' => WP_REST_Server::CREATABLE,
                    'callback' => array($this, 'save_user_device_token'),
                    'args' => array(),
                ),
                    )
            );
             register_rest_route($namespace, '/' . $base . '/remove-token',
                    array(
                array(
                    'methods' => WP_REST_Server::READABLE,
                    'callback' => array($this, 'get_items'),
                    'args' => array(
                    ),
                ),
                array(
                    'methods' => WP_REST_Server::CREATABLE,
                    'callback' => array($this, 'remove_user_device_token'),
                    'args' => array(),
                ),
                    )
            );


            register_rest_route($namespace, '/' . $base . '/reset-password',
                    array(
                array(
                    'methods' => WP_REST_Server::READABLE,
                    'callback' => array($this, 'get_items'),
                    // 'permission_callback' => array( $this, 'get_items_permissions_check' ),
                    'args' => array(
                    ),
                ),
                array(
                    'methods' => WP_REST_Server::CREATABLE,
                    'callback' => array($this, 'forgot_password'),
                    //'permission_callback' => array( $this, 'create_item_permissions_check' ),
                    'args' => array(),
                ),
                    )
            );

            register_rest_route($namespace, '/' . $base . '/appointments',
                    array(
                array(
                    'methods' => WP_REST_Server::READABLE,
                    'callback' => array($this, 'get_my_appoinments'),
                    // 'permission_callback' => array( $this, 'get_items_permissions_check' ),
                    'args' => array(
                    ),
                ),
                array(
                    'methods' => WP_REST_Server::CREATABLE,
                    'callback' => array($this, 'create_item'),
                    //'permission_callback' => array( $this, 'create_item_permissions_check' ),
                    'args' => array(),
                ),
            ));

             register_rest_route($namespace, '/' . $base . '/services',
                    array(
                array(
                    'methods' => WP_REST_Server::READABLE,
                    'callback' => array($this, 'get_my_services'),
                    // 'permission_callback' => array( $this, 'get_items_permissions_check' ),
                    'args' => array(
                    ),
                ),
                array(
                    'methods' => WP_REST_Server::CREATABLE,
                    'callback' => array($this, 'set_my_services'),
                    //'permission_callback' => array( $this, 'create_item_permissions_check' ),
                    'args' => array(),
                ),
            ));


             register_rest_route($namespace, '/' . $base . '/favorites',
                    array(
                array(
                    'methods' => WP_REST_Server::READABLE,
                    'callback' => array($this, 'get_my_favorites'),
                    // 'permission_callback' => array( $this, 'get_items_permissions_check' ),
                    'args' => array(
                    ),
                ),
                array(
                    'methods' => WP_REST_Server::CREATABLE,
                    'callback' => array($this, 'set_my_favorites'),
                    //'permission_callback' => array( $this, 'create_item_permissions_check' ),
                    'args' => array(),
                ),
            ));


        }

        /**
         * Get a collection of items
         *
         * @param WP_REST_Request $request Full data about the request.
         * @return WP_Error|WP_REST_Response
         */
        public function get_items($request) {
            $items['data'] = array();
            return new WP_REST_Response($items, 200);
        }

        public function get_my_services($request) {
            $user_id = $_GET['user_id'];
            $services  =  get_user_meta($_GET['user_id'], 'profile_services', true);
            //print_r($services);
            //$items = unserialize($services);

             $items = array();
            if( !empty($services )){
                $items = array_values($services);    
            }
            return new WP_REST_Response($items, 200);
        }




        public function get_my_favorites($request) {

            $user_id = $_GET['user_id'];
            $favorites  =  get_user_meta($_GET['user_id'], 'wishlist', true);
            $items = array();
            if( empty($favorites) ){
                $items =  array();
                return new WP_REST_Response($items, 200);
                exit;

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

            $query_args['include'] =  $favorites ; 
            if (!empty($meta_query_args)) {
                $query_relation = array('relation' => 'AND',);
                $meta_query_args = array_merge($query_relation, $meta_query_args);
                $query_args['meta_query'] = $meta_query_args;
            }

            
            //Count total users for pagination
            $total_query = new WP_User_Query($query_args);
            $total_users = $total_query->total_users;
            $query_args['number'] = $limit;
            $query_args['offset'] = $offset;

            $default_view = 'list';
            if (function_exists('fw_get_db_post_option')) {
                $default_view = fw_get_db_settings_option('dir_search_view');
            }

            $user_query = new WP_User_Query($query_args);
            $sp_userslist['status'] = 'none';
            $sp_usersdata = array();
            $sp_userslist = array();

            if (!empty($user_query->results)) {

                $sp_userslist['status'] = 'found';

                if (!empty($sp_category)) {
                    $title = get_the_title($sp_category);
                    $postdata = get_post($sp_category);
                    $slug = $postdata->post_name;
                } else {
                    $title = '';
                    $slug = '';
                }

                foreach ($user_query->results as $user) {

                    if (isset($media_type) && $media_type === 'banner') {
                        $thumb = apply_filters(
                                'listingo_get_media_filter', listingo_get_user_banner(array('width' => 370, 'height' => 270), $user->ID), array('width' => 370, 'height' => 270)
                        );
                    } else {
                        $thumb = apply_filters(
                                'listingo_get_media_filter', listingo_get_user_avatar(array('width' => 370, 'height' => 270), $user->ID), array('width' => 370, 'height' => 270)
                        );
                    }

                    $item['category_id'] = get_user_meta($user->ID, 'category', true);
                    $item['category'] = get_the_title($user->category);
                    $item['ID'] = $user->ID;
                    $item['latitude'] = $user->latitude;
                    $item['longitude'] = $user->longitude;
                    $item['phone'] = $user->phone;
                    $username = listingo_get_username($user->ID);
                    $item['username'] = $username;
                    $item['avatar'] = $thumb;


                    $item['wp_capabilities'] = $user->wp_capabilities;
                    $item['wp_user_level'] = $user->wp_user_level;
                    $item['usertype'] = $user->usertype;

                    $item['email'] = $user->user_email;
                    $item['website'] = $user->user_url;


                    $item['full_name'] = $user->full_name;
                    $item['nickname'] = $user->nickname;
                    $item['r_id'] = $user->r_id;
                    //$item['category'] = $user->category;
                    $item['sub_category'] = $user->sub_category;
                    $item['company_name'] = $user->company_name;
                    $item['profile_avatar'] = empty($user->profile_avatar) ? new stdClass() : $user->profile_avatar;
                    if (!empty($user->profile_gallery_photos)) {
                        $a = $user->profile_gallery_photos;
                        $a['image_data'] = array_values($a['image_data']);
                    } else {
                        $a['image_data'] = array();
                    }

                    $db_category_type = get_user_meta($user->ID, 'category', true);


                    /* Get the rating headings */
                    $rating_titles = $this->listingo_get_reviews_evaluation($db_category_type, 'leave_rating');
                    $item['rating_titles'] = $rating_titles;

                    $item['profile_gallery_photos'] = empty($user->profile_gallery_photos) ? new stdClass() : ( $a);
                    $item['facebook'] = $user->facebook;
                    $item['twitter '] = $user->twitter;
                    $item['linkedin'] = $user->linkedin;
                    $item['pinterest'] = $user->pinterest;
                    $item['google_plus'] = $user->google_plus;
                    $item['tumblr'] = $user->tumblr;
                    $item['instagram'] = $user->instagram;
                    $item['skype'] = $user->skype;
                    $item['activation_status'] = $user->activation_status;
                    $item['address'] = $user->address;
                    $item['latitude'] = $user->latitude;
                    $item['longitude'] = $user->longitude;
                    $item['tag_line'] = $user->tag_line;
                    $item['phone'] = $user->phone;
                    $item['fax'] = $user->fax;
                    $item['profile_languages'] = empty($user->profile_languages) ? array() : $user->profile_languages;
                    $item['zip'] = $user->zip;
                    $item['verify_user'] = $user->verify_user;
                    $item['privacy'] = empty($user->privacy) ? new stdClass() : $user->privacy;
                    $item['country'] = $user->country;
                    $item['city'] = $user->city;
                    $item['description'] = $user->description;
                    $item['first_name'] = $user->first_name;
                    $item['last_name'] = $user->last_name;
                    $item['professional_statements'] = $user->professional_statements;
                    $item['subscription_featured_expiry'] = $user->subscription_featured_expiry;
                    $item['subscription_expiry'] = $user->subscription_expiry;
                    $item['subscription_id'] = $user->subscription_id;
                    $item['sp_subscription'] = empty($user->sp_subscription) ? new stdClass() : $user->sp_subscription;
                    $item['awards'] = empty($user->awards) ? array() : $user->awards;
                    $item['business_hours'] = empty($user->business_hours) ? new stdClass() : $user->business_hours;
                    $item['business_hours_format'] = $user->business_hours_format;
                    $item['default_slots'] = empty($user->default_slots) ? new stdClass() : $user->default_slots;
                    $item['experience'] = empty($user->experience) ? array() : $user->experience ;
                    $item['profile_brochure'] = $user->profile_brochure;
                    $item['profile_insurance'] = empty($user->profile_insurance) ? array() : $user->profile_insurance;
                    $amenities = $user->profile_amenities;
                    $item['isfav'] = true;    

                    $_amenities = array();
                    if (!empty($amenities))
                        foreach ($amenities as $key => $amenitie) {
                            $_amenities[] = get_term_by('slug', $amenitie, 'amenities');
                        }


                    $item['profile_amenities'] = empty($user->profile_amenities) ? array() : $_amenities;
                    $item['audio_video_urls'] =empty($user->audio_video_urls) ? array() : $user->audio_video_urls;
                    $item['qualification'] = empty($user->qualification) ? array() : $user->qualification;
                    $item['privacy_settings'] = empty($user->privacy_settings) ? new stdClass() : $user->privacy_settings;
                    $item['profile_photo'] = $user->profile_photo;
                    $item['profile_banner'] = $user->profile_banner;
                    $item['profile_appointment'] = $user->profile_appointment;
                    $item['profile_contact'] = $user->profile_contact;
                    $item['profile_hours'] = $user->profile_hours;
                    $item['profile_service'] = $user->profile_service;
                    $item['profile_team'] = $user->profile_team;
                    $item['profile_gallery'] = $user->profile_gallery;
                    $item['profile_videos'] = $user->profile_videos;
                    $item['appt_booking_approved'] = $user->appt_booking_approved;
                    $item['appt_booking_confirmed'] = $user->appt_booking_confirmed;
                    $item['appt_booking_cancelled'] = $user->appt_booking_cancelled;
                    $item['appt_cancelled_title'] = $user->appt_cancelled_title;
                    $item['appt_approved_title'] = $user->appt_approved_title;
                    $item['appt_confirmation_title'] = $user->appt_confirmation_title;
                    $item['appointment_currency'] = $user->appointment_currency;
                    $item['appointment_inst_desc'] = $user->appointment_inst_desc;
                    $item['appointment_inst_title'] = $user->appointment_inst_title;


                    $item['profile_services'] = empty($user->profile_services) ? array() : array_values($user->profile_services);
                    $item['appointment_reasons'] = empty($user->appointment_reasons) ? array() : array_values($user->appointment_reasons);
                    $item['appointment_types'] = empty($user->appointment_types) ? array() : array_values($user->appointment_types);
                    $item['teams_data'] = empty($user->teams_data) ? array() : (array) array_values($user->teams_data);

                    $review_data = get_user_meta($user->ID, 'review_data', true);
                    $item['review_data'] = empty($review_data) ? new stdClass() : $review_data;

                    $items[] = $item;
                }
            }

            // print_r($items);die;
            return new WP_REST_Response($items, 200);



            $items = array();
            if( !empty($services )){
                $items = ($services);    
            }
            
            return new WP_REST_Response($items, 200);
        }

public function listingo_get_reviews_evaluation($category_type, $reviews_type) {

        $reviews_evaluation = array('leave_rating' => array(),'total_wait_time' =>array() );

        $reviews = '';
        if (function_exists('fw_get_db_settings_option')) {
            $reviews = fw_get_db_post_option($category_type, 'enable_reviews', true);
        }   
        // echo $reviews_type;
        // print_r($reviews);
        // print_r($reviews['enable'][$reviews_type]);
        // die;
        if( !empty( $reviews['enable'][$reviews_type] )){
                foreach ($reviews['enable'][$reviews_type] as $key => $value) {
                    $reviews_evaluation['leave_rating'][$key]['title'] = $value;
                    $reviews_evaluation['leave_rating'][$key]['slug'] = sanitize_title($value);
                }
                 foreach ($reviews['enable']['total_wait_time'] as $key => $value) {
                    $reviews_evaluation['total_wait_time'][] = $value;
                }
        }
        

        // $reviews_check = !empty($reviews['gadget']) ? $reviews['gadget'] : '';

        // if (!empty($reviews_check) && $reviews_check === 'enable' && $reviews_type === 'total_wait_time') {
        //     $reviews_evaluation = !empty($reviews['enable'][$reviews_type]) ? $reviews['enable'][$reviews_type] : array();
        // } else if (!empty($reviews_check) && $reviews_check === 'enable' && $reviews_type === 'leave_rating') {
        //     $reviews_evaluation = !empty($reviews['enable'][$reviews_type]) ? $reviews['enable'][$reviews_type] : array();
        // }

        // $reviews_evaluation = array_filter($reviews_evaluation);
        // $reviews_evaluation = array_combine(array_map('sanitize_title', $reviews_evaluation), $reviews_evaluation);
        return $reviews_evaluation;
    }
        public function set_my_services($request) {
            $params = $request->get_params();

         //   print_r($params['profile_services']);die;
            $services  =  update_user_meta($params['user_id'],'profile_services', ($params['profile_services']));
            $items['type'] =  "success";
            $items['message'] =  'Saved';
            return new WP_REST_Response($items, 200);
        }

        public function set_my_favorites($request) {
            $params = $request->get_params();
            $favorites  =  get_user_meta($params['user_id'], 'wishlist', true);
            
            if( $params['isfav'] == 'yes'){
                 if( empty( $favorites )){
                $favorites= array();
                }
                array_push($favorites, $params['provider_id']);
            }

            if( $params['isfav'] == 'no'){
               // print_r($favorites);
                if( !empty( $favorites )){
                    //unset($favorites[  ]);
                   $favorites = array_diff($favorites, array($params['provider_id']));
                }
            }
           // print_r($favorites);die;
           
            $services  =  update_user_meta($params['user_id'], 'wishlist', $favorites );
            $items['type'] =  "success";
            $items['message'] =  'Saved';
            return new WP_REST_Response($items, 200);
        }

        public function get_my_appoinments($request) {

            $sort_by = !empty($_GET['sortby']) ? $_GET['sortby'] : 'ID';
            $showposts = !empty($_GET['showposts']) ? $_GET['showposts'] : -1;
            $items = array();
            //Order
            $order = 'DESC';
            if (!empty($_GET['orderby'])) {
                $order = esc_attr($_GET['orderby']);
            }

            if (!empty($_GET['appointment_date'])) {
                $apt_date = strtotime(esc_attr($_GET['appointment_date']));
            }

            $status = array('pending', 'publish');
            if (!empty($_GET['appointment_status'])) {
                $status = array();
                $status[] = $_GET['appointment_status'];
            }


            $query_args = array(
                'posts_per_page' => "-1",
                'post_type' => 'sp_appointments',
                'order' => $order,
                'orderby' => $sort_by,
                'post_status' => $status,
                'ignore_sticky_posts' => 1);

            $total_query = new WP_Query($query_args);
            $total_posts = $total_query->post_count;

            $query_args = array(
                'posts_per_page' => $showposts,
                'post_type' => 'sp_appointments',
                'paged' => $paged,
                'order' => $order,
                'orderby' => $sort_by,
                'post_status' => $status,
                'ignore_sticky_posts' => 1);

            $meta_query_args[] = array(
                'key' => 'apt_user_from',
                'value' => $_GET['user_id'],
                'compare' => '=',
                'type' => 'NUMERIC'
            );

            if (!empty($apt_date)) {
                $meta_query_args[] = array(
                    'key' => 'apt_date',
                    'value' => esc_attr($apt_date),
                    'compare' => '=',
                );
            }
            if (!empty($meta_query_args)) {
                $query_relation = array('relation' => 'AND',);
                $meta_query_args = array_merge($query_relation, $meta_query_args);
                $query_args['meta_query'] = $meta_query_args;
            }

            $items = array();

            $appt_data = new WP_Query($query_args);
            $date_format = get_option('date_format');
            $time_format = get_option('time_format');
            if ($appt_data->have_posts()) {
                $counter = 1;
                while ($appt_data->have_posts()) : $appt_data->the_post();
                    global $post;
                    $item['apt_types'] = $apt_types = get_post_meta($post->ID,
                            'apt_types', true);

                    $item['key'] = $apt_types = get_the_title();
                    $item['apt_services'] = $apt_services = get_post_meta($post->ID,
                            'apt_services', true);
                    $item['apt_reasons'] = $apt_reasons = get_post_meta($post->ID,
                            'apt_reasons', true);
                    $item['apt_description'] = $apt_description = get_post_meta($post->ID,
                            'apt_description', true);
                    $apt_user_from = get_post_meta($post->ID, 'apt_user_from',
                            true);
                    $apt_user_to = get_post_meta($post->ID, 'apt_user_to', true);



                    $item['apt_currency_symbol'] = $apt_currency_symbol = get_post_meta($post->ID,
                            'apt_currency_symbol', true);
                    $item['apt_user_to'] = $apt_user_to = get_post_meta($post->ID,
                            'apt_user_to', true);
                    $item['provider'] = $username = listingo_get_username($apt_user_to);
                    $item['apt_date'] = $apt_date = get_post_meta($post->ID,
                            'apt_date', true);
                    $apt_time = get_post_meta($post->ID,
                            'apt_time', true);
$time = explode('-', $apt_time);
 $item['apt_time'] = date_i18n($time_format, strtotime('2016-01-01 ' . $time[0])) . ' - ' .date_i18n($time_format, strtotime('2016-01-01 ' . $time[1]));
                    $item['time'] = $time = explode('-', $apt_time);

                    $item['booking_services'] = $booking_services = get_user_meta($apt_user_to,
                            'profile_services', true);
                    $item['booking_types'] = $booking_types = get_user_meta($apt_user_to,
                            'appointment_types', true);
                    $item['booking_reasons'] = $booking_reasons = get_user_meta($apt_user_to,
                            'appointment_reasons', true);

                    $item['apt_user_from'] = $apt_user_from = get_post_meta($post->ID,
                            'apt_user_from', true);
                    $item['username'] = $username = listingo_get_username($apt_user_from);

                    $item['avatar'] = $avatar = apply_filters(
                            'listingo_get_media_filter',
                            listingo_get_user_avatar(array('width' => 100, 'height' => 100),
                                    $apt_user_from),
                            array('width' => 100, 'height' => 100)
                    );
                    $items[] = $item;
                    $counter++;
                endwhile;
                wp_reset_postdata();
            }


            return new WP_REST_Response($items, 200);
        }

        
        /**
         * Login user for application
         *
         * @param WP_REST_Request $request Full data about the request.
         * @return WP_Error|WP_REST_Request
         */
        public function save_user_device_token($request) {
             $params = $request->get_params();
             
                $user = get_userdata( $params['user_id']);

            if ( $user === false ) {
                $json['message'] = 'User does not exists!';
            } else {

                 $tokens = get_user_meta($params['user_id'], 'device_token' );
                if ( !in_array($params['device_token'], $tokens )  ){ 
                     add_user_meta( $params['user_id'] , 'device_token', $params['device_token']);
                    $json['message'] = 'Token saved!';
                }else{
                    $json['message'] = 'Token already exists';
                }
                
                
            }

            return new WP_REST_Response($json, 200);

        } 

        public function remove_user_device_token($request) {
            $params = $request->get_params();
             
            $user = get_userdata( $params['user_id']);

            if ( $user === false ) {
                $json['message'] = 'User does not exists!';
            } else {

                 $tokens = get_user_meta($params['user_id'], 'device_token' );
                if ( in_array($params['device_token'], $tokens )  ){ 
                     delete_user_meta( $params['user_id'] , 'device_token', $params['device_token']);
                    $json['message'] = 'Token removed!';
                }else{
                    $json['message'] = 'Token does not exists';
                }
                
                
            }

            return new WP_REST_Response($json, 200);

        } 


        /**
         * Login user for application
         *
         * @param WP_REST_Request $request Full data about the request.
         * @return WP_Error|WP_REST_Request
         */
        public function user_login($request) {
            $params = $request->get_params();
            $_POST = $request->get_params();
            if (isset($params['username']) && isset($params['password'])) {
                $creds = array(
                    'user_login' => $params['username'],
                    'user_password' => $params['password'],
                    'remember' => true
                );

                $user = wp_signon($creds, false);
                unset($user->allcaps);
                unset($user->filter);
                $user->meta = get_user_meta($user->data->ID, '', true);

                $user->avatar = apply_filters(
                        'listingo_get_media_filter',
                        listingo_get_user_avatar(array('width' => 100, 'height' => 100),
                                $user->data->ID),
                        array('width' => 100, 'height' => 100)
                );

                $user->banner = apply_filters(
                        'listingo_get_media_filter',
                        listingo_get_user_banner(array('width' => 270, 'height' => 120),
                                $user->data->ID),
                        array('width' => 270, 'height' => 120)//size width,height
                );

                if (is_wp_error($user)) {
                    return new WP_Error('wrong-credentials',
                            __('message', 'listingo-app'), array('status' => 500));
                } else {
                    $json['type'] = "success";
                    $json['message'] = esc_html__(".", "listingo_core");
                    $json['data'] = $user;
                    return new WP_REST_Response($json, 200);
                }
            }




            return new WP_Error('cant-create', __('message', 'listingo-app'),
                    array('status' => 500));
        }

        /**
         * Forgot password for application
         *
         * @param WP_REST_Request $request Full data about the request.
         * @return WP_Error|WP_REST_Request
         */
        public function forgot_password($request) {
            $params = $request->get_params();
            $_POST = $request->get_params();
            $json = array();
            if (isset($params['email'])) {
                $user_login = $params['email'];
                $status = true;
                $response_message = '';
                $json['message'] = 'Some error occured';
                global $wpdb, $wp_hasher;

                $user_login = sanitize_text_field($user_login);


                if (empty($user_login)) {
                    $status = false;
                    $response_message = 'Please enter email address';
                } else if (strpos($user_login, '@')) {
                    $user_data = get_user_by('email', trim($user_login));
                    if (empty($user_data))
                        $status = false;
                    $response_message = 'Email address does not exist';
                } else {
                    $login = trim($user_login);
                    $user_data = get_user_by('login', $login);
                }

                do_action('lostpassword_post');


                if ($user_data) {



                    // redefining user_login ensures we return the right case in the email
                    $user_login = $user_data->user_login;
                    $user_email = $user_data->user_email;

                    do_action('retreive_password', $user_login);  // Misspelled and deprecated
                    do_action('retrieve_password', $user_login);

                    $allow = apply_filters('allow_password_reset', true,
                            $user_data->ID);

                    if (!$allow) {
                        $status = false;
                        $json['message'] = 'Password change not allowed';
                    } else if (is_wp_error($allow))
                        $status = false;

                    $key = wp_generate_password(20, false);
                    do_action('retrieve_password_key', $user_login, $key);

                    if (empty($wp_hasher)) {
                        require_once ABSPATH . 'wp-includes/class-phpass.php';
                        $wp_hasher = new PasswordHash(8, true);
                    }
                    $hashed = $wp_hasher->HashPassword($key);
                    $wpdb->update($wpdb->users,
                            array('user_activation_key' => $hashed),
                            array('user_login' => $user_login));

                    $message = __('Someone requested that the password be reset for the following account:') . "\r\n\r\n";
                    $message .= network_home_url('/') . "\r\n\r\n";
                    $message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
                    $message .= __('If this was a mistake, just ignore this email and nothing will happen.') . "\r\n\r\n";
                    $message .= __('To reset your password, visit the following address:') . "\r\n\r\n";
                    $message .= '<' . network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login),
                                    'login') . ">\r\n";

                    if (is_multisite())
                        $blogname = $GLOBALS['current_site']->site_name;
                    else
                        $blogname = wp_specialchars_decode(get_option('blogname'),
                                ENT_QUOTES);

                    $title = sprintf(__('[%s] Password Reset'), $blogname);

                    $title = apply_filters('retrieve_password_title', $title);
                    $message = apply_filters('retrieve_password_message',
                            $message, $key);

                    if ($message && !wp_mail($user_email, $title, $message))
                        $response_message = ( __('The e-mail could not be sent.') . "<br />\n" . __('Possible reason: your host may have disabled the mail() function...') );

                    $response_message = '<p>Link for password reset has been emailed to you. Please check your email.</p>';
                }

                $json['message'] = $response_message;
                if ($status) {
                    $json['type'] = "success";
                    $json['data'] = array();
                    return new WP_REST_Response($json, 200);
                } else {
                    $json['type'] = "error";
                    return new WP_REST_Response($json, 200);
                }
            }
        }

    }

}
add_action('rest_api_init',
        function () {
    $controller = new ListingoApp_User_Route;
    $controller->register_routes();
});
