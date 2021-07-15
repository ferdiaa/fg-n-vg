<?php 
require ListingoGlobalSettings::get_plugin_path() . 'libraries/PhpSpreadsheet/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * @Import Users
 * @return{}
 */     

if ( !class_exists('SP_Import_User') ) {    
    class SP_Import_User {        
        function __construct(){
            // Constructor Code here..
   		}
		
		/*
		 * @import users
		 */
		public function listingo_import_user(){
		
			global $wpdb, $wpdb_data_table;
	
			// User data fields list used to differentiate with user meta
			$userdata_fields       = array(
				'ID', 
				'username', 
				'user_pass',
				'user_email', 
				'user_url', 
				'user_nicename',
				'display_name', 
				'user_registered', 
				'first_name',
				'last_name', 
				'nickname', 
				'description',
				'rich_editing', 
				'comment_shortcuts', 
				'admin_color',
				'use_ssl', 
				'show_admin_bar_front', 
				'show_admin_bar_admin',
				'role'
			);

			$wp_user_table		= $wpdb->prefix.'users';
			$wp_usermeta_table	= $wpdb->prefix.'usermeta';

			if ( isset( $_FILES['users_csv']['tmp_name'] ) ) {
				$file = $_FILES['users_csv']['tmp_name'];
				$name = !empty( $_FILES['users_csv']['name'] ) ? $_FILES['users_csv']['name'] : '';
				
				$filetype	= '';
				if( !empty( $name ) ){
					$filetype = pathinfo($name, PATHINFO_EXTENSION);
				}
				
			} else{
				$file 		= ListingoGlobalSettings::get_plugin_path().'/import-users/users.xlsx';
				$filetype	= 'xlsx';
			}
			
			try {
				//Load the excel(.xls/.xlsx) file
				$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
			} catch (Exception $e) {
				die('Error loading file "' . pathinfo($file, PATHINFO_BASENAME). '": ' . $e->getMessage());
			}

			$worksheet = $spreadsheet->getActiveSheet();
			// Get the highest row and column numbers referenced in the worksheet
			$total_rows = $worksheet->getHighestRow(); // e.g. 10
			$highest_column = $worksheet->getHighestColumn(); // e.g 'F'
	
			$first = true;
			$rkey = 0;
			for($row =1; $row <= $total_rows; $row++) {
	
				// If the first line is empty, abort
				// If another line is empty, just skip it
				if ( empty( $row ) ) {
					if ( $first )
						break;
					else
						continue;
				}
	
				// If we are on the first line, the columns are the headers
				if ( $first ) {
					$line = $spreadsheet->getActiveSheet()
									->rangeToArray(
										'A' . $row . ':' . $highest_column . $row,     // The worksheet range that we want to retrieve
										NULL,        // Value that should be returned for empty cells
										TRUE,        // Should formulas be calculated (the equivalent of getCalculatedValue() for each cell)
										FALSE,       // Should values be formatted (the equivalent of getFormattedValue() for each cell)
										FALSE        // Should the array be indexed by cell row and cell column
									);
					//$line = $sheet->rangeToArray('A' . $row . ':' . $highest_column . $row, NULL, TRUE, FALSE);
					$headers = !empty( $line[0] ) ? $line[0] : array();
					$first = false;
					continue;
				} else{
					//$data = array_map("utf8_encode", $line); //Encoding other than english language
					$data = $spreadsheet->getActiveSheet()
									->rangeToArray(
										'A' . $row . ':' . $highest_column . $row,     // The worksheet range that we want to retrieve
										NULL,        // Value that should be returned for empty cells
										TRUE,        // Should formulas be calculated (the equivalent of getCalculatedValue() for each cell)
										FALSE,       // Should values be formatted (the equivalent of getFormattedValue() for each cell)
										FALSE        // Should the array be indexed by cell row and cell column
									);
				}

				// Separate user data from meta
				$userdata = $usermeta = array();
				foreach ( $data[0] as $ckey => $column ) {
					$column_name = $headers[$ckey];
					$column = trim( $column );
	
					if ( in_array( $column_name, $userdata_fields ) ) {
						$userdata[$column_name] = $column;
					} else {
						$usermeta[$column_name] = $column;
					}
				}
				
				// If no user data, bailout!
				if ( empty( $userdata ) )
					continue;
	
				// Something to be done before importing one user?
				//do_action( 'is_iu_pre_user_import', $userdata, $usermeta );
	
				$user = $user_id = false;
	
				if ( isset( $userdata['user_id'] ) ) {
					$user = get_user_by( 'ID', $userdata['user_id'] );
				}
	
				if ( ! $user ) {
					if ( isset( $userdata['username'] ) )
						$user = get_user_by( 'login', $userdata['username'] );
	
					if ( ! $user && isset( $userdata['user_email'] ) )
						$user = get_user_by( 'email', $userdata['user_email'] );
				}
				
				$update = false;
				if ( $user ) {
					$userdata['ID'] = $user->ID;
					$update = true;
				}
	
				// If creating a new user and no password was set, let auto-generate one!
				if ( ! $update && $update == false  && empty( $userdata['user_pass'] ) ) {
					$userdata['user_pass'] = wp_generate_password( 12, false );
				}
				
				if (isset($update)&& $update == true) {
					$userdata['ID']	= $usermeta['user_id'];
					//$sql = "UPDATE $wp_user_table SET VALUE=".$value_to_store." WHERE USER_ID=$wp_userid AND FIELD_ID=".$ef_details["ID"];
					$user_id = wp_update_user( $userdata );
				} else {
					
					$display_name	= '';
					if( $userdata['role'] === 'business' ){
						$display_name	= !empty( $userdata['username'] ) ? $userdata['username'] : '';
					} else{
						$display_name	= $userdata['first_name'].' '.$userdata['last_name'];
					}
					
					$sql = "INSERT INTO $wp_user_table (ID, 
														user_login, 
														user_pass, 
														user_email, 
														user_registered,
														user_status, 
														display_name, 
														user_nicename, 
														user_url
														) VALUES ('".$usermeta['user_id']."',
														'".$userdata['username']."',
														'".md5($userdata['user_pass'])."',
														'".$userdata['user_email']."',
														'".date('Y-m-d H:i:s')."',
														0,
														'".$display_name."',
														'".$userdata['username']."',
														'".$userdata['user_url']."'
													)";
					$wpdb->query($sql);
					$lastid = $wpdb->insert_id;
					$new_user = new WP_User( $lastid );
					
					if( $userdata['role'] === 'business' ){
						$new_user->set_role( 'business' );	
					} else{
						$new_user->set_role( 'professional' );	
					}

					$user_id =	$lastid;
					
					// Include again meta fields
					$usermeta['description']	= $userdata['description'];
					$usermeta['first_name']	    = $userdata['first_name'];
					$usermeta['last_name']	    = $userdata['last_name'];

					update_user_meta( $user_id, 'usertype', $userdata['role'] ); //update user type
					update_user_meta( $user_id, 'show_admin_bar_front', 'false' );
					update_user_meta( $user_id, 'full_name', $display_name );
					update_user_meta( $user_id, 'rich_editing', 'true' );
					update_user_meta( $user_id, 'nickname', $display_name );
					update_user_meta( $user_id, 'activation_status', 'active');
					
				}

				// Is there an error o_O?
				if ( is_wp_error( $user_id ) ) {
					$errors[$rkey] = $user_id;
				} else {
					// If no error, let's update the user meta too!
					$db_schedules	= array();
					if ( $usermeta ) {
						foreach ( $usermeta as $metakey => $metavalue ) {
							$metavalue = maybe_unserialize( $metavalue );

							//Privacy Settings
							if( $metakey == 'privacy' && !empty( $metavalue ) ){
								$awards	= array();
								$user_privacy	= explode('||',$metavalue);
								$user_privacy	= array_diff( $user_privacy, array('') );

								$db_user_privacy = array();
								$counter	= 0;
								foreach( $user_privacy as $key => $value ){
									$privacy_data	= explode(':',$value);
									if( !empty( $privacy_data[0] ) ) {
										$db_user_privacy[trim( $privacy_data[0])]	= trim( $privacy_data[1] ); 
										$counter++;
									}
								}

								update_user_meta( $user_id, $metakey, $db_user_privacy );
							} else if( $metakey == 'gallery' && !empty( $metavalue ) ){
								$user_gallery	= explode('|',$metavalue);
								$metakey	= 'profile_gallery_photos';
								$gallery_data	= array();
								$gallery_data['image_type']	= 'profile_gallery';
								$gallery_data['default_image']	= $user_gallery[0];
								
								foreach( $user_gallery as $key => $value ){
									$full = listingo_prepare_image_source($value, 0, 0);
									$thumbnail = listingo_prepare_image_source($value, 150, 150);
									
									$gallery_data['image_data'][$value]['full'] = $full;
									$gallery_data['image_data'][$value]['thumb']  = $thumbnail;
									$gallery_data['image_data'][$value]['banner']  = $full;
									$gallery_data['image_data'][$value]['image_id']  = $value;
								}

								update_user_meta( $user_id, $metakey, $gallery_data );
							} else if( $metakey == 'avatar' && !empty( $metavalue ) ){
								$user_gallery	= explode('|',$metavalue);
								$metakey	= 'profile_avatar';
								$gallery_data	= array();
								$gallery_data['image_type']	= 'profile_photo';
								$gallery_data['default_image']	= $user_gallery[0];
								
								foreach( $user_gallery as $key => $value ){
									$full = listingo_prepare_image_source($value, 0, 0);
									$thumbnail = listingo_prepare_image_source($value, 150, 150);
									
									$gallery_data['image_data'][$value]['full'] = $full;
									$gallery_data['image_data'][$value]['thumb']  = $thumbnail;
									$gallery_data['image_data'][$value]['banner']  = $full;
									$gallery_data['image_data'][$value]['image_id']  = $value;
								}

								update_user_meta( $user_id, $metakey, $gallery_data );
							} else{
								update_user_meta( $user_id, $metakey, trim( $metavalue ) );
							}	
							
						}
					
					}
	
					// If we created a new user, maybe set password nag and send new user notification?
					if ( ! $update ) {
					}
				}
	
				$rkey++;
			}
		}
	}
}