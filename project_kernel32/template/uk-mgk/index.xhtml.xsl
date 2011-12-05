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
					&#169; 2011 <a href="#">Гросcтех</a>.
				</td>
			</tr>
		</tbody>
	</table>
</body>
</html>
</xsl:template>
</xsl:stylesheet>