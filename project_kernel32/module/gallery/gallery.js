$(function(){
	gallery_add_event();
});

function gallery_add_event(obj){
	if(obj){
		if(obj.css('display')=='none')
			obj.parent().find('>.category_content').toggle('slow');
		$('.gallery .loading').css('display','none');
	}
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
		var obj_a = $(this);
		obj_a[0].opened = obj_a[0].opened?0:1;
		obj_a.find('>img:first').attr('src',obj_a[0].opened?'module/base_module/img/category_up.png':'module/base_module/img/category_down.png');
		var content = obj_a.parent().find('.category_content');
		if(content.css('display')!='block'){
			if(content.html().length)
				content.toggle('slow');
			var loading = obj_a.find('.loading:last');
			if(loading[0])
				loading.css('display','inline');
			else
				obj_a.append('<img class="loading" src="module/module_link/img/loading1.gif"/>');
			_get_content(obj_a.attr('href'),content,'gallery_add_event');
		}
		else
			content.toggle('slow');
		return false;
	});
}