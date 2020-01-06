<?php
namespace Simple_CRM\Classes;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Load class which responsible for frontend only
 *
 * @return void 
 */

Class Frontend {

	/**
     * Frontend Display constructor.
     */
	function __construct() {
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_script' ] );
		add_shortcode( 'customer_form', [ $this, 'add_fields'] );
		
		//ajax		
		add_action( 'wp_ajax_post_customer_info', [ $this, 'post_customer_info_func'] ); // when logged in
		add_action( 'wp_ajax_nopriv_post_customer_info', [ $this, 'post_customer_info_func'] );//when logged out 

		//single view
		add_filter( 'single_template', [ $this, 'single_view' ], 10, 1 );

	}

	/**
	 * enqueue fontend scripts & styles
	 *
	 * @return void 
	 */
	public function enqueue_script() {
		// styles
		wp_enqueue_style('simple-crm-style');

		//jquery define
		wp_enqueue_script( 'jquery' );
	}

	/**
	 * Add custer optin from
	 *
	 * @return mixed 
	 */
	public function add_fields($atts, $content = null) {

		extract( 
			shortcode_atts(
				array(
					'title'					=> 'Name',
					'email_label'			=> 'Email Address',
					'phone_label'			=> 'Your Phone No',
					'desired_budget'		=> 'Desired Budget',
					'your_msg'				=> 'Message',
				), $atts 
			)
		);

		ob_start();

		//by php done
		// if( isset($_POST['submit_post']) == '1' ){

		// 	$name = $_POST['your_name'];
		// 	$email = $_POST['your_email'];
		// 	$phone = $_POST['your_phone'];
		// 	$budget = $_POST['your_budget'];
		// 	$msg =  $_POST['your_message'];
		// 	$poststatus = 'private';

		// 	$post = array(
		// 		'post_title'    => $name,
		// 		'your_email'  => $email,
		// 		'your_phone'   => $phone,
		// 		'your_budget' => $budget,
		// 		'post_content'    => $msg,
		// 		'post_status'   => $poststatus,
		// 		'post_type' => 'customer'
		// 	);
		// 	$post_insert = wp_insert_post( $post );
		// 	$your_email = sanitize_text_field( $_POST['your_email'] );
		// 	$your_phone = sanitize_text_field( $_POST['your_phone'] );
		// 	$your_budget = sanitize_text_field( $_POST['your_budget'] );
		// 	update_post_meta( $post_insert, 'your_email', $your_email );
		// 	update_post_meta( $post_insert, 'your_phone', $your_phone );
		// 	update_post_meta( $post_insert, 'your_budget', $your_budget );
		// }

		?>

		<div id="customer-form-container">

			<div class="form-group">
				<label for="<?php echo esc_attr($title) ?>"><?php echo esc_html($title) ?></label>
				<input type="text" name="your_name" class="form-control" id="yourname" placeholder="Name" maxlength="20" required>
				<small id="emailHelp" class="form-text text-muted">Max Length 20</small>
			</div>
			<div class="form-group">
				<label for="<?php echo esc_attr($email_label) ?>"><?php echo esc_html($email_label) ?></label>
				<input type="email" name="your_email" class="form-control" id="youremail" placeholder="Enter Email Here" maxlength="30" required>
				<small id="emailHelp" class="form-text text-muted">Max Length 30</small>
			</div>
			<div class="form-group">
				<label for="<?php echo esc_attr($phone_label) ?>"><?php echo esc_html($phone_label) ?></label>
				<input type="number" name="your_phone" class="form-control" id="yourphone" placeholder="Your Phone" maxlength="12" required>
			</div>
			
			<div class="form-group">
				<label for="<?php echo esc_attr($desired_budget) ?>"><?php echo esc_html($desired_budget) ?></label>
				<input type="number" name="your_budget" class="form-control" id="yourbudget" placeholder="23424" maxlength="5" required>
			</div>
			<div class="form-group">
				<label for="<?php echo esc_attr($your_msg) ?>"><?php echo esc_html($your_msg) ?></label>
				<textarea name="your_message" col="5" rows="3" class="form-control" id="yourmsg" placeholder="Write Your Message" maxlength="500" required></textarea>
			</div>

			<?php 
			$time = $this->get_time_date()->currentDateTime;
			?>
			<input id="current_time" type="hidden" name="current_time" value="<?php echo $time ?>" />
			<!-- <input type="hidden" name="submit_post" value="1" />
				<input type="submit" name="submit" value="Submit" /> -->
				<button id="customer_info_submit" class="btn btn-primary">Submit</button>

			</div>
			<div id="submission_msg"></div>
			
			
			<script>
				jQuery("#customer_info_submit").on( 'click', function( e ) {
					e.preventDefault();
					jQuery.ajax({
						type       : "POST",
						data       : {
							'action': 'post_customer_info',
							'post_title': jQuery('#customer-form-container #yourname').val(),
							'your_email': jQuery('#customer-form-container #youremail').val(), 
							'your_phone': jQuery('#customer-form-container #yourphone').val(), 
							'your_budget': jQuery('#customer-form-container #yourbudget').val(),
							'post_content': jQuery('#customer-form-container #yourmsg').val(),
							'current_time': jQuery('#customer-form-container #current_time').val(),
						},
						url        : '<?php echo admin_url( 'admin-ajax.php' ); ?>',
						beforeSend: function () {
							// console.log('sending');
							jQuery("#submission_msg").html("Loading");

						},
						success: function(data) {
							// console.log(data);
							jQuery("#submission_msg").addClass("success");
							jQuery("#submission_msg").html("Your Data Sent Successfully");
						},
						error: function() {
							// console.log('opps');
							jQuery("#submission_msg").addClass("fail");
							jQuery("#submission_msg").html("Something Is Wrong");

						}
					})
				});
			</script>

			<?php
			return ob_get_clean();

		}


		/**
		 * ajax request
		 */
		public function post_customer_info_func() {

			$name = $_POST['post_title'];
			$email = $_POST['your_email'];
			$phone = $_POST['your_phone'];
			$budget = $_POST['your_budget'];
			$msg =  $_POST['post_content'];
			$current_time =  $_POST['current_time'];
			$poststatus = 'private';
			$post = array(
				'post_title'    => $name,
				'your_email'  => $email,
				'your_phone'   => $phone,
				'desired_budget' => $budget,
				'post_content'    => $msg,
				'post_status'   => $poststatus,
				'post_type' => 'customer'
			);
			$post_insert = wp_insert_post( $post );
			$your_email = sanitize_text_field( $_POST['your_email'] );
			$your_phone = sanitize_text_field( $_POST['your_phone'] );
			$your_budget = sanitize_text_field( $_POST['your_budget'] );
			$current_time = sanitize_text_field( $_POST['current_time'] );
			update_post_meta( $post_insert, 'your_email', $your_email );
			update_post_meta( $post_insert, 'your_phone', $your_phone );
			update_post_meta( $post_insert, 'your_budget', $your_budget );
			update_post_meta( $post_insert, 'current_time', $current_time );

			wp_die();
		}


		/**
		 * single view
		 *
		 * @return template 
		 */
		public function single_view($template){

			global $post;

			if ( $post->post_type == "customer" && $template !== locate_template(array("single-customer.php"))){
		        /* 
		        * Load single page 
		        */
		        return SCRM_DIR . "includes/view/single-customer.php";
		    }

		    return $template;
		}


		/**
		 * get current time date from api response
		 *
		 * @return array 
		 */
		public function get_time_date() {
			$url = file_get_contents('http://worldclockapi.com/api/json/est/now');
			$data = json_decode($url);

			return $data;
		}


	}