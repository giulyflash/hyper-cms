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

<xsl:template match="items" priority="0.5">
	<xsl:variable name="class"><xsl:choose>
		<xsl:when test="../uncategorized">uncategorized</xsl:when>
		<xsl:otherwise>list</xsl:otherwise>
	</xsl:choose></xsl:variable>
	<!-- <xsl:for-each select="item">{<xsl:value-of select="id"/>}</xsl:for-each> -->
	<!-- <ul class="nested_items_{$class} nested_items {../../_method_name}"> -->
	<xsl:for-each select="item">
		<li class="img">
			<a href="{path}" alt="{title}" title="{title}">
				<div>
					<div class="border">
						<xsl:variable name="thumb_path"><xsl:choose>
							<xsl:when test="../../_method_name='_admin' and thumb2_path and thumb2_path!=''"><xsl:value-of select="thumb2_path"/></xsl:when>
							<xsl:otherwise><xsl:value-of select="thumb_path"/></xsl:otherwise>
						</xsl:choose></xsl:variable>
						<img src="{$thumb_path}" alt="{title}" title="{title}"/>
					</div>
				</div>
				<span class="item_text"><xsl:value-of select="title"/></span>
			</a>
			<xsl:call-template name="controls_item"/>
		</li>
	</xsl:for-each>
	<xsl:if test="not(../uncategorized) and not(item)">
		<xsl:call-template name="_base_obj_not_found"/>
		<!-- 01 -->
	</xsl:if>
</xsl:template>

<xsl:template match="item/item/title" priority="0.5">
	<a href="/?call=gallery&amp;title={../translit_title}">
		<span class="folder_img"/>
		<span><xsl:value-of select="."/></span>
	</a>
	<!-- <xsl:variable name="ul">ul class="nested_items <xsl:value-of select="../../_method_name"/>" <xsl:if test="../is_current">style="display: block"</xsl:if></xsl:variable>
	<xsl:value-of select="concat(../../lt,$ul,../../gt)" disable-output-escaping="yes"/> -->
	<ul class="nested_items {../../_method_name} category_content">
		<xsl:choose>
			<xsl:when test="../is_current">
				<!-- <xsl:attribute name="style">display: block</xsl:attribute> -->
				<xsl:choose>
					<xsl:when test="not(../items/item)">
						<xsl:call-template name="_base_obj_not_found"/>
						<!-- 02 -->
					</xsl:when>
					<xsl:otherwise>
						<xsl:for-each select="../items">
							<xsl:apply-templates select="."/>
						</xsl:for-each>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:when>
			<xsl:when test="not(../items or ../../item[uncategorized]) and ../../_show='current'">
				<xsl:call-template name="_base_obj_not_found"/>
				<!-- 04 -->
			</xsl:when>
		</xsl:choose>
	</ul>
</xsl:template>

<!--
<xsl:template match="text()[../../../_module_name='gallery' and not(../items) and not(../../item/items) and ../../../_show='current']" priority="0.3">
	<xsl:call-template name="_base_obj_not_found">
		<xsl:with-param name="text" select="/root/language/gallery/get_category/no_obj_msg"/>
	</xsl:call-template>
</xsl:template>

<xsl:template match="title[../../../_module_name='gallery' and not(../uncategorized) and not(items)]" priority="0.3">
	<xsl:call-template name="_base_obj_not_found">
		<xsl:with-param name="text" select="/root/language/gallery/get_category/no_obj_msg"/>
	</xsl:call-template>
</xsl:template>

<xsl:template match="_module_name[.='gallery' and not(item)]" priority="0.3">
	<xsl:call-template name="_base_obj_not_found">
		<xsl:with-param name="text" select="/root/language/gallery/get_category/no_obj_msg"/>
	</xsl:call-template>
</xsl:template> 
 -->

<xsl:template name="_base_obj_not_found">
	<!-- <xsl:param name = "text">Object not found</xsl:param>
	<div class="empty">
		<xsl:value-of select="$text"/>
	</div> -->
	<li class="empty">EMPTY</li>
</xsl:template>

<xsl:template name="nested_tree">
	<xsl:param name="class">nested_tree</xsl:param>
	<div class="{_module_name} {$class}">
		<xsl:call-template name="nested_tree_core"/>
	</div>
</xsl:template>

<xsl:template name="nested_tree_core">
	<ul class="nested_items {_method_name}">
		<xsl:if test="item[not(uncategorized)]">
			<xsl:for-each select="item[not(uncategorized)]">
				<xsl:call-template name="nested_tag_before">
					<xsl:with-param name="ul_class" select="concat('nested_items ',_method_name)"/>
				</xsl:call-template>
				<xsl:apply-templates select="title"/>
				<xsl:call-template name="controls_category"/>
				<xsl:if test="position() = last()">
					<xsl:call-template name="nested_close_tag">
						<xsl:with-param name="num" select="depth"/>
					</xsl:call-template>
				</xsl:if>
			</xsl:for-each>
		</xsl:if>
		<xsl:for-each select="item[uncategorized]">
			<xsl:apply-templates select="items"/>
		</xsl:for-each>
		<xsl:if test="not(item)">
			<xsl:call-template name="_base_obj_not_found"/>
			<!-- 03 -->
		</xsl:if>
		<xsl:call-template name="nested_tag_after"/>
	</ul>
</xsl:template>

<xsl:template name="nested_tag_before">
	<xsl:param name="ul_class"/>
	<xsl:variable name="prev_pos"><xsl:value-of select="position()-1"/></xsl:variable>
	<xsl:variable name="prev_depth"><xsl:value-of select="../item[position()=$prev_pos]/depth"/></xsl:variable>
	<xsl:variable name="li">li<xsl:if test="active=1"> class="active"</xsl:if></xsl:variable>
	<xsl:choose>
		<xsl:when test="depth &gt; $prev_depth">
			<xsl:variable name="ul">ul<xsl:if test="$ul_class!=''"> class="<xsl:value-of select="$ul_class"/>"</xsl:if></xsl:variable>
			<xsl:value-of select="concat(../lt,$ul,../gt)" disable-output-escaping="yes"/>
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

<!-- <xsl:template match="root/module/item[_method_name='get']" priority="0">
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
</xsl:template> -->

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
		<form class="controls" method="post" action="admin.php?call={../_module_name}.move_category">
			<input type="hidden" value="{id}" name="id"/>
			<a href="/admin.php?call={../_module_name}.edit_category&amp;id={id}" class="edit">редактировать</a>
			<a href="/admin.php?call={../_module_name}.remove_category&amp;id={id}" class="remove">удалить</a>
			<span>Вставить:</span> 
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
			<span>Категория:</span>
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
			<span>Вставить после:</span>
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