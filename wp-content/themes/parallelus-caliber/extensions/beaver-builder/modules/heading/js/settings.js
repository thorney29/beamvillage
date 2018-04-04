(function($){

	FLBuilder.registerModuleHelper('heading', {

		rules: {
			heading: {
				required: true
			}
		},
		
		init: function()
		{
			var form = $('.fl-builder-settings');

			// Init validation events.
			this._fontSizeChanged();
			this._mobileFontSizeChanged();
			this._headingTypeChanged();
			
			// Validation events.
			form.find('select[name=font_size]').on('change', this._fontSizeChanged);
			form.find('select[name=r_font_size]').on('change', this._mobileFontSizeChanged);
			form.find('select[name=heading_type]').on('change', this._headingTypeChanged);
		},
		
		_fontSizeChanged: function()
		{
			var form        = $('.fl-builder-settings'),
				fontSize    = form.find('select[name=font_size]').val(),
				customSize  = form.find('input[name=custom_font_size]');
				
			customSize.rules('remove');
			
			if(fontSize == 'custom') {
				customSize.rules('add', { 
					number: true,
					required: true 
				});
			}
		},
		
		_mobileFontSizeChanged: function()
		{
			var form        = $('.fl-builder-settings'),
				fontSize    = form.find('select[name=r_font_size]').val(),
				customSize  = form.find('input[name=r_custom_font_size]');
				
			customSize.rules('remove');
			
			if(fontSize == 'custom') {
				customSize.rules('add', { 
					number: true,
					required: true 
				});
			}
		},

		_headingTypeChanged: function() {
			var form        = $('.fl-builder-settings'),
				type        = form.find('select[name=heading_type]').val();

			if ( type == 'caliber' ) {
				form.find('#fl-builder-settings-section-default_options').hide();
				form.find('.fl-builder-settings-tabs a[href*="style_default"]').hide();
				form.find('#fl-builder-settings-section-caliber_options').show();
				form.find('#fl-field-pre_heading').show();
				form.find('#fl-field-lead').show();
			} else {
				form.find('#fl-builder-settings-section-default_options').show();
				form.find('.fl-builder-settings-tabs a[href*="style_default"]').show();
				form.find('#fl-builder-settings-section-caliber_options').hide();
				form.find('#fl-field-pre_heading').hide();
				form.find('#fl-field-lead').hide();
			}
		}
	});


})(jQuery);