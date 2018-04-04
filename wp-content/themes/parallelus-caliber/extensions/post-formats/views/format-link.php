<div class="cf-elm-block" id="cfpf-format-link-url" style="display: none;">
	<div id="postbox-container-postformat" class="postbox-container">
		<div id="meta-box-postformat-video" class="postbox" style="display: block;">

			<div class="handlediv"><br></div>

			<h3><span><?php _e( 'Link URL', 'runway' ); ?></span></h3>

			<div class="inside">
				<table class="form-table">
					<tbody>
						<tr>
							<th>
								<label for="postformat_link_url">
									<strong><?php _e( 'Link URL', 'runway' ); ?></strong>
									<span><?php _e( 'The URL of your link.', 'runway' ); ?></span>
								</label>
							</th>
							<td>
								<input type="text" name="postformat_link_url" id="postformat_link_url"
								       value="<?php echo esc_attr( get_post_meta( $post->ID, 'postformat_link_url', true ) ); ?>"
								       size="30">
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="clear"></div>
</div>