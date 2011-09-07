/*window.onload = function() {
   article_load_swfupload();
};*/
$(function(){
	article_load_swfupload();
});

var swfu;

function article_load_swfupload(){
	//alert(PHPSESSID);
	var settings = {
		flash_url : "/extensions/SWFUpload/Flash/swfupload.swf",
		upload_url: "./index.php",
		file_post_name : "upload_file", 
		post_params: {"PHPSESSID" : PHPSESSID,"call":"file.upload","module":"article","_no_link":"1","_echo_error":"1"},
		file_size_limit : "20 MB",
		file_types : "*.*",
		file_types_description : "All Files",
		file_upload_limit : 100,
		file_queue_limit : 0,
		custom_settings : {
			progressTarget : "fsUploadProgress",
			cancelButtonId : "btnCancel"
		},
		debug: false,

		// Button settings
		button_image_url: "/extensions/SWFUpload/images/ImageNoText_65x29.png",
		button_width: "65",
		button_height: "29",
		button_placeholder_id: "spanButtonPlaceHolder",
		button_text: '<span class="theFont">Обзор</span>',
		button_text_style: ".theFont { font-size: 16; }",
		button_text_left_padding: 12,
		button_text_top_padding: 3,
		
		// The event handler functions are defined in handlers.js
		file_queued_handler : fileQueued,
		file_queue_error_handler : fileQueueError,
		file_dialog_complete_handler : fileDialogComplete,
		upload_start_handler : uploadStart,
		upload_progress_handler : uploadProgress,
		upload_error_handler : uploadError,
		upload_success_handler : article_admin_uploadSuccess,
		upload_complete_handler : uploadComplete,
		queue_complete_handler : queueComplete	// Queue plugin event
	};
	swfu = new SWFUpload(settings);
 };
 
 function article_admin_uploadSuccess(file, serverData) {
	 	//alert(serverData);
		if(!serverData || !(serverData.substr(0,4)=='<div' || serverData.substr(0,5)=='<!DOC')){
			if(serverData)
				alert(serverData);
			upload_progress('произошла ошибка при загрузке');
		}
		else{
			upload_progress('');
			var article_file_list = document.getElementById('article_file_list');
			if (article_file_list){
				if (article_file_list.innerHTML.substr(0,no_files_msg.length)==no_files_msg)
					article_file_list.innerHTML = article_file_list.innerHTML.substring(no_files_msg.length);
				article_file_list.innerHTML = serverData + article_file_list.innerHTML;
				//article_file_list.scrollTop = article_file_list.scrollHeight;
			}
			else
				alert('article_file_list not found');
		}
}

function article_file_2_wysiwyg(obj,file_path,thumb_path,file_type,thumb){
	//alert(file_path+"\n"+thumb_path+"\n"+type+"\n"+thumb);
	var type = obj.value;
	var article_file_div = document.getElementById(file_path);
	var html = '';
	switch(type){
		case 'paste':{
			html="<a href='"+file_path+"'>"+file_path+"</a>";
			break;
		}
		case 'copy':{
			var text = file_path;
			//copyToClipboard(text);
			alert(text);
			break;
		}
		case 'image':{
			html="<img src='"+file_path+"'/>";
			break;
		}
		case 'preview':{
			html="<a href='"+file_path+"'><img src='"+thumb_path+"'/></a>";
			break;
		}
		case 'videoplayer':{
			html='<object width="425" height="344"><param name="movie" value="'+file_path+'"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="'+file_path+'" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="425" height="344"></embed></object>';
			break;
		}
		default: {}//alert('type not found: "'+type+'"');
	}
	CKEDITOR.instances.article_text.insertHtml(html);
	obj.value='';
	return false;
}

function trim(s){
	return s.replace(/(^\s+)|(\s+$)/g,"");
}

function upload_progress(text){
	var fsUploadProgress = document.getElementById('fsUploadProgress');
	if(fsUploadProgress)
		fsUploadProgress.innerHTML=text;
	else{
		alert("fsUploadProgress not found");
		return;
	}
}

function remove_file(id, file_id){
	upload_progress('идет удаление файла...');
	if(!confirm('Вы действительно хотите удалить файл?')){
		upload_progress('удаление отменено');
		return;
	}
	$.ajax({
		url: "/admin.php?call=file.remove&id="+id+"&_target=content&_echo_error=1",
		success: function(html){
			if(html){
				upload_progress('произошла ошибка при удалении');
				alert(html);
			}
			else
			{
				upload_progress('');
				var el = document.getElementById(file_id);
				if (el){
					var parent = el.parentNode;
					el.parentNode.removeChild(el);
					if(!trim(parent.textContent))
						parent.innerHTML = no_files_msg;
				}
				else
					alert("element id='"+file_id+"' not found");
			}
		}
	});
}

function file2wysiwyg_dialog(file_path,thumb_path,internal_type,file_name){
	//document.createElement
	$('#_wysiwyg_form')
		.attr('file_path',file_path)
		.attr('thumb_path',thumb_path)
		.attr('file_type',internal_type)
		.attr('file_name',file_name);
	$('#_href2wisiwig input').attr('value',file_name);
	$('#_href2wisiwig').css('display','block');
	switch(internal_type){
		/*case 'document':{
			$('#_pdf2wisiwig input').attr('value',file_name);
			$('#_pdf2wisiwig').css('display','block');
			$('#_img2wisiwig, #_preview2wisiwig, #_video2wisiwig').css('display','none');
			break;
		};*/
		case 'image':{
			$('#_img2wisiwig, #_preview2wisiwig').css('display','block');
			$('#_pdf2wisiwig, #_video2wisiwig').css('display','none');
			break;
		};
		default:{
			$('#_pdf2wisiwig, #_img2wisiwig, #_preview2wisiwig, #_video2wisiwig').css('display','none');
		}
	}
	$('#_wysiwyg_form, #_background').css('display','block');
}

$(function(){
	$('#_wysiwyg_form .cancel').click(function(){
		$('#_wysiwyg_form, #_background').css('display','none');
		return false;
	});
	$('#_href2wisiwig button, #_pdf2wisiwig button, #_img2wisiwig button, #_preview2wisiwig button, #_video2wisiwig button').click(to_wisiwig);
});

function to_wisiwig(obj){
	//alert($('#_wysiwyg_form').attr('file_name'));
	var html;
	//alert(this.parentNode.id);
	var form = $('#_wysiwyg_form');
	var value;
	switch($(this).parent().attr('id')){
		case '_href2wisiwig':
		//case '_pdf2wisiwig':
		{
			value = $('#'+$(this).parent().attr('id')+' input').attr('value');
			if(!value)
				value = form.attr('file_name');
			html = '<a href="'+form.attr('file_path')+'">'+value+'</a>';
			break;
		};
		case '_img2wisiwig':{
			html = '<img src="'+form.attr('file_path')+'"/>';
			break;
		};
		case '_preview2wisiwig':{
			html = '<a href="'+form.attr('file_path')+'"><img src="'+form.attr('thumb_path')+'"/></a>';
			break;
		};
		case '_video2wisiwig':{
			alert('work in progress');
			break;
		};
		default:{
			alert('2wisiwig id not defined');
		}
	}
	var re = new RegExp('<img.*?src=".*?'+form.attr('thumb_path')+'".+?title="'+form.attr('file_path')+'".*?/>',"i");
	var text = CKeditor.getData();
	if(re.test(text))
		CKeditor.setData(text.replace(re, html));
	else if(html)
		//CKEDITOR.instances.article_text.insertHtml(html);
		CKeditor.insertHtml(html);
	$('#_wysiwyg_form, #_background').css('display','none');
	return false;
}