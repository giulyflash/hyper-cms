<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="utf-8" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />

<xsl:template name="nested_tag_before">
	<xsl:variable name="prev_pos"><xsl:value-of select="position()-1"/></xsl:variable>
	<xsl:variable name="prev_depth"><xsl:value-of select="../item[position()=$prev_pos]/depth"/></xsl:variable>
	<xsl:choose>
		<xsl:when test="depth &gt; $prev_depth">
			<xsl:value-of select="concat(../lt,'ul',../gt)" disable-output-escaping="yes"/>
			<xsl:value-of select="concat(../lt,'li',../gt)" disable-output-escaping="yes"/>
			<xsl:if test="items">
				<xsl:apply-templates match="items"/>
			</xsl:if>
		</xsl:when>
		<xsl:when test="depth &lt; $prev_depth">
			<xsl:call-template name="nested_close_tag">
				<xsl:with-param name="num" select="$prev_depth - depth"/>
			</xsl:call-template>
			<xsl:value-of select="concat(../lt,'/li',../gt)" disable-output-escaping="yes"/>
			<xsl:value-of select="concat(../lt,'li',../gt)" disable-output-escaping="yes"/>
			<xsl:if test="items">
				<xsl:apply-templates match="items"/>
			</xsl:if>
		</xsl:when>
		<xsl:when test="position()=1">
			<xsl:value-of select="concat(../lt,'li',../gt)" disable-output-escaping="yes"/>
			<xsl:if test="items">
				<xsl:apply-templates match="items"/>
			</xsl:if>
		</xsl:when>
		<xsl:otherwise>
			<xsl:value-of select="concat(../lt,'/li',../gt)" disable-output-escaping="yes"/>
			<xsl:value-of select="concat(../lt,'li',../gt)" disable-output-escaping="yes"/>
			<xsl:if test="items">
				<xsl:apply-templates match="items"/>
			</xsl:if>
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
		<!-- <xsl:apply-templates select="items[_module_name=../../module_name]/item"/> -->
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
	<div class="{_module_name} nested_tree">
		<ul>
			<xsl:for-each select="item">
				<xsl:call-template name="nested_tag_before"/>
				<div class="item_cont">
					<img class="nested_item_img" src="template/admin/images/folder_opened.png"/>
					<a href="#">
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
				<xsl:if test="position() = last()">
					<xsl:call-template name="nested_close_tag">
						<xsl:with-param name="num" select="depth"/>
					</xsl:call-template>
				</xsl:if>
			</xsl:for-each>
			<xsl:call-template name="nested_tag_after"/>
		</ul>
		<xsl:if test="items">
			<xsl:apply-templates match="items"/>
		</xsl:if>
	</div>
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
		-
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

</xsl:stylesheet>