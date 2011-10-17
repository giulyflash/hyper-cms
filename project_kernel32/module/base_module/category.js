$(function(){
	category_add_event();
});

function category_add_event(obj){
	if(obj){
		if(obj.css('display')=='none'){
			obj.toggle('slow');
		}
		$('.nested_items.dropdown .loading').css('display','none');
	}
	else
		obj = $('.nested_items.dropdown');
	if(obj.lightBox)
		obj.find('.item a').lightBox({
			imageLoading: 'extensions/jquery_lightbox/images/ru/loading.gif',
			imageBtnClose: 'extensions/jquery_lightbox/images/ru/closelabel.gif',
			imageBtnPrev: 'extensions/jquery_lightbox/images/ru/prev.gif',
			imageBtnNext: 'extensions/jquery_lightbox/images/ru/next.gif',
			imageBlank: 'extensions/jquery_lightbox/images/lightbox-blank.gif',
			txtImage: 'Изображение',
			txtOf: 'из'
		});
	obj.find('li a').click(function(){
		var obj_a = $(this);
		if(obj_a.parent()[0].tagName!='LI')
			return false;
		var content = obj_a.parent().find('>ul:first');
		if(!content.size()){
			obj_a.parent().append('<ul></ul>');
			content = obj_a.parent().find('>ul:first');
			obj_a[0].opened = 0;
		}
		else
			obj_a[0].opened = content.css('display')=='block';
		obj_a.parent().attr('class',obj_a[0].opened?'':'active');
		if(!obj_a[0].opened){
			content.css('display','none');
			if(content.html().length){
				content.find('li.active').attr('class','');
				content.find('ul').css('display','none');
				content.toggle('slow');
			}
			var loading = obj_a.parent().find('.loading:first');
			if(loading.size())
				loading.css('display','inline');
			else
				content.before('<img class="loading" src="module/module_link/img/loading1.gif"/>');
			if(typeof _get_content == 'function')
				_get_content(obj_a.attr('href'),content,'category_add_event');
			else
				alert('error: function "_get_content" not found');
		}
		else{
			content.toggle('slow');
		}
		return false;
	});
}