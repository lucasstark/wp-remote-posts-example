<?php get_header(); ?>
<div id="primary" class="content-area">
	<div id="content" class="site-content" role="main">
		<h1>Your Remote Post Archive</h1>
		
		<p>Modify the WP_Remote_Posts_Data_Api class to get the real data from your remote feed.</p>
		<p>The data below is just mocked up in the WP_Remote_Posts_Data_Api class as of now</p>
		
		<?php $items_data = WP_Remote_Posts_Data_Api::get_posts_data(); ?>
		<ul>
			<?php foreach ( $items_data as $item_data ): ?>
				<?php $item = WP_Remote_Posts_Item::get_instance( $item_data['ID'] ); ?>
				<li><a href="<?php echo $item->permalink; ?>"><?php echo $item->title; ?></a></li>
			<?php endforeach; ?>
		</ul>

	</div>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>