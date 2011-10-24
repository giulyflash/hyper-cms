$(function(){
	$('span.more_input').click(function(){
		$('table.more_input').toggle('slow');
	});
	
	$('.article.admin #generate_translit').click(function(){
		var input = $('.article.admin .translit_title');
		if(this.checked){
			this.default_value = input.attr('value');
			input.attr('value','');
		}
		else
			input.attr('value',this.default_value);
	});
	
	$('.article.admin .submit.delete').click(function(){
		if(confirm('Вы действительно хотите безвозвратно удалить эту статью?')){
			window.location = 'admin.php?call=article.remove&id='+this.id;
			return false;
		}
		else
			return false;
	});
	
	CKeditor = new CKEDITOR.replace('article_text');
	CKeditor.config.height = '450px';
	CKeditor.config.width = '570px';
});