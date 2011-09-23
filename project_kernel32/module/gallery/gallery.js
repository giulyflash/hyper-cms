$(function(){
	gallery_add_event();
});

function gallery_add_event(obj){
	if(!obj)
		obj = $('.gallery');
	obj.find('.img a').lightBox({
		imageLoading: 'extensions/jquery_lightbox/images/ru/loading.gif',
		imageBtnClose: 'extensions/jquery_lightbox/images/ru/closelabel.gif',
		imageBtnPrev: 'extensions/jquery_lightbox/images/ru/prev.gif',
		imageBtnNext: 'extensions/jquery_lightbox/images/ru/next.gif',
		txtImage: 'Изображение',
		txtOf: 'из'
	});
	obj.find('li a').click(function(){
		var obj = $(this);
		obj[0].opened = obj[0].opened?0:1;
		obj.find('>img').attr('src',obj[0].opened?'module/base_module/img/category_up.png':'module/base_module/img/category_down.png');
		var content = obj.parent().find('.category_content');
		if(content.css('display')!='block')
			_get_content(obj.attr('href'),content,'gallery_add_event');
		content.toggle();
		return false;
	});
}