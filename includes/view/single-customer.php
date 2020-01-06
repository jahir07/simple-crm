<?php get_header(); ?>

<div class="container">
	<?php if(have_posts()) : while (have_posts()) : the_post(); ?>
		<?php 
		$phone = get_post_meta( get_the_ID(),'your_phone', true );
		$your_email = get_post_meta( get_the_ID(),'your_email', true );
		$your_budget = get_post_meta( get_the_ID(),'your_budget', true );
		$current_time = get_post_meta( get_the_ID(),'current_time', true );
		?>
		<b>Name:</b> 
		<br><?php the_title() ?> <br>
		<?php if(!empty($your_email )) { ?>
			<b>Your Email:</b> <br><?php echo esc_html( $your_email ) ?> <br>
		<?php } ?>

		<?php if(!empty($phone )) { ?>
			<b>Phone:</b> <br><?php echo esc_html( $phone ) ?> <br>
		<?php } ?>

		<?php if(!empty($your_budget )) { ?>
			<b>Your Budget: </b><br><?php echo esc_html( $your_budget ) ?> <br>
		<?php } ?>

		<b>Message:</b> <br><?php the_content() ?> <br>

		<?php if(!empty($current_time )) { ?>
			<b>Time: </b><?php echo esc_html( $current_time ) ?> <br>
		<?php } ?>

		
	<?php endwhile; endif; ?>
</div>
<?php get_footer(); ?>