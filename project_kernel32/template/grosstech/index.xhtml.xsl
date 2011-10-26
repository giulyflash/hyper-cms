<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="utf-8" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />
<xsl:template match="/">
<html><head>
	<link href="template/grosstech/index.css" rel="stylesheet" type="text/css"/>
	<xsl:call-template name="_head"/>
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
				<div class="img_preload">
					<img src="/template/grosstech/images/button2_center_left.png"/>
					<img src="/template/grosstech/images/button2_center_right.png"/>
					<img src="/template/grosstech/images/button2_bottom_left.png"/>
					<img src="/template/grosstech/images/button2_bottom_right.png"/>
					<img src="template/grosstech/images/round/round_on_left.png"/>
					<img src="template/grosstech/images/round/round_on_right.png"/>
				</div>
				<td>
					<div class="menu wrap_left">
						<div class="menu wrap_right">
							<xsl:call-template name="_call">
								<xsl:with-param name="position">top</xsl:with-param>
							</xsl:call-template>
						</div>
					</div>
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
	<script type="text/javascript">var _gaq = _gaq || []; _gaq.push(['_setAccount', 'UA-26570427-1']); _gaq.push(['_setDomainName', 'grosstech.ru']); _gaq.push(['_trackPageview']); (function() { var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true; ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js'; var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s); })();</script>
</body>
</html>
</xsl:template>
</xsl:stylesheet>