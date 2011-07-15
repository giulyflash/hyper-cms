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
});