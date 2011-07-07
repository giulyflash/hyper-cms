<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="utf-8" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />
<xsl:template match="/">
<html><head>
	<link href="template/uk-mgk/default.css" rel="stylesheet" type="text/css"/>
	<xsl:call-template name="_head"/>
</head>
<body>
	<div class="wrap">
		<a href="index.php" alt="На главную" title="На главную">
			<div class="header">
				<h1>Мурманская городская Компания</h1>
			</div>
		</a>
		<div class="center_wrap">
			<div class="left">
				<div class="menu">
					<xsl:call-template name="_call">
						<xsl:with-param name="position">top</xsl:with-param>
					</xsl:call-template>
				</div>
			</div>
			<div class="right">
				<xsl:call-template name="_call">
					<xsl:with-param name="position">right</xsl:with-param>
				</xsl:call-template>
			</div>
			<div class="center">
				<xsl:call-template name="_call"/>
				<p style="clear:both"></p>
			</div>
		</div>
		<div class="footer">
			&#169; 2011 <a href="#">Гросcтех</a>. УК состоит в Некоммерческом Партнёрстве СРО «Жилищно-строительное объединение Мурмана»
		</div>
	</div>
</body>
</html>
</xsl:template>
</xsl:stylesheet>