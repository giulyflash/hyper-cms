<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="utf-8" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />

<xsl:template match="/">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<link href="/template/dekre/style/style.css" rel="stylesheet" type="text/css" />
		<xsl:call-template name="_head"/>
	</head>

	<body>
		<table class="outer">
			<tbody>
				<tr>
					<td colspan="2">
						<div id="logo-bg">
							<div class="name">Декре</div>
							<div class="tag">Услуги нотариусов</div>
						</div>
						<div id="headimage"></div>
					</td>
				</tr>
				<tr>
					<td colspan="2" class="toplinks">
						<div id="rc1"></div>
						<div id="rc2"></div>
						<xsl:call-template name="_call">
							<xsl:with-param name="position">top</xsl:with-param>
						</xsl:call-template>
					</td>
				</tr>
				<tr id="outer2">
					<td id="content">
						<xsl:call-template name="_call">
							<xsl:with-param name="position">breadcrumbs</xsl:with-param>
						</xsl:call-template>
						<xsl:call-template name="_call"/>
					</td>
					<td id="left-nav">
						<xsl:call-template name="_call">
							<xsl:with-param name="position">right</xsl:with-param>
						</xsl:call-template>
					</td>
				</tr>
				<tr>
					<td id="bottom" colspan="2">
						<p>
							&#169; 2011 <a target="_blank" href="http://grosstech.ru">Grosstech</a>
						</p>
					</td>
				</tr>
			</tbody>
		</table>
	</body>
</html>

</xsl:template>
</xsl:stylesheet>