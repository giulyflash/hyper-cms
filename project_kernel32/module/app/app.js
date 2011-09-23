function _get_content(href,obj,add_event_callback){
	if(!obj){
		alert('ajax target object not found');
		return;
	}
	obj.prepend('<img src="module/module_link/img/loading1.gif" style="float: right;"/>');
	href = href+'&_content=json_html'+(PHPSESSID?('&PHPSESSID='+PHPSESSID):'');
	//alert(href);
	$.ajax({
		url: href,
		success: function(html){
			_process_html(html,obj);
			if(add_event_callback){
				obj.ready(function(){
					(window[add_event_callback])(obj);
				});
			}
		},
		error: function(html){alert('ajax error!');}
	});
}

function _process_errors(errors){
	var str = "serverside error:\n";
	for(i in errors)
		if(errors[i])
			str = str + errors[i]['text'] + "\n";
	alert(str);
	str = null;
}

function _process_html(html,callback,call_obj){
	if(!html){
		alert('server return empty result');
		return;
	}
	try{
		eval('var res = '+html);
	}
	catch(err){
		alert('json error!');
		alert(html);
		return;
	}
	if(!res)
		alert('server result is not evaluable!');
	else{
		if(res.session)
			document.session = res.session;
		if(res.errors && res.errors.length)
			_process_errors(res.errors);
		else{
			if(!call_obj)
				call_obj = this;
			if(callback){
				type = typeof(callback);
				switch(type){
					case 'string':{
						call_obj[callback](res);
						break;
					}
					case 'array':{
						for(i in callback)
							call_obj[callback[i]](res);
						break;
					}
					case 'object':{
						callback = $(callback);
						callback.html(res['html']);
						break;
					}
				}
			}
		}
	}
}