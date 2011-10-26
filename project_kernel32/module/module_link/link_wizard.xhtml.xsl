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
					<input type="hidden" name="link[{$num}][module]"/>
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
					<input type="hidden" name="link[{$num}][method]"/>
				</td>
			</tr>
			<tr class="params">
				<td>
					Параментры:
				</td>
				<td>
					<table class="param_box">
					</table>
				</td>
			</tr>
		</tbody>
	</table>
</xsl:template>

</xsl:stylesheet>