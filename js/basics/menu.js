//var root = document.querySelector(':root');
//root.removeAttribute('class', 'no-js');
//root.setAttribute('class', 'js');

var $ = jQuery,
$body = $( 'body' ),
$h = $('header'),

extraMenuCtrl = {
	handlersBinded : false,
	initSetting : false,//
	active : false,
	$menuBtn : '',
	init : function (reset) {
		var self = this;

		// Add link for mobile submenu navigation
		if(reset || self.initSetting == false){
			self.destroy();
			$("#navMain > ul li:has(ul)").append('<a href="#" class="subbutton" title="Untermenü öffnen"></a>');
			

			$("#navMain > ul li:has(ul)").each(function() {
				var link = $(this).find(">:first-child")[0].outerHTML;
				var html = '<li class="submenu--header"><a href="#" class="backbutton" title="Untermenü schließen"></a>'+link+'</li>';
				$(this).find("> ul").prepend(html);
			
			});

			$(".shopNavWrap > ul li:has(ul)").append('<a href="#" class="subbutton" title="Untermenü öffnen"></a>');
			

			$(".shopNavWrap > ul li:has(ul)").each(function() {
				var link = $(this).find(">:first-child")[0].outerHTML;
				var html = '<li class="submenu--header"><a href="#" class="backbutton" title="Untermenü schließen"></a>'+link+'</li>';
				$(this).find("ul").first().prepend(html);			
			});

			

			self.initSetting = true;
		}

		self.$menuBtn = $( '.menu-button' );

		if(!self.handlersBinded) self.bindHandlers();

	},
	destroy: function() {
		var self = this;
		// Remove link for mobile submenu navigation
		$('a.subbutton, .submenu--header').each(function(){
			$(this).remove();
		});

		self.$menuBtn = $( '.menu-button' );
		self.reset();

		self.initSetting = false;
	},
	bindHandlers: function(){
		var self = this;
		self.handlersBinded = true;

		$(document).on('click', '.menu-item-has-children a.subbutton', function(e){
			e.preventDefault();
			var $btn = $(this);
			var ul = $btn.prev();

			self.active = $btn.parent().attr('data-id');			
			self.subMenu.show($btn, ul);				
		})

		$(document).on('click', '.menu-item-has-children a.backbutton', function(e){
			e.preventDefault();
			var $btn = $(this);
			var ul = $btn.parent('ul');
			self.subMenu.reset($btn, ul);
		});


		$(document).on('click', '.menu-button', self.show.bind(self));

		$(document).on('click', '.app--nav--trigger', function(e){
			if($(this).data('nav')) {
				$('.app--nav--trigger').not($(this)).removeClass('active');
				$(this).toggleClass('active');
				var nav = $(this).data('nav');
				e.preventDefault();
				// loop to deactivate all else
				if($body.hasClass('active-menu') && !$body.hasClass('active-'+nav)){
				} else if ($body.hasClass('active-menu') && $body.hasClass('active-'+nav)) {
					$body.removeClass('active-menu');
					self.subMenu.reset();
				} else {
					$body.addClass('active-menu');
				}
				$('.app--nav--trigger').each(function(e){
					if ($(this).data('nav') && $(this).data('nav') != nav) {
						$body.removeClass( 'active-'+$(this).data('nav') );
					}
				});
				$body.toggleClass( 'active-'+nav ).removeClass( 'active-submenu' );				
				self.subMenu.reset();
			}
		});

	},
	show : function (e) {
		// Show menu and reset submenu
		var self = this;
		e.preventDefault();
		$body.toggleClass( 'active-menu' );
		self.$menuBtn.toggleClass( 'active-button' );

		//self.subMenu.reset();
	},
	reset : function () {
		// Reset menu
		var self = this;
		$body.removeClass( 'active-menu' );
		self.$menuBtn.removeClass( 'active-button' );

		//self.subMenu.reset();
	},
	subMenu : {
			// Submenu methods
		show : function ($link, ul) {
			// Show submenu
			if(ul.hasClass('sub-active')) $link.off('mouseenter mouseleave');
			ul.toggleClass('sub-active');
			$body.toggleClass('active-submenu');
		},
		reset : function () {
			// Reset submenu
			var self = this;
			$body.removeClass('active-submenu');
			setTimeout( function(){
				$('.sub-active').removeClass('sub-active');					
				$('.subbutton.active').removeClass('active');
			}, 300);
		}
	}
}
