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
		<input type="hidden" name="id" value="{link_data/id}"/>
		<input type="hidden" name="menu" value="{link_data/menu}"/>
		<div>
			<table class="link_controls">
				<tr>
					<td><b>Позиция:</b></td>
					<td>
						<select name="position" autocomplete="off">
							<xsl:for-each select="position/*">
								<option value="{name()}">
									<xsl:if test="name()=../../link_data/position">
										<xsl:attribute name="selected">1</xsl:attribute>
									</xsl:if>
									<xsl:value-of select = "."/>
								</option>
							</xsl:for-each>
						</select>
					</td>
					<td><b>Порядковый номер:</b></td>
					<td>
						<select name="order" autocompleate="off">
							<xsl:call-template name="order_loop">
								<xsl:with-param name="max">10</xsl:with-param>
								<xsl:with-param name="selected" select="link_data/order"/>
							</xsl:call-template>
						</select>
					</td>
					<td><b>Черновик:</b></td>
					<td>
						<input type="checkbox" name="draft">
							<xsl:if test="link_data/exclude!='0'">
								<xsl:attribute name="checked">1</xsl:attribute>
							</xsl:if>
						</input>
					</td>
				</tr>
			</table>
			<xsl:call-template name="module_link_wizard">
				<xsl:with-param name="name">Что связываем:</xsl:with-param>
				<xsl:with-param name="num">1</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="module_link_wizard">
				<xsl:with-param name="name">С чем связываем:</xsl:with-param>
				<xsl:with-param name="num">2</xsl:with-param>
			</xsl:call-template>
		</div>
		<input type="submit" value="сохранить"/>
	</form>
</xsl:template>

<xsl:template name="order_loop">
	<xsl:param name="var">1</xsl:param>
	<xsl:param name="max"/>
	<xsl:param name="selected"/>
	<option value="{$var}">
		<xsl:if test="$var = $selected">
			<xsl:attribute name="selected">1</xsl:attribute>
		</xsl:if>
		<xsl:value-of select="$var"/>
	</option>
	<xsl:if test = "$var &lt; $max">
		<xsl:call-template name="order_loop">
			<xsl:with-param name="var" select="$var+1"/>
			<xsl:with-param name="max" select="$max"/>
			<xsl:with-param name="selected" select="$selected"/>
		</xsl:call-template>
	</xsl:if>
</xsl:template>

</xsl:stylesheet>