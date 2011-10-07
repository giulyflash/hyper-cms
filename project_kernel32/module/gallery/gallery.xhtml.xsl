<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="utf-8" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />

<xsl:template match="text()[../../../_module_name='gallery' and not(../items) and not(../../item/items) and ../../../_show='current']" priority="0.3">
	<xsl:call-template name="_base_obj_not_found">
		<xsl:with-param name="text" select="/root/language/gallery/get_category/no_obj_msg"/>
	</xsl:call-template>
</xsl:template>

<xsl:template match="title[../../../_module_name='gallery' and not(../uncategorized) and not(items)]" priority="0.3">
	<xsl:call-template name="_base_obj_not_found">
		<xsl:with-param name="text" select="/root/language/gallery/get_category/no_obj_msg"/>
	</xsl:call-template>
</xsl:template>

<xsl:template match="_module_name[.='gallery' and not(item)]" priority="0.3">
	<xsl:call-template name="_base_obj_not_found">
		<xsl:with-param name="text" select="/root/language/gallery/get_category/no_obj_msg"/>
	</xsl:call-template>
</xsl:template>

</xsl:stylesheet>