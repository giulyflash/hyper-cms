$(function(){
	CKeditor_cont = {};
	$('textarea').each(function(num,obj){
		CKeditor_cont[num] = new CKEDITOR.replace($(obj).attr('id'));
		CKeditor_cont[num].config.toolbarStartupExpanded = false;
	});
	$('.nbk_input .date input[type="text"]').datepicker();
});