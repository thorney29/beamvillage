<div class="cf-elm-block" id="cfpf-format-video-fields" style="display: none;">
	<div id="postbox-container-postformat" class="postbox-container">
		<div id="meta-box-postformat-video" class="postbox" style="display: block;">

			<div class="handlediv"><br></div>
			<h3><span><?php _e( 'Video Options', 'runway' ); ?></span></h3>

			<div class="inside">

				<p>
					<?php _e( 'For HTML5 video support and Flash fallback please include an M4V file. Include an OGV file optionally to increase cross browser support.', 'runway' ); ?>
				</p>

				<table class="form-table">
					<tbody>
						<tr class="postformat_video_m4v_tr">
							<th>
								<label for="postformat_video_m4v">
									<strong><?php _e( 'M4V File URL', 'runway' ); ?></strong>
									<span><?php _e( 'The URL to the .m4v video file', 'runway' ); ?></span>
								</label>
							</th>
							<td>
								<input type="text" name="postformat_video_m4v" id="postformat_video_m4v"
								       value="<?php echo esc_attr( get_post_meta( $post->ID, 'postformat_video_m4v', true ) ); ?>"
								       size="30">
							</td>
						</tr>
						<tr class="postformat_video_ogv_tr">
							<th>
								<label for="postformat_video_ogv">
									<strong><?php _e( 'OGV File URL', 'runway' ); ?></strong>
									<span><?php _e( 'The URL to the .ogv video file', 'runway' ); ?></span>
								</label>
							</th>
							<td>
								<input type="text" name="postformat_video_ogv" id="postformat_video_ogv"
								       value="<?php echo esc_attr( get_post_meta( $post->ID, 'postformat_video_ogv', true ) ); ?>"
								       size="30">
							</td>
						</tr>
						<tr class="postformat_video_webm_tr">
							<th>
								<label for="postformat_video_webm">
									<strong><?php _e( 'WEBM File URL', 'runway' ); ?></strong>
									<span><?php _e( 'The URL to the .webm video file', 'runway' ); ?></span>
								</label>
							</th>
							<td>
								<input type="text" name="postformat_video_webm" id="postformat_video_webm"
								       value="<?php echo esc_attr( get_post_meta( $post->ID, 'postformat_video_webm', true ) ); ?>"
								       size="30">
							</td>
						</tr>
						<tr class="postformat_video_poster_tr">
							<th>
								<label for="postformat_video_poster">
									<strong><?php _e( 'Video Poster', 'runway' ); ?></strong>
									<span><?php _e( 'A preivew image.', 'runway' ); ?></span>
								</label>
							</th>
							<td>
								<input type="text" name="postformat_video_poster" id="postformat_video_poster"
								       value="<?php echo esc_attr( get_post_meta( $post->ID, 'postformat_video_poster', true ) ); ?>"
								       size="30">
							</td>
						</tr>
						<tr class="postformat_fields_div">
							<td colspan="2" style="padding: 20px 0;">
								<div style="border-top: 1px solid #eeeeee;"></div>
							</td>
						</tr>
						<tr class="postformat_video_embed_tr">
							<th>
								<label for="postformat_video_embed">
									<strong><?php _e( 'Embedded Code', 'runway' ); ?></strong>
									<span>
										<?php _e( 'If not using self hosted video you can include embedded code here.', 'runway' ); ?>
									</span>
								</label>
							</th>
							<td>
								<textarea name="postformat_video_embed" id="postformat_video_embed" rows="8"
								          cols="5"><?php echo esc_textarea( get_post_meta( $post->ID, 'postformat_video_embed', true ) ); ?>
								</textarea>
							</td>
						</tr>
					</tbody>
				</table>

			</div>
		</div>
	</div>
	<div class="clear"></div>
</div>
