$(function(){
	gallery_add_event();
});

function gallery_add_event(obj){
	if(obj){
		if(obj.css('display')=='none'){
			//obj.toggle('slow');
			obj.parent().find('>.category_content:first').toggle('slow');
		}
		$('.gallery .loading').css('display','none');
	}
	if(!obj)
		obj = $('.gallery');
	obj.find('.img a').lightBox({
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
		if(obj_a.parent().parent().hasClass('nested_items'))
			return;
		obj_a[0].opened = obj_a[0].opened?0:1;
		obj_a.parent().attr('class',obj_a[0].opened?'active':'');
		//module/base_module/img/category_up.png':'module/base_module/img/category_down.png
		var content = obj_a.parent().find('>.category_content:first');
		if(content.css('display')!='block'){
			if(content.html().length){
				content.find('.category_content').css('display','none');
				content.find('li').attr('class','');
				content.toggle('slow');
			}
			var loading = obj_a.find('.loading:last');
			//TODO check on a lot of categories
			if(loading[0])
				loading.css('display','inline');
			else
				obj_a.append('<img class="loading" src="module/module_link/img/loading1.gif"/>');
			_get_content(obj_a.attr('href'),content,'gallery_add_event');
		}
		else{
			content.toggle('slow');
		}
		return false;
	});
}