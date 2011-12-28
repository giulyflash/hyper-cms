<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="utf-8" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />

<xsl:template match="root/module/item[_module_name='breadcrumbs' and _method_name='get']">
	<div class="{_module_name}">
		<xsl:for-each select="item">
			<a href="{href}" title="{title}" alt="{title}"><xsl:value-of select="title"/></a>
			<xsl:if test="not(position()=last())">&#160;<span><xsl:value-of select="../_config/delimiter" disable-output-escaping="yes"/></span> </xsl:if>
		</xsl:for-each>
	</div>
</xsl:template>

</xsl:stylesheet>