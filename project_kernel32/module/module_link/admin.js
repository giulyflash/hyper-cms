$(function(){
	$('.module_link_list .remove a').click(function(){
		if(!confirm('Вы действительно хотите удалить связь?'))
			return false;
	});
});