<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="utf-8" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />

<xsl:template match="root/module/item[_method_name='get_category']" priority="0.5">
	<xsl:choose>
		<xsl:when test="/root/meta/content_type='json_html'">
			<xsl:call-template name="nested_tree_core"/>
		</xsl:when>
		<xsl:otherwise>
			<xsl:call-template name="nested_tree"/>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template match="item/item/items" priority="0.5">
	<xsl:variable name="class"><xsl:choose>
		<xsl:when test="../uncategorized">uncategorized</xsl:when>
		<xsl:otherwise>list</xsl:otherwise>
	</xsl:choose></xsl:variable>
	<ul class="nested_items_{$class} nested_items">
		<xsl:for-each select="item">
			<li class="img">
				<a href="{path}" alt="{title}" title="{title}">
					<div>
						<div class="border">
							<img src="{thumb_path}" alt="{title}" title="{title}"/>
						</div>
					</div>
					<xsl:value-of select="title"/>
				</a>
			</li>
		</xsl:for-each>
	</ul>
	<xsl:if test="not(../uncategorized) and not(items)">
		<xsl:apply-templates/>
		<!-- 01 -->
	</xsl:if>
</xsl:template>

<xsl:template match="item/item/title" priority="0.5">
	<a href="/?call=gallery&amp;title={../translit_title}">
		<span class="folder_img"/>
		<span><xsl:value-of select="."/></span>
	</a>
	<div class="category_content">
	</div>
	<xsl:if test="not(../items or ../../item[uncategorized]) and ../../_show='current'">
		<xsl:apply-templates/>
		<!-- 02 -->
	</xsl:if>
</xsl:template>

<xsl:template match="title[not(../uncategorized) and not(items)]" priority="0.2">
	<xsl:call-template name="_base_obj_not_found"/>
</xsl:template>

<xsl:template match="text()[not(../items or ../../../item[uncategorized]/items) and ../../../_show='current']" priority="0.2">
	<xsl:call-template name="_base_obj_not_found"/>
</xsl:template>

<xsl:template match="*[_module_name and not(item) and not(items)]" priority="0.2">
	<xsl:call-template name="_base_obj_not_found"/>
</xsl:template>

<xsl:template name="_base_obj_not_found">
	<xsl:param name = "text">Object not found</xsl:param>
	<div class="empty">
		<xsl:value-of select="$text"/>
	</div>
</xsl:template>

<xsl:template name="nested_tree">
	<xsl:param name="class">nested_tree</xsl:param>
	<div class="{_module_name} {$class}">
		<xsl:call-template name="nested_tree_core"/>
	</div>
</xsl:template>

<xsl:template name="nested_tree_core">
	<xsl:if test="item[not(uncategorized)]">
		<ul>
			<xsl:for-each select="item[not(uncategorized)]">
				<xsl:call-template name="nested_tag_before"/>
				<xsl:apply-templates/>
				<xsl:call-template name="controls_category"/>
				<xsl:if test="position() = last()">
					<xsl:call-template name="nested_close_tag">
						<xsl:with-param name="num" select="depth"/>
					</xsl:call-template>
				</xsl:if>
			</xsl:for-each>
			<xsl:call-template name="nested_tag_after"/>
		</ul>
	</xsl:if>
	<xsl:for-each select="item[uncategorized]">
		<xsl:apply-templates/>
	</xsl:for-each>
	<xsl:if test="not(item)">
		<ul><li>
			<xsl:apply-templates/>
		</li></ul>
		<!-- 03 -->
	</xsl:if>
</xsl:template>

<xsl:template name="nested_tag_before">
	<xsl:variable name="prev_pos"><xsl:value-of select="position()-1"/></xsl:variable>
	<xsl:variable name="prev_depth"><xsl:value-of select="../item[position()=$prev_pos]/depth"/></xsl:variable>
	<xsl:variable name="li">li<xsl:if test="active=1"> class="active"</xsl:if></xsl:variable>
	<xsl:choose>
		<xsl:when test="depth &gt; $prev_depth">
			<xsl:value-of select="concat(../lt,'ul',../gt)" disable-output-escaping="yes"/>
			<xsl:value-of select="concat(../lt,$li,../gt)" disable-output-escaping="yes"/>
		</xsl:when>
		<xsl:when test="depth &lt; $prev_depth">
			<xsl:call-template name="nested_close_tag">
				<xsl:with-param name="num" select="$prev_depth - depth"/>
			</xsl:call-template>
			<xsl:value-of select="concat(../lt,'/li',../gt)" disable-output-escaping="yes"/>
			<xsl:value-of select="concat(../lt,$li,../gt)" disable-output-escaping="yes"/>
		</xsl:when>
		<xsl:when test="position()=1">
			<xsl:value-of select="concat(../lt,$li,../gt)" disable-output-escaping="yes"/>
		</xsl:when>
		<xsl:otherwise>
			<xsl:value-of select="concat(../lt,'/li',../gt)" disable-output-escaping="yes"/>
			<xsl:value-of select="concat(../lt,$li,../gt)" disable-output-escaping="yes"/>
		</xsl:otherwise> 
	</xsl:choose>
</xsl:template>

<xsl:template match="*" priority="-2">
</xsl:template>

<xsl:template name="nested_tag_after">
	<xsl:if test="item">
		<xsl:value-of select="concat(lt,'/li',gt)" disable-output-escaping="yes"/>
	</xsl:if>
	<xsl:if test="_config/close_nested_folder='1'">
		<script type="text/javascript">
			document.close_nested_folder = 1;
		</script>
	</xsl:if>
</xsl:template>

<xsl:template name="nested_close_tag">
	<xsl:param name="num">1</xsl:param>
	<xsl:if test="$num &gt; 0">
		<xsl:value-of select="concat(../lt,'/ul',../gt)" disable-output-escaping="yes"/>
		<xsl:value-of select="concat(../lt,'/li',../gt)" disable-output-escaping="yes"/>
		<xsl:call-template name="nested_close_tag">
			<xsl:with-param name="num" select="$num+(-1)"/>
		</xsl:call-template>
	</xsl:if>
</xsl:template>

<xsl:template match="root/module/item[_method_name='get']" priority="0">
	<div id="_module_name">
		<ul>
			<xsl:for-each select="item">
				<xsl:call-template name="nested_tag_before"/>
				<a href="{link}">
					<xsl:value-of select="title"/>
				</a>
			</xsl:for-each>
			<xsl:call-template name="nested_tag_after"/>
		</ul>
	</div>
</xsl:template>

<xsl:template match="root/module/item[_method_name='_admin']" priority="0">
	<xsl:call-template name="nested_tree"/>
	<xsl:if test="_config/has_category='1'">
		<p>
			<a href="admin.php?call={_module_name}.edit_category">Новая категория</a>
		</p>
	</xsl:if>
	<xsl:if test="_config/has_item='1'">
		<p>
			<a href="admin.php?call={_module_name}.edit">Новая статья</a>
		</p>
	</xsl:if>
</xsl:template>

<xsl:template name="menu_print_level">
	<xsl:param name = "start" select="0"/>
	<xsl:param name = "count" select="depth"/>
	<xsl:if test="$start &lt; $count">
		&#8212;
		<xsl:call-template name="menu_print_level">
			<xsl:with-param name="start" select="$start+1"/>
			<xsl:with-param name="count" select="$count"/>
		</xsl:call-template>
	</xsl:if> 
</xsl:template>

<xsl:template match="root/module/item[_method_name='edit_category']" priority="0">
	<form class="menu" method="post" action="admin.php?call={_module_name}.save_category">
		<xsl:if test="id">
			<input type="hidden" value="{id}" name="id"/>
		</xsl:if>
		<xsl:if test="argument/insert_place">
			<input type="hidden" value="{argument/insert_place}" name="insert_place"/>
		</xsl:if>
		<table class="edit_item">
			<tr>
				<td class="first">
					Текст:
				</td>
				<td>
					<input type="text" value="{title}" name="title"/>
				</td>
			</tr>
		</table>
		<input type="submit" value="Сохранить"/>
	</form>
</xsl:template>

<xsl:template name="controls_category">
	<xsl:if test="/root/meta/admin_mode=1">
		<div class="item_cont">
			<img class="nested_item_img" src="template/admin/images/folder_opened.png"/>
			<a href="#" class="item_text">
				<xsl:value-of select="title"/>
			</a>
			<form class="controls" method="post" action="admin.php?call={../_module_name}.move_category">
				<input type="hidden" value="{id}" name="id"/>
				<a href="/admin.php?call={../_module_name}.remove_category&amp;id={id}" class="remove">удалить</a>
				<a href="/admin.php?call={../_module_name}.edit_category&amp;id={id}" class="edit">редактировать</a>
				Вставить: 
				<select name="insert_type" autocomplete='off'>
					<option value="0" selected="1">-</option>
					<option value="before">перед</option>
					<option value="inside">в</option>
				</select>
				<select class="insert_place" name="insert_place" autocomplete='off'>
					<option value="0" selected="1">-</option>
					<xsl:variable name="current_id" select="id"/>
					<xsl:for-each select="../item">
						<xsl:if test="id!=$current_id">
							<option value="{id}">
								<xsl:call-template name="menu_print_level"/>
								<xsl:value-of select="title"/>
							</option>
						</xsl:if>
					</xsl:for-each>
					<option value="last">-</option>
				</select>
				<a href="/admin.php?call={../_module_name}.edit_category&amp;insert_place={id}" class="subitem">добавить подпункт</a>
			</form>
		</div>
	</xsl:if>
</xsl:template>

<xsl:template name="controls_item">
	<xsl:param name="edit_module_name" select="../../../_module_name"/>
	<xsl:if test="/root/meta/admin_mode=1">
		<form class="controls" method="post" action="admin.php?call={../../../_module_name}.move_item">
			<xsl:variable name="current_position" select="position()"/>
			<input type="hidden" name="insert_after" value="{../item[position()=-1+$current_position]/id}"/>
			<input type="hidden" value="{id}" name="id"/>
			<a href="/admin.php?call={../../../_module_name}.remove&amp;id={id}" class="remove">удалить</a>
			<a href="/admin.php?call=file.edit&amp;id={id}" class="edit">редактировать</a>
			<xsl:variable name="category_id" select="category_id"/>
			<xsl:variable name="current_id" select="id"/>
			Категория: 
			<select class="insert_category" name="insert_category" autocomplete='off'>
				<option value="">&#8212;</option>
				<xsl:for-each select="../../../item[not(uncategorized)]">
					<option value="{id}">
						<xsl:if test="id=$category_id">
							<xsl:attribute name="selected">selected</xsl:attribute>
						</xsl:if>
						<xsl:call-template name="menu_print_level"/>
						<xsl:value-of select="title"/>
					</option>
				</xsl:for-each>
			</select>
			&#160;&#160;Вставить после:
			<select class="insert_item" name="insert_item" autocomplete='off'>
				<option value="">&#8212;</option>
				<xsl:for-each select="../item">
					<xsl:if test="id!=$current_id">
						<option value="{id}">
							<xsl:variable name="position" select="position()"/>
							<xsl:if test="../item[position()=1+$position]/id=$current_id">
								<xsl:attribute name="selected">selected</xsl:attribute>
							</xsl:if>
							<xsl:value-of select="name"/>
						</option>
					</xsl:if>
				</xsl:for-each>
			</select>
		</form>
	</xsl:if>
</xsl:template>

</xsl:stylesheet>