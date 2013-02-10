/***************************************************************span close*******************************************************************/
$('.success span, .warning span, .attention span, .information span').live('click', function() {
$(this).parent().fadeOut('slow', function() {
$(this).remove();
});
});

/******************************************************************************************************************************************/
/********************************************************************************menu********************************************************/
	$(document).ready(function() { 
	$('ul.menu').superfish({ 
		delay:       600,                            // one second delay on mouseout 
		animation:   {opacity:'show',height:'show'},  // fade-in and slide-down animation 
		speed:       'normal',                          // faster animation speed 
		autoArrows:  false,                           // disable generation of arrow mark-up 
		dropShadows: false                            // disable drop shadows 
	}); 
}); 
/********************************************************************************************************************************************/
/**********************************************************prettyphoto*****************************************************************************/
    $(document).ready(function(){
       $("a[data-gal^='prettyPhoto']").prettyPhoto({
  animationSpeed:'slow',theme:'facebook',slideshow:5000, autoplay_slideshow: true
        });
    });
/************************************************************************************************************************************************/
/***********************************************************************backtotop*******************************************************************/
	jQuery(document).ready(function(){
	// hide #back-top first
	jQuery("#back-top").hide();
	// fade in #back-top
	jQuery(function () {
		jQuery(window).scroll(function () {
			if (jQuery(this).scrollTop() > 100) {
				jQuery('#back-top').fadeIn();
			} else {
				jQuery('#back-top').fadeOut();
			}
		});

		// scroll body to 0px on click
		jQuery('#back-top a').click(function () {
			jQuery('body,html').animate({
				scrollTop: 0
			}, 800);
			return false;
		});
	});

});
/************************************************************************************************************************************************/
$(function(){
$('#header .links li').last().addClass('last');
$('.breadcrumb a').last().addClass('last');
$('.cart tr').eq(0).addClass('first');
});
/****************************************************************************bx-slider********************************************************/

$(document).ready(function(){
    $('#prod').bxSlider(
		{
			pager: true,
			controls:false
			}
						);
  });
  
/**********************************************************tooltips**************************************************************************/
$(document).ready(function() { 
	
	$(".list-services a.tooltips").easyTooltip();
	$(".wishlist a.tip").easyTooltip();
	$(".compare a.tip2").easyTooltip();
}); 
/*******************************************************************************************************************************************/

/********************************************************************************************************************************************/
$(document).ready(function() {
$('.column li a').css({paddingLeft:'0px'});					   
$('.column li a ').hover(function(){
			$(this).stop().animate({paddingLeft:'5px'},300, 'easeOutQuad')
		}, function(){
			$(this).stop().animate({paddingLeft:'0px'},300, 'easeOutQuad')
		}
	)
$('.box-product.box-subcat li img').hover(function(){
     jQuery(this).stop(true,false).animate({borderTopColor: '#181818', borderLeftColor: '#181818', borderRightColor: '#181818', borderBottomColor: '#181818'}, {duration: 250});
    },function(){
     jQuery(this).stop(true,false).animate({borderTopColor: '#dfe1e4', borderLeftColor: '#dfe1e4', borderRightColor: '#dfe1e4', borderBottomColor: '#dfe1e4'}, {duration: 250});
   }
   )
$('.jcarousel-list li a img').css({opacity:'0.3'});
$('.jcarousel-list li a img').hover(function(){
     jQuery(this).stop(true,false).animate({opacity:'1'}, {duration: 250});
    },function(){
     jQuery(this).stop(true,false).animate({opacity:'0.3'}, {duration: 250});
   }
   )
});


function addToCart(product_id) {
$.ajax({
url: 'index.php?route=checkout/cart/add',
type: 'post',
data: 'product_id=' + product_id,
dataType: 'json',
success: function(json) {
$('.success, .warning, .attention, .information, .error').remove();
if (json['redirect']) {
location = json['redirect'];
}
if (json['error']) {
if (json['error']['warning']) {
$('#notification').html('<div class="warning" style="display: none;">' + json['error']['warning'] + '<span class="close"><img src="catalog/view/theme/theme017/image/close.png" alt="" class="close" /></span></div>');
}
}
				if (json['success']) {
				$('#notification').html('<div class="success" style="display: none;">' + json['success'] + '<span class="close"><img src="catalog/view/theme/theme017/image/close.png" alt="" class="close" /></span></div>');
				
				$('.success').fadeIn('slow');
$('#cart-total').html(json['total']);
$('#cart .content').html(json['output']);
var image = $('#img_'+product_id).offset();
if (image) {
var cart = $('#cart').offset();
$('<img src="' + $('#img_'+product_id).attr('src') + '" id="temp" style="position: absolute; z-index:9999; top: ' + image.top + 'px; left: ' + image.left + 'px;" />').appendTo('body');
params = {
top : cart.top + 'px',
left : cart.left + 'px',
opacity : 0.2,
width :$('#img_'+product_id).width(),
height : $('#img_'+product_id).height()
};
// uncomment line below if you also want to scroll up
//$('html, body').animate({ scrollTop: 0 }, 'slow');
$('#temp').animate(params, 'slow', false, function () {
$('#temp').remove();
});
} else {
//alert("hi there");
}
}
}
});
}

/***********************************************************************************************************************************************************************/
$(document).ready(function() {
/* AddToCart */
$('.addToCart').click(function() {
var tis = $(this);
$.ajax({
url: 'index.php?route=checkout/cart/add',
type: 'post',
data: 'product_id=' + tis.attr("data-id"),
dataType: 'json',
content: this,
success: $.proxy(function(json) {
$('.success, .warning, .attention, .information, .error').remove();
if (json['redirect']) {
location = json['redirect'];
}
if (json['error']) {
if (json['error']['warning']) {
$('#notification').html('<div class="warning" style="display: none;">' + json['error']['warning'] + '<span class="close"><img src="catalog/view/theme/theme017/image/close.png" alt="" class="close" /></span></div>');
}
}
if (json['success']) {
		$('#notification').html('<div class="success" style="display: none;">' + json['success'] + '<span class="close"><img src="catalog/view/theme/theme017/image/close.png" alt="" class="close" /></span></div>');
				
				$('.success').fadeIn('slow');
$('#cart-total').html(json['total']);
$('#cart .content').html(json['output']);
animateProduct(tis.parents().find(".image2 img") , $("#cart"));
}
}, this)
});
});

function animateProduct(image,cart){
var image_offset = image.offset();
$("body").append('<img src="' + image.attr('src') + '" id="temp" style="position: absolute; z-index:9999; top:'+image_offset.top+'px; left:'+image_offset.left+'px" />');
var cart_offset = cart.offset();
params = {
top : cart_offset.top + 'px',
left : cart_offset.left + 'px',
opacity : 0.0,
width : cart.width(),
height : cart.height()
};
$('#temp').animate(params, 'slow', false, function () {
$('#temp').remove();
});
}});


/*************************************************************************************************************related coroucel*****************************************************************************/
$(document).ready(function() {
$('.related-carousel .box-product ul').jcarousel({
	vertical: false,
	visible: 4,
	scroll: 1
});
});