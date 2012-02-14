<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="utf-8" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />

<xsl:template match="root/module/item[_module_name='question' and _method_name='get_list']">
	<xsl:variable name="_admin"><xsl:if test="_config/admin_mode=1">admin</xsl:if></xsl:variable>
	<div class="{_module_name} {_method_name} {$_admin}">
		<xsl:for-each select="item">
			<div>
				<H3><xsl:value-of select="title"/></H3>
				<p><xsl:value-of select="text"/></p>
			</div>
		</xsl:for-each>
	</div>
	<div>
		<xsl:call-template name="edit_question">
			<xsl:with-param name="button_text">Здать вопрос</xsl:with-param>
		</xsl:call-template>
	</div>
</xsl:template>

<xsl:template match="root/module/item[_module_name='question' and _method_name='get']">
	
</xsl:template>

<xsl:template match="root/module/item[_module_name='question' and _method_name='_admin']">
	
</xsl:template>

<xsl:template match="root/module/item[_module_name='question' and _method_name='edit']" name="edit_question">
	<xsl:param name="button_text">Сохранить</xsl:param>
	<form name="{_module_name} edit">
		<textarea name="text">
			<xsl:value-of select="text"/>
		</textarea>
		<button><xsl:value-of select="$button_text"/></button>
	</form>
</xsl:template>

</xsl:stylesheet>