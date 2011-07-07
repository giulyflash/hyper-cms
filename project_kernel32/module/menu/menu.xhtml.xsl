<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="utf-8" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />

<xsl:template match="root/module/item[_module_name='menu' and _method_name='get']">
	<div id="menu" class="menu">
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

<xsl:template match="root/module/item[_module_name='menu' and _method_name='_admin']">
	<ul>
		<xsl:for-each select="item">
			<li>
				<a href="admin.php?call=menu.edit&amp;id={id}">
					<xsl:choose>
						<xsl:when test="title and title!=''">
							<xsl:value-of select="title"/>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="id"/>
						</xsl:otherwise>
					</xsl:choose>
				</a>
			</li>			
		</xsl:for-each>
	</ul>
	<a href="admin.php?call=menu.edit">Новое меню</a>
</xsl:template>

<xsl:template match="root/module/item[_module_name='menu' and _method_name='edit']">
	<div class="menu nested_tree">
		<xsl:choose>
			<xsl:when test="argument/id and not(id)">
				Меню не найдено
			</xsl:when>
			<xsl:otherwise>
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
			</xsl:otherwise>
		</xsl:choose>
	</div>
	<xsl:if test="id">
		<a href="admin.php?call=menu.edit_item&amp;menu_id={id}">Новый пункт меню</a>
	</xsl:if>
</xsl:template>

<xsl:template match="root/module/item[_module_name='menu' and (_method_name='edit_item' or _method_name='save_item')]">
	<form class="menu nested_tree" method="post" action="admin.php?call=menu.save_item&amp;menu_id={argument/menu_id}">
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
			<tr>
				<td>
					Ссылка:
				</td>
				<td>
					<input id="radio_href_input" type="radio" value="input" name="input_type" checked="1"/>
					<label for="radio_href_input">Ввести ссылку</label>
					<br/>
					<input id="radio_href_article" type="radio" value="article" name="input_type"/>
					<label for="radio_href_article">Ссылка на статью</label>
					<br/>
				</td>
			</tr>
		</table>
		<div class="input_text">
			<input id="href_input" type="text" value="{link}" name="link"/>
		</div>
		<div class="input_article">
			<select name="link_article" autocomplete="off">
				<xsl:for-each select="article/item">
					<option value="{translit_title}">
						<xsl:if test="translit_title=../../link">
							<xsl:attribute name="selected">1</xsl:attribute>
						</xsl:if>
						<xsl:value-of select="title"/>
					</option>
				</xsl:for-each>
			</select>
		</div>
		<input type="submit" value="Сохранить"/>
	</form>
</xsl:template>

</xsl:stylesheet>