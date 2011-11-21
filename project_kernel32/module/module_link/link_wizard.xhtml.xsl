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
	<xsl:param name="link"/>
	<xsl:param name="module_name" select="_module_name"/>
	<xsl:param name="method_name" select="_method_name"/>
	<xsl:param name="param"/>
	<xsl:param name="param_name">translit_title</xsl:param>
	<xsl:param name="param_title">title</xsl:param>
	<xsl:param name="title">редактор связей</xsl:param>
	<xsl:param name="link_method">_admin</xsl:param>
	<xsl:variable name="q">"</xsl:variable>
	<xsl:variable name="href"><xsl:choose>
		<xsl:when test="$link!=''"><xsl:value-of select="$link"/></xsl:when>
		<xsl:otherwise><xsl:variable name="param_last"><xsl:choose>
				<xsl:when test="$param_name!=''"><xsl:value-of select="concat('{',$q,'name',$q,':',$q,$param_title,$q,',',$q,'value',$q,':',$q,*[name()=$param_name],$q,'}')" disable-output-escaping="yes"/></xsl:when>
				<xsl:otherwise><xsl:value-of select="$param"/></xsl:otherwise></xsl:choose></xsl:variable><xsl:value-of select="concat('{',$q,'module_name',$q,':',$q,$module_name,$q,',',$q,'method_name',$q,':',$q,$method_name,$q,',',$q,'param',$q,':[',$param_last,']','}')" disable-output-escaping="yes"/></xsl:otherwise>
	</xsl:choose></xsl:variable>
	<xsl:text disable-output-escaping="yes">&lt;a href='/admin.php?call=module_link.</xsl:text>
	<xsl:value-of select="$link_method"/>
	<xsl:text disable-output-escaping="yes">&amp;link=</xsl:text>
	<xsl:value-of select="$href" disable-output-escaping="yes"/>
	<xsl:text disable-output-escaping="yes">' &gt;</xsl:text>
	<xsl:value-of select="$title"/>
	<xsl:text disable-output-escaping="yes">&lt;/a&gt;</xsl:text>
</xsl:template>

</xsl:stylesheet>