<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="utf-8" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />

<xsl:template match="root/module/item[_module_name='question' and (_method_name='get_list' or _method_name='_admin')]">
	<xsl:variable name="_admin"><xsl:if test="_config/admin_mode=1">admin</xsl:if></xsl:variable>
	<div class="{_module_name} {_method_name} {$_admin}">
		<xsl:for-each select="item">
			<xsl:call-template name="query_item"/>
		</xsl:for-each>
	</div>
	<div class="{_module_name}">
		<xsl:call-template name="edit_question">
			<xsl:with-param name="button_text">Здать вопрос</xsl:with-param>
		</xsl:call-template>
	</div>
</xsl:template>

<xsl:template match="root/module/item[_module_name='question' and _method_name='get']">
	
</xsl:template>

<xsl:template match="root/module/item[_module_name='question' and _method_name='_admin']">
	
</xsl:template>

<xsl:template name="query_item">
	<xsl:param name="hide_answer"/>
	<div class="item">
		<xsl:if test="parent!=id">
			<xsl:attribute name="class">item tab</xsl:attribute>
		</xsl:if>
		<div><xsl:value-of select="date"/><span><xsl:value-of select="username"/>:</span>
			<xsl:if test="/root/session/user_info">
				<a class="remove" href="/{admin_mode}?call=question.remove&amp;id={id}">удалить</a>
			</xsl:if>
		</div>
		<!-- <H3><xsl:value-of select="title"/></H3>-->
		<p><xsl:value-of select="text"/></p>
		<xsl:if test="parent=id and $hide_answer=''">
			<xsl:variable name="admin_mode"><xsl:if test="/root/meta/admin_mode=1">admin.php</xsl:if></xsl:variable>
			<a href="/{admin_mode}?call=question.edit&amp;parent={parent}">ответить</a>
		</xsl:if>
	</div>
</xsl:template>

<xsl:template match="root/module/item[_module_name='question' and _method_name='edit']" name="edit_question">
	<xsl:param name="button_text">Сохранить</xsl:param>
	<xsl:if test = "parent">
		<div class="question">
			<xsl:for-each select="parent">
				<xsl:call-template name="query_item"><xsl:with-param name="hide_answer">1</xsl:with-param></xsl:call-template>
			</xsl:for-each>
		</div>
	</xsl:if>
	<xsl:variable name="admin_mode"><xsl:if test="/root/meta/admin_mode=1">admin.php</xsl:if></xsl:variable>
	<form class="{_module_name} edit" method="post" action="/{$admin_mode}?call={_module_name}.save">
		<table>
			<tr>
				<td>Имя:</td>
				<td>
					<xsl:variable name="username"><xsl:choose>
						<xsl:when test="username"><xsl:value-of select="username"/></xsl:when>
						<xsl:otherwise><xsl:value-of select="/root/session/user_info/login"/></xsl:otherwise>
					</xsl:choose></xsl:variable>
					<input type="text" value="{$username}" name="username"/>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<textarea name="value">
						<xsl:value-of select="text"/>
					</textarea>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<button><xsl:value-of select="$button_text"/></button>
				</td>
			</tr>
		</table>
		<xsl:if test="parent">
			<input type="hidden" name="parent" value="{parent/parent}"/>
		</xsl:if>
	</form>
</xsl:template>

</xsl:stylesheet>