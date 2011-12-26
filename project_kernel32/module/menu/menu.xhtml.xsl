<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="utf-8" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />

<xsl:template match="root/module/item[_module_name='menu' and _method_name='get']">
	<xsl:call-template name="nested_items_category"/>
</xsl:template>

<xsl:template match="root/module/item[_module_name='menu' and _method_name='get_category']">
	<xsl:choose>
		<xsl:when test="_argument/menu_id!=''">
			<xsl:call-template name="nested_items_category">
				<xsl:with-param name="param">&amp;menu_id=<xsl:value-of select="_argument/menu_id"/></xsl:with-param>
			</xsl:call-template>
		</xsl:when>
		<xsl:otherwise>
			<xsl:call-template name="nested_items_category"/>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template match="root/module/item[_module_name='menu' and _method_name='edit_category']">
	<xsl:if test ="data"> 
	 	<script type="text/javascript">
			document.module_data=<xsl:value-of select="data" disable-output-escaping="yes"/>
			<xsl:if test="link_data">
				document.link_data=<xsl:value-of select="link_data" disable-output-escaping="yes"/>;
			</xsl:if>
		</script>
	</xsl:if>
	<form class="menu menu_editor link_form" method="post" action="admin.php?call=menu.save_category">
		<xsl:if test="id">
			<input type="hidden" value="{id}" name="id"/>
			<input type="hidden" value="{menu_id}" name="menu_id"/>
		</xsl:if>
		<xsl:if test="_argument/menu_id">
			<input type="hidden" value="{_argument/menu_id}" name="menu_id"/>
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
		<xsl:when test="_argument/menu_id!=''">
			<xsl:choose>
				<xsl:when test="_argument/menu_id!=''">
					<xsl:call-template name="nested_items_category">
						<xsl:with-param name="param">&amp;menu_id=<xsl:value-of select="_argument/menu_id"/></xsl:with-param>
					</xsl:call-template>
				</xsl:when>
				<xsl:otherwise>
					<xsl:call-template name="nested_items_category"/>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:when>
		<xsl:otherwise>
			<xsl:if test="item">
				<div class="nested_items {_module_name} {_method_name} {_config/category_type}">
					<ul>
						<xsl:for-each select="item">
							<li class="no_controls">
								<a href="/admin.php?call={../_module_name}.{../_method_name}&amp;menu_id={id}">
									<xsl:value-of select="title"/>
								</a>
							</li>
						</xsl:for-each>
					</ul>
					<!-- <xsl:call-template name="controls_add"/> -->
					<p class="controls_add">
						<a href="/admin.php?call=menu.edit">
							<xsl:value-of select="/root/language/menu/_admin/add_menu"/>
						</a>
					</p>
				</div>
			</xsl:if>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

</xsl:stylesheet>