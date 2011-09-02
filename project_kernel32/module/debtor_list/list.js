$(function(){
	console.log(window.location);
	$('.page_nav select[name="page"]').first().focus();
	$('.nbk_admin a.remove').click(function(){
		if(!confirm('Вы действительно хотите удалить запись?'))
			return false;
	});
	$('a.clear').click(function(){
		if(!confirm('Вы действительно хотите удалить все записи?'))
		return false;
	});
	$('.page_nav select').change(function(){
		page_redirect($(this).parent().parent());
	});
	$('.page_nav select').keypress(function(e) {
		if(e['originalEvent']['keyCode']>=37 && e['originalEvent']['keyCode']<=40)
			page_redirect($(this).parent().parent());
	});
	$('.nbk_input input.drop').click(function(){
		var text = 'Вы действительно хотите ';
		var table =  $(this).parent().parent().parent().parent().parent();
		switch(table.parent().attr('class')){
			case 'filter': text = text + 'сбросить текущий фильтр'; break;
			case 'column': text = text + 'сбросить выбор колонок'; break;
			default: text = text + 'отменить редактирование';
		}
		text = text+'?';
		if(confirm(text)){
			var location = table.attr('action');
			if(location != location.href)
				window.location = location;
			else
				hide_curtain();
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
	$('div.curtain').click(hide_curtain);
	$('.filter_radio_type_switch').change(function(){
		var matches = $(this).parent().find('input[type="text"]');
		var first = matches.first();
		var last = matches.last();
		if($(this).val()==1){
			first.css('width','100%');
			last.css('display','none');
			$(this).parent().find('span').css('display','none');
		}
		else{
			first.css('width','');
			last.css('display','inline');
			$(this).parent().find('span').css('display','inline');
		}
	});
	$('.nbk_input .date input[type="text"]').datepicker();
	$('#zero_debt_input').change(function(){ page_redirect($(this.form)); });
});

function hide_curtain(){
	$('.page_nav span.filter>div, .page_nav span.column>div,div.curtain').hide();
}

function page_redirect(form){
	var location = /*'http://'+window.location.hostname+*/form.attr('action');
	if(form[0]['count'].value && form[0]['count'].value != _default_page_count)
		location = location+'&count='+form[0]['count'].value;
	if(form[0]['search'].value)
		location = location+'&search='+form[0]['search'].value;
	if(form[0]['page'].value && form[0]['page'].value!=1)
		location = location+'&page='+form[0]['page'].value;
	if(form[0]['zero_debt'] && form[0]['zero_debt'].checked)
		location = location+'&zero_debt=1';
	if(location != location.href)
		window.location = location;
	else
		hide_curtain();
}