$(function(){
	$('.nbk_admin a.remove').click(function(){
		if(!confirm('Вы действительно хотите удалить запись?'))
			return false;
	});
	$('.page_nav select').change(function(){
		form = $(this).parent().parent().find('form');
		form.attr('action',form.attr('action')+'&page='+$(this).val());
		form.submit();
	});
	$('.filter_radio_type_switch').change(function(){
		$(this).parent().find('span,input[type="text"]:last').toggle();
		first_input = $(this).parent().find('input[type="text"]:first');
		first_input.attr('size',first_input.attr('size')==10?32:10);
	});
});