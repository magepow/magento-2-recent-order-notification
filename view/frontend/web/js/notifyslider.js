define([
    'jquery',
    ], function ($) {
	"use strict";
	/* notifySlider */
	(function ($) {
	    "use strict";
	    $.fn.notifySlider = function (options) {
	      	var defaults = {
		        autoplay   : true,
		        firsttime  : 3000,
		        timeout    : 3000,
		        interval   : 10000
	      	};

			var settings    = $.extend(defaults, options);
			var firsttime   = settings.firsttime;
			var timeout     = settings.timeout;
			var interval    = settings.interval;
			var autoplay    = settings.autoplay;

	      	var methods = {
		        init : function() {
			        return this.each(function() {
			        	methods.suggestLoad($(this));
			        });
		        },
		        
		        suggestLoad: function(suggest){
		            var el  = suggest.find('.notify-slider-wrapper');
		            suggest.find('.x-close').click(function() {
		                suggest.addClass('close')
		            });
		            var slideCount    = suggest.find('.slider >.item').length;
		            var slideWidth    = suggest.find('.slider >.item').width();
		            var slideHeight   = suggest.find('.slider >.item').height();
		            var sliderUlWidth = slideCount * slideWidth;
		            /*suggest.find('.notify-slider').css({ width: slideWidth, height: slideHeight });*/
		            suggest.find('.notify-slider .slider').css({ width: sliderUlWidth});
		            suggest.find('.notify-slider .slider >.item:last-child').prependTo('.notify-slider .slider');
		            setTimeout(function(){ el.slideDown('slow'); }, firsttime);
		            if(!autoplay) return;
		            setInterval(function () {
		                el.slideUp({
		                        duration:'slow', 
		                        easing: 'swing',
		                        complete: function(){
		                            methods.moveRight(suggest, slideWidth);
		                            setTimeout(function(){ el.slideDown('slow'); }, timeout);
		                        }
		                    });

		            }, interval);
		        },

		        moveRight: function(suggest, slideWidth){
		            suggest.find('.notify-slider .slider').animate({
		                left: - slideWidth
		            }, 0, function () {
		                var slider = suggest.find('.notify-slider .slider');
		                suggest.find('.notify-slider .slider >.item:first-child').appendTo(slider);
		                slider.css('left', '');
		            })
		        }

	      	};

	      	if (methods[options]) {
	        	return methods[options].apply(this, Array.prototype.slice.call(arguments, 1));
	      	} else if (typeof options === 'object' || !options) {
	        	return methods.init.apply(this);
	      	} else {
	        	$.error('Method "' + method + '" does not exist in timer plugin!');
	      	}
	    }

	    $(document).ready(function($) {
		    $('.suggest-slider').each(function() {
		    	if($(this).hasClass('autoplay')){
		    		var config = $(this).data();
		    		$(this).notifySlider(config);
		    	}
		    });  
	    });
	    
  	})($);

	/* End notifySlider */
});

