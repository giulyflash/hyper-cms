<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="utf-8" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />

<xsl:template match="root/module/item[_module_name='gallery' and _method_name='edit']">
	<div class="{_module_name} file_edit">
		<xsl:if test="id">
			<a href="{path}">
				<div>
					<div>
						<img src="{thumb_path}"/>
					</div>
				</div>
			</a>
		</xsl:if>
		<form method="post" action="admin.php?call={_module_name}.save" enctype="multipart/form-data">
			<xsl:variable name="category_id"><xsl:choose>
				<xsl:when test="id"><xsl:value-of select="category_id"/></xsl:when>
				<xsl:otherwise><xsl:value-of select="_argument/category_id"/></xsl:otherwise>
			</xsl:choose></xsl:variable>
			<input type="hidden" name="id" value="{id}"/>
			<table>
				<tr>
					<td>
						Файл<xsl:if test="not(id)">(ы)</xsl:if>:
					</td>
					<td>
						<input type="file" name="file[]" accept="image/*">
							<xsl:if test="not(id)">
								<xsl:attribute name="multiple">multiple</xsl:attribute>
							</xsl:if>
						</input>
					</td>
				</tr>
				<tr>
					<td>
						Заголовок:
					</td>
					<td class="header">
						<input type="text" name="title" value="{title}"/>
					</td>
				</tr>
				<tr>
					<td>
						Категория:
					</td>
					<td>
						<select name="category_id">
							<xsl:call-template name="_get_category_list">
								<xsl:with-param name="category_id" select="$category_id"/>
							</xsl:call-template>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<input type="submit" value="Сохранить"/>
					</td>
				</tr>
			</table>
		</form>
	</div>
</xsl:template>

<xsl:template match="items/item">
	<div class="item">
		<a href="{path}" alt="{title}" title="{title}">
			<div class="border">
				<img src="{thumb_path}"/>
			</div>
			<div><xsl:value-of select="title"/></div>
		</a>
	</div>
</xsl:template>

</xsl:stylesheet> 