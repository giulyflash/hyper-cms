$(function(){
	$('.page_nav select[name="page"]').first().focus();
	$('.nbk_admin a.remove').click(function(){
		if(!confirm('Вы действительно хотите удалить запись?'))
			return false;
	});
	$('.page_nav select').change(function(){
		page_redirect($(this).parent().parent());
	});
	$('.page_nav select').keypress(function(e) {
		if(e['originalEvent']['keyCode']>=37 && e['originalEvent']['keyCode']<=40)
			page_redirect($(this).parent().parent());
	});
	$('.filter_table input.drop').click(function(){
		if(confirm('Вы действительно хотите сбросить текущий фильтр?')){
			window.location = $(this).parent().parent().parent().parent().parent().attr('action');
		}
		return false;
	});
	$('.page_nav span.filter>img, .page_nav span.column>img, .page_nav .div_logo, .page_nav input.cancel').click(function(){
		if($(this).hasClass('div_logo'))
			var div = $(this).parent();
		else if($(this).hasClass('cancel'))
			var div = $(this).parent().parent().parent().parent().parent().parent().parent();
		else
			var div = $(this).parent().find('>div');
		div.toggle();
		var curtain = $('div.curtain'); 
		curtain.toggle();
		curtain.height($(document).height());
		return false;
	});
	$('div.curtain').click(function(){
		$('.page_nav span.filter>div, .page_nav span.column>div,div.curtain').hide();
	});
	$('.filter_radio_type_switch').click(function(){
		if($(this).val()=='1'){
			
		}
	});
});

function page_redirect(form){
	var location = /*'http://'+window.location.hostname+*/form.attr('action');
	if(form[0]['count'].value && form[0]['count'].value != _default_page_count)
		location = location+'&count='+form[0]['count'].value;
	if(form[0]['search'].value)
		location = location+'&search='+form[0]['search'].value;
	if(form[0]['page'].value && form[0]['page'].value!=1)
		location = location+'&page='+form[0]['page'].value;
	window.location = location; 
}