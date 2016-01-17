<?php get_header(); ?>

<div id="primary" class="content-area">
	<div id="content" class="site-content" role="main">
		<?php $item = WP_Remote_Posts_Item::get_instance( (int) get_query_var( 'wp_remote_posts_id' ) ); ?>
		<h1><?php echo $item->title; ?></h1>
		<?php echo $item->content; ?>
	</div>
</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>