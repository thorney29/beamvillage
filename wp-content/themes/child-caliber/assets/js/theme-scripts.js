"use strict";

var p_thm = {
	'navBreak' : 960,
	'width'    : window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth
};
var p_lastWidth = 0;

jQuery(document).ready(function($){

	// refresh vars on resize
	$(window).resize(function () {
		p_thm.width = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
	});

	// Page page sticky navbar
	// ------------------------------------------------------------------------

	if ( $('.navbar-sticky').length ) {

		var stickyElement = $('.navbar-sticky');
		var navbarTop = 0; // stickyElement.outerHeight();
		var boxedLayout = false;

		// If we have a boxed layout. This changes a few things.
		if ( $('.boxed .navbar-sticky').length ) {
			boxedLayout = true;
			var navbarOffset = $('.boxed .navbar-sticky').offset();
			var navbarHeight = stickyElement.outerHeight();
			navbarTop = navbarOffset.top + (navbarHeight * 1.35);
			stickyElement.parent().css('height', navbarHeight + 'px');
		}

		$(window).scroll(function () {
			var y = $(this).scrollTop();
			if (y > navbarTop) {
				if (boxedLayout && !$('body').hasClass('shrink-nav')) {
					stickyElement.css('top',-navbarHeight+'px');
				}
				$('body').addClass('shrink-nav');
				if (stickyElement.hasClass('navbar-static-top')) {
					stickyElement.addClass('navbar-fixed-top static-placeholder').removeClass('navbar-static-top');
				}
				if (stickyElement.css('top') !== 0) {
					stickyElement.stop().delay(100).animate({top: 0}, 300);
				}

			} else if (y <= navbarTop && $('body').hasClass('shrink-nav')) {
				if (boxedLayout) {
					stickyElement.css('top',0);
				}
				$('body').removeClass('shrink-nav');
				if (stickyElement.hasClass('static-placeholder')) {
					stickyElement.addClass('navbar-static-top').removeClass('navbar-fixed-top static-placeholder');
				}
			}

		});

		$(window).trigger('scroll');
	}


	// Layout helper for special containers
	// ------------------------------------------------------------------------

	// Masthead Full Screen
	if ( $('.masthead-full').length ) {
		$(window).on("orientationchange resize",function(e) {

			if (p_thm.width <= 1024 && p_lastWidth === p_thm.width)
				return false; // prevent resize trigger on mobile scroll

			p_lastWidth = p_thm.width;
			var offsetSubtract = ( $('body').hasClass('transparent-nav') ) ?  0 : $('.top-nav').height();
			$('.masthead-full').css('height', $(this).height() - offsetSubtract - parseInt($('html').css('margin-top')));
		});
	}

	// Auto-margins
	$('body:not(.boxed) .no-padding-x-r').find('.container-xl:first').addClass('auto-margin-l');
	$('body:not(.boxed) .no-padding-x-l').find('.container-xl:first').addClass('auto-margin-r');

	if ( $('body:not(.boxed) .auto-margin-l').length ) {
		$(window).resize(function () {
			$('.auto-margin-l').each( function() {
				$(this).css('margin-left', $('.navbar-wrapper > .navbar > .container-xl').offset().left + 'px');
			});
		});
	}

	if ( $('body:not(.boxed) .auto-margin-r').length ) {
		$(window).resize(function () {
			$('.auto-margin-r').each( function() {
				$(this).css('margin-right', $('.navbar-wrapper > .navbar > .container-xl').offset().left + 'px');
			});
		});
	}

	// Pull Bottom (bottom aligned image in scpecial container structure)
	var $pullBottom = $('.section-half.pull-bottom, .section-wrapper .col-md-12.pull-bottom') || 0;
	var posUpdate;

	if ( $pullBottom.length ) {
		$(window).on('section-pull-bottom', function() {
			$pullBottom.each( function() {
				var $section = $(this).closest('.section-wrapper'),
				    $container = $(this).closest('[class*="container"]'),
				    cHeight = $section.height(),
				    height = $(this).outerHeight();

				// Helper for bottom align content
				if ($container.hasClass('container-v-align')) {
					if ( cHeight > height ) {
						$(this).css('vertical-align','bottom');
					} else {
						$(this).css('vertical-align','middle');
					}
				}

				// Double check and force position if not correct
				var margins = parseInt($(this).css('margin-top')) + parseInt($(this).css('margin-bottom'));
				var distance = (margins - (cHeight - height)) / 2;
				if (distance < 0 && cHeight < height) {
					$(this).css({
						'position' : 'relative',
						'bottom' : distance+'px'
					});
				} else {
					$(this).css('bottom','auto');
				}
			});
		});
	}

	$(window).on('resize', function(e) {
		clearTimeout(posUpdate);
		posUpdate = setTimeout( function() { jQuery(window).trigger('section-pull-bottom'); }, 250);

	});

	$(window).trigger('resize');


	// Carousel - Gallery Blog Posts
	// ------------------------------------------------------------------------
	if ( $('.post-format-gallery').length ) {

		// Post List
		$(".post-format-gallery:not(.multi)").owlCarousel({
			items: 1,
			loop: true,
			autoplay: true,
			autoplayTimeout: 3800,
			autoplaySpeed: 800,
			navSpeed: 500,
			dots: true,
			nav: true,
			navText: [
				'<i class="fa fa-arrow-left"></i>',
				'<i class="fa fa-arrow-right"></i>'
			]
		});

		// Single
		var multiPostGal = $(".multi.post-format-gallery").owlCarousel({
			center: true,
			autoWidth: true,
			// stagePadding: 50,
			margin:50,
			items: 2,
			loop: true,
			autoplay: true,
			autoplayHoverPause: true,
			autoplayTimeout: 3800,
			autoplaySpeed: 800,
			navSpeed: 500,
			dots: true,
			nav: true,
			navText: [
				'<i class="fa fa-arrow-left"></i>',
				'<i class="fa fa-arrow-right"></i>'
			],
			responsive : {
				// breakpoint from 0 up
				0 : {
					margin:25,
				},
				// breakpoint from 768 up
				768 : {
					margin:32,
				},
				// breakpoint from 992 up
				992 : {
					margin:40,
				},
				// breakpoint from 1200 up
				1200 : {
					margin:50,
				}
			}
		});

		// Fix for problem with FireFox container auto width
		$(window).on('multi-gallery-ff-width-fix', function(event){
			$(".multi.post-format-gallery .owl-item .item").each(function(i){
				var img_width = $(this).css('width','auto').find('img').width();
				$(this).css('width',img_width);
			});
		});

		// Triggered when carousel is ready
		multiPostGal.on('initialized.owl.carousel', function(event) {
			// do these things...
			$(window).trigger('fast-load-images'); // makes sure clone elements get images loaded
			$(window).trigger('multi-gallery-ff-width-fix'); // for a Firefox issue
		});

		// Triggered when carousel adjusts (like a window resize)
		multiPostGal.on('refresh.owl.carousel', function(event) {
			$(window).trigger('multi-gallery-ff-width-fix');
		});
	}


	// Responsive videos
	// ------------------------------------------------------------------------
	if (typeof $().fitVids == 'function') {
		$("#wrapper").fitVids({ ignore: '.video-wrapper, .video-element'});
	}


	// Lightbox (colorbox)
	// -------------------------------------------------------------------
	if( jQuery().colorbox) {

		// [gallery] (groups items for lightbox next/prev)
		$(".gallery .gallery-item a").attr('rel','gallery');

		// Attach rel attribute for groups
		$("[data-lightbox]").each( function() {
			$(this).attr('rel',$(this).data('lightbox'));
		});

		// generic links to images open in lightbox
		$("a[href$='.jpg'],a[href$='.jpeg'],a[href$='.png'],a[href$='.gif'],a[href$='.tif'],a[href$='.tiff'],a[href$='.bmp']").each(function () {
			var $this = $(this);

			if (typeof $this.data('magnificPopup') === 'undefined') {
				$this.colorbox({
					maxWidth: function () {
						return ( p_thm.width >= p_thm.navBreak ) ? '88%' : '100%';
					},
					maxHeight: function () {
						return ( p_thm.width >= p_thm.navBreak ) ? '88%' : '100%';
					},
					fixed: true,
					retinaImage: true,
					current: "{current} / {total}",
					title: function () {
						var title = $this.attr('title');
						return (typeof title == 'string') ? '<span>' + title + '</span>' : '';
					}
				});
			}
		});
	}


	// Post Formats - Audio/Video Player
	// ------------------------------------------------------------------------
	if($().jPlayer) {

		// Audio Player
		$('.jp-jplayer-audio').each( function() {

			var supplied_types = '',
			    media = {},
			    format = ['mp3','m4a','ogg'],
			    selectorID = '#' + $(this).closest('.jp-audio').attr('id');
			for (var i = 0; i < format.length; i++) {
				var value = format[i];
				var key = (value == 'ogg') ? 'oga' : value;
				var file = $(this).data(value) || 0;
				if (file) {
					media[key] = file;
					supplied_types += key + ',';
				}
			}

			$(this).jPlayer({
				ready: function (event) {
					$(this).jPlayer("setMedia", media);// set media
				},
				play: function() {
					$(this).jPlayer("pauseOthers"); // Stop other players
				},
				swfPath: ThemeJS.assets_url + "/js",
				cssSelectorAncestor: selectorID,
				supplied: supplied_types,
				wmode: "window",
				volume: 1,
				useStateClassSkin: true,
				autoBlur: false,
				smoothPlayBar: true,
				keyEnabled: true,
				remainingDuration: true,
				toggleDuration: true
			});
		});

		// Video Player
		$('.jp-jplayer-video').each( function() {

			var supplied_types = '',
			    media = {},
			    format = ['mp4','webm','ogg','bg'],
			    selectorID = '#' + $(this).closest('.jp-video').attr('id'),
			    bgImg = $(this).data('bg'),
			    fastBgImg = $(this).data('fast-bg') || 0;
			for (var i = 0; i < format.length; i++) {
				var value = format[i];
				var key = value;
				switch(key) {
					case 'mp4':
						key = 'm4v';
						break;
					case 'webm':
						key = 'webmv';
						break;
					case 'ogg':
						key = 'ogv';
						break;
					case 'bg':
						key = 'poster';
						break;
				}
				var file = $(this).data(value) || 0;
				if (file) {
					media[key] = file;
					supplied_types += key + ',';
				}
			}

			$(this).jPlayer({
				ready: function (event) {
					$(this).jPlayer("setMedia", media); // set media
					// Custom poster image (better scaling)
					var posterImg = $(this).find('img'),
					    inner = '<img src="'+bgImg+'">';
					// add fast load images
					if (fastBgImg) {
						inner = '<div class="fast-load-img" style="background-image: url('+fastBgImg+')"></div>';
					}
					// insert new poster
					posterImg.replaceWith(function(i, v){
						var $poster = $('<div/>', {
							'style': 'background-image: url('+bgImg+')',
							'class': 'poster',
							'html': inner, // '<img src="'+bgImg+'">'
						});
						if (fastBgImg) {
							$poster.attr('style', 'background-image: url('+fastBgImg+')');
							$poster.attr('data-image',bgImg);
							$poster.addClass('fast-load');
						}
						return $poster;
					});
					$(window).trigger('fast-load-images');
				},
				play: function() {
					$(this).jPlayer("pauseOthers"); // Stop other players
					$(this).find('.poster').css('display','none'); // Hide custom poster on play
				},
				seeked: function() {
					$(this).find('.poster').css('display','none'); // Hide custom poster on jump to time
				},
				swfPath: "assets/js",
				cssSelectorAncestor: selectorID,
				supplied: supplied_types,
				wmode: "window",
				volume: 1,
				useStateClassSkin: true,
				autoBlur: false,
				smoothPlayBar: true,
				keyEnabled: true,
				remainingDuration: true,
				toggleDuration: true
			});
		});
	}

	// Fast Load Images (needs to be one of the last things we run)
	// ------------------------------------------------------------------------
	$(window).on('fast-load-images', function() {
		$('.fast-load').each(function() {
			var $main = $(this),
			    fullImg = $main.data('image') || 0; // if image data attribute
			// use .one('load') to avoid double processing
			$('<img>').one("load", function() {
				// this binds the load event before setting "src"
				if (fullImg) {
					// when image set as data attribute
					$main.css('background-image', 'url('+fullImg+')');
				}
				$main.addClass('done');
			}).attr('src',function() {
				// set the image SRC of the blur thumbnail
				var mainImage = (fullImg) ? fullImg : $main.css('background-image');
				var imgSrc = mainImage.replace(/url\((['"]?)(.+?)\1\)/g, "$2");
				return imgSrc;
			}).each(function() {
				// fail-safe, cached images sometimes won't trigger "load" events
				if(this.complete) $(this).load();
			});
		});
	});

	$(window).trigger('fast-load-images');


	// Navbar Hover/Click Responsive Behavior
	// ------------------------------------------------------------------------

	// hover sub-menu items
	$('.navbar-nav a').click( function(e) {
		var $this = $(e.target);
		var href = $this.attr('href'); // Link URL

		// Check link value
		if (href === undefined || !href.length || href === '#' || href === 'javascript:;') {
			href = false;
		}
		// Link behavior
		if ($this.hasClass('dropdown-toggle')) {
			// Parent menu items
			if (p_thm.width >= p_thm.navBreak) {
				if (href) {
					// large screens, follow the parent menu link when clicked
					if (e.which !== 2 && e.target.target != '_blank') {
						window.location.href = href;
					}
				}
			 } else if ( $this.parent().hasClass('open') && href !== false) {
				// small screens, 1st tap opens sub-menu & 2nd tap follows link
				$(document).trigger('collapse-menus');
				window.location.href = href;
			}
		} else {
			// All other menu items, close menu on click
			$(document).trigger('collapse-menus');
		}
	});
	// Keep parent menus open on sub-menu expand
	$(document).on('show.bs.dropdown', function(obj) {
		if (p_thm.width < p_thm.navBreak) {
			$(obj.target).parents('.show-on-hover, .dropdown-submenu').addClass('open');
		}
	});
	$('.navbar a:not(.dropdown-toggle)').click( function(e) {

		var $this = $(e.target);
		var href = $this.attr('href'); // Link URL

		// Check link value
		if (href === undefined || !href.length || href === '#' || href === 'javascript:;') {
			href = false;
		}
		// Link behavior
		if (p_thm.width >= p_thm.navBreak) {
			if (href) {
				// large screens, follow the parent menu link when clicked
				if (e.which !== 2 && e.target.target != '_blank') {
					window.location.href = href;
				}
			}
		 } else if ( $this.parent().hasClass('open') && href !== false) {
			// small screens, 1st tap opens sub-menu & 2nd tap follows link
			$(document).trigger('collapse-menus');
			window.location.href = href;
		}
	});
	// Close all menus
	$(document).on('collapse-menus', function () {
		$('.collapse.in').removeClass('in').children().removeClass('open');
	});
	// Hover styling helpers
	$('.navbar-nav > li.show-on-hover').hover(function() {
		if (p_thm.width >= p_thm.navBreak) {
			$(this).addClass('open');
		}
	}, function() {
		if (p_thm.width >= p_thm.navBreak) {
			$(this).removeClass('open');
		}
	});

	// Responsive Menu Display Helpers
	$(document).on('responsive-menu-size', function () {
		var nav_height = 0;
		if (p_thm.width < p_thm.navBreak) {
			var nav_height =  $(window).height() - ($('#wpadminbar').outerHeight() + $('.top-nav .navbar-header').outerHeight()) + 2;
		}
		$('.navbar-fixed-top .navbar-collapse').css({
			'min-height': nav_height+'px',
			'max-height': nav_height+'px'
		});
	});
	$(window).on('show.bs.collapse resize', function () {
		$(document).trigger('responsive-menu-size');
	});

	// Force content to appear above background video
	$('.fl-row-bg-video .fl-bg-video').siblings().each(function () {
		var position = $(this).css('position');

		if (position === 'static') {
			$(this).css('position', 'relative');
		}
	});

}); // END - jQuery(document).ready()
