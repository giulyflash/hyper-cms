$(function(){
	$('.nested_tree .controls .remove').click(function(){
		var container = $(this).parent().parent();
		var text = container.find('.item_text:first');
		var obj_text=text.size()?('объект "'+text.html()+'"'):'этот объект';
		var text = container.find('.nested_item_img:first');
		if(text.size()){
			text = container.find('.item_text:first');
			obj_text=text.size()?('категорию "'+text.html()+'"'):'эту категорию';
		}
		if(!confirm('Вы действительно хотите безвозвратно удалить '+obj_text+'?'));
			return false;
	});
	
	$('.nested_tree .remove_menu').click(function(){
		if(!confirm('Вы действительно хотите безвозвратно удалить это меню и все его пункты?'))
			return false;
	});
	
	$('.nested_tree .controls .insert_place').change(function(){
		if(this.form.elements['insert_type'].value!='0' && this.form.elements['insert_place'].value!='0')
			this.form.submit();
	});
	
	$('.nested_tree .controls .insert_category, .nested_tree .controls .insert_item').change(function(){
		this.form.submit();
		//TODO ajax
	});
});