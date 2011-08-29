<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="utf-8" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />
<xsl:template match="/">
<html><head>
	<link href="template/nbk/index.css" rel="stylesheet" type="text/css"/>
	<xsl:call-template name="_head"/>
</head>
<body>
	<table class="wrap">
		<tbody>
			<tr class="top">
				<xsl:comment>Главное меню тут</xsl:comment>
			</tr>
			<tr class="container">
				<td>
					<div class="right">
						<xsl:call-template name="_call">
							<xsl:with-param name="position">right</xsl:with-param>
						</xsl:call-template>
					</div>
					<xsl:call-template name="_call"/>
				</td>
			</tr>
			<tr class="footer">
				<td>&#169; 2011 <a href="http://grosstech.ru">Grosstech</a>, <a href="mailto:kulakov.serg@gmail.com">Кулаков Сергей</a></td>
			</tr>
		</tbody>
	</table>
</body>
</html>
</xsl:template>
</xsl:stylesheet>