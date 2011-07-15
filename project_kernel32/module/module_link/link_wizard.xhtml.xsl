<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="utf-8" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />

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