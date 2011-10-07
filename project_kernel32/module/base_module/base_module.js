$(function(){
	//TODO replace this ugly XSLT-performance hack by PHPtemplate
	$.each($('ul.nested_items_list'), function(key,ul){
		ul = $(ul);
		ul.insertAfter(ul.parent().children().last());
		if(typeof(document.close_nested_folder)=='undefined' || !document.close_nested_folder)
			ul.css('display','block');
	});
	
	/*$('.nested_item_img').click(function(){
		$(this).parent().parent().find('>ul').toggle('normal');
		change_nested_folder_img($(this));
	});
	
	if(document.close_nested_folder){
		$.each($('.nested_item_img'), function(key,ul){
			change_nested_folder_img($(ul),1);
		});
	}*/
});

/*function change_nested_folder_img($obj,$hide){
	if($hide)
		$obj.parent().parent().find('>ul').hide();
	var src  = $obj.attr('src')=='template/admin/images/folder_opened.png'?'template/admin/images/folder_closed.png':'template/admin/images/folder_opened.png';
	$obj.attr('src',src);
}*/