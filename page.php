<?php get_header(); ?>
    		<div class="row">
    			<div class="col-sm-8">
    				<div class='home-page-content'>
    				<?php get_template_part('content'); ?>
    				</div>
    			</div>
    			<div class="col-sm-4">
                    <div class="sidebar">
    				<?php get_template_part('sidebar'); ?>
                </div>
    			</div>
    		</div>
<?php get_footer(); ?>