<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="utf-8" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />

<xsl:template name="module_link_wizard">
	<xsl:param name="name"/>
	<xsl:param name="num">0</xsl:param>
	<table>
		<tbody>
			<xsl:if test="$name">
				<tr class="thead">
					<th colspan="2">
						<xsl:value-of select="$name"/>
					</th>
				</tr>
			</xsl:if>
			<tr class="module">
				<td>
					Тип объекта:
				</td>
				<td>
					<select class="module_select" autocomplete = "off">
						<option value=""></option>
					</select>
					<input type="hidden" name="link[{$num}][module_name]"/>
				</td>
			</tr>
			<tr class="method">
				<td>
					<p>
						Объект:
					</p>
					<span>или </span><span>действие:</span>
				</td>
				<td>
					<p>
						<select class="object_select" autocomplete = "off">
							<option value=""></option>
						</select>
					</p>
					<select class="method_select" autocomplete = "off">
						<option value=""></option>
					</select>
					<input type="hidden" name="link[{$num}][method_name]"/>
				</td>
			</tr>
			<tr class="params">
				<td>
					Параметры:
				</td>
				<td>
					<table class="param_box">
					</table>
				</td>
			</tr>
		</tbody>
	</table>
</xsl:template>

<xsl:template name="link_editor_href">
	<xsl:param name="module_name" select="_module_name"/>
	<xsl:param name="method_name" select="_method_name"/>
	<xsl:param name="param"/>
	<xsl:param name="param_name">translit_title</xsl:param>
	<xsl:param name="param_title">title</xsl:param>
	<xsl:param name="title">добавить связь</xsl:param>
	<xsl:variable name="open_bracket">{</xsl:variable>
	<xsl:variable name="close_bracket">}</xsl:variable>
	<xsl:variable name="param_last"><xsl:choose>
		<xsl:when test="$param_name!=''">{"name":"<xsl:value-of select="$param_title"/>","value":"<xsl:value-of select="*[name()=$param_name]"/>"}</xsl:when>
		<xsl:otherwise><xsl:value-of select="$param"/></xsl:otherwise>
	</xsl:choose></xsl:variable>
	<a href='/admin.php?call=module_link.edit&amp;link={$open_bracket}"module_name":"{$module_name}","method_name":"{$method_name}",param:[{$param_last}]{$close_bracket}'><xsl:value-of select="$title"/></a>
</xsl:template>

</xsl:stylesheet>