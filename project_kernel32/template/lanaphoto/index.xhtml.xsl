<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="html" encoding="utf-8" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />
    <xsl:template match="/">
<html lang="lang"><head>
    <link href="/template/lanaphoto/index.css" rel="stylesheet" type="text/css"/>
    <link href="/template/grosstech/images/gross32.ico" rel="shortcut icon" type="image/x-icon" />
    <xsl:call-template name="_head"/>

    <link rel="stylesheet" href="/template/lanaphoto/css/reset.css" type="text/css" media="all"/>
    <link rel="stylesheet" href="/template/lanaphoto/css/css.css" type="text/css"/>
    <link rel="stylesheet" href="/template/lanaphoto/css/jquery.fancybox-1.3.4.css" type="text/css" media="screen"/>
    <link rel="stylesheet" href="/template/lanaphoto/css/style.css" type="text/css" media="all"/>
    <!--<script type="text/javascript" async="" src="http://www.google-analytics.com/ga.js"></script>-->
    <script type="text/javascript" src="/template/lanaphoto/js/jquery-1.9.0.min.js"></script>
    <script type="text/javascript" src="/template/lanaphoto/js/script.js"></script>
    <script type="text/javascript" src="/template/lanaphoto/js/content_switch.js"></script>
    <script type="text/javascript" src="/template/lanaphoto/js/jquery.easing.1.3.js"></script>
    <script type="text/javascript" src="/template/lanaphoto/js/superfish.js"></script>
    <script type="text/javascript" src="/template/lanaphoto/js/jquery.color.js"></script>
    <script type="text/javascript" src="/template/lanaphoto/js/jquery.fancybox-1.3.4.js"></script>
    <script type="text/javascript" src="/template/lanaphoto/js/googleMap.js"></script>
    <script type="text/javascript" src="/template/lanaphoto/js/jquery-ui.js"></script>
    <script type="text/javascript" src="/template/lanaphoto/js/cScroll.js"></script>
    <script type="text/javascript" src="/template/lanaphoto/js/jquery.mousewheel.js"></script>
    <script type="text/javascript" src="/template/lanaphoto/js/my.js"></script>
    <!--[if lt IE 9]>
    <script type="text/javascript" src="/template/lanaphoto/js/html5.js"></script>
    <![endif]-->
    <!--[if lt IE 8]>
    <div style=' clear: both; text-align:center; position: relative;'>
        <a href="http://windows.microsoft.com/en-US/internet-explorer/products/ie/home?ocid=ie6_countdown_bannercode"><img src="http://storage.ie6countdown.com/assets/100/images/banners/warning_bar_0000_us.jpg" border="0" height="42" width="820" alt="You are using an outdated browser. For a faster, safer browsing experience, upgrade for free today." /></a>
    </div>
    <![endif]-->
</head>

<body style="overflow: visible;">
<div class="page_spinner" style="display: none;"><div></div></div>
<div class="bg">
    <div id="img1" class="active" style="opacity: 1;"></div>
    <div id="img2" style="opacity: 0;"></div>
    <div id="img3" style="opacity: 0;"></div>
    <div id="img4" style="opacity: 0;"></div>
</div>
<div class="center" style="margin-top: 32px;">
    <div class="top_line" style="width: 516.5px;"></div>
    <div class="main">
        <!--header  <header>-->
            <h1><a href="#close" id="logo">Светлана Добрынина<br/><SPAN>ФОТОГРАФ</SPAN></a></h1>
        <!--header end </header>-->
    </div>
    <!--content -->
    <article id="content">
        <div class="main">
            <nav class="menu">
               <!--<ul id="menu" class="sf-js-enabled">
                    <li data-type="#img1"><span style="opacity: 0.2;"><a href="#!/page_about">Обо мне</a></span></li>
                    <li data-type="#img2" class="with_ul"><span style="opacity: 0.2;"><a href="#!/page_wedding">Свадьбы</a></span>
                        <ul class="submenu_1" style="display: none; visibility: hidden;">
                            <li><span style="opacity: 0.2;"></span><a href="#!/page_wedding">2012</a>
                                <ul class="submenu_2" style="display: none; visibility: hidden;">
                                    <li><span style="opacity: 0.2;"></span><a href="#!/page_wedding">Julia &amp; Mark</a></li>
                                    <li><span style="opacity: 0.2;"></span><a href="#!/page_wedding">Valery &amp; Tim</a></li>
                                    <li><span style="opacity: 0.2;"></span><a href="#!/page_wedding">Ann &amp; Andrew</a></li>
                                    <li><span style="opacity: 0.2;"></span><a href="#!/page_wedding">Jessica &amp; Luk</a></li>
                                </ul>
                            </li>
                            <li><span style="opacity: 0.2;"></span><a href="#!/page_wedding">2011</a></li>
                            <li><span style="opacity: 0.2;"></span><a href="#!/page_wedding">2010</a></li>
                        </ul>
                    </li>
                    <li data-type="#img3"><span style="opacity: 0.2;"><a href="#!/page_people">Портреты</a></span></li>
                    <li data-type="#img4"><span style="opacity: 0.2;"><a href="#!/page_fashion">Мода</a></span></li>
                    <li data-type="#img1"><span style="opacity: 0.2;"><a href="#!/page_mail">Цены</a></span></li>
                    <li data-type="#img1"><span style="opacity: 0.2;"><a href="#!/page_mail">Контакты</a></span></li>
                </ul>-->
                <xsl:call-template name="_call">
                    <xsl:with-param name="position">top</xsl:with-param>
                </xsl:call-template>
            </nav>
        </div>
        <ul style="display: none;">
            <li id="page_about" style="position: absolute; display: none;">
                <div class="main">
                    <h2 style="left: -1800px; opacity: 0.2;">about</h2>
                    <div class="box" style="left: -1800px;">
                        <a href="#close" class="close"><span style="opacity: 0;"></span></a>
                        <div class="wrapper">
                            <figure class="left marg_right1"><img src="/template/lanaphoto/images/page1_img1.jpg" alt=""/></figure>
                            <h3>Hello</h3>
                            <p class="pad_bot1">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Praesent vestibulum molestie lacus. Aenean nonummy hendrerit mauris. Phasellus porta. Fusce suscipit varius mi. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nulla dui. Fusce feugiat malesuada odio. Morbi nunc odio, gravida at, cursus nec, luctus a, lorem. Maecenas tristique orci ac sem. Duis ultricies pharetra magna. Donec accumsan malesuada orci. Donec sit amet eros. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Mauris fermentum dictum magna. Sed laoreet aliquam leo. Ut tellus dolor dapibus eget, elementum vel, cursus eleifend, elit. </p>
                            <ul class="list1">
                                <li><a href="#!/page_more">Lorem ipsum dolor sit amet </a></li>
                                <li><a href="#!/page_more">Consectetuer adipiscing elit praesent </a></li>
                                <li><a href="#!/page_more">Vestibulum molestie lacus aenean </a></li>
                                <li><a href="#!/page_more">Nonummy hendrerit mauris</a></li>
                                <li><a href="#!/page_more">Phasellus porta fusce suscipit</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </li>
            <li id="page_wedding" style="position: absolute; display: none;">
                <div class="main">
                    <h2 class="pad_top1" style="left: -1800px; opacity: 0.2;">wedding</h2>
                </div>
                <div class="gallery" style="left: -2800px;">
                    <ul style="width: 2040px; left: 10px;">
                        <li><a href="/template/lanaphoto/images/gallery1_big_img1.jpg" data-gal="example_group"><img src="/template/lanaphoto/images/gallery1_img1.jpg" alt="" style="opacity: 0.3;"/><span style="opacity: 0;"></span></a></li>
                        <li><a href="/template/lanaphoto/images/gallery1_big_img2.jpg" data-gal="example_group"><img src="/template/lanaphoto/images/gallery1_img2.jpg" alt="" style="opacity: 0.3;"/><span style="opacity: 0;"></span></a></li>
                        <li><a href="/template/lanaphoto/images/gallery1_big_img3.jpg" data-gal="example_group"><img src="/template/lanaphoto/images/gallery1_img3.jpg" alt="" style="opacity: 0.3;"/><span style="opacity: 0;"></span></a></li>
                        <li><a href="/template/lanaphoto/images/gallery1_big_img4.jpg" data-gal="example_group"><img src="/template/lanaphoto/images/gallery1_img4.jpg" alt="" style="opacity: 0.3;"/><span style="opacity: 0;"></span></a></li>
                        <li><a href="/template/lanaphoto/images/gallery1_big_img5.jpg" data-gal="example_group"><img src="/template/lanaphoto/images/gallery1_img5.jpg" alt="" style="opacity: 0.3;"/><span style="opacity: 0;"></span></a></li>
                        <li><a href="/template/lanaphoto/images/gallery1_big_img6.jpg" data-gal="example_group"><img src="/template/lanaphoto/images/gallery1_img6.jpg" alt="" style="opacity: 0.3;"/><span style="opacity: 0;"></span></a></li>
                        <li><a href="/template/lanaphoto/images/gallery1_big_img7.jpg" data-gal="example_group"><img src="/template/lanaphoto/images/gallery1_img7.jpg" alt="" style="opacity: 0.3;"/><span style="opacity: 0;"></span></a></li>
                        <li><a href="/template/lanaphoto/images/gallery1_big_img8.jpg" data-gal="example_group"><img src="/template/lanaphoto/images/gallery1_img8.jpg" alt="" style="opacity: 0.3;"/><span style="opacity: 0;"></span></a></li>
                        <li><a href="/template/lanaphoto/images/gallery1_big_img9.jpg" data-gal="example_group"><img src="/template/lanaphoto/images/gallery1_img9.jpg" alt="" style="opacity: 0.3;"/><span style="opacity: 0;"></span></a></li>
                        <li><a href="/template/lanaphoto/images/gallery1_big_img10.jpg" data-gal="example_group"><img src="/template/lanaphoto/images/gallery1_img10.jpg" alt="" style="opacity: 0.3;"/><span style="opacity: 0;"></span></a></li>
                    </ul>
                </div>
                <div class="main">
                    <a href="#close" class="back" style="left: -1800px;"><span style="opacity: 0;"></span>back to home</a>
                </div>
            </li>
            <li id="page_people" style="position: absolute; display: none;">
                <div class="main">
                    <h2 class="pad_top1" style="left: -1800px; opacity: 0.2;">people</h2>
                </div>
                <div class="gallery" style="left: -2800px;">
                    <ul style="width: 2040px; left: 10px;">
                        <li><a href="/template/lanaphoto/images/gallery2_big_img1.jpg" data-gal="example_group2"><img src="/template/lanaphoto/images/gallery2_img1.jpg" alt="" style="opacity: 0.3;"/><span style="opacity: 0;"></span></a></li>
                        <li><a href="/template/lanaphoto/images/gallery2_big_img2.jpg" data-gal="example_group2"><img src="/template/lanaphoto/images/gallery2_img2.jpg" alt="" style="opacity: 0.3;"/><span style="opacity: 0;"></span></a></li>
                        <li><a href="/template/lanaphoto/images/gallery2_big_img3.jpg" data-gal="example_group2"><img src="/template/lanaphoto/images/gallery2_img3.jpg" alt="" style="opacity: 0.3;"/><span style="opacity: 0;"></span></a></li>
                        <li><a href="/template/lanaphoto/images/gallery2_big_img4.jpg" data-gal="example_group2"><img src="/template/lanaphoto/images/gallery2_img4.jpg" alt="" style="opacity: 0.3;"/><span style="opacity: 0;"></span></a></li>
                        <li><a href="/template/lanaphoto/images/gallery2_big_img5.jpg" data-gal="example_group2"><img src="/template/lanaphoto/images/gallery2_img5.jpg" alt="" style="opacity: 0.3;"/><span style="opacity: 0;"></span></a></li>
                        <li><a href="/template/lanaphoto/images/gallery2_big_img6.jpg" data-gal="example_group2"><img src="/template/lanaphoto/images/gallery2_img6.jpg" alt="" style="opacity: 0.3;"/><span style="opacity: 0;"></span></a></li>
                        <li><a href="/template/lanaphoto/images/gallery2_big_img7.jpg" data-gal="example_group2"><img src="/template/lanaphoto/images/gallery2_img7.jpg" alt="" style="opacity: 0.3;"/><span style="opacity: 0;"></span></a></li>
                        <li><a href="/template/lanaphoto/images/gallery2_big_img8.jpg" data-gal="example_group2"><img src="/template/lanaphoto/images/gallery2_img8.jpg" alt="" style="opacity: 0.3;"/><span style="opacity: 0;"></span></a></li>
                        <li><a href="/template/lanaphoto/images/gallery2_big_img9.jpg" data-gal="example_group2"><img src="/template/lanaphoto/images/gallery2_img9.jpg" alt="" style="opacity: 0.3;"/><span style="opacity: 0;"></span></a></li>
                        <li><a href="/template/lanaphoto/images/gallery2_big_img10.jpg" data-gal="example_group2"><img src="/template/lanaphoto/images/gallery2_img10.jpg" alt="" style="opacity: 0.3;"/><span style="opacity: 0;"></span></a></li>
                    </ul>
                </div>
                <div class="main">
                    <a href="#close" class="back" style="left: -1800px;"><span style="opacity: 0;"></span>back to home</a>
                </div>
            </li>
            <li id="page_fashion" style="position: absolute; display: none;">
                <div class="main">
                    <h2 class="pad_top1" style="left: -1800px; opacity: 0.2;">fashion</h2>
                </div>
                <div class="gallery" style="left: -2800px;">
                    <ul style="width: 2040px; left: 10px;">
                        <li><a href="/template/lanaphoto/images/gallery3_big_img1.jpg" data-gal="example_group3"><img src="/template/lanaphoto/images/gallery3_img1.jpg" alt="" style="opacity: 0.3;"/><span style="opacity: 0;"></span></a></li>
                        <li><a href="/template/lanaphoto/images/gallery3_big_img2.jpg" data-gal="example_group3"><img src="/template/lanaphoto/images/gallery3_img2.jpg" alt="" style="opacity: 0.3;"/><span style="opacity: 0;"></span></a></li>
                        <li><a href="/template/lanaphoto/images/gallery3_big_img3.jpg" data-gal="example_group3"><img src="/template/lanaphoto/images/gallery3_img3.jpg" alt="" style="opacity: 0.3;"/><span style="opacity: 0;"></span></a></li>
                        <li><a href="/template/lanaphoto/images/gallery3_big_img4.jpg" data-gal="example_group3"><img src="/template/lanaphoto/images/gallery3_img4.jpg" alt="" style="opacity: 0.3;"/><span style="opacity: 0;"></span></a></li>
                        <li><a href="/template/lanaphoto/images/gallery3_big_img5.jpg" data-gal="example_group3"><img src="/template/lanaphoto/images/gallery3_img5.jpg" alt="" style="opacity: 0.3;"/><span style="opacity: 0;"></span></a></li>
                        <li><a href="/template/lanaphoto/images/gallery3_big_img6.jpg" data-gal="example_group3"><img src="/template/lanaphoto/images/gallery3_img6.jpg" alt="" style="opacity: 0.3;"/><span style="opacity: 0;"></span></a></li>
                        <li><a href="/template/lanaphoto/images/gallery3_big_img7.jpg" data-gal="example_group3"><img src="/template/lanaphoto/images/gallery3_img7.jpg" alt="" style="opacity: 0.3;"/><span style="opacity: 0;"></span></a></li>
                        <li><a href="/template/lanaphoto/images/gallery3_big_img8.jpg" data-gal="example_group3"><img src="/template/lanaphoto/images/gallery3_img8.jpg" alt="" style="opacity: 0.3;"/><span style="opacity: 0;"></span></a></li>
                        <li><a href="/template/lanaphoto/images/gallery3_big_img9.jpg" data-gal="example_group3"><img src="/template/lanaphoto/images/gallery3_img9.jpg" alt="" style="opacity: 0.3;"/><span style="opacity: 0;"></span></a></li>
                        <li><a href="/template/lanaphoto/images/gallery3_big_img10.jpg" data-gal="example_group3"><img src="/template/lanaphoto/images/gallery3_img10.jpg" alt="" style="opacity: 0.3;"/><span style="opacity: 0;"></span></a></li>
                    </ul>
                </div>
                <div class="main">
                    <a href="#close" class="back" style="left: -1800px;"><span style="opacity: 0;"></span>back to home</a>
                </div>
            </li>
            <li id="page_mail" style="position: absolute; display: none;">
                <div class="main">
                    <h2 style="left: -1800px; opacity: 0.2;">mail</h2>
                    <div class="box" style="left: -1800px;">
                        <a href="#close" class="close"><span style="opacity: 0;"></span></a>
                        <figure class="google_map" style=""></figure>
                        <h3>Address</h3>
                        <p>The Company Name Inc.<br/>
                            8901 Marmora Road,<br/>
                            Glasgow, D04 89GR.</p>
                        <p><span class="col1">Freephone:</span>		+1 800 559 6580<br/>
                            <span class="col1">Telephone:</span>		+1 800 603 6035<br/>
                            <span class="col1">FAX:</span>			+1 800 889 9898 <br/>
                            E-mail: <a href="mailto:" class="link2"><span style="background-color: rgb(121, 121, 121); background-position: initial initial; background-repeat: initial initial;"></span>mail@demolink.org</a></p>
                    </div>
                </div>
            </li>
            <li id="page_privacy" style="position: absolute; display: none;">
                <div class="main">
                    <h2 style="left: -1800px; opacity: 0.2;">privacy</h2>
                    <div class="box" style="left: -1800px;">
                        <a href="#close" class="close"><span style="opacity: 0;"></span></a>
                        <div class="col1">
                            <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Praesent vestibulum molestie lacus. <br/>
                                Aenean nonummy hendrerit mauris. Phasellus porta. Fusce suscipit varius mi. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nulla dui. Fusce feugiat malesuada odio. Morbi nunc odio, gravida at, cursus nec, luctus a, lorem. Maecenas tristique orci ac sem. Duis ultricies pharetra magna. Donec accumsan malesuada orci. Donec sit amet eros. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Mauris fermentum dictum magna. </p>
                            <p>Sed laoreet aliquam leo. Ut tellus dolor dapibus eget, elementum vel, cursus eleifend, elit. <br/>
                                Aenean auctor wisi et urna. Aliquam erat volutpat. Duis ac turpis. Integer rutrum ante eu lacus. Quisque nulla. Vestibulum libero nisl, porta vel, scelerisque eget, malesuada at, neque. Vivamus eget nibh. Etiam cursus leo vel metus. Nulla facilisi. Aenean nec eros. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Suspendisse sollicitudin velit sed leo. Ut pharetra augue nec augue. Nam elit magna, hendrerit sit amet, tincidunt ac, viverra sed, nulla. Donec porta diam eu massa. Quisque diam lorem, interdum vitae, dapibus ac, scelerisque vitae, pede. </p>
                            <p>Donec eget tellus non erat lacinia fermentum. Donec in velit vel ipsum auctor pulvinar. <br/>
                                Proin ullamcorper urna et felis. Vestibulum iaculis lacinia est. Proin dictum elementum velit. Fusce euismod consequat ante. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Pellentesque sed dolor. Aliquam congue fermentum nisl. Mauris accumsan nulla vel diam. Sed in lacus ut enim adipiscing aliquet. Nulla venenatis. In pede mi, aliquet sit amet, euismod in, auctor ut, ligula. Aliquam dapibus tincidunt metus. Praesent justo dolor, lobortis quis. Donec sagittis euismod purus. Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque.</p>
                            <p>E-mail: <a href="mailto:" class="link2"><span style="background-color: rgb(121, 121, 121); background-position: initial initial; background-repeat: initial initial;"></span>info@demolink.org</a></p>
                        </div>
                    </div>
                </div>
            </li>
            <li id="page_more" style="position: absolute; display: none;">
                <div class="main">
                    <h2 style="left: -1800px; opacity: 0.2;">Lorem</h2>
                    <div class="box" style="left: -1800px;">
                        <a href="#close" class="close"><span style="opacity: 0;"></span></a>
                        <div class="relative">
                            <div class="scroll" style="position: relative; z-index: 1; overflow: hidden;">
                                <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Praesent vestibulum molestie lacus. <br/>
                                    Aenean nonummy hendrerit mauris. Phasellus porta. Fusce suscipit varius mi. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nulla dui. Fusce feugiat malesuada odio. Morbi nunc odio, gravida at, cursus nec, luctus a, lorem. Maecenas tristique orci ac sem. Duis ultricies pharetra magna. Donec accumsan malesuada orci. Donec sit amet eros. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Mauris fermentum dictum magna. </p>
                                <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Praesent vestibulum molestie lacus. <br/>
                                    Aenean nonummy hendrerit mauris. Phasellus porta. Fusce suscipit varius mi. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nulla dui. Fusce feugiat malesuada odio. Morbi nunc odio, gravida at, cursus nec, luctus a, lorem. Maecenas tristique orci ac sem. Duis ultricies pharetra magna. Donec accumsan malesuada orci. Donec sit amet eros. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Mauris fermentum dictum magna. </p>
                                <p>Sed laoreet aliquam leo. Ut tellus dolor dapibus eget, elementum vel, cursus eleifend, elit. <br/>
                                    Aenean auctor wisi et urna. Aliquam erat volutpat. Duis ac turpis. Aenean auctor wisi et urna. Aliquam erat volutpat. Duis ac turpis. Aenean auctor wisi et urna. Aliquam erat volutpat. Duis ac turpis. Aenean auctor wisi et urna. Aliquam erat volutpat. Duis ac turpis. Integer rutrum ante eu lacus. Quisque nulla. Vestibulum libero nisl, porta vel, scelerisque eget, malesuada at, neque. Vivamus eget nibh. Etiam cursus leo vel metus. Nulla facilisi. Aenean nec eros. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Suspendisse sollicitudin velit sed leo. Ut pharetra augue nec augue. Nam elit magna, hendrerit sit amet, tincidunt ac, viverra sed, nulla. Donec porta diam eu massa. Quisque diam lorem, interdum vitae, dapibus ac, scelerisque vitae, pede. </p>
                                <p>Donec eget tellus non erat lacinia fermentum. Donec in velit vel ipsum auctor pulvinar. <br/>
                                    Proin ullamcorper urna et felis. Vestibulum iaculis lacinia est. Proin dictum elementum velit. Fusce euismod consequat ante. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Pellentesque sed dolor. Aliquam congue fermentum nisl. Mauris accumsan nulla vel diam. Sed in lacus ut enim adipiscing aliquet. Nulla venenatis. In pede mi, aliquet sit amet, euismod in, auctor ut, ligula. Aliquam dapibus tincidunt metus. Praesent justo dolor, lobortis quis. Donec sagittis euismod purus. Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque.</p>
                            </div>
                            <div class="track ui-slider ui-slider-vertical ui-widget ui-widget-content ui-corner-all" style="position: absolute; z-index: 2; padding: 0px; left: 699px; top: 104px; height: 310px; width: 1px; background-image: none; background-position: initial initial; background-repeat: initial initial;"><a href="#" class="ui-slider-handle ui-state-default ui-corner-all" style="bottom: 100%;"></a><div class="shuttle" style="margin-top: -29px; cursor: pointer; position: absolute;"></div></div><div style="width: 1px; height: 430px; position: absolute; left: 735px; top: -15px; background-color: rgb(204, 204, 204); background-image: none; background-position: 0% 0%; background-repeat: repeat repeat;"><div class="_up-butt" style="position: absolute; width: 1px; height: 1px; left: 0px; top: 0px; cursor: pointer; z-index: 3;"></div><div class="_down-butt" style="position: absolute; width: 1px; height: 1px; left: 0px; bottom: 0px; cursor: pointer; z-index: 3;"></div></div></div>
                    </div>
                </div>
            </li>
        </ul>
    </article>
    <!--content end-->
    <div class="main">
        <!--footer -->
        <footer>
            Светлана Добрынина © 2012<!--  &#160;|&#160;  <a href="#!/page_privacy" class="link1"><span style="background-color: rgb(32, 32, 32); background-position: initial initial; background-repeat: initial initial;"></span>Privacy Policy</a>--><br/>
            <ul id="icons">
                <li><a href="#"><img src="/template/lanaphoto/images/icon1.gif" alt=""/><img src="/template/lanaphoto/images/icon1_active.jpg" alt="" class="img_act" style="opacity: 0;"/></a></li>
                <li><a href="#"><img src="/template/lanaphoto/images/icon2.gif" alt=""/><img src="/template/lanaphoto/images/icon2_active.jpg" alt="" class="img_act" style="opacity: 0;"/></a></li>
                <li><a href="#"><img src="/template/lanaphoto/images/icon3.gif" alt=""/><img src="/template/lanaphoto/images/icon3_active.jpg" alt="" class="img_act" style="opacity: 0;"/></a></li>
            </ul>
            <!-- {%FOOTER_LINK} -->
        </footer>
        <!--footer end-->
    </div>
</div>
<!--<script>
    $(window).load(function() {
        $('.page_spinner').fadeOut();
        $('body').css({overflow:'visible'})
    })
</script>-->
<!--coded by koma-->
<!--<script type="text/javascript">

    var _gaq = _gaq || [];
    _gaq.push(['_setAccount', 'UA-7078796-5']);
    _gaq.push(['_trackPageview']);

    (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
    })();

</script>   -->

    <div id="fancybox-tmp"></div>
    <div id="fancybox-loading">
        <div></div>
    </div>
    <div id="fancybox-overlay"></div>
    <div id="fancybox-wrap">
        <div id="fancybox-outer">
            <div class="fancybox-bg" id="fancybox-bg-n"></div>
            <div class="fancybox-bg" id="fancybox-bg-ne"></div>
            <div class="fancybox-bg" id="fancybox-bg-e"></div>
            <div class="fancybox-bg" id="fancybox-bg-se"></div>
            <div class="fancybox-bg" id="fancybox-bg-s"></div>
            <div class="fancybox-bg" id="fancybox-bg-sw"></div>
            <div class="fancybox-bg" id="fancybox-bg-w"></div>
            <div class="fancybox-bg" id="fancybox-bg-nw"></div>
            <div id="fancybox-content"></div>
            <a id="fancybox-close">
                <span style="opacity: 0;"></span>
            </a>
            <div id="fancybox-title"></div>
            <a href="javascript:;" id="fancybox-left">
                <span style="opacity: 0;"></span>
            </a>
            <a href="javascript:;" id="fancybox-right">
                <span style="opacity: 0;"></span>
            </a>
        </div>
    </div>
</body>
</html>
</xsl:template>

<xsl:template match="root/module/item[_module_name='menu' and _method_name='get']" priority="1">
   <ul id="menu" class="sf-js-enabled">
        <xsl:for-each select="item">
            <li>
                <xsl:attribute name="data-type">#img<xsl:value-of select="position()"/></xsl:attribute>
                <span style="opacity: 0.2;">
                    <a href="{link}"><xsl:value-of select="title"/></a>
                </span>
            </li>
        </xsl:for-each>
    </ul>
</xsl:template>
</xsl:stylesheet>