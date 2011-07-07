<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="utf-8" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />

<xsl:template match="root/module/item[_module_name='module_link' and _method_name='_admin']">
	<table class="module_link_list">
		<thead>
			<th>Что связываем</th>
			<th>Условия</th>
			<th>С чем связываем</th>
			<th>Удалить</th>
		</thead>
		<xsl:for-each select="item">
			<xsl:variable name="href" select="concat('admin.php?call=module_link.edit&amp;id=',link_id)"/>
			<tr>
				<td>
					<a href="{$href}">
						<xsl:value-of select="concat(module_name,'.',method_name)"/>
					</a>
				</td>
				<td>
					<a href="{$href}">
						<xsl:for-each select="params/item">
							<xsl:value-of select="concat(param_name,'=',value)"/><xsl:if test="position()!=last()"><br/></xsl:if>
						</xsl:for-each>
					</a>
				</td>
				<td>
					<a href="{$href}">
						<xsl:value-of select="concat(center_module,'.',center_method)"/>
					</a>
				</td>
				<td class="remove">
					<a href="admin.php?call=module_link.remove&amp;id={link_id}">X</a>
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
		</script>
	</xsl:if>
	<from class="link_form">
		<div>
			<table>
				<tr class="module">
					<td>
						Модуль:
					</td>
					<td>
						<select id="module_select" autocomplete = "off">
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
						Метод:
					</td>
					<td>
						<select id="method_select" autocomplete = "off">
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
		</div>
		<input type="submit" value="сохранить"/>
	</from>
</xsl:template>

</xsl:stylesheet>