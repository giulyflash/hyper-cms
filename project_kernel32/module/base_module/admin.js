$(function(){
	$('.nested_tree .controls .remove').click(function(){
		if(!confirm('Вы действительно хотите безвозвратно удалить этот пункт меню?'))
			return false;
	});
	
	$('.nested_tree .remove_menu').click(function(){
		if(!confirm('Вы действительно хотите безвозвратно удалить это меню и все его пункты?'))
			return false;
	});
	
	$('.nested_tree .controls select').change(function(){
		if(this.form.elements['insert_type'].value!='0' && this.form.elements['insert_place'].value!='0')
			this.form.submit();
	});
	
	$('.nested_tree #radio_href_input').click(function(){
		$('.input_text').css('display','block');
		$('.input_article').css('display','none');
	});
	$('.nested_tree #radio_href_article').click(function(){
		$('.input_article').css('display','block');
		$('.input_text').css('display','none');
	});
});