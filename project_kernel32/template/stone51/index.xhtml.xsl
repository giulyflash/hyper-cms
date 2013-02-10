<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="utf-8" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />
<xsl:template match="/">
<html><head>
	<link href="/template/grosstech/index.css" rel="stylesheet" type="text/css"/>
	<link href="/template/grosstech/images/gross32.ico" rel="shortcut icon" type="image/x-icon" />
	<xsl:call-template name="_head"/>

    <link rel="icon" href="Spare%20Parts_files/favicon0.png"/>
    <link rel="stylesheet" type="text/css" href="Spare%20Parts_files/styleshe.css"/>
    <link rel="stylesheet" type="text/css" href="Spare%20Parts_files/colorbox.css" media="screen"/>
    <link rel="stylesheet" href="Spare%20Parts_files/cloud-zo.css" type="text/css"/>
    <link rel="stylesheet"  href="Spare%20Parts_files/superfis.css" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="Spare%20Parts_files/slidesho.css" media="screen"/>
    <link rel="stylesheet" type="text/css" href="Spare%20Parts_files/jquery-u.css"/>
    <link rel="stylesheet" type="text/css" href="Spare%20Parts_files/jquery00.css" media="screen"/>
    <link rel="stylesheet" href="Spare%20Parts_files/css00000.css" type="text/css"/>
    <link rel="stylesheet" href="Spare%20Parts_files/css00001.css" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="Spare%20Parts_files/livesear.css"/>
    <link rel="stylesheet" type="text/css" href="my.css"/>
</head>
<body>
	<table class="wrap">
		<tbody>
			<tr class="head">
				<td>
					<a href="/">
						<img alt="Grosstech - Гросстех - ремонт оргтехники" title="Grosstech - Гросстех - ремонт оргтехники в Мурманске" src="/template/grosstech/images/font.png"/>
					</a>
					<div class="contact">
						<p alt="Grosstech - Гросстех - ремонт оргтехники" title="Grosstech - Гросстех - ремонт оргтехники в Мурманске">г. Мурманск, ул. Марата 26</p>
						<p alt="Grosstech - Гросстех - ремонт оргтехники" title="Grosstech - Гросстех - ремонт оргтехники в Мурманске">тел. 231021, +7911-308-81-99</p>
					</div>
				</td>
			</tr>
			<tr class="menu_cont">
				<td>
					<div class="img_preload">
						<img src="/template/grosstech/images/button2_center_left.png"/>
						<img src="/template/grosstech/images/button2_center_right.png"/>
						<img src="/template/grosstech/images/button2_bottom_left.png"/>
						<img src="/template/grosstech/images/button2_bottom_right.png"/>
						<img src="template/grosstech/images/round/round_on_left.png"/>
						<img src="template/grosstech/images/round/round_on_right.png"/>
					</div>
					<div class="menu wrap_left">
						<div class="menu wrap_right">
							<xsl:call-template name="_call">
								<xsl:with-param name="position">top</xsl:with-param>
							</xsl:call-template>
						</div>
					</div>
					<xsl:call-template name="_call">
						<xsl:with-param name="position">breadcrumbs</xsl:with-param>
					</xsl:call-template>
				</td>
			</tr>
			<tr class="content">
				<td>
					<div class="left_container">
						<xsl:call-template name="_call">
							<xsl:with-param name="position">left</xsl:with-param>
						</xsl:call-template>
					</div>
					<div class="container">
						<xsl:call-template name="_call"/>
					</div>
				</td>
			</tr>
			<tr class="footer">
				<td>
					<div>
						<div>
							<div>
								<span class="metrika">
									<xsl:variable name="onclick">try{Ya.Metrika.informer({i:this,id:11615119,type:0,lang:'ru'});return false}catch(e){}</xsl:variable>
									<a href="http://metrika.yandex.ru/stat/?id=11157355&amp;from=informer" target="_blank" rel="nofollow"><img src="//bs.yandex.ru/informer/11157355/3_0_FFFFFFFF_EFEFEFFF_0_pageviews" style="width:88px; height:31px; border:0;" alt="Яндекс.Метрика" title="Яндекс.Метрика: данные за сегодня (просмотры, визиты и уникальные посетители)" onclick="{$onclick}"/></a>
									<div style="display:none;"><script type="text/javascript">(function(w, c) { (w[c] = w[c] || []).push(function() { try { w.yaCounter11157355 = new Ya.Metrika({id:11157355, enableAll: true}); } catch(e) { } }); })(window, "yandex_metrika_callbacks");</script></div><script src="//mc.yandex.ru/metrika/watch.js" type="text/javascript" defer="defer"></script><noscript><div><img src="//mc.yandex.ru/watch/11157355" style="position:absolute; left:-9999px;" alt="" /></div></noscript><script type='text/javascript'>  var _gaq = _gaq || [];  _gaq.push(['_setAccount', 'UA-27520759-1']);  _gaq.push(['_trackPageview']);  (function() {    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);  })();</script>
								</span>
								<a target="_blank" class="kasperski" href="http://www.kaspersky.ru/" alt="http://www.kaspersky.ru/" title="http://www.kaspersky.ru/"></a>
								<p>
									&#169; 2011 <a href="http://grosstech.ru">Grosstech</a>, <a href="mailto:kulakov.serg@gmail.com">Кулаков Сергей</a>
								</p>
							</div>
						</div>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
	<!-- <script type="text/javascript">var _gaq = _gaq || []; _gaq.push(['_setAccount', 'UA-26570427-1']); _gaq.push(['_setDomainName', 'grosstech.ru']); _gaq.push(['_trackPageview']); (function() { var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true; ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js'; var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s); })();</script>-->
</body>
</html>
</xsl:template>
</xsl:stylesheet>