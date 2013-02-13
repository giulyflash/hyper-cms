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
    curr.find('.wrapper').html(content.text);
    curr.find('.main h2').html(content.title);
}

function h_request(curr){
    //alert(curr[0].id+' requested')
    var module = '';
    switch (curr[0].id)
    {
        case "page_wedding":
        case "page_people":
        case "page_fashion":
            module="gallery.get_category"
            break
        default:{module="article.get"}
    }
    //alert(module);
    if(h_cache[curr[0].id])
        h_put_content(curr,h_cache[curr[0].id])
    else{
        var href = window.location.origin+"/index.php?call="+module+"&_content=json&id="+curr[0].id;
        var cur = curr;
        $.ajax({
            url: href,
            dataType: "json",
            success: function(html){
                if(html=='')
                    alert("get data error!\n"+href)
                else{
                    console.log(html);
                    h_cache[curr[0].id] = html;
                    h_put_article(cur,html);
                }
            }
        })
    }
}