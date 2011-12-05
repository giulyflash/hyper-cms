<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="utf-8" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />
<xsl:template match="/">
<html><head>
	<link href="/template/jkomfort/default.css" rel="stylesheet" type="text/css"/>
	<xsl:call-template name="_head"/>
</head>
<body>
	<table class="wrap">
		<tbody>
			<tr>
				<td colspan="2">
					<a href="/">
						<div id="header" alt="На главную" title="На главную">
							<div id="logo1">
								<xsl:variable name="domain" select="/root/meta/domain"/>
									<xsl:variable name="img_prefix">/template/jkomfort/domain/<xsl:value-of select="$domain"/><xsl:if test="substring($domain,string-length($domain)-2,3)!='.ru'">.ru</xsl:if>_</xsl:variable>
								<div id="logo2" style="background-image: url('{$img_prefix}arms.png')">
									<img title="На главную" src="{$img_prefix}text.png" alt="{root/meta/app_config/default_page_title}"/>
								</div>
							</div>
						</div>
					</a>
				</td>
			</tr>
			<tr id="page">
				<td>
					<div id="content">
						<div class="post post1">
							<xsl:call-template name="_call">
								<xsl:with-param name="position">top</xsl:with-param>
							</xsl:call-template>
							<xsl:call-template name="_call"/>
							<p class="meta"></p>
						</div>
					</div>
				</td>
				<td>
					<div id="sidebar">
						<xsl:call-template name="_call">
							<xsl:with-param name="position">right</xsl:with-param>
						</xsl:call-template>
					</div>
				</td>
			</tr>
			<tr>
				<td id="footer" colspan="2">
					<p>&#169; 2011 <a href="http://grosstech.ru">Гросcтех</a>.<!--  УК состоит в Некоммерческом Партнёрстве СРО «Жилищно-строительное объединение Мурмана» -->
						<span class="metrika">
							<xsl:choose>
								<xsl:when test="/root/meta/domain='jkomfort.ru'">
									<xsl:variable name="onclick">try{Ya.Metrika.informer({i:this,id:11157355,type:0,lang:'ru'});return false}catch(e){}</xsl:variable>
									<!-- Yandex.Metrika informer --><a href="http://metrika.yandex.ru/stat/?id=11157355&amp;from=informer" target="_blank" rel="nofollow"><img src="//bs.yandex.ru/informer/11157355/3_0_FFFFFFFF_EFEFEFFF_0_pageviews" style="width:88px; height:31px; border:0;" alt="Яндекс.Метрика" title="Яндекс.Метрика: данные за сегодня (просмотры, визиты и уникальные посетители)" onclick="{$onclick}"/></a><!-- /Yandex.Metrika informer --><!-- Yandex.Metrika counter --><div style="display:none;"><script type="text/javascript">(function(w, c) { (w[c] = w[c] || []).push(function() { try { w.yaCounter11157355 = new Ya.Metrika({id:11157355, enableAll: true}); } catch(e) { } }); })(window, "yandex_metrika_callbacks");</script></div><script src="//mc.yandex.ru/metrika/watch.js" type="text/javascript" defer="defer"></script><noscript><div><img src="//mc.yandex.ru/watch/11157355" style="position:absolute; left:-9999px;" alt="" /></div></noscript><!-- /Yandex.Metrika counter -->
								</xsl:when>
								<xsl:when test="/root/meta/domain='apatity.jkomfort.ru'">
									<xsl:variable name="onclick">try{Ya.Metrika.informer({i:this,id:11161525,type:0,lang:'ru'});return false}catch(e){}</xsl:variable>
									<!-- Yandex.Metrika informer --><a href="http://metrika.yandex.ru/stat/?id=11161525&amp;from=informer" target="_blank" rel="nofollow"><img src="//bs.yandex.ru/informer/11161525/3_0_FFFFFFFF_EFEFEFFF_0_pageviews" style="width:88px; height:31px; border:0;" alt="Яндекс.Метрика" title="Яндекс.Метрика: данные за сегодня (просмотры, визиты и уникальные посетители)" onclick="{$onclick}"/></a><!-- /Yandex.Metrika informer --><!-- Yandex.Metrika counter --><div style="display:none;"><script type="text/javascript">(function(w, c) { (w[c] = w[c] || []).push(function() { try { w.yaCounter11161525 = new Ya.Metrika({id:11161525, enableAll: true}); } catch(e) { } }); })(window, "yandex_metrika_callbacks");</script></div><script src="//mc.yandex.ru/metrika/watch.js" type="text/javascript" defer="defer"></script><noscript><div><img src="//mc.yandex.ru/watch/11161525" style="position:absolute; left:-9999px;" alt="" /></div></noscript><!-- /Yandex.Metrika counter -->
								</xsl:when>
								<xsl:when test="/root/meta/domain='kandalaksha.jkomfort.ru'">
									<xsl:variable name="onclick">try{Ya.Metrika.informer({i:this,id:11162239,type:0,lang:'ru'});return false}catch(e){}</xsl:variable>
									<!-- Yandex.Metrika informer --><a href="http://metrika.yandex.ru/stat/?id=11162239&amp;from=informer" target="_blank" rel="nofollow"><img src="//bs.yandex.ru/informer/11162239/3_0_FFFFFFFF_EFEFEFFF_0_pageviews" style="width:88px; height:31px; border:0;" alt="Яндекс.Метрика" title="Яндекс.Метрика: данные за сегодня (просмотры, визиты и уникальные посетители)" onclick="{$onclick}"/></a><!-- /Yandex.Metrika informer --><!-- Yandex.Metrika counter --><div style="display:none;"><script type="text/javascript">(function(w, c) { (w[c] = w[c] || []).push(function() { try { w.yaCounter11162239 = new Ya.Metrika({id:11162239, enableAll: true}); } catch(e) { } }); })(window, "yandex_metrika_callbacks");</script></div><script src="//mc.yandex.ru/metrika/watch.js" type="text/javascript" defer="defer"></script><noscript><div><img src="//mc.yandex.ru/watch/11162239" style="position:absolute; left:-9999px;" alt="" /></div></noscript><!-- /Yandex.Metrika counter -->
								</xsl:when>
								<xsl:when test="/root/meta/domain='komfortplus.jkomfort.ru'">
									<xsl:variable name="onclick">try{Ya.Metrika.informer({i:this,id:11162290,type:0,lang:'ru'});return false}catch(e){}</xsl:variable>
									<!-- Yandex.Metrika informer --><a href="http://metrika.yandex.ru/stat/?id=11162290&amp;from=informer" target="_blank" rel="nofollow"><img src="//bs.yandex.ru/informer/11162290/3_0_FFFFFFFF_EFEFEFFF_0_pageviews" style="width:88px; height:31px; border:0;" alt="Яндекс.Метрика" title="Яндекс.Метрика: данные за сегодня (просмотры, визиты и уникальные посетители)" onclick="{$onclick}"/></a><!-- /Yandex.Metrika informer --><!-- Yandex.Metrika counter --><div style="display:none;"><script type="text/javascript">(function(w, c) { (w[c] = w[c] || []).push(function() { try { w.yaCounter11162290 = new Ya.Metrika({id:11162290, enableAll: true}); } catch(e) { } }); })(window, "yandex_metrika_callbacks");</script></div><script src="//mc.yandex.ru/metrika/watch.js" type="text/javascript" defer="defer"></script><noscript><div><img src="//mc.yandex.ru/watch/11162290" style="position:absolute; left:-9999px;" alt="" /></div></noscript><!-- /Yandex.Metrika counter -->
								</xsl:when>
								<xsl:when test="/root/meta/domain='nordservis.jkomfort.ru'">
									<xsl:variable name="onclick">try{Ya.Metrika.informer({i:this,id:11162320,type:0,lang:'ru'});return false}catch(e){}</xsl:variable>
									<!-- Yandex.Metrika informer --><a href="http://metrika.yandex.ru/stat/?id=11162320&amp;from=informer" target="_blank" rel="nofollow"><img src="//bs.yandex.ru/informer/11162320/3_0_FFFFFFFF_EFEFEFFF_0_pageviews" style="width:88px; height:31px; border:0;" alt="Яндекс.Метрика" title="Яндекс.Метрика: данные за сегодня (просмотры, визиты и уникальные посетители)" onclick="{$onclick}"/></a><!-- /Yandex.Metrika informer --><!-- Yandex.Metrika counter --><div style="display:none;"><script type="text/javascript">(function(w, c) { (w[c] = w[c] || []).push(function() { try { w.yaCounter11162320 = new Ya.Metrika({id:11162320, enableAll: true}); } catch(e) { } }); })(window, "yandex_metrika_callbacks");</script></div><script src="//mc.yandex.ru/metrika/watch.js" type="text/javascript" defer="defer"></script><noscript><div><img src="//mc.yandex.ru/watch/11162320" style="position:absolute; left:-9999px;" alt="" /></div></noscript><!-- /Yandex.Metrika counter -->
								</xsl:when>
								<xsl:when test="/root/meta/domain='olenegorsk.jkomfort.ru'">
									<xsl:variable name="onclick">try{Ya.Metrika.informer({i:this,id:11162365,type:0,lang:'ru'});return false}catch(e){}</xsl:variable>
									<!-- Yandex.Metrika informer --><a href="http://metrika.yandex.ru/stat/?id=11162365&amp;from=informer" target="_blank" rel="nofollow"><img src="//bs.yandex.ru/informer/11162365/3_0_FFFFFFFF_EFEFEFFF_0_pageviews" style="width:88px; height:31px; border:0;" alt="Яндекс.Метрика" title="Яндекс.Метрика: данные за сегодня (просмотры, визиты и уникальные посетители)" onclick="{$onclick}"/></a><!-- /Yandex.Metrika informer --><!-- Yandex.Metrika counter --><div style="display:none;"><script type="text/javascript">(function(w, c) { (w[c] = w[c] || []).push(function() { try { w.yaCounter11162365 = new Ya.Metrika({id:11162365, enableAll: true}); } catch(e) { } }); })(window, "yandex_metrika_callbacks");</script></div><script src="//mc.yandex.ru/metrika/watch.js" type="text/javascript" defer="defer"></script><noscript><div><img src="//mc.yandex.ru/watch/11162365" style="position:absolute; left:-9999px;" alt="" /></div></noscript><!-- /Yandex.Metrika counter -->
								</xsl:when>
								<xsl:when test="/root/meta/domain='zelenoborskiy.jkomfort.ru'">
									<xsl:variable name="onclick">try{Ya.Metrika.informer({i:this,id:11162401,type:0,lang:'ru'});return false}catch(e){}</xsl:variable>
									<!-- Yandex.Metrika informer --><a href="http://metrika.yandex.ru/stat/?id=11162401&amp;from=informer" target="_blank" rel="nofollow"><img src="//bs.yandex.ru/informer/11162401/3_0_FFFFFFFF_EFEFEFFF_0_pageviews" style="width:88px; height:31px; border:0;" alt="Яндекс.Метрика" title="Яндекс.Метрика: данные за сегодня (просмотры, визиты и уникальные посетители)" onclick="{$onclick}"/></a><!-- /Yandex.Metrika informer --><!-- Yandex.Metrika counter --><div style="display:none;"><script type="text/javascript">(function(w, c) { (w[c] = w[c] || []).push(function() { try { w.yaCounter11162401 = new Ya.Metrika({id:11162401, enableAll: true}); } catch(e) { } }); })(window, "yandex_metrika_callbacks");</script></div><script src="//mc.yandex.ru/metrika/watch.js" type="text/javascript" defer="defer"></script><noscript><div><img src="//mc.yandex.ru/watch/11162401" style="position:absolute; left:-9999px;" alt="" /></div></noscript><!-- /Yandex.Metrika counter -->
								</xsl:when>
							</xsl:choose>
						</span>
					</p>
				</td>
			</tr>
		</tbody>
	</table>
</body>
</html>
</xsl:template>
</xsl:stylesheet>