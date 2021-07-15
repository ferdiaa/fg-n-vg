<?php
/**
 * @Set Post Views
 * @return {}
 */
if (!function_exists('listingo_add_dynamic_styles')) {

    function listingo_add_dynamic_styles() {

        if (function_exists('fw_get_db_settings_option')) {
            $color_base = fw_get_db_settings_option('color_settings');
            $enable_typo = fw_get_db_settings_option('enable_typo');
            $background = fw_get_db_settings_option('background');
            $custom_css = fw_get_db_settings_option('custom_css');
            $body_font = fw_get_db_settings_option('body_font');
            $h1_font = fw_get_db_settings_option('h1_font');
            $h2_font = fw_get_db_settings_option('h2_font');
            $h3_font = fw_get_db_settings_option('h3_font');
            $h4_font = fw_get_db_settings_option('h4_font');
            $h5_font = fw_get_db_settings_option('h5_font');
            $h6_font = fw_get_db_settings_option('h6_font');
        }
		
		//Demo colors
		$post_name = listingo_get_post_name();
		
		if( isset( $_SERVER["SERVER_NAME"] ) && $_SERVER["SERVER_NAME"] === 'themographics.com' ){
			if( $post_name === 'home-page-7' ||  $post_name === 'home-page-8' ){
				$color_base['gadget'] = 'custom';
			}
		}

        ob_start();
		
        echo (isset($custom_css)) ? $custom_css : '';
		
        if (isset($enable_typo) && $enable_typo == 'on') {
            ?>
             body,p,ul,li {
				font-size:<?php echo (isset($body_font['size'])) ? $body_font['size'] : '100%'; ?>px;
				font-family:<?php echo (isset($body_font['family'])) ? $body_font['family'] : 'Lato'; ?>;
				font-style:<?php echo (isset($body_font['style'])) ? $body_font['style'] : ''; ?>;
				color:<?php echo (isset($body_font['color'])) ? $body_font['color'] : '#000'; ?>;
            }
            body h1{
				font-size:<?php echo (isset($h1_font['size'])) ? $h1_font['size'] : ''; ?>px;
				font-family:<?php echo (isset($h1_font['family'])) ? $h1_font['family'] : ''; ?>;
				font-style:<?php echo (isset($h1_font['style'])) ? $h1_font['style'] : ''; ?>;
				color:<?php echo (isset($h1_font['color'])) ? $h1_font['color'] : ''; ?>;
            }
            body h2{
				font-size:<?php echo (isset($h2_font['size'])) ? $h2_font['size'] : ''; ?>px;
				font-family:<?php echo (isset($h2_font['family'])) ? $h2_font['family'] : ''; ?>;
				font-style:<?php echo (isset($h2_font['style'])) ? $h2_font['style'] : ''; ?>;
				color:<?php echo (isset($h2_font['color'])) ? $h2_font['color'] : ''; ?>;
            }
            body h3{font-size:<?php echo (isset($h3_font['size'])) ? $h3_font['size'] : ''; ?>px;
				font-family:<?php echo (isset($h3_font['family'])) ? $h3_font['family'] : ''; ?>;
				font-style:<?php echo (isset($h3_font['style'])) ? $h3_font['style'] : ''; ?>;
				color:<?php echo (isset($h3_font['color'])) ? $h3_font['color'] : ''; ?>;
            }
            body h4{font-size:<?php echo (isset($h4_font['size'])) ? $h4_font['size'] : 'Lato'; ?>px;
				font-family:<?php echo (isset($h4_font['family'])) ? $h4_font['family'] : 'Lato'; ?>;
				font-style:<?php echo (isset($h4_font['style'])) ? $h4_font['style'] : 'Lato'; ?>;
				color:<?php echo (isset($h4_font['color'])) ? $h4_font['color'] : 'Lato'; ?>;
            }
            body h5{font-size:<?php echo (isset($h5_font['size'])) ? $h5_font['size'] : 'Lato'; ?>px;
				font-family:<?php echo (isset($h5_font['family'])) ? $h5_font['family'] : 'Lato'; ?>;
				font-style:<?php echo (isset($h5_font['style'])) ? $h5_font['style'] : 'Lato'; ?>;
				color:<?php echo (isset($h5_font['color'])) ? $h5_font['color'] : 'Lato'; ?>;
            }
            body h6{font-size:<?php echo (isset($h6_font['size'])) ? $h6_font['size'] : 'Lato'; ?>px;
				font-family:<?php echo (isset($h6_font['family'])) ? $h6_font['family'] : 'Lato'; ?>;
				font-style:<?php echo (isset($h6_font['style'])) ? $h6_font['style'] : 'Lato'; ?>;
				color:<?php echo (isset($h6_font['color'])) ? $h6_font['color'] : 'Lato'; ?>;
            }	
        <?php } ?>

        <?php 
        if (isset($color_base['gadget']) && $color_base['gadget'] === 'custom') {
            if (!empty($color_base['custom']['primary_color'])) {
                $theme_color = $color_base['custom']['primary_color'];
				
				$theme_color = apply_filters('listingo_get_page_color',$theme_color);
                ?>
                
                /*Theme background Color*/
                .tg-btn:before,
				.tg-theme-tag,
				.tg-dropdownmenu li a:before,
				.tg-navigation ul li a:after,
				.tg-searchbox,
				.tg-formsearch .tg-btn:hover:before,
				.tg-panel .tg-radio label:hover,
				.tg-panel .tg-radio input[type=radio]:checked + label,
				.tg-featuredprofiles h1 span:before,
				.tg-featuredprofilesbtns .tg-btnprev:hover i,
				.tg-featuredprofilesbtns .tg-btnnext:hover i,
				.tg-themetag:before,
				.tg-quotes,
				.navbar-toggle,
				.tg-dropdowarrow,
				.tg-pagination ul li a:hover,
				.tg-pagination ul li.tg-active a,
				.tg-bordertitle:before,
				.tg-timelinenav li a:hover:after,
				.tg-timelinenav li.active a:after,
				.tg-formrefinesearch fieldset h4:before,
				.tg-btngallery,
				.tg-recommendedradio .tg-radio label,
				.tg-widgettitle:before,
				.tg-widgetcontent .tg-tag:hover,
				.tg-currentday .tg-timebox i,
				.tg-tabnav li:hover a,
				.tg-tabnav li.active a,
				.tg-formprogressbar li:after,
				.tg-tablejoblidting tr:before,
				.ui-slider-range,
				.tg-dashboardnav ul li a:before,
				.alert-success i,
				.tg-btnrefresh:hover,
				.tg-servicesmodal .tg-modalcontent .close,
				.tg-btnaction li.tg-email a,
				.tg-addtimeslot:hover,
				.tg-actionnav li:hover,
				.tg-emailnav li .form-group button,
				.tg-emailnav li:before,
				.tg-btnactions a:hover,
				.tg-btndownloadattachment,
				.tg-dashboardappointment .tg-btntimeedit .tg-btnedite,
				.geo_distance.ui-slider .ui-slider-handle,
				.pin, 
				.tg-uploadingbar-percentage,
				.tg-formleavefeedback .tg-servicesrating li > em + div strong,
				.tg-contactusarea .tg-themeform .tg-btn,
				.chosen-container .chosen-results li.result-selected,
				.chosen-container .chosen-results li.highlighted,
				.post-password-form p input[type=submit],
				.checkout_coupon p + p input[type=submit],
				html input[type="button"],
				input[type="reset"],
				input[type="submit"],
				.checkout-button.button.alt.wc-forward,
				.sp-search-provider-banner-v2 .tg-formsearch .tg-btn,
               	.tg-headervtwo .tg-navigationarea,
				.owl-dots .owl-dot:hover span,
				.owl-dots .owl-dot.active span,
				.tg-headervthree .tg-navigationarea,
               	.sp-header-v3:before,	
				.sp-header-v4:before,
                .sp-view-profile,
				.added_to_cart.wc-forward:before,
                .sp-av-link,
                .tg-pagination .page-numbers li .page-numbers.current
                {background:<?php echo esc_attr($theme_color); ?>;}
                
                .tg-btnedite,
				.tg-uploadhead,
				.tg-uploadingbar:after,
				.tg-galleryimg figure figcaption .fa-check,
				.tg-expireytimecounter,
				.tg-timecounter,
				.button.logout-link,
				#bbpress-forums #bbp-search-form input[type="submit"],
				#bbpress-forums + #bbp-search-form > div input[type="submit"],
				.sp-slider-v3 .tg-btn:before
				{background:<?php echo esc_attr($theme_color); ?>;}
				
				.tg-emailnavscroll .mCSB_dragger .mCSB_dragger_bar,
				.tg-horizontalthemescrollbar .mCSB_dragger .mCSB_dragger_bar,
				.tg-themescrollbar .mCSB_dragger .mCSB_dragger_bar,
				div.bbp-submit-wrapper > .button.submit
				{background: <?php echo esc_attr($theme_color); ?> !important;}

				.tg-datepicker .ui-datepicker td a.ui-state-active,
				.tg-datepicker .ui-datepicker td a:hover,
				.tg-datepicker .ui-widget-header a:hover,
				.tg-timeslotsradio .tg-radio input[type=radio]:checked + label,
				.tg-closemodal
                {background: <?php echo esc_attr($theme_color); ?>;}

                /*Theme Text Color*/
                a,
				a:hover,
				a:focus,
				a:active,
				p a,
				p a:hover,
				.tg-widgettwitter .tg-widgetcontent ul li a,
				.tg-widgetfreeinspection ul li:last-child a,
				.tg-breadcrumb li a,
				.tg-matadata li a:hover,
				.alert-info strong,
				.tg-btnaddservices,
				.tg-weatherarea i{color: #42a5f5;}
				.tg-memberinfo h5:hover a,
				.sp-upload-container:hover,
				.sp-upload-container:hover i,
				.sp-upload-container:hover span,
				.tg-widget ul li a:hover{color: #66bb6a;}
				.tg-addressinfo li a:hover,
				.tg-inputwithicon .tg-icon.fa-crosshairs,
				.tg-sectiontitle:after,
				.tg-serviceprovidercontent .tg-title h3:hover a,
				.tg-postmatadata li a:hover,
				.tg-postmatadata li a:hover i,
				.tg-postmatadata li a:hover span,
				.tg-footernav ul li a:hover,
				.tg-widgetcontent ul li a:hover,
				.tg-widgetcontent ul li a:hover:before,
				.tg-listinglistvone .tg-serviceprovider:hover .tg-title h3 a,
				.tg-jobdetail .tg-title .tg-jobpostedby a:hover,
				.tg-themeliststylecircletick li:before,
				.tg-tabnav li.active a > span,
				.tg-tabnav li:hover a > span,
				.tg-tablejoblidting tr:hover .tg-contentbox .tg-title h3 a,
				.tg-dashboardnav ul li:hover a i,
				.tg-dashboardnav ul li.tg-active a i,
				.tg-alertmessages .alert-success strong,
				.tg-dashboardservice:hover .tg-servicetitle h2 a,
				blockquote:after,
				blockquote:before,
				.tg-iosstylcheckbox input[type=checkbox]:checked + label:before,
				.mega-menu ul li ul.sub-menu li a:hover,
				.locate-me-wrap .geolocate,
				.tg-headervthree .tg-btnpostanewjob:hover,
				.tg-headervthree .tg-btnpostanewjob:hover:before
				{color: <?php echo esc_attr($theme_color); ?>;}
				
				.alert-success strong,
				.tg-dashboardservice:hover .tg-servicetitle h2 a,
				.tg-imgandtitle h3 a:hover,
				.tg-pkgplanhead h4 span,
				.tg-loginregister .tg-btnlogin
				{color: <?php echo esc_attr($theme_color); ?>;}


                /*Theme Border Color*/
                
                .tg-theme-tag:after,
				.tg-theme-tag:before,
				input:focus,
				.tg-select select:focus,
				.form-control:focus,
				.tg-testimonialnavigationslider .item figure:hover,
				.tg-testimonialnavigationslider .current .item figure,
				.tg-header,
				.tg-timelinenav li a:hover,
				.tg-timelinenav li.active a,
				.tg-widgetcontent .tg-tag:hover,
				.tg-btndownload:hover,
				.tg-datepicker .ui-datepicker td a:hover,
				.tg-datepicker .ui-datepicker td a.ui-state-active,
				.tg-datepicker .ui-datepicker td a.ui-state-highlight,
				.tg-timeslotsradio .tg-radio input[type=radio]:checked + label,
				.tg-tabnav li,
				.tg-iosstylcheckbox input[type=checkbox]:checked + label,
				.tg-actionnav li:hover,
				.geo_distance.ui-slider .ui-slider-handle,
				.geo_distance.ui-slider .ui-slider-handle:hover,
				.tg-formleavefeedback .tg-servicesrating li > em + div strong:before,
				.tg-infoBox,
				.tg-navigationarea,
				.infoBox:after
                {border-color:<?php echo esc_attr($theme_color); ?>;}
                
				.pulse:after{
					-webkit-box-shadow: 0 0 1px 2px <?php echo esc_attr($theme_color); ?>;
					box-shadow: 0 0 1px 2px <?php echo esc_attr($theme_color); ?>;
				}
		   
			   .tg-formprogressbar li:after{
					-webkit-box-shadow: inset -2px -2px 2px 0 <?php echo esc_attr($theme_color); ?>;
					box-shadow: inset -2px -2px 2px 0 <?php echo esc_attr($theme_color); ?>;
				}
           
            <?php } ?>
            <?php
        }
		
         return ob_get_clean();
    }
}