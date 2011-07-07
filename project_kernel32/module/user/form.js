$(function(){
	function _user_input_focus(){
		if(typeof(input_just_checked) == 'undefined' || !input_just_checked)
			$.each($('.user_container .input_container label'),function(key,label){
				$(label).css('display','none'); 
			});
		this.is_focused = true;
	}
	function _user_input_blur(){
		this.is_focused = false;
		var input_is_empty = true;
		var input_is_focused = false;
		$.each($('.user_container .input_container input'),function(key,input){
			if(input.value)
				input_is_empty = false;
			if(typeof(input.is_focused) != 'undefined' && input.is_focused)
				input_is_focused = true;
		});
		if(!input_is_empty || input_is_focused)
			return;
		$.each($('.user_container .input_container label'),function(key,label){
			$(label).css('display','block'); 
		});
	}
	$('.user_container .input_container label').click(_user_input_focus);
	$('.user_container .input_container input').click(_user_input_focus).blur(_user_input_blur);
	var login = $('#_user_login');
	login.attr('autocomplete','on');
	if(navigator.userAgent.indexOf('Chrome'))
		setTimeout(function(){
			if($('#_user_login').attr('value'))
				_user_input_focus();
		},50);
});