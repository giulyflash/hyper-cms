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
	if(!obj)
		obj = $('.nested_items.dropdown');
	/*obj.find('.item a').lightBox({
		imageLoading: 'extensions/jquery_lightbox/images/ru/loading.gif',
		imageBtnClose: 'extensions/jquery_lightbox/images/ru/closelabel.gif',
		imageBtnPrev: 'extensions/jquery_lightbox/images/ru/prev.gif',
		imageBtnNext: 'extensions/jquery_lightbox/images/ru/next.gif',
		imageBlank: 'extensions/jquery_lightbox/images/lightbox-blank.gif',
		txtImage: 'Изображение',
		txtOf: 'из',
	});*/
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
			//TODO check on a lot of categories
			if(loading.size())
				loading.css('display','inline');
			else
				content.before('<img class="loading" src="module/module_link/img/loading1.gif"/>');
			_get_content(obj_a.attr('href'),content,'category_add_event');
		}
		else{
			content.toggle('slow');
		}
		return false;
	});
}