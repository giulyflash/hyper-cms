<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="utf-8" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />

<xsl:template match="root/module/item[_module_name='menu' and _method_name='get']">
	<div id="menu" class="menu {_argument/type}">
		<xsl:call-template name="menu_items"/>
	</div>
</xsl:template>

<xsl:template name="menu_items">
	<xsl:if test="item">
		<ul>
			<xsl:for-each select="item">
				<li>
					<xsl:if test="active=1">
						<xsl:attribute name="class">active</xsl:attribute>
					</xsl:if>
					<a href="{link}">
						<xsl:value-of select="title"/>
					</a>
					<xsl:call-template name="menu_items"/>
				</li>
			</xsl:for-each>
		</ul>
	</xsl:if>
</xsl:template>

<xsl:template match="root/module/item[_module_name='menu' and _method_name='edit']">
	<div class="menu nested_tree">
		<xsl:if test="_argument/id='' or id">
			<form method="post" action="admin.php?call=menu.save&amp;id={id}">
				Заголовок: <input value="{title}" type="text" name="title"/>
				<input type = "submit" value="ок"/>
				<xsl:if test = "id">
					<a class="remove_menu" href="admin.php?call=menu.remove&amp;id={id}">Удалить меню</a>
				</xsl:if>
			</form>
			<br/>
			<ul>
				<xsl:for-each select="item">
					<xsl:call-template name="nested_tag_before"/>
					<div class="item_cont">
						<img class="nested_item_img" src="template/admin/images/folder_opened.png"/>
						<a href="#">
							<xsl:value-of select="title"/>
						</a>
						<form class="controls" method="post" action="admin.php?call=menu.move_item&amp;menu_id={menu_id}&amp;id={id}">
							<a href="/admin.php?call=menu.remove_item&amp;menu_id={menu_id}&amp;id={id}" class="remove">удалить</a>
							<a href="/admin.php?call=menu.edit_item&amp;menu_id={menu_id}&amp;id={id}" class="edit">редактировать</a>
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
							<a href="/admin.php?call=menu.edit_item&amp;menu_id={menu_id}&amp;insert_place={id}" class="subitem">добавить подпункт</a>
						</form>
					</div>
				</xsl:for-each>
				<xsl:call-template name="nested_tag_after"/>
			</ul>
		</xsl:if>
	</div>
	<xsl:if test="id">
		<a href="admin.php?call=menu.edit_item&amp;menu_id={id}">Новый пункт меню</a>
	</xsl:if>
</xsl:template>

<xsl:template match="root/module/item[_module_name='menu' and _method_name='edit_item']">
	<xsl:if test ="data"> 
	<!--
	$this->result['data'] = $module_link->_result['data'];
		if(isseet($module_link->_result['link_data']))
			$this->result['link_data'] = $module_link->_result['link_data'];
		if(isseet($module_link->_result['link']))
			$this->result['link0'] = $module_link->_result['link']; 
	 -->
	 	<script type="text/javascript">
			document.module_data=<xsl:value-of select="data"/>
			<xsl:if test="link">
				document.link_data=<xsl:value-of select="link"/>
			</xsl:if>
		</script>
		<script type="text/javascript">
			document.module_data=<xsl:value-of select="data"/>
			<xsl:if test="link0">
				document.link_data=<xsl:value-of select="link0"/>
			</xsl:if>
		</script>
	</xsl:if>
	<form class="menu menu_editor link_form" method="post" action="admin.php?call=menu.save_item&amp;menu_id={_argument/menu_id}">
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
				<td>
					Ссылка:
				</td>
				<td>
					<input id="radio_href_article" type="radio" value="wizard" name="input_type" checked="1"/>
					<label for="radio_href_article">Редактор ссылок</label>
					<br/>
					<input id="radio_href_input" type="radio" value="text" name="input_type"/>
					<label for="radio_href_input">Ввести ссылку</label>
					<br/>
				</td>
			</tr>
		</table>
		<div class="input_article">
			<xsl:call-template name="module_link_wizard"/>
		</div>
		<div class="input_text">
			<input id="href_input" type="text" value="{link}" name="link_text"/>
		</div>
		<input type="submit" value="Сохранить"/>
	</form>
</xsl:template>

<!--  -->

<xsl:template match="root/module/item[_module_name='menu' and _method_name='_admin']">
	<xsl:choose>
		<xsl:when test="/root/meta/content_type='json_html'">
			<xsl:call-template name="menu_base">
				<xsl:with-param name="need_ul">0</xsl:with-param>
			</xsl:call-template>
		</xsl:when>
		<xsl:when test="_argument/menu_id!=''">
			<div class="nested_items {_module_name} {_method_name} {_config/category_type}">
				<xsl:call-template name="menu_base"/>
			</div>
		</xsl:when>
		<xsl:otherwise>
			<xsl:if test="item">
				<div class="nested_items {_module_name} {_method_name} {_config/category_type}">
					<ul>
						<xsl:for-each select="item">
							<li>
								<a href="/admin.php?call={../_module_name}.{../_method_name}&amp;menu_id={id}">
									<xsl:value-of select="title"/>
								</a>
							</li>
						</xsl:for-each>
					</ul>
				</div>
			</xsl:if>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template name="menu_base">
	<xsl:param name="need_ul">1</xsl:param>
	<xsl:param name="module_name" select="_module_name"/>
	<xsl:choose>
		<xsl:when test="item or items">
			<xsl:choose>
				<xsl:when test="$need_ul=1">
					<ul>
						<xsl:call-template name="menu_core">
							<xsl:with-param name="module_name" select="$module_name"/>
						</xsl:call-template>
						<xsl:if test="active=1 and not(items)">
							<xsl:call-template name="base_no_items"/>
						</xsl:if>
					</ul>
				</xsl:when>
				<xsl:otherwise>
					<xsl:call-template name="menu_core">
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

<xsl:template name="menu_core">
	<xsl:param name="module_name" select="_module_name"/>
	<xsl:variable name="admin_mode"><xsl:if test="/root/meta/admin_mode=1">admin.php</xsl:if></xsl:variable>
	<xsl:for-each select="item">
		<li>
			<xsl:if test="active=1">
				<xsl:attribute name="class">active</xsl:attribute>
			</xsl:if>
			<div class="item_cont">
				<a class="_ajax" href="/{$admin_mode}?call={$module_name}.get_category&amp;menu_id={/root/module/item[_module_name=$module_name]/_argument/menu_id}&amp;title={translit_title}" alt="{title}" title="{title}">
					<span class="folder_icon"></span>
					<span class="text"><xsl:value-of select="title"/></span>
				</a>
				<xsl:call-template name="controls_category">
					<xsl:with-param name="module_name" select="$module_name"/>
					<xsl:with-param name="edit_module_name" select="$module_name"/>
				</xsl:call-template>
			</div>
			<xsl:call-template name="menu_core">
				<xsl:with-param name="module_name" select="$module_name"/>
			</xsl:call-template>
		</li>
	</xsl:for-each>
</xsl:template>

</xsl:stylesheet>