/*hyper-cms exchange*/
var h_cache = {};

function h_get_content(curr){
   h_loading(curr);
   h_request(curr);
   //h_put_content(curr,);
}

function h_loading(curr,cancel){
    if(!cancel){
        curr.find('.box').html('<a href="#close" class="close"><span style="opacity: 0;"></span></a><div class="wrapper"></div></div>');
        curr.find('.wrapper').css({'background': "url('template/lanaphoto/images/ajax-loader.gif')", 'background-position': 'center', 'background-repeat': 'no-repeat', 'height': '100%'});
        curr.find('.main h2').html('&#160;');
    }
    else
        curr.find('.wrapper').css({'background':'none'});
}

function h_put_article(curr,content){
    h_loading(curr,true);
    curr.find('.wrapper').html(content.text);
    curr.find('.main h2').html(content.title);
}

function h_put_gallery(curr,content){
    h_loading(curr,true);
    var ul = $(".gallery > ul");
    ul.html('');
    //TODO one string + load from HTML
    for(i in content.items)
        ul.append('<li><a href="'+content.items[i].path+'" data-gal="example_group3"><img src="'+content.items[i].thumb_path+'" alt="'+content.items[i].title+'" title="'+content.items[i].title+'" style="opacity: 1;"></a></li>');
    if(ul.lightBox){
        ul.find('li a').lightBox({
            imageLoading: 'extensions/jquery_lightbox/images/ru/loading.gif',
            imageBtnClose: 'extensions/jquery_lightbox/images/ru/closelabel.gif',
            imageBtnPrev: 'extensions/jquery_lightbox/images/ru/prev.gif',
            imageBtnNext: 'extensions/jquery_lightbox/images/ru/next.gif',
            imageBlank: 'extensions/jquery_lightbox/images/lightbox-blank.gif',
            txtImage: 'Изображение',
            txtOf: 'из'
        });
    }
    else
        alert("lightBox plugin not found!")
}

function h_request(curr){
    //alert(curr[0].id+' requested')
    var module = '';
    var ref = '';
    switch (curr[0].id)
    {
        case "page_wedding":
        case "page_people":
        case "page_fashion":{
            module="gallery"
            ref="gallery.get_category&_content=json"
            break
        }
        default:{
            module="article"
            ref="article.get&_content=json"
        }
    }
    if(h_cache[curr[0].id])
        h_put_content(curr,h_cache[curr[0].id])
    else{
        var href = window.location.origin+"/index.php?call="+ref+"&id="+curr[0].id;
        var cur = curr;
        $.ajax({
            url: href,
            dataType: "json",
            success: function(html){
                if(html=='')
                    alert("get data error!\n"+href)
                else{
                    h_cache[curr[0].id] = html;
                    //alert(module);
                    if(module=='gallery')
                        h_put_gallery(cur,html)
                    else
                        h_put_article(cur,html);
                }
            }
        })
    }
}