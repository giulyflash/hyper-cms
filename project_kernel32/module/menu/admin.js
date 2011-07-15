$(function(){
	$('.nested_tree #radio_href_input').click(function(){
		$('.input_text').css('display','block');
		$('.input_article').css('display','none');
	});
	$('.nested_tree #radio_href_article').click(function(){
		$('.input_article').css('display','block');
		$('.input_text').css('display','none');
	});
});