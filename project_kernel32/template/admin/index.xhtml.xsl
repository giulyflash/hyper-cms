<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="utf-8" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />
<xsl:template match="/">
<html><head>
	<link href="template/admin/default.css" rel="stylesheet" type="text/css"/>
	<xsl:call-template name="_head"/>
</head>
<body>
	<div id="header" alt="На главную" title="На главную">
		<div class="header_right">
			<xsl:call-template name="_call">
				<xsl:with-param name="position">head</xsl:with-param>
			</xsl:call-template>
		</div>
		<a href="/admin.php"><h2>Панель управления</h2></a>
	</div>
<hr />
<div id="page">
	<div id="bg">
		<div id="content">
			<div class="post post1">
				<xsl:call-template name="_call">
					<xsl:with-param name="position">top</xsl:with-param>
				</xsl:call-template>
				<xsl:call-template name="_call"/>
				<p class="meta"></p>
			</div>
		</div>
		<!-- end contentn -->
		<div id="sidebar">
			<xsl:call-template name="_call">
				<xsl:with-param name="position">right</xsl:with-param>
			</xsl:call-template>
		</div>
		<!-- end sidebar -->
		<div style="clear: both;">&#160;</div>
	</div>
</div>
<!-- end page -->
<hr />
<div id="footer">
	<p>&#169; 2011 <a href="http://grosstech.ru" target="_blank">Гросcтех</a>.</p>
</div>
</body>
</html>
</xsl:template>
</xsl:stylesheet>