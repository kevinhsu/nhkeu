<?php
// 
//  esu-utility-functions.php
//  easy-sign-up
//  
//  Created by Rew Rixom on 2011-03-29.
//
/** 
* Utility Functions
* Helper functions
* **/

/* Get plugin URL */

// Start esu_plugin_url
function esu_plugin_url($path='')
{
 	global $wp_version;
	if ( version_compare( $wp_version, '2.8', '<' ) ) { // Using WordPress 2.7
		$folder = dirname( plugin_basename( __FILE__ ) );
		if ( '.' != $folder )
			$path = path_join( ltrim( $folder, '/' ), $path );
		return plugins_url( $path );
	}
	return plugins_url( $path, __FILE__ );
}

// Start esu_check_table_existance // Check that the $new_table is here
if ( ! function_exists('esu_check_table_existance')) 
{
  function esu_check_table_existance($new_table)
  {
   	//NB Always set wpdb globally!
  	global $wpdb;
  	foreach ($wpdb->get_col("SHOW TABLES",0) as $table ){
  		if ($table == $new_table){
  			return true;
  		}
  	}
  	return false;
  }
} // End esu_check_table_existance

function esu_is_win_server(){
  if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
      return true;
  } else {
      return false;
  }
} 

function esu_ar($array, $name){
  if(isset($array[$name]))
      return $array[$name];
  return '';
}

/**
* Filters, Hooks & Actions
**/

/**
* Sets up the default ESU form elements
* called by EsuForms::esu_build_form() 
* found in -> /lib/esu-front-end-class.php
* has 3 filter hooks:
*   esu_add_extra_form_fields_before
*   esu_add_extra_form_fields_middle
*   esu_add_extra_form_fields_after
* @since 3.0
***/
function esu_default_form_setup($esu_class=null){
  $formArray   = array();
  // let the filters know what type of form by the class
  $formArray['esu-form-type'] = $esu_class;
  // before filter
  $extra_options_before = apply_filters('esu_add_extra_form_fields_before',false);
  if ($extra_options_before!=null) $formArray = array_merge($extra_options_before,$formArray);

  $formArray['fn'] = array(
    'name'    => __('First Name', 'esu_lang'),
    'validate'=> 'esu-required-input',
    'id'      => 'fname',
    'class'   => 'esu-input',
    'type'    => 'text');

  $formArray['ln'] = array(
    'name'    => __('Last Name', 'esu_lang'),
    'validate'=> 'esu-required-input',
    'id'      => 'lname',
    'class'   => 'esu-input',
    'type'    => 'text');

  $formArray['n'] = array(
    'name'    => __('Name', 'esu_lang'),
    'validate'=> 'esu-required-input',
    'id'      => 'name',
    'class'   => 'esu-input',
    'type'    => 'text');

  // middle filter
  $extra_options_middle = apply_filters('esu_add_extra_form_fields_middle',false);
  if ($extra_options_middle!=null) $formArray = array_merge($formArray,$extra_options_middle);

  $formArray['e'] = array(
    'name'    => __('Email', 'esu_lang'),
    'validate'=> 'esu-required-email',
    'id'      => 'email',
    'class'   => 'esu-input',
    'type'    => 'text');

  $formArray['p'] = array(
    'name'    => __('Phone', 'esu_lang'),
    'validate'=> 'esu-required-phone',
    'id'      => 'phone',
    'class'   => 'esu-input',
    'type'    => 'text');

  // after filter
  $extra_options_after = apply_filters('esu_add_extra_form_fields_after',false);
  if ($extra_options_after!=null) $formArray = array_merge($formArray,$extra_options_after);

  $formArray['send'] = array(
    'name'    => __('Send', 'esu_lang'),
    'id'      => 'send',
    'class'   => 'esu-button',
    'type'    => 'submit');
  // filter for entire array
  $formArray = apply_filters('esu_default_form_options', $formArray );
  if ( array_key_exists('img',$formArray) && array_key_exists('send',$formArray) ) unset($formArray['send']);
  return $formArray;
}

/**
 * This is an array of options for the back-end
 * Has Filter esu_add_extra_options
 **/
function esu_options_array(){
  $e_options = array (
        array("name" => __('Notification Email','esu_lang'),
              "desc" => __('<p class="description">Where you want <strong>your</strong> Notification Email sent. Normally your email address.</p>','esu_lang'),
              "size"=> "50",
              "maxlength"=> "70",
              "id" => ESU_S_NAME."_co_email",
              "std" => ESU_DEFAULT_EMAIL,
              "type" => "text"),
        array("name" => __('Automated Reply Email','esu_lang'),
              "desc" => __('<p class="description">This is the <strong>Reply To</strong> email address the user sees.
                </p><p class="description">You may want to set it to your general email address.</p>','esu_lang'),
              "size"=> "50",
              "maxlength"=> "70",
              "id" => ESU_S_NAME."_co_from_email",
              "std" => ESU_DEFAULT_EMAIL,
              "type" => "text"),
        array("name" => __('Redirection URL','esu_lang'),
              "desc" => __('<p class="description">This is the website address that your user will be redirect to once they press the send button. 
              You could send them to a thank you page in your website.</p><p class="description"><em><strong>Tip:</strong> It does not have to be on your website.</em></p>','esu_lang'),
              "size"=> "80",
              "maxlength"=> "350",
              "id" => ESU_S_NAME."_url",
              "std" => WP_URL,
              "type" => "text"),
        array("name" => __('Thank You Email to Client','esu_lang'),
              "desc" => '<p><code>#fullname#</code>'.__(' is the placeholder for the name field of the form.<br> When you customize the text <strong>remember to add it back in if you want to personalize</strong> the confirmation letter.</p>','esu_lang').
                '<p class="description">'.
                  __("Be aware that some email applications block images from loading and displaying unless a user clicks on show or download images in their email account. So if they have this security feature enabled (By choice or default) it will appear that there are no images showing in your recipients' inbox. There is nothing that you can do to influence that, force images to display or get around that email inbox security feature.",'esu_lang').
                '</p>',
              "id" => ESU_S_NAME."_thank_you_email",
              "std" => __("Hi #fullname# Thank you for visiting our website<br>We hope that you found it informative.<br><br>Regards,<br> ".WP_BLOG_NAME,'esu_lang'),
              "type" => "wp_editor"),
        array("name" => __('Slam Spam with Akismet','esu_lang'),
              "desc" => __('This requires you to have the <a href="http://wordpress.org/extend/plugins/akismet/" target="_blank">Akismet plugin</a> installed and enable.' ,'esu_lang'),
              "id" => ESU_S_NAME."_use_askismet",
              "std" => 'true', // on by default
              "type" => "checkbox"),
  );
  $add_array = apply_filters('esu_add_extra_options',false);
  if ($add_array!=null) {
   $e_options = array_merge($e_options,$add_array);
  }
  $deactivate_array = array(
    array(
      "name" => __('Delete Setting and Data* on Deactivation','esu_lang'),
      "desc" => __('Before you deactivate this plugin check this box if you want to clean up the database. <p class="description">* If you use the <a href="http://wordpress.org/extend/plugins/akismet/" target="_blank">Easy Data Plugin</a> your data will not be removed.</p>','esu_lang'),
      "id" => ESU_S_NAME."_delete_settings",
      "std" => '', // off by default
      "type" => "checkbox"
    ),
  );
  $e_options = array_merge($e_options,$deactivate_array);

  $e_options = apply_filters('esu_default_options_filter', $e_options );

  return $e_options;
}

/**
* Custom administration alert message
**/


/**
* RSS Feeds
* **/
if (is_admin()) {
  include_once(ABSPATH . WPINC . '/feed.php');
}

function esu_feeds($url=null, $args){
  $defaults = array(
                'id'=>null,
                'ele_class'=>'easy-rss',
                'feed_items'=>5,
                'show_sub_link'=>true,
                'show_content'=>false,
                'debug'=>false,
              );
  $args = wp_parse_args($args, $defaults); 
  $args = (object)$args;
  if ($url==null) return false;
  // Get a SimplePie feed object from the specified feed source.
  $rss = fetch_feed($url);// http://feeds.feedburner.com/easysignup
  if ( is_wp_error($rss) ) return; // bad feed

  if (!is_wp_error( $rss ) ) : // Checks that the object is created correctly 
    // Figure out how many total items there are, but limit it to 5. 
    $maxitems = $rss->get_item_quantity($args->feed_items); 
    // Build an array of all the items, starting with element 0 (first element).
    $rss_items = $rss->get_items(0, $maxitems); 
  endif;
  
  $return  = '';
  $return .= "<ul class='{$args->ele_class}' id='{$args->id}'>";
  if ($maxitems == 0) $return .= '<li>No items.</li>';
  
  // Loop through each feed item and display each item as a hyperlink.
  foreach ( $rss_items as $item ) :
    $return .= "<li>";
      $return .= "<a href='{$item->get_permalink()}'";
      $return .= "title='Posted ".$item->get_date('j F Y | g:i a')."'>";
      $return .= esc_html($item->get_title());
      $return .= "</a>";
      if ($args->show_content):
        $return .= "<ul>";
          $return .= "<li>";
            $return .= $item->get_content();
          $return .= "</li>";
        $return .= "</ul>";
      endif;
    $return .= "</li>";
  endforeach; 
  if($args->debug): $return .= "<pre>"; $return .= print_r( $rss_items, true ); $return .= "</pre>"; endif; // debug
  $return .= "</ul>";
  if ($args->show_sub_link):
    $return .= "<p>";
      $return .= "<a href='{$url}' rel='alternate' type='application/rss+xml'><img src='http://www.feedburner.com/fb/images/pub/feed-icon16x16.png' alt='' style='vertical-align:middle;border:0'></a>&nbsp;";
      $return .= "<a href='{$url}' rel='alternate' type='application/rss+xml'>Subscribe in a reader</a>";
    $return .= "</p>";
  endif;
  return $return;
} // ends the function esu_feeds

/* EOF */

