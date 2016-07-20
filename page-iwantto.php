<?php
/*
Template Name: I want to Page
*/
?>
<?php get_header(); ?>
<div style='margin-top:-40px;margin-bottom:100px;padding-bottom:30px;border-bottom:1px solid rgba(0,0,0,0.1);'>
	<div style='width:800px;margin-left:auto;margin-right:auto;max-width:100%;'>
	<a style='width:800px;margin-left:auto;margin-right:auto;' href='http://www.e-atelier.id.tue.nl/?e_tutorial=getting-started'><img style='max-width:100%;margin-left:auto;margin-right:auto; width:800px;' src='http://www.e-atelier.id.tue.nl/wp-content/uploads/2015/08/homepagebanner@2x.png'></a>
	</div>
</div>
<p class='iwantto-intro-sentence'>
	<br>
	the <strong>start</strong> of <br>your <em>electronics</em> project
	<br><br>
	<i class="fa fa-arrow-down"></i>
</p>
<h1 class='iwantto-title'>i want to</h1>
<div class="row">
	<div class="col-sm-4">
		<?php $illustration = get_field('build_image'); ?>
		<h2 class='iwantto-answer'>Build<img width='50px' height='50px' src="<?php echo $illustration['url'] ?>"></h2>
		<p class='iwantto-tutorial-label'><i class="fa fa-arrow-down"></i> tutorials</p>
		<?php
		while(have_rows('build_tutorials')) : the_row();

			$tutorial = get_sub_field('tutorial');
			$post = $tutorial;
			setup_postdata( $post );
			?>
			<a href="<?php the_permalink(); ?>">
			<div class='iwantto-tutorial-block'>
				<h3><?php the_title(); ?></h3>
				<p>tutorial excerpt</p>
			</div>
			</a>

			 <?php wp_reset_postdata(); ?>
		<?php endwhile; ?>
		<p>more coming soon!</p>
	</div>
	<div class="col-sm-4">
		<?php $illustration = get_field('sense_image'); ?>
		<h2 class='iwantto-answer'>Sense<img width='50px' height='50px' src="<?php echo $illustration['url'] ?>"></h2>
		<p class='iwantto-tutorial-label'><i class="fa fa-arrow-down"></i> tutorials</p>
		<?php
		while(have_rows('sense_tutorials')) : the_row();

			$tutorial = get_sub_field('tutorial');
			$post = $tutorial;
			setup_postdata( $post );
			?>
			<a href="<?php the_permalink(); ?>">
			<div class='iwantto-tutorial-block'>
				<h3><?php the_title(); ?></h3>
				<p>tutorial excerpt</p>
			</div>
			</a>

			 <?php wp_reset_postdata(); ?>
		<?php endwhile; ?>
		<p>more coming soon!</p>
	</div>
	<div class="col-sm-4">
		<?php $illustration = get_field('move_image'); ?>
		<h2 class='iwantto-answer'>(Re)act<img width='50px' height='50px' src="<?php echo $illustration['url'] ?>"></h2>
		<p class='iwantto-tutorial-label'><i class="fa fa-arrow-down"></i> tutorials</p>
		<?php
		while(have_rows('move_tutorials')) : the_row();

			$tutorial = get_sub_field('tutorial');
			$post = $tutorial;
			setup_postdata( $post );
			?>
			<a href="<?php the_permalink(); ?>">
			<div class='iwantto-tutorial-block'>
				<h3><?php the_title(); ?></h3>
				<p>tutorial excerpt</p>
			</div>
			</a>

			 <?php wp_reset_postdata(); ?>
		<?php endwhile; ?>
		<p>more coming soon!</p>
	</div>


</div>
<p style='text-align:center;position:relative;top:60px;'>More tutorials can be found at the specific tutorial pages.</p>
<!--
<div class="row">
	<div class="col-sm-12">
		<h1><small>hi,</small>Welcome!</h1>
	</div>
</div>
<div class="row">
	<div class='col-sm-6'>
		<h2>I want to sense...</h2>
	</div>
	<div class='col-sm-6'>
		<h2>I want to make/influence...</h2>
	</div>
	<div class='col-sm-6'>
		<h2>Other electronic stuff I need...</h2>
	</div>
	<div class='col-sm-6'>
		<h2>Skills I need...</h2>
	</div>
</div>
-->

<?php get_footer(); ?>
