<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="utf-8" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />
<xsl:template match="/">
<html><head>
	<link href="template/uk-mgk/default.css" rel="stylesheet" type="text/css"/>
	<xsl:call-template name="_head"/>
</head>
<body>
	<table class="wrap">
		<tbody>
			<tr>
				<td colspan="3" class="header">
					<a href="index.php" alt="На главную" title="На главную">
						<h1>Мурманская Городская Компания</h1>
					</a>
				</td>
			</tr>
			<tr>
				<td class="left">
					<xsl:call-template name="_call">
						<xsl:with-param name="position">top</xsl:with-param>
					</xsl:call-template>
				</td>
				<td class="center content">
					<xsl:call-template name="_call"/>
				</td>
				<td class="right">
					<xsl:call-template name="_call">
						<xsl:with-param name="position">right</xsl:with-param>
					</xsl:call-template>
				</td>
			</tr>
			<tr>
				<td class="footer" colspan="3">
					&#169; 2011 <a target="_blank" href="http://grosstech.ru">Гросcтех</a>.
					<span class="metrika">
						<xsl:if test="/root/meta/domain='uk-mgk.ru'">
							<xsl:variable name="onclick">try{Ya.Metrika.informer({i:this,id:11185258,type:0,lang:'ru'});return false}catch(e){}</xsl:variable>
							<!-- Yandex.Metrika informer --><a href="http://metrika.yandex.ru/stat/?id=11185258&amp;from=informer" target="_blank" rel="nofollow"><img src="//bs.yandex.ru/informer/11185258/3_0_FFFFFFFF_EFEFEFFF_0_pageviews" style="width:88px; height:31px; border:0;" alt="Яндекс.Метрика" title="Яндекс.Метрика: данные за сегодня (просмотры, визиты и уникальные посетители)" onclick="{$onclick}"/></a><!-- /Yandex.Metrika informer --><!-- Yandex.Metrika counter --><div style="display:none;"><script type="text/javascript">(function(w, c) { (w[c] = w[c] || []).push(function() { try { w.yaCounter11185258 = new Ya.Metrika({id:11185258, enableAll: true}); } catch(e) { } }); })(window, "yandex_metrika_callbacks");</script></div><script src="//mc.yandex.ru/metrika/watch.js" type="text/javascript" defer="defer"></script><noscript><div><img src="//mc.yandex.ru/watch/11185258" style="position:absolute; left:-9999px;" alt="" /></div></noscript><!-- /Yandex.Metrika counter -->
							<script type="text/javascript">  var _gaq = _gaq || [];  _gaq.push(['_setAccount', 'UA-27520598-1']);  _gaq.push(['_trackPageview']);  (function() {    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);  })();</script>
						</xsl:if>
					</span>
				</td>
			</tr>
		</tbody>
	</table>
</body>
</html>
</xsl:template>
</xsl:stylesheet>