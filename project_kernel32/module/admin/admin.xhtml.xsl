<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="utf-8" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />

<xsl:template match="root/module/item[_module_name='admin' and _method_name='get']">
	<xsl:variable name="admin"><xsl:if test="/root/meta/admin_mode='1'">/admin.php</xsl:if></xsl:variable>
	<ul class="module_list">
		<xsl:for-each select="*/method">
			<li>
				<a href="{$admin}?call={name(..)}.{name(*)}">
					<xsl:value-of select="*/title"/>
				</a>
			</li>
		</xsl:for-each>
	</ul>
	<p>
		<a href="/admin.php?call=admin._admin">Общие настройки</a>
	</p>
</xsl:template>

<xsl:template match="root/module/item[_module_name='admin' and _method_name='']">
	<a href="/admin.php?call=admin.main_page">Главная страница</a>
</xsl:template>

<xsl:template match="root/module/item[_module_name='admin' and _method_name='main_page']">
	<xsl:call-template name="module_link_wizard">
		<xsl:with-param name="name">Что связываем:</xsl:with-param>
	</xsl:call-template>
</xsl:template>

</xsl:stylesheet>