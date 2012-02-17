<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="utf-8" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />
<xsl:template match="/">
<html><head>
	<link href="/template/urhelp51/index.css" rel="stylesheet" type="text/css"/>
	<xsl:call-template name="_head"/>
</head>
<body>
	<div class="bg_top">
		<div class="header">
			<div class="menu_cont0">
				<div class="menu_cont">
					<div class="menu_bg_top">
					</div>
					<!-- <div class="menu">
					</div> -->
					<xsl:call-template name="_call">
						<xsl:with-param name="position">top</xsl:with-param>
					</xsl:call-template>
					<div class="menu_bg_bottom">
					</div>
				</div>
			</div>
		</div>
		<div class="wrapper_bg">
			<div class="wrapper_bg_bottom">
				<div class="wrapper">
					<div class="right_column">
						<h1 class="top_heading">Наши услуги</h1>
						<xsl:call-template name="_call">
							<xsl:with-param name="position">right</xsl:with-param>
						</xsl:call-template>
					</div>
					<div class="left_column">
						<a href="/" title="На главную" alt="На главную"><h1 class="top_heading"><span>Юр</span>помощь</h1></a>
						<xsl:call-template name="_call"/>
					</div>
					<div class="footer">
						&#169; 2011 <a target="_blank" href="http://grosstech.ru">Grosstech</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>

</xsl:template>
</xsl:stylesheet>