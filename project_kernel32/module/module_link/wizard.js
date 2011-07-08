$(function(){
	admin_method_name = '_admin';
	//sorting elements
	sort_data();
	module_list_obj = $('.module_select');
	module_list_obj.each(function(num,module_list){
		module_list = $(module_list);
		module_list.html('<option></option>');
		for(var i in document.module_data)
			module_list.append('<option value="'+i+'">'+document.module_data[i]['title']+'<optin/>');
	});
	//event for module select
	module_list_obj.change(function(){
		parent = $(this).parent().parent().parent().parent();
		if(!document.module_data){
			alert('fatal error: data array not found!');
			return;
		}
		value = $(this).val();
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
		if(!document.module_data){
			alert('fatal error: data array not found!');
			return;
		}
		method = $(this).val();
		module = parent.find('.module_select').val();
		//console.log(document.module_data[module]);return;
		parent.find('.params').css('display','none');
		if(!method || !document.module_data[module]['method'][method]['params'])
			return;
		parent.find('.params .param_box').html('');
		param_change(parent);
		parent.find('.params').css('display','table-row');
	});
});

function param_change(parent,param_select){
	module = parent.find('.module_select').val();
	method = parent.find('.method_select').val();
	param = param_select?($(param_select).val()):null;
	if(param_select){
		if(param)
			get_param_value(module,method,param,param_select);
		else
			param_select.parent().parent().remove();
	}
	select_list = parent.find('.params .param_box td:first-child .param_select');
	param_count = 0;
	for(param_name in document.module_data[module]['method'][method]['params'])
		param_count++;
	if(!param_select || !select_list.last().val()  || select_list.size()<param_count){
		parent.find('.params .param_box').append(get_param_html());
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
			select_each.attr('name',param_name?('param['+parent.index()+']['+param_tr.index()+']'):'');
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
		for(param_name in option_html){
			new_option = $("<option/>");
			new_option.attr('value',param_name);
			new_option.append(option_html[param_name]);
			p_select.append(new_option);
		}
	});
	$('.link_form .param_box').css('display','block');
}

function get_param_html(num){
	return '<tr><td><select class="param_select" autocomplete = "off"><option value=""></option></select></td><td></td></tr>';
}

function get_param_value(module,method,param,obj){
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
					set_param_value(obj, null);
					alert(error_str);
				}
				else
					set_param_value(obj, value_list);
			}
			catch(e){
				alert(e);
				alert('can not evaluete server data!');
			}
		},
		error: function(html){alert('can not recieve data from server!');}
	});
}

function set_param_value(obj, value){
	obj.parent().next().html(null);
	parent = obj.parent().parent().parent().parent().parent().parent().parent().parent();
	name_str = ' name="param_value['+parent.index()+']['+obj.parent().parent().index()+']"';
	new_input = null;
	for(param_dinamic in value){
		if(!new_input)
			new_input = $('<select'+name_str+'/>');
		new_input.append('<option value="'+param_dinamic+'">'+value[param_dinamic]+'<optin/>');
	}
	if(!new_input)
		new_input = $('<input type="text"'+name_str+'/>');
	obj.parent().next().append(new_input);
	new_input.focus();
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