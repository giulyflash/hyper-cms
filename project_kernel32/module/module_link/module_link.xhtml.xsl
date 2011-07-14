<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="utf-8" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />

<xsl:template match="root/module/item[_module_name='module_link' and _method_name='_admin']">
	<xsl:param name="method_separator"> - </xsl:param>
	<xsl:param name="param_separator"> = </xsl:param>
	<table class="module_link_list">
		<thead>
			<th>Позиция</th>
			<th>Что связываем</th>
			<th>С чем связываем</th>
			<th>Удалить</th>
		</thead>
		<xsl:for-each select="item">
			<xsl:variable name="href" select="concat('admin.php?call=module_link.edit&amp;id=',id)"/>
			<tr>
				<td>
					<span><xsl:value-of select="position_title"/></span>
				</td>
				<td>
					<a href="{$href}">
						<xsl:value-of select="concat(module_title,$method_separator,method_title)"/>
						<xsl:if test = "params/item[type='param']">
							:<br/>
						</xsl:if>
					</a>
					<xsl:for-each select="params/item[type='param']">
						<xsl:value-of select="concat(title,$param_separator)"/>
						<span><xsl:value-of select="value"/></span>
						<xsl:if test="position()!=last()">
							,<br/>
						</xsl:if>
					</xsl:for-each>
				</td>
				<td>
					<a href="{$href}">
						<xsl:value-of select="concat(center_module_title,$method_separator,center_method_title)"/>
						<xsl:if test = "params/item[type='condition']">
							:<br/>
						</xsl:if>
					</a>
					<xsl:for-each select="params/item[type='condition']">
						<xsl:value-of select="concat(title,$param_separator)"/>
						<span><xsl:value-of select="value"/></span>
						<xsl:if test="position()!=last()">
							,<br/>
						</xsl:if>
					</xsl:for-each>
				</td>
				<td class="remove">
					<a href="admin.php?call=module_link.remove&amp;id={id}">X</a>
				</td>
			</tr>
		</xsl:for-each>
	</table>
	<p>
		<a href="/admin.php?call=module_link.edit">Новая связь</a>
	</p>
</xsl:template>

<xsl:template match="root/module/item[_module_name='module_link' and _method_name='edit']">
	<xsl:if test ="data"> 
		<script type="text/javascript">
			document.module_data=<xsl:value-of select="data"/>
			<xsl:if test="link">
				document.link_data=<xsl:value-of select="link"/>
			</xsl:if>
		</script>
	</xsl:if>
	<form class="link_form" action="/admin.php?call=module_link.save" method="post">
		<input type="hidden" value="{link/id}"/>
		<div>
			<p>
				<b>Позиция:</b>&#160;&#160;<select name="position">
				<xsl:for-each select="position/*">
					<option value="{name()}">
						<xsl:if test="name()=position">
							<xsl:attribute name="selected">1</xsl:attribute>
						</xsl:if>
						<xsl:value-of select = "."/>
					</option>
				</xsl:for-each>
				</select>
			</p>
			<xsl:call-template name="module_link_wisard">
				<xsl:with-param name="name">Что связываем:</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="module_link_wisard">
				<xsl:with-param name="name">С чем связываем:</xsl:with-param>
				<xsl:with-param name="num">2</xsl:with-param>
			</xsl:call-template>
		</div>
		<input type="submit" value="сохранить"/>
	</form>
</xsl:template>

<xsl:template name="module_link_wisard">
	<xsl:param name="name"/>
	<xsl:param name="num">1</xsl:param>
	<table>
		<xsl:if test="$name">
			<tr class="thead"><th colspan="2"><xsl:value-of select="$name"/></th><th></th></tr>
		</xsl:if>
		<tr class="module">
			<td>
				Модуль:
			</td>
			<td>
				<select class="module_select" autocomplete = "off" name="link[{$num}][module]">
					<option value=""></option>
					<xsl:for-each select="module_list/*">
						<option value="{name()}" >
							<xsl:value-of select="."/>
						</option>
					</xsl:for-each>
				</select>
			</td>
		</tr>
		<tr class="method">
			<td>
				Действие:
			</td>
			<td>
				<select class="method_select" autocomplete = "off" name="link[{$num}][method]">
					<option value=""></option>
				</select>
			</td>
		</tr>
		<tr class="params">
			<td>
				Параментры:
			</td>
			<td>
				<table class="param_box">
					<tr>
						<td>
							<select class="param_select" autocomplete = "off"><option value=""></option></select>
						</td>
						<td>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</xsl:template>

</xsl:stylesheet>