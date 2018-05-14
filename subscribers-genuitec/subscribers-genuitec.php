<?php
/*
Plugin Name:  Subscribers Form
Description:  My first plugin
Version:      1.0
Author:       Cesar Apodaca
License:      GPL2
Text Domain:  wporg
Domain Path:  /languages
*/


/* HOOKS */
	//Register shortcodes
	add_action('init','sl_register_shortcodes');

	//allow use admin-ajax for regular visitors
	add_action('wp_ajax_nopriv_sl_save_subscription', 'sl_save_subscription');

	//allow use admin-ajax admin users
	add_action('wp_ajax_sl_save_subscription', 'sl_save_subscription'); // admin user

	//include my scripts (js)
	add_action('wp_enqueue_scripts', 'sl_public_scripts');

	//include my styles
	wp_enqueue_style( 'styles', get_site_url() . '/wp-content/plugins/subscribers-genuitec/style.css',false,'1.1','all');

/* SHORTCODES */
	function sl_register_shortcodes()
	{
		add_shortcode('sl_form','sl_form_shortcode');
	}

	function sl_form_shortcode ($args, $content="")
	{
		$list_id = 0;
		try
		{
			$subscription_lists = \MailPoet\API\API::MP( 'v1' )->getLists();
			foreach ($subscription_lists as $key => $value)
				if($value['name'] == 'Newsletter')
				{
					$list_id = $value['id'];
					break;
				}
		} catch ( Exception $e ) {}

		if($list_id)
		{
			$current_user = wp_get_current_user();
			$email = $current_user->user_email;
			$output = '
			<div class="sl">
			    <form id="sl_form" name="sl_form" class="sl_form" method="POST"
			    action="' . get_site_url() . '/wp-admin/admin-ajax.php?action=sl_save_subscription" >
			    	<input type="hidden" name="sl_list" value="' . $list_id . '">
			        <p class="sl_input_container">
			            <label>Your email</label><br />
			            <input type="email" name="sl_email" placeholder="Email" value="' . $email . '" required/>
			        </p>
			        <p class="slb_input_container">
			            <input type="submit" name="sl_submit" value="Sign Me Up" />
			            </p>
			        </form>
			    </div>
			    <div id="successForm" class="successForm notVisible">
			        <div class="">
			        	<p>Thanks for subscribing to our Newsletter.</p>
			        	<span>Close</span>
					</div>
				</div>
			    ';

			return $output;
		}
		return "You need to create a list called Newsletter";
	}


/* EXTERNAL SCRIPTS */
	function sl_public_scripts()
	{
		wp_enqueue_script( 'script', get_site_url() . '/wp-content/plugins/subscribers-genuitec/script.js', array ( 'jquery' ), 1.1, true);
	}


/* ACTIONS */
	function sl_save_subscription()
	{
		try
		{
			$subscriber_email = array(
				'email' => sanitize_text_field($_POST['sl_email'])
			);
			$list_id= array($_POST['sl_list']);
			$options = array(
				'send_confirmation_email' => false, // default: true
                'schedule_welcome_email' => false // default: true
			);
			$subscriber = \MailPoet\API\API::MP('v1')->addSubscriber($subscriber_email, $list_id, $options);
			echo json_encode($subscriber);
		}
		catch (Exception $e){}

	}

