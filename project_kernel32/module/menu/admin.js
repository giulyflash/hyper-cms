$(function(){
	$('.menu_editor #radio_href_input').click(function(){
		$('.input_text').css('display','block');
		$('.input_article').css('display','none');
	});
	$('.menu_editor #radio_href_article').click(function(){
		$('.input_article').css('display','block');
		$('.input_text').css('display','none');
	});
	$('.remove_menu').click(function(){
		if(!confirm('Удалить это меню безвозвратно?'))
			return false;
	});
});