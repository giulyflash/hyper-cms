<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="utf-8" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />

<xsl:template match="root/module/item[_module_name='gallery' and (_method_name='get_category')]">
	<xsl:choose>
		<xsl:when test="/root/meta/content_type='json_html'">
			<xsl:call-template name="_gallery"/>
		</xsl:when>
		<xsl:otherwise>
			<div class="gallery">
				<xsl:call-template name="_gallery"/>
			</div>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template name="_gallery">
	<ul>
		<xsl:for-each select="item">
			<li>
				<a href="/?call=gallery&amp;title={translit_title}">
					<img src="module/base_module/img/category_down.png"/>
					<span><xsl:value-of select="title"/></span>
				</a>
				<div class="category_content"></div>
			</li>
		</xsl:for-each>
		<xsl:if test="not(items/item) and (argument/title!='' or not(item))">
			<span class="empty">Изображений не найдено.</span>
		</xsl:if>
	</ul>
	<xsl:if test="items/item">
		<xsl:call-template name="_gallery_items"/>
	</xsl:if>
</xsl:template>

<xsl:template name="_gallery_items">
	<xsl:for-each select="items/item">
		<div class="img">
			<a href="{path}" alt="{name}" title="{name}">
				<div>
					<div class="border">
						<img src="{thumb_path}" alt="{name}" title="{name}"/>
					</div>
				</div>
				<xsl:value-of select="name"/>
			</a>
		</div>
	</xsl:for-each>
</xsl:template>

<!-- items -->

<xsl:template match="item[_module_name='gallery' and _method_name='_admin']/item/items">
	<xsl:call-template name="gallery_item"/>
</xsl:template>

<xsl:template match="item[_module_name='gallery' and _method_name='_admin']/items">
	<xsl:call-template name="gallery_item">
		<xsl:with-param name="class">uncategorized</xsl:with-param>
	</xsl:call-template>
</xsl:template>

<xsl:template name="gallery_item">
	<xsl:param name="class">list</xsl:param>
	<ul class="nested_items_{$class} nested_items">
		<xsl:for-each select="item">
			<li>
				<a href="?call=gallery.edit&amp;id={id}">
					<div class="img_cont"><div><img src="{thumb2_path}"/></div></div>
					<xsl:value-of select="name"/>
				</a>
				<form class="controls" method="post" action="admin.php?call={../../../_module_name}.move_item">
					<input type="hidden" value="{id}" name="id"/>
					<a href="/admin.php?call={../../../_module_name}.remove&amp;id={id}" class="remove">удалить</a>
					<a href="/admin.php?call=file.edit&amp;id={id}" class="edit">редактировать</a>
					Категория: 
					<select class="insert_place" name="insert_place" autocomplete='off'>
						<option value="0" selected="1">-</option>
						<xsl:variable name="current_id" select="category_id"/>
						<xsl:choose>
							<xsl:when test="$class='uncategorized'">
								<xsl:for-each select="../../item">
									<xsl:if test="id!=$current_id">
										<option value="{id}">
											<xsl:call-template name="menu_print_level"/>
											<xsl:value-of select="title"/>
										</option>
									</xsl:if>
								</xsl:for-each>
							</xsl:when>
							<xsl:otherwise>
								<xsl:for-each select="../../../item">
									<option value="{id}">
										<xsl:if test="id=$current_id">
											<xsl:attribute name="selected">selected</xsl:attribute>
										</xsl:if>
										<xsl:call-template name="menu_print_level"/>
										<xsl:value-of select="title"/>
									</option>
								</xsl:for-each>
							</xsl:otherwise>
						</xsl:choose>
					</select>
				</form>
			</li>
		</xsl:for-each>
	</ul>
</xsl:template>

</xsl:stylesheet>