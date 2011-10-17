<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="utf-8" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />

<xsl:template match="root/module/item[_method_name='get_category']" priority="0.3">
	<xsl:call-template name="nested_items_category"/>
</xsl:template>

<xsl:template name="nested_items_category">
	<xsl:choose>
		<xsl:when test="/root/meta/content_type='json_html'">
			<xsl:call-template name="nested_items_category_base">
				<xsl:with-param name="need_ul">0</xsl:with-param>
			</xsl:call-template>
		</xsl:when>
		<xsl:otherwise>
			<div class="nested_items {_module_name} {_method_name} {_config/category_type}">
				<xsl:call-template name="nested_items_category_base"/>
			</div>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template name="nested_items_category_base">
	<xsl:param name="need_ul">1</xsl:param>
	<xsl:param name="module_name" select="_module_name"/>
	<xsl:choose>
		<xsl:when test="item or items">
			<xsl:choose>
				<xsl:when test="$need_ul=1">
					<ul>
						<xsl:call-template name="nested_items_category_core">
							<xsl:with-param name="module_name" select="$module_name"/>
						</xsl:call-template>
						<xsl:if test="active=1 and not(items)">
							<xsl:call-template name="base_no_items"/>
						</xsl:if>
					</ul>
				</xsl:when>
				<xsl:otherwise>
					<xsl:call-template name="nested_items_category_core">
						<xsl:with-param name="module_name" select="$module_name"/>
					</xsl:call-template>
					<xsl:if test="not(items)">
						<xsl:call-template name="base_no_items"/>
					</xsl:if>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:when>
		<xsl:otherwise>
			<xsl:if test="active=1 or _module_name">
				<xsl:choose>
					<xsl:when test="$need_ul=1">
						<ul>
							<xsl:call-template name="base_no_items"/>
						</ul>
					</xsl:when>
					<xsl:otherwise>
						<xsl:call-template name="base_no_items"/>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:if>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template name="base_no_items">
	<li class="empty">
		<xsl:value-of select="/root/language/*/no_obj_msg"/>
	</li>
</xsl:template>

<xsl:template name="nested_items_category_core">
	<xsl:param name="module_name" select="_module_name"/>
	<xsl:variable name="admin_mode"><xsl:if test="/root/meta/admin_mode=1">admin.php</xsl:if></xsl:variable>
	<xsl:for-each select="item">
		<li>
			<xsl:if test="active=1">
				<xsl:attribute name="class">active</xsl:attribute>
			</xsl:if>
			<a class="_ajax" href="/{$admin_mode}?call={$module_name}.get_category&amp;title={translit_title}" alt="{title}" title="{title}">
				<span class="folder_icon"></span>
				<span class="text"><xsl:value-of select="title"/></span>
			</a>
			<xsl:call-template name="controls_category">
				<xsl:with-param name="module_name" select="$module_name"/>
			</xsl:call-template>
			<xsl:call-template name="nested_items_category_base">
				<xsl:with-param name="module_name" select="$module_name"/>
			</xsl:call-template>
		</li>
	</xsl:for-each>
	<xsl:if test="items">
		<li class="items">
			<xsl:for-each select="items/item">
				<div class="item">
					<a href="{path}" alt="{title}" title="{title}">
						<div>
							<div>
								<img src="{thumb_path}" alt="{title}" title="{title}"/>
							</div>
						</div>
						<span class="text"><xsl:value-of select="title"/></span>
					</a>
					<xsl:call-template name="controls_item">
						<xsl:with-param name="module_name" select="$module_name"/>
					</xsl:call-template>
				</div>
			</xsl:for-each>
			<div style="clear:both"></div>
		</li>
	</xsl:if>
</xsl:template>

<xsl:template match="root/module/item[_method_name='_admin']" priority="0">
	<xsl:call-template name="nested_items_category"/>
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
	<xsl:param name="module_name" select="../_module_name"/>
	<xsl:if test="/root/meta/admin_mode=1">
		<form class="controls" method="post" action="admin.php?call={../_module_name}.move_category">
			<div>
				<input type="hidden" value="{id}" name="id"/>
				<a href="/admin.php?call={../_module_name}.edit_category&amp;id={id}" class="edit">редактировать</a>
				<a href="/admin.php?call={../_module_name}.remove_category&amp;id={id}" class="remove">удалить</a>
				<span>Вставить:</span> 
				<select name="insert_type" autocomplete='off'>
					<option value="0" selected="1">&#8212;</option>
					<option value="before">перед</option>
					<option value="inside">в</option>
				</select>
				<select class="insert_place" name="insert_place" autocomplete='off'>
					<option value="0" selected="1">&#8212;</option>
					<xsl:call-template name="_get_category_list"/>
					<option value="last">&#8212;</option>
				</select>
				<a href="/admin.php?call={../_module_name}.edit_category&amp;insert_place={id}" class="subitem">добавить подпункт</a>
			</div>
		</form>
	</xsl:if>
</xsl:template>

<xsl:template name="controls_item">
	<xsl:param name="module_name" select="../_module_name"/>
	<xsl:param name="edit_module_name"><xsl:choose>
		<xsl:when test="$module_name='gallery'">file</xsl:when>
		<xsl:otherwise><xsl:value-of select="$module_name"/></xsl:otherwise>
	</xsl:choose></xsl:param>
	<xsl:if test="/root/meta/admin_mode=1">
		<form class="controls" method="post" action="admin.php?call={$module_name}.move_item">
			<div>
				<xsl:variable name="current_position" select="position()"/>
				<input type="hidden" name="insert_after" value="{../item[position()=-1+$current_position]/id}"/>
				<input type="hidden" value="{id}" name="id"/>
				<a href="/admin.php?call={$edit_module_name}.edit&amp;id={id}" class="edit">редактировать</a>
				<a href="/admin.php?call={$module_name}.remove&amp;id={id}" class="remove">удалить</a>
				<xsl:variable name="category_id" select="category_id"/>
				<xsl:variable name="current_id" select="id"/>
				<span>Категория:</span>
				<select class="insert_category" name="insert_category" autocomplete='off'>
					<option value="">&#8212;</option>
					<xsl:choose>
						<xsl:when test="../../_module_name">
							<xsl:for-each select="../../item[position()=1]">
								<xsl:call-template name="_get_category_list">
									<xsl:with-param name="id" select="$category_id"/>
								</xsl:call-template>
							</xsl:for-each>
						</xsl:when>
						<xsl:otherwise>
							<xsl:for-each select="../..">
								<xsl:call-template name="_get_category_list">
									<xsl:with-param name="id" select="$category_id"/>
								</xsl:call-template>
							</xsl:for-each>
						</xsl:otherwise>
					</xsl:choose>
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
								<xsl:value-of select="title"/>
							</option>
						</xsl:if>
					</xsl:for-each>
				</select>
			</div>
		</form>
	</xsl:if>
</xsl:template>

<xsl:template name="_get_category_list">
	<xsl:param name="id">0</xsl:param>
	<xsl:choose>
		<xsl:when test="../_module_name">
			<xsl:for-each select="../_category_list/item">
				<option value="{id}">
					<xsl:if test="id=$id">
						<xsl:attribute name="selected">1</xsl:attribute>
					</xsl:if>
					<xsl:call-template name="menu_print_level"/>
					<xsl:value-of select="title"/>
				</option>
			</xsl:for-each>
		</xsl:when>
		<xsl:otherwise>
			<xsl:for-each select="..">
				<xsl:call-template name="_get_category_list">
					<xsl:with-param name="id" select="$id"/>
				</xsl:call-template>
			</xsl:for-each>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

</xsl:stylesheet>