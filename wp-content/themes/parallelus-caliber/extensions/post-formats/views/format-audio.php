<div class="cf-elm-block" id="cfpf-format-audio-fields" style="display: none;">
	<div id="postbox-container-postformat" class="postbox-container">
		<div id="meta-box-postformat-video" class="postbox" style="display: block;">

			<div class="handlediv"><br></div>

			<h3><span><?php _e( 'Audio Options', 'runway' ); ?></span></h3>

			<div class="inside">
				<p><?php _e( 'Enter the paths to your audio files in the fields below', 'runway' ); ?></p>

				<table class="form-table">
					<tbody>
						<tr class="postformat_audio_mp3_tr">
							<th>
								<label for="postformat_audio_mp3">
									<strong><?php _e( 'MP3 File URL', 'runway' ); ?></strong>
									<span><?php _e( 'URL to an .mp3 file', 'runway' ); ?></span>
								</label>
							</th>
							<td>
								<input type="text" name="postformat_audio_mp3" id="postformat_audio_mp3"
								       value="<?php echo esc_attr( get_post_meta( $post->ID, 'postformat_audio_mp3', true ) ); ?>"
								       size="30">
							</td>
						</tr>
						<tr class="postformat_audio_ogg_tr">
							<th>
								<label for="postformat_audio_ogg">
									<strong><?php _e( 'OGA File URL', 'runway' ); ?></strong>
									<span><?php _e( 'URL to an .oga or .ogg file', 'runway' ); ?></span>
								</label>
							</th>
							<td>
								<input type="text" name="postformat_audio_ogg" id="postformat_audio_ogg"
								       value="<?php echo esc_attr( get_post_meta( $post->ID, 'postformat_audio_ogg', true ) ); ?>"
								       size="30">
							</td>
						</tr>
						<tr class="postformat_fields_div">
							<td colspan="2" style="padding: 20px 0;">
								<div style="border-top: 1px solid #eeeeee;"></div>
							</td>
						</tr>
						<tr class="postformat_audio_embedded_tr">
							<th>
								<label for="postformat_audio_embedded">
									<strong><?php _e( 'Embedded Audio', 'runway' ); ?></strong>
									<span><?php _e( 'Add embedded audio formats.', 'runway' ); ?></span>
								</label>
							</th>
							<td>
								<textarea name="postformat_audio_embedded" id="postformat_audio_embedded" rows="8"
								          cols="5"><?php echo esc_textarea( get_post_meta( $post->ID, 'postformat_audio_embedded', true ) ); ?>
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