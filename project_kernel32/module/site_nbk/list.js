$(function(){
	$('.nbk_admin a.remove').click(function(){
		if(!confirm('Вы действительно хотите удалить запись?'))
			return false;
	});
	$('.page_nav select').change(function(){
		form = $(this).parent().find('form');
		form.attr('action',form.attr('action')+'&page='+$(this).val());
		form.submit();
	});
});