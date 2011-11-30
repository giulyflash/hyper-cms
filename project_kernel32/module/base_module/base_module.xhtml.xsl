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
				<xsl:if test="/root/meta/admin_mode=1">
					<xsl:call-template name="controls_add"/>
				</xsl:if>
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
	<xsl:variable name="method" select="/root/module/item[_module_name=$module_name]/_method_name"/>
	<xsl:for-each select="item">
		<li>
			<xsl:if test="active=1">
				<xsl:attribute name="class">active</xsl:attribute>
			</xsl:if>
			<div class="item_cont">
				<a class="_ajax" href="/{$admin_mode}?call={$module_name}.{$method}&amp;title={translit_title}" alt="{title}" title="{title}">
					<span class="folder_icon"></span>
					<span class="text"><xsl:value-of select="title"/></span>
				</a>
				<xsl:call-template name="controls_category">
					<xsl:with-param name="module_name" select="$module_name"/>
					<xsl:with-param name="edit_module_name" select="$module_name"/>
				</xsl:call-template>
			</div>
			<xsl:call-template name="nested_items_category_base">
				<xsl:with-param name="module_name" select="$module_name"/>
			</xsl:call-template>
		</li>
	</xsl:for-each>
	<xsl:if test="items">
		<li class="items">
			<xsl:for-each select="items/item">
				<div class="item">
					<xsl:variable name="path"><xsl:choose>
						<xsl:when test="path"><xsl:value-of select="path"/></xsl:when>
						<xsl:otherwise>/<xsl:value-of select="$admin_mode"/>?call=<xsl:value-of select="$module_name"/>.edit&amp;id=<xsl:value-of select="id"/></xsl:otherwise>
					</xsl:choose></xsl:variable>
					<a href="{$path}" alt="{title}" title="{title}">
						<div>
							<div>
								<xsl:if test="not(thumb_path) or thumb_path=''">
									<xsl:attribute name="class">default_thumb</xsl:attribute>
								</xsl:if>
								<xsl:variable name="thumb_path">
									<xsl:choose>
										<xsl:when test="thumb_path and thumb_path!=''"><xsl:value-of select="thumb_path"/></xsl:when>
										<xsl:otherwise><xsl:value-of select="/root/module/item[_module_name=$module_name]/_config/default_thumb"/></xsl:otherwise>
									</xsl:choose>
								</xsl:variable>
								<img src="{$thumb_path}" alt="{title}" title="{title}"/>
							</div>
						</div>
						<xsl:variable name="bracket_l"><xsl:choose>
							<xsl:when test="draft=1">(</xsl:when>
							<xsl:when test="draft=2">[</xsl:when>
						</xsl:choose></xsl:variable>
						<xsl:variable name="bracket_r"><xsl:choose>
							<xsl:when test="draft=1">)</xsl:when>
							<xsl:when test="draft=2">]</xsl:when>
						</xsl:choose></xsl:variable>
						<span class="text"><xsl:value-of select="concat($bracket_l,title,$bracket_r)"/></span>
					</a>
					<xsl:call-template name="controls_item">
						<xsl:with-param name="module_name" select="$module_name"/>
						<xsl:with-param name="edit_module_name" select="$module_name"/>
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
	<form class="{_module_name}" method="post" action="admin.php?call={_module_name}.save_category">
		<xsl:if test="id">
			<input type="hidden" value="{id}" name="id"/>
		</xsl:if>
		<xsl:if test="_argument/insert_place">
			<input type="hidden" value="{_argument/insert_place}" name="insert_place"/>
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
			<tr>
				<td colspan="2">
					<input type="submit" value="Сохранить"/>
				</td>
			</tr>
		</table>
	</form>
</xsl:template>

<xsl:template name="controls_category">
	<xsl:param name="module_name" select="../_module_name"/>
	<xsl:param name="edit_module_name" select="../_module_name"/>
	<xsl:param name="edit_category_method">edit_category</xsl:param>
	<xsl:if test="/root/meta/admin_mode=1">
		<form class="controls" method="post" action="admin.php?call={$module_name}.move_category">
			<a href="/admin.php?call={$module_name}.{$edit_category_method}&amp;id={id}" class="edit always">редактировать</a>
			<a href="/admin.php?call={$module_name}.remove_category&amp;id={id}" class="remove always">удалить</a>
			<span>Вставить:</span> 
			<select name="insert_type" autocomplete='off'>
				<option value="0" selected="1">&#8212;</option>
				<option value="before">перед</option>
				<option value="inside">в</option>
			</select>
			<select class="insert_place" name="insert_place" autocomplete='off'>
				<xsl:for-each select="..">
					<xsl:call-template name="_get_category_list"/>
				</xsl:for-each>
				<option value="last">&#8212;</option>
			</select>
			<xsl:call-template name="controls_add">
				<xsl:with-param name="module_name" select="$module_name"/>
				<xsl:with-param name="edit_module_name" select="$edit_module_name"/>
			</xsl:call-template>
			<input type="hidden" value="{id}" name="id"/>
			<!-- <a href="/admin.php?call={$module_name}.edit_category&amp;insert_place={id}" class="subitem">добавить подпункт</a>
			<xsl:if test="/root/module/item[_module_name=$module_name]/_config/has_item">
				<a href="/admin.php?call={$edit_module_name}.edit&amp;category_id={id}" class="subitem">добавить объект</a>
			</xsl:if> -->
		</form>
	</xsl:if>
</xsl:template>

<xsl:template name="controls_add">
	<xsl:param name="module_name" select="_module_name"/>
	<xsl:param name="edit_module_name" select="$module_name"/>
	<xsl:param name="method_name" select="/root/module/item[_module_name=$module_name]/_method_name"/>
	<xsl:variable name="href_tail"><xsl:if test="id">&amp;insert_place=<xsl:value-of select="id"/></xsl:if></xsl:variable>
	<xsl:variable name="href_tail2"><xsl:if test="id">&amp;category_id=<xsl:value-of select="id"/></xsl:if></xsl:variable>
	<a href="/admin.php?call={$edit_module_name}.edit_category{$href_tail}" class="subitem">
		<xsl:value-of select="/root/language/*[name()=$module_name]/*[name()=$method_name]/add_category"/>
	</a>
	<xsl:if test="not(id)">
		<br/>
	</xsl:if>
	<xsl:if test="/root/module/item[_module_name=$module_name]/_config/has_item=1">
		<a href="/admin.php?call={$edit_module_name}.edit{$href_tail2}" class="subitem">
			<xsl:value-of select="/root/language/*[name()=$module_name]/*[name()=$method_name]/add_item"/>
		</a>
	</xsl:if>
</xsl:template>

<xsl:template name="controls_item">
	<xsl:param name="module_name" select="../../_module_name"/>
	<xsl:param name="edit_module_name" select="../../_module_name"/>
	<xsl:if test="/root/meta/admin_mode=1">
		<form class="controls" method="post" action="admin.php?call={$edit_module_name}.move_item">
			<xsl:variable name="current_position" select="position()"/>
			<input type="hidden" name="insert_after" value="{../item[position()=-1+$current_position]/id}"/>
			<input type="hidden" value="{id}" name="id"/>
			<a href="/admin.php?call={$edit_module_name}.edit&amp;id={id}" class="edit">редактировать</a>
			<a href="/admin.php?call={$edit_module_name}.remove&amp;id={id}" class="remove">удалить</a>
			<xsl:variable name="category_id" select="category_id"/>
			<xsl:variable name="current_id" select="id"/>
			<span>Категория:</span>
			<select class="insert_category" name="insert_category" autocomplete='off'>
				<xsl:for-each select="../..">
					<xsl:call-template name="_get_category_list">
						<xsl:with-param name="id" select="$category_id"/>
					</xsl:call-template>
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
							<xsl:value-of select="title"/>
						</option>
					</xsl:if>
				</xsl:for-each>
			</select>
		</form>
	</xsl:if>
</xsl:template>

<xsl:template name="_get_category_list">
	<xsl:param name="id">0</xsl:param>
	<xsl:choose>
		<xsl:when test="_module_name">
			<option value="">&#8212;</option>
			<xsl:for-each select="_category_list/item">
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

<xsl:template name="_base_create_date">
	<xsl:param name="create_date" select="create_date"/>
	<!-- 2011-10-21 12:01:45 -->
	<xsl:variable name="year" select="substring-before($create_date,'-')"/>
	<xsl:variable name="temp1" select="substring-after($create_date,'-')"/>
	<xsl:variable name="month" select="substring-before($temp1,'-')"/>
	<xsl:variable name="temp2" select="substring-after($temp1,'-')"/>
	<xsl:variable name="day" select="substring-before($temp2,' ')"/>
	<xsl:variable name="temp3" select="substring-after($temp2,' ')"/>
	<xsl:variable name="hour" select="substring-before($temp3,':')"/>
	<xsl:variable name="temp4" select="substring-after($temp3,':')"/>
	<xsl:variable name="minute" select="substring-before($temp4,':')"/>
	<xsl:variable name="second" select="substring-after($temp4,':')"/>
	<!-- <p><xsl:value-of select="$create_date"/></p>
	<p><xsl:value-of select="$year"/></p>
	<p><xsl:value-of select="$month"/></p>
	<p><xsl:value-of select="$day"/></p>
	<p><xsl:value-of select="$hour"/></p>
	<p><xsl:value-of select="$minute"/></p>
	<p><xsl:value-of select="$second"/></p> -->
	<select name="create_date[y]" autocomplete="off">
		<xsl:call-template name="_base_date_loop">
			<xsl:with-param name="var" select="2010"/>
			<xsl:with-param name="max" select="2040"/>
			<xsl:with-param name="selected" select="$year"/>
		</xsl:call-template>
	</select>
	<select  name="create_date[m]" autocomplete="off">
		<xsl:call-template name="_base_date_loop">
			<xsl:with-param name="var" select="1"/>
			<xsl:with-param name="max" select="12"/>
			<xsl:with-param name="selected" select="$month"/>
			<xsl:with-param name="month" select="1"/>
		</xsl:call-template>
	</select>
	<select  name="create_date[d]" autocomplete="off">
		<xsl:call-template name="_base_date_loop">
			<xsl:with-param name="var" select="1"/>
			<xsl:with-param name="max" select="31"/>
			<xsl:with-param name="selected" select="$day"/>
		</xsl:call-template>
	</select>
	&#160;&#160;
	<select name="create_date[h]" autocomplete="off">
		<xsl:call-template name="_base_date_loop">
			<xsl:with-param name="var" select="0"/>
			<xsl:with-param name="max" select="23"/>
			<xsl:with-param name="selected" select="$hour"/>
		</xsl:call-template>
	</select>
	:
	<select name="create_date[i]" autocomplete="off">
		<xsl:call-template name="_base_date_loop">
			<xsl:with-param name="var" select="0"/>
			<xsl:with-param name="max" select="59"/>
			<xsl:with-param name="selected" select="$minute"/>
		</xsl:call-template>
	</select>
	:
	<select name="create_date[s]" autocomplete="off">
		<xsl:call-template name="_base_date_loop">
			<xsl:with-param name="var" select="0"/>
			<xsl:with-param name="max" select="59"/>
			<xsl:with-param name="selected" select="$second"/>
		</xsl:call-template>
	</select>
</xsl:template>

<xsl:template name="_base_date_loop">
	<xsl:param name="var"/>
	<xsl:param name="max"/>
	<xsl:param name="selected"/>
	<xsl:param name="month">0</xsl:param>
	<option value="{$var}">
		<xsl:if test="$var = $selected">
			<xsl:attribute name="selected">1</xsl:attribute>
		</xsl:if>
		<xsl:choose>
			<xsl:when test="$month='1'">
				<xsl:choose>
					<xsl:when test="$var='1'">Январь</xsl:when>
					<xsl:when test="$var='2'">Февраль</xsl:when>
					<xsl:when test="$var='3'">Март</xsl:when>
					<xsl:when test="$var='4'">Апрель</xsl:when>
					<xsl:when test="$var='5'">Май</xsl:when>
					<xsl:when test="$var='6'">Июнь</xsl:when>
					<xsl:when test="$var='7'">Июль</xsl:when>
					<xsl:when test="$var='8'">Август</xsl:when>
					<xsl:when test="$var='9'">Сентябрь</xsl:when>
					<xsl:when test="$var='10'">Октябрь</xsl:when>
					<xsl:when test="$var='11'">Ноябрь</xsl:when>
					<xsl:when test="$var='12'">Декабрь</xsl:when>
				</xsl:choose>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$var"/>
			</xsl:otherwise>
		</xsl:choose>
	</option>
	<xsl:if test = "$var &lt; $max">
		<xsl:call-template name="_base_date_loop">
			<xsl:with-param name="var" select="$var+1"/>
			<xsl:with-param name="max" select="$max"/>
			<xsl:with-param name="selected" select="$selected"/>
			<xsl:with-param name="month" select="$month"/>
		</xsl:call-template>
	</xsl:if>
</xsl:template>

</xsl:stylesheet>