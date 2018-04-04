<?php
/**
 * The template for displaying search forms
 */
?>
<form role="search" method="get" class="search-form form-inline" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<div class="form-group">
		<input type="search" class="search-field form-control" placeholder="<?php echo esc_attr_x( 'Search...', 'placeholder', 'parallelus-caliber' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s" title="<?php _ex( 'Search for:', 'label', 'parallelus-caliber' ); ?>">
		<input type="submit" class="search-submit btn btn-default" value="<?php echo esc_attr_x( 'Search', 'submit button', 'parallelus-caliber' ); ?>">
	</div>
</form>
