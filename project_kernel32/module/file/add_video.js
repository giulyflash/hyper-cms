function add_video(id,callback,noajax, module){
	var src = document.getElementById(id);
	if(!src){
		alert("element id='"+id+"' not found");
		return;
	}
	if(!src.value)
		return;
	if(!noajax)
		upload_progress('идет загрузка видео...');
/*	var re = new RegExp("(http:\/\/)?(www\.)?youtube\.com\/watch\?.*");
	if(!re.test(src.value)){
		alert("неправильная ссылка");
		return;
	}*/
	var parent=this;
	if(!parent[callback]){
		alert("callback='"+callback+"' not found");
		return;
	}
	if(noajax){
		parent[callback]();
		return;
	}
	else{
		var url_str = "/admin.php?call="+((module)?module:'file')+".load_outer_video&_target=content";
		if(document.file_category_id || document.file_category_id===0)
			url_str = url_str + "&category_id=" + document.file_category_id; 
		$.ajax({
			type: "POST",
			url: url_str,
			data: "href="+src.value,
			success: function(html){
				/*alert(html);
				return;*/
				//article_admin_uploadSuccess(null,html);
				//alert(this['article_admin_uploadSuccess']);
				parent[callback](null,html);
				//null неизбежно из-за swv_upload в article_admin
			}
		});
	}
	//
}