$(function(){
	admin_method_name = '_admin';
	//sorting elements
	sort_data();
	//init module
	$('.module_select').each(function(num,module_list){
		module_list = $(module_list);
		if(document.link_data)
			get_module_name(module_list);
		else{
			module_name = '';
			method_name = '';
		}
		for(i in document.module_data){
			module_list.append('<option value="'+i+'" '+(module_name==i?'selected="1"':'')+'>'+document.module_data[i]['title']+'</option>');
		}
	});
	if(document.link_data){
		//init method
		$('.method_select').each(function(num,method_list){
			method_list = $(method_list);
			get_module_name(method_list);
			for(i in document.module_data[module_name]['method'])
				method_list.append('<option value="'+i+'" '+(method_name==i?'selected="1"':'')+'>'+document.module_data[module_name]['method'][i]['title']+'</option>');
			method_list.parent().parent().css('display','table-row');
		});
		//init params
		$('.param_box').each(function(num,param_box){
			param_box = $(param_box);
			get_module_name(param_box);
			for(i in document.link_data['param']){
				if(document.link_data['param'][i]['type']==(is_condition?'condition':'param')){
					param_box.append(get_param_td_html());
					param_list = param_box.find('.param_select').last();
					var parent_internal = parent;
					param_list.change(function(){
						param_change(parent_internal,$(this));
					});
					parameter_name = document.link_data['param'][i]['param_name'];
					param_list.append('<option value="'+parameter_name+'" selected=1>'+document.module_data[module_name]['method'][method_name]['params'][parameter_name]+'</option>');
					get_param_value(module_name,method_name,parameter_name,param_list,document.link_data['param'][i]['value']);
				}
			}
			param_change(parent,param_list,true);
			param_box.parent().parent().css('display','table-row');
		});
	}
	//event for module select
	$('.module_select').change(function(){
		parent = $(this).parent().parent().parent().parent();
		if(!document.module_data){
			alert('fatal error: data array not found!');
			return;
		}
		value = $(this).val();
		$(this).parent().find('input').val(value);
		//alert(document.module_data[value]['_method']+': '+document.module_data[value]['param']);
		if(value && document.module_data[value] && document.module_data[value]['_method'] && document.module_data[value]['param']){
			show_object_select(true,true,parent);
			get_param_value(value,document.module_data[value]['_method'],document.module_data[value]['param'],parent.find('.object_select'),null,true);
		}
		else
			show_object_select(false,true,parent);
		if(!value){
			parent.find('.method, .params').css('display','none');
			return;
		}
		if(!document.module_data[value]){
			alert('fatal error: module "'+value+'" not found!');
			return;
		}
		method_select = parent.find('.method_select');
		method_select.html('<option></option>');
		for(var method_name in document.module_data[value]['method']){
			method_select.append('<option value="'+method_name+'">'+document.module_data[value]['method'][method_name]['title']
			+(method_name==admin_method_name?' (admin)':'')+'</option>');
		}
		parent.find('.method').css('display','table-row');
		parent.find('.params').css('display','none');
	});
	
	//event for method select
	$('.method_select').change(function(){
		parent = $(this).parent().parent().parent().parent();
		parent.find('.params input[type="hidden"]').remove();
		if(!document.module_data){
			alert('fatal error: data array not found!');
			return;
		}
		method = $(this).val();
		if(method)
			show_object_select(false,true,parent);
		else
			show_object_select(document.module_data[module]['_method']?true:false,true,parent);
		$(this).parent().find('input').val(method);
		module = parent.find('.module_select').val();
		parent.find('.params').css('display','none');
		if(!method || !document.module_data[module]['method'][method]['params'])
			return;
		parent.find('.params .param_box').html('');
		param_change(parent);
		parent.find('.params').css('display','table-row');
	});
	
	$('.object_select').change(function(){
		parent = $(this).parent().parent().parent().parent().parent();
		module = parent.find('.module_select').val();
		object = method = $(this).val();
		parent.find('.params input[type="hidden"]').remove();
		if(object){
			parent.find('.params').css('display','none');
			if(document.module_data[module] && document.module_data[module]['_method'] && document.module_data[module]['param'])
				$(this).parent().parent().find('.method input').val(document.module_data[module]['_method']);
			else{
				alert('wrong data: '+module+'._method not found');
				return;
			}
			parent.find('.params td:last').append(
				'<input type="hidden" name="link['+parent.index()+'][param][0][name]" value="'+document.module_data[module]['param']+'"/>'+
				'<input type="hidden" name="link['+parent.index()+'][param][0][value]" value="'+object+'"/>'
			);
			show_object_select(true,false,parent);
		}
		else
			show_object_select(true,true,parent);
	});
});

function param_change(parent,param_select,need_not_value){
	module = parent.find('.module_select').val();
	method = parent.find('.method_select').val();
	param = param_select?($(param_select).val()):null;
	//alert(module+'; '+method+'; '+param);
	if(param_select){
		if(param){
			if(!need_not_value)
				get_param_value(module,method,param,param_select);
		}
		else
			param_select.parent().parent().remove();
	}
	select_list = parent.find('.params .param_box td:first-child .param_select');
	param_count = 0;
	for(param_name in document.module_data[module]['method'][method]['params'])
		param_count++;
	if((!select_list.size() || select_list.last().val())  && select_list.size()<param_count){
		parent.find('.params .param_box').append(get_param_td_html());
		parent.find('.params .param_box:last .param_select').change(function(){
			param_change(parent,$(this));
		});
	}
	param_all = parent.find('.params .param_box tr');
	current_param_list = [];
	if(param_select){
		$.each(param_all, function(key,param_tr){
			param_tr = $(param_tr);
			select_each = param_tr.find('td:first-child select');
			param_name = select_each.val();
			select_each.attr('name',param_name?('link['+parent.index()+'][param]['+param_tr.index()+'][name]'):'');
			if(param_name)
				current_param_list[param_name] = 1;
		});
	}
	option_html = [];
	for(param_name in document.module_data[module]['method'][method]['params']){
		if(!current_param_list[param_name])
			option_html[param_name] = document.module_data[module]['method'][method]['params'][param_name];
	}
	param_all.each(function(key,tr_obj){
		tr_obj = $(tr_obj);
		p_select = tr_obj.find('td:first-child select').first();
		p_select_value = p_select.val();
		p_select.find('option').each(function(key,option){
			option = $(option);
			if(option.val() && p_select_value!=option.val())
				option.remove();
		});
		console.log(option_html);
		for(param_name in option_html){
			new_option = $("<option/>");
			new_option.attr('value',param_name);
			new_option.append(option_html[param_name]);
			p_select.append(new_option);
		}
	});
	$('.link_form .param_box').css('display','block');
}

function get_param_td_html(){
	return '<tr><td><select class="param_select" autocomplete = "off"><option value=""></option></select></td><td></td></tr>';
}

function get_select_html(class_name,value,title){
	return '<tr><td><select class="'+class_name+'" autocomplete = "off"><option></option>'+(value&&title?('<option value="'+value+'">'+title+'</option>'):'')+'</select></td><td></td></tr>';
}

function get_loading_html(){
	return '<img src="module/module_link/img/loading1.gif" class="loading"/>';
}

function get_param_value(module,method,param,obj,value,exact){
	if(exact){
		obj.css('display','none');
		obj.parent().append(get_loading_html());
	}
	else
		obj.parent().next().html(get_loading_html());
	url_str = 'http://'+(window.location+'/').split( '/' )[2]+'/'+window.location.pathname+'?call='+module+'._get_param_value&_content=json&PHPSESSID='+PHPSESSID+'&method_name='+method+'&param_name='+param;
	$.ajax({
		url: url_str,
		success: function(html){
			//cache?
			try{
				eval('value_list = '+html);
				if(value_list['__error']){
					error_str = '';
					for(error_module in value_list['__error'])
						for(error_method in value_list['__error'][error_module])
							for(error_i in value_list['__error'][error_module][error_method])
								error_str += error_module+'.'+error_method+': '+value_list['__error'][error_module][error_method][error_i]['text']+"\n";
					set_param_value(obj, null, value);
					alert(error_str);
				}
				else
					set_param_value(obj, value_list, value,exact);
			}
			catch(e){
				alert(e);
				alert('can not evaluete server data!');
			}
		},
		error: function(html){alert('can not recieve data from server!');}
	});
}

function set_param_value(obj, value_list, value, exact){
	if(exact){
		obj.html('<option></option>');
		for(param_dinamic in value_list){
			obj.append('<option value="'+param_dinamic+'">'+value_list[param_dinamic]+'</option>');
		}
		obj.parent().find('img').remove();
		obj.css('display','block');
	}
	else{
		cont = obj.parent().next();
		parent = obj.parent().parent().parent().parent().parent().parent().parent().parent();
		name_str = ' name="link['+parent.index()+'][param]['+obj.parent().parent().index()+'][value]"';
		new_input = null;
		for(param_dinamic in value_list){
			if(!new_input)
				new_input = $('<select'+name_str+'/>');
			new_input.append('<option value="'+param_dinamic+'"'+((typeof(value)!='undefined' && param_dinamic==value)?' selected="1"':'')+'>'+value_list[param_dinamic]+'</option>');
		}
		if(!new_input)
			new_input = $('<input type="text"'+name_str+' '+((typeof(value)!='undefined')?(' value="'+value+'"'):'')+'/>');
		cont.html('');
		cont.append(new_input);
		new_input.focus();
	}
}

function sort_data(){
	for(var module_name in document.module_data)
		document.module_data[module_name]['method'] = sort_by_title(document.module_data[module_name]['method']);
	document.module_data = sort_by_title(document.module_data);
}

function sort_by_title(values){
    sorted_keys = [];
    sorted_obj = {};
    for (var key in values)
        sorted_keys.push([key,values[key]['title']]);
    sorted_keys.sort(function(a,b){
    	if(a[1]>b[1])
    		return 1;
    	else if(a[1]<b[1])
    		return -1;
    });
    for (var key in sorted_keys)
        sorted_obj[sorted_keys[key][0]] = values[sorted_keys[key][0]];
    return sorted_obj;
}

function sort_obj(arr){
    var sortedKeys = [];
    var sortedObj = {};
    for (var i in arr)
        sortedKeys.push(i);
    sortedKeys.sort();
    for (var i in sortedKeys)
        sortedObj[sortedKeys[i]] = arr[sortedKeys[i]];
    return sortedObj;
}

function get_module_name($obj){
	parent = $obj.parent().parent().parent().parent();
	is_condition = parent.index()-1;
	if(is_condition<0)
		is_condition = 0;
	if(is_condition){
		module_name = document.link_data['center_module'];
		method_name = document.link_data['center_method'];
	}
	else{
		module_name = document.link_data['module_name'];
		method_name = document.link_data['method_name'];
	}
}

function show_object_select(object_flag, method_flag, parent){
	select = parent.find('.object_select');
	if(!select.size())
		return;
	select.parent().css('display',(object_flag?'block':'none'));
	select.parent().parent().prev().find('p').css('display', (object_flag?'block':'none'));
	select.parent().parent().prev().find('span:first').css('display', (object_flag?'inline':'none'));
	select.parent().parent().prev().find('span:last').html(ucfirst(select.parent().parent().prev().find('span:last').html(),!object_flag));
	if(method_flag){
		select.parent().parent().prev().find('span:last').css('display', 'inline');
		select = parent.find('.method_select').css('display','block');
	}
	else{
		select.parent().parent().prev().find('span').css('display', 'none');
		select = parent.find('.method_select').css('display','none');
	}
}

function ucfirst(text,flag){
	return (flag?text.charAt(0).toUpperCase():text.charAt(0).toLowerCase()) + text.substr(1);
}