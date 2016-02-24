<?php
/* @var $post_type_object WP_Remote_Posts_Post_Type_Model */
/* @var $post WP_Remote_Post_Type_Post */

$_edit_link = $this->_remote_post_type->_edit_link;
$post_new_file = admin_url( sprintf( $_edit_link . '&amp;action=create', 'new' ) );
$post_update_file = admin_url( sprintf( $_edit_link . '&amp;action=edit', $post->id ) );
$post_trash_file = $trash_link = admin_url( sprintf( $_edit_link . '&amp;action=trash', $post->id ) );

$form_action = 'editpost';
$nonce_action = 'update-post_' . $post->id;


?>

<div class="wrap">
	<h1><?php
		echo esc_html( $post_type_object->labels->name );
		if ( isset( $post_new_file ) && current_user_can( $post_type_object->cap->create_posts ) ) :
			echo ' <a href="' . $post_new_file . '" class="page-title-action">' . esc_html( $post_type_object->labels->add_new ) . '</a>';
		endif;
		?>
	</h1>


	<form name="post" action="<?php echo $post_update_file; ?>" method="post" id="post">
		<input type="hidden" name="post_id" value="<?php echo $post->id; ?>" />
		<input type="hidden" name="form_action" value="<?php echo $form_action; ?>" />
		<?php wp_nonce_field($nonce_action); ?>
		
		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-<?php echo 1 == get_current_screen()->get_columns() ? '1' : '2'; ?>">
				<div id="post-body-content">

					<?php if ( $post_type_object->supports( 'title' ) ) : ?>
						<div id="titlediv">
							<div id="titlewrap">
								<?php
								/**
								 * Filter the title field placeholder text.
								 *
								 * @since 3.1.0
								 *
								 * @param string  $text Placeholder text. Default 'Enter title here'.
								 * @param WP_Post $post Post object.
								 */
								$title_placeholder = apply_filters( 'enter_title_here', __( 'Enter title here' ), $post );
								?>
								<label class="screen-reader-text" id="title-prompt-text" for="title"><?php echo $title_placeholder; ?></label>
								<input type="text" name="post_title" size="30" value="<?php echo esc_attr( $post->title->raw ); ?>" id="title" spellcheck="true" autocomplete="off" />
							</div>
						</div>
					<?php endif; ?>
				</div>

				<div id="postbox-container-1" class="postbox-container">
					<div id="side-sortables" class="meta-box-sortables">

						<div id="submitdiv" class="postbox">
							<h2 class="hndle ui-sortable-handle">
								<span><?php _e( 'Publish' ); ?></span>
							</h2>
							<div class="inside">
								<div id="major-publishing-actions">
									<div id="delete-action">
										<a class="submitdelete deletion" href="<?php echo wp_nonce_url($post_trash_file, "'trash-post_{$post->id}"); ?>"><?php _e('Move to Trash'); ?></a></div>
									<div id="publishing-action">
										<span class="spinner"></span>
										<input name="original_publish" type="hidden" id="original_publish" value="Update">
										<input name="save" type="submit" class="button button-primary button-large" id="publish" value="<?php _e('Update'); ?>">
									</div>
									<div class="clear"></div>
								</div>
							</div>
						</div>

					</div>

				</div>


			</div>
		</div>

	</form>

</div>
