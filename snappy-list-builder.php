<?php
 include('functions.php');
/*
Plugin Name: Snappy List Builder
Plugin URI: http://wordpressplugincourse.com/plugins/snappy-list-builder
Description: The ultimate email list building plugin for WordPress. Capture new subscribers. Reward subscribers with a custom download upon opt-in. Build unlimited lists. Import and export subscribers easily with .csv
Version: 1.0
Author: Joel Funk @ Code College
Author URI: http://joelfunk.codecollege.ca
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: snappy-list-builder
*/


/* !0 Table of contents  */

/*
	1. Hooks
		1.1 - register all shotcodes to init
		1.2 - register custom admin column headers
		1.3 - register custom admin column data
	    1.4 - registers custom list column headers
	    1.5 - registers custom question column headers
		1.6 - registers custom question column data
		1.7 - register custom list column data
		1.8 - register ajax actions

	2. Shortcodes
		2.1 - slb_register_shortcodes()
		2.2 - slb_form_shorcode()

	3. Filters
		3.1 - slb_subscribers_column_headers()
		3.2 - slb_subscribers_column_headers()
		3.2.2 - slb_register_custom_admin_titles()
		3.2.3 - slb_custom_admin_titles()
		3.3 - slb_list_column_headers()
		3.4 - slb_list_column_data()
		3.5 - slb_question_column_data()

	4. External Scripts

	5. Actions
		5.1 - save subscription data to an existing or a new subscriber
		5.2 - slb_save_subscriber()
		5.3 - slb_add_subscription()

	6. Helpers
		6.1 - slb_subscriber_has_subscription()
		6.2 - slb_get_subscriber_id()
		6.3 - slb_get_subscriptions()
		6.4 - slb_return_json()
		6.5 - slb_get_acf_key()
		6.6 - slb_get_subscriber_data()

	7. Custom Post Types

	8. Admin Pages

	9. Settings
*/

/* !1. HOOKS */

//@ 1.1
// register all shotcodes to init
add_action( 'init', 'slb_register_shortcodes' );

//1.2
// register custom admin column headers
add_filter('manage_edit-slb_subscriber_columns', 'slb_subscriber_column_headers');

//1.3
// registers custom admin column data
add_filter('manage_slb_subscriber_posts_custom_column', 'slb_subscriber_column_data', 1, 2);
add_action('admin_head-edit.php', 'slb_register_custom_admin_titles');

//1.4
//registers custom list column headers
add_filter('manage_edit-slb_list_columns', 'slb_list_column_headers');

//1.5
//registers custom question column headers
add_filter('manage_edit-slb_question_columns', 'sbl_question_column_headers');

//1.6
//registers custom question column data
add_filter('manage_slb_question_posts_custom_column', 'slb_question_column_data', 1, 2);

//1.7
//register custom list column data
add_filter('manage_slb_list_posts_custom_column', 'slb_list_column_data', 1, 2);

//1.8
//register ajax actions
add_action('wp_ajax_nopriv_slb_save_subscription', 'slb_save_subscription'); // for user
add_action('wp_ajax_slb_save_subscription', 'slb_save_subscription'); // for admins

//1.9
// load external files
add_action('wp_enqueue_scripts', 'slb_public_scripts');

/***********************************************************************************************************************/


/* 2. SHORTCODES */

// 2.1
// registers all the custom shortcodes
function slb_register_shortcodes() {
	add_shortcode( 'slb_form', 'slb_form_shortcode' );
}

// 2.2
// returns a html string for email capture form
function slb_form_shortcode( $args, $content="") {

	$list_id = 0;
	if(isset($args['id'])) {$list_id = (int)$args['id'];}

	// setup the output variable - the form html
	$output = '

		<div class="slb">

			<form id="slb_form" name="slb_form" class="slb-form" method="post"
			action="'.site_url().'/wp-admin/admin-ajax.php?action=slb_save_subscription" >

				<input type="hidden" name="slb_list" value ="' . $list_id . '" >

				<p class="slb-input-container-class">

					<label> Name </label><br />
					<input type="text" name="slb_fname" placeholder="First Name">
					<input type="text" name="slb_lname" placeholder="Last Name">

				</p>

				<p class="slb-input-container-class">

					<label> Your Email </label><br />
					<input type="email" name="slb_email" placeholder="ex. you@email.com">

				</p>';

				if(strlen($content)):

					$output .= '<div class="slb-content">' . wpautop($content) . '</div>';

				endif;

				$output .= '<p class="slb-input-container-class">

					<input type="submit" id ="slb_submit" name="slb_submit" placeholder="Sign me up !">

				</p>

			</form>

		</div>

	';

	// return the results
	return $output;
}

/***********************************************************************************************************************/

/* 3. FILTERS*/

// 3.1
 function slb_subscriber_column_headers($columns){

 	// creating column header data
 	$columns = array(
 		'cb' => '<input type="checkbox" />',
 		'title' => __('Subscriber Name'),
 		'email' => __('Email Address'),
 		'subscriptions' => __('Subscriptions')
 	);

 	return $columns;
 }

 // 3.2
 function slb_subscriber_column_data($column, $post_id){

 	$output = '';

 	switch($column) {
 		case 'title':
 			  $fname = get_field('slb_fname', $post_id);
 			  $lname = get_field('slb_lname', $post_id);
 			  $output .= $fname . ' ' . $lname;
 			  break;
 		case 'email':
 			  $email = get_field('slb_email', $post_id);
 			  $output .= $email;
 			  break;
 		case 'subscriptions':
              $list_items = get_field('slb_subscriptions');

              for($i = 0; $i < count($list_items); $i++){

              	$post = $list_items[$i];
				setup_postdata( $post );

              	if($i == count($list_items)-1){
              		$output .= $post->post_title;
              	}
              	else{
              		$output .= $post->post_title . ', ';
              	}
              }

              wp_reset_postdata();
              break;
 	}

 	echo $output;
 }

 // 3.2.2
 // register special custome admin title columns
 function slb_register_custom_admin_titles(){
 	add_filter('the_title','slb_custom_admin_titles', 99,2);
 }

// 3.2.3
// handles custom admin title "title" column data for post types without title
 function slb_custom_admin_titles($title, $post_id){

 	global $post;

 	$output = $title;

 	if(isset($post->post_type)){

 		switch($post->post_type){
 			case 'slb_subscriber':
 					$fname = get_field('slb_fname', $post_id);
 					$lname = get_field('slb_lname',$post_id);
 					$output = $fname . ' ' . $lname;
 					break;
 			case 'slb_question':
 					$ques = get_field('slb_ques', $post_id);
 					$output = $ques;
 					break;
 		}
 	}

 	return $output;
 }

 //3.3
  function slb_list_column_headers($columns){

  	$columns = array(
  		'cb' => '<input type="checkbox">',
  		'title' => __('List Name'),
  		'shortcode' => __('Shortcode'),
  		'date' => __('Date')
  	);

  	return $columns;
  }

 //3.3.2
  function slb_list_column_data($column, $post_id){

  	$output = '';

  	switch ($column) {
  		case 'shortcode':
  			$output .= '[slb_form id="'. $post_id .'"]';
  			break;
  	}

  	echo $output;
  }

 //3.4
  function sbl_question_column_headers( $columns){

  	$columns = array(
  		'cb' => '<input type="checkbox />',
  		'title' => __('Questions'),
  		'email' => __('Email')
  	);

  	return $columns;
  }

   // 3.5
 function slb_question_column_data($column, $post_id){

 	$output = '';

 	switch($column) {
 		case 'title':
 			  $ques = get_field('slb_ques', $post_id);
 			  $output .= $ques;
 			  break;
 		case 'email':
 			  $email = get_field('slb_question_email', $post_id);
 			  $output .= $email;
 			  break;
 		}

 		echo $output;
 	}

/***********************************************************************************************************************/


/* 4. EXTERNAL SCRIPTS*/
// 4.1
// loads external files into PUBLIC website
function slb_public_scripts(){

    // register scripts with wordpress's internal library
    wp_register_script('snappy-list-builder-js', plugins_url('/js/public/snappy-list-builder.js', __FILE__),
    array('jquery'),'',true);

    // add to que of scripts that get loaded into every page
    wp_enqueue_script('snappy-list-builder-js');

}

/***********************************************************************************************************************/


/* 5. ACTIONS*/
// 5.1
//  save subscription data to an existing or a new subscriber
 function slb_save_subscription(){

 	// setup default result data
 	$result = array(
 		'status' => 0,
 		'message' => 'Subscription was not saved',
 		'error' => '',
 		'errors' => array()
 	);

 	try{

 		//get list id
 		$list_id = (int)$_POST['slb_list'];

 		//prepare subscriber data
 		$subscriber_data = array(
 			'fname' => esc_attr( $_POST['slb_fname']),
 			'lname' => esc_attr($_POST['slb_lname']),
 			'email' => esc_attr($_POST['slb_email'])
 		);

 		// setup error array
 		// array for storing errors
 		$errors = [];

 		// form validation
 		if(!strlen($subscriber_data['fname'])) { $errors['fname'] = "First name is required.";}

 		if(!strlen($subscriber_data['email'])) { $errors['email'] = "Email address is required.";}

 		if(strlen($subscriber_data['email']) && !is_email($subscriber_data['email'])){
 			$errors['email'] = "Email address must be valid.";
 		}


 		if(count($errors)){

 			$result['error'] = "Some fields are still required.";
 			$result['errors'] = $errors;
 		}
 		else{
	 		//attempt to create subscriber id
	 		$subscriber_id = slb_save_subscriber($subscriber_data);

	 		// If subscriber was not saved successfully, the method will return 0,
	 		if($subscriber_id){

	 			// If the subscriber is already subscribed to that list
	 			$has_subscription = slb_subscriber_has_subscription($subscriber_id, $list_id);

	 			if($has_subscription){

	 				$list = get_post($list_id);

	 				$result['error'] = esc_attr($subscriber_data['email'] . ' is already subscribed to ' . $list->post_title . '.');

	 			}
	 			else{

	 				// Add the subscription to the subscriber's list
	 				$subscription_saved = slb_add_subscription($subscriber_id, $list_id);

	 				if($subscription_saved){
	 					$result['status'] = 1;
	 					$result['message'] = 'Subscription saved';
	 				}
	 				else{
	 					$result['error'] = "Unable to save subscription.";
	 				}
	 			}
	 		}
 		}
 	}
 	catch (Exception $e){

 		// a php exception
 		$result['error'] = 'Caught exception' . $e->getMessage();
 	}

 	$email = get_field('slb_email', $subscriber_id);

 	foreach($email as $e){
 		echo $e;
 	}

 	// return result as json
 	slb_return_json($result);


 }

 // 5.2 //Check
 function slb_save_subscriber( $subscriber_data){

 	//setup default subscriber id
 	//0 means subscriber was not saved
 	$subscriber_id = 0;

 	try{

 		$subscriber_id = slb_get_subscriber_id($subscriber_data['email']);

 		// If subscriber does not exist
		if(!$subscriber_id){

			$subscriber_id = wp_insert_post(
				array(
					  'post_type' => 'slb_subscriber',
				      'fname' => $subscriber_data['fname'] . ' ' . $subscriber_data['lname'],
				      'post_status' => 'publish'
			         ),
			         true
			);

		}

		update_field(slb_get_acf_key('slb_fname'), $subscriber_data['fname'], $subscriber_id);
		update_field(slb_get_acf_key('slb_lname'), $subscriber_data['lname'], $subscriber_id);
		update_field(slb_get_acf_key('slb_email'), $subscriber_data['email'], $subscriber_id);

 	} catch( Exception $e){ }

 	return $subscriber_id;
 }

 // 5.3
  function slb_add_subscription($subscriber_id, $list_id){

  	//setup default return value
  	$subscription_saved = false;


  	// IF subscriber does NOT have the current list subscription
  	if(!slb_subscriber_has_subscription($subscriber_id, $list_id)){

  		$subscriptions = array();

  		// get subscriptions and append new $list id
  		$subscriptions = slb_get_subscriptions($subscriber_id);
  		$subscriptions[]= $list_id;


  		// update slb subscriptions
  		update_field(slb_get_acf_key('slb_subscriptions'), $subscriptions, $subscriber_id);

  		// subscriptions update
  		$subscription_saved = true;
  	}

  	return $subscription_saved;
  }

/***********************************************************************************************************************/


/* 6. HELPERS */

//6.1
 function slb_subscriber_has_subscription( $subscriber_id, $list_id){

 	// setup default return value
 	$has_subscription = false;

 	// get subscriber
 	$subscriber = get_post();

 	// get subscriptions
 	$subscriptions = slb_get_subscriptions($subscriber_id);

 	// check subscriptions for list
 	if(in_array($list_id, $subscriptions)){
 		$has_subscription = true;
 	}

 	return $has_subscription;
 }

// 6.2 // Check
 function slb_get_subscriber_id($email){

 	$subscriber_id = 0;

 	try{

 		$subscriber_query = new WP_Query(
 			array(
 				  'post_type' => 'slb_subscriber',
 				  'posts_per_page' => 1,
 				  'meta_key' => 'slb_email',
 				  'meta_query' => array(
 				  		array(
 				  			'key' => 'slb_email',
 				  			'value' => $email,
 				  			'compare' => '='
 				  			),
 				  		),
 				  )
 		);

 		if($subscriber_query -> have_posts()){

 			$subscriber_query -> the_post();
 			$subscriber_id = get_the_ID();
 		}

 	} catch( Exception $e){

 	}

 	wp_reset_query();


 	return (int)$subscriber_id;
 }

// 6.3
 //  get subscriptions
 //  check
  function slb_get_subscriptions($subscriber_id){
  	$subscriptions = array();

  	//get subscriptions (returns array of list object)

  	$lists = get_field('slb_subscriptions', $subscriber_id);

  	// If $lists return something
  	if($lists){

  		// If $lists is an array and there is more than one item
  		if(is_array($lists) && count($lists)){

  			// build lists: array of list ids
  			foreach($lists as $list){
  				$subscriptions[]= $list->ID;
  			}
  		} else if(is_numeric($lists)){

  			// single result is returned
  			$subscriptions[]= $lists;

  		}
  	}

  	return (array)$subscriptions;
  }

// 6.4
  function slb_return_json( $php_array){

  	// encode result as json string
  	$json_result = json_encode($php_array);

  	// return result
  	die($json_result);

  	//stop all processing
  	exit;
  }

// 6.5
// gets the unqiue acf field from the field name
  function slb_get_acf_key( $field_name ){

  	$field_key = ' ';

  	switch ($field_name) {
  		case 'slb_fname':
  			$field_key = 'field_5a579ff87a3f0';
  			break;
  		case 'slb_lname':
  			$field_key = 'field_5a57a0177a3f1';
  			break;
  		case 'slb_email':
  			$field_key = 'field_5a57a02a7a3f2';
  			break;
  		case 'slb_subscriptions':
  			$field_key = 'field_5a57a04f7a3f3';
  			break;
  	}

  	return $field_key;
  }

// 6.6
 // returns an array of subscriber data including subscriptions
  function slb_get_subscriber_data($subscriber_id){

  	// setup subscriber data
  	$subscriber_data = array();

  	// get subscriber object
  	$subscriber = get_post($subscriber_id);

  	// IF subscriber object is valid
  	if( isset($subscriber->post_type) && $subscriber->post_type == 'slb_subscriber'){

  		// build subscriber data for return
  		$subscriber_data = array(
  			'name' => $subscriber->title,
  			'fname' => get_field(slb_get_acf_key('slb_fname'), $subscriber_id),
  			'lname' => get_field(slb_get_acf_key('slb_lname'), $subscriber_id),
  			'email' => get_field(slb_get_acf_key('slb_email'), $subscriber_id),
  			'subscriptions' => slb_get_subscriptions($subscriber_id)
  		);
  	}

  	return $subscriber_data;
  }

/* !7. CUSTOM POST TYPES */




/* !8. ADMIN PAGES */




/* !9. SETTINGS */
