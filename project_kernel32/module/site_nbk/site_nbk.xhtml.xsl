<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="utf-8" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />

<xsl:template match="root/module/item[_module_name='site_nbk' and (_method_name='get' or _method_name='search')]">
	<xsl:call-template name="debt_nav"/>
	<table class="nbk_admin">
		<xsl:variable name="href_order">/?call=site_nbk<xsl:if test="argument/count!=''">&amp;count=<xsl:value-of select="argument/count"/></xsl:if><xsl:if test="argument/search!=''">&amp;search=<xsl:value-of select="argument/search"/></xsl:if><xsl:if test="argument/filter!=''">&amp;filter=<xsl:value-of select="argument/filter"/></xsl:if>&amp;order=</xsl:variable>
		<xsl:variable name="sort_base_text">сортировка по полю </xsl:variable>
		<tr>
			<xsl:for-each select="_field/*">
				<th>
					<a href="{$href_order}{order}" alt="{$sort_base_text}{title}" title="{$sort_base_text}{title}">
						<xsl:value-of select="title"/>
					</a>
				</th>
			</xsl:for-each>
			<th>
				Удалить
			</th>
		</tr>
		<xsl:variable name="href_base">/?call=site_nbk.edit&amp;id=</xsl:variable>
		<xsl:variable name="edit_popup">Редактировать должника № </xsl:variable>
		<xsl:for-each select="item">
			<tr>
				<td><center><a href="{$href_base}{id}" alt="{$edit_popup}{id}"  title="{$edit_popup}{id}"><xsl:value-of select="num"/></a></center></td>
				<td><a href="{$href_base}{id}" alt="{$edit_popup}{id}"  title="{$edit_popup}{id}"><xsl:value-of select="account"/></a></td>
				<td><a href="{$href_base}{id}" alt="{$edit_popup}{id}"  title="{$edit_popup}{id}"><xsl:value-of select="street"/></a></td>
				<td><a href="{$href_base}{id}" alt="{$edit_popup}{id}"  title="{$edit_popup}{id}"><xsl:value-of select="house"/></a></td>
				<td><a href="{$href_base}{id}" alt="{$edit_popup}{id}"  title="{$edit_popup}{id}"><xsl:value-of select="flat"/></a></td>
				<td>
					<xsl:if test="privatizated=1">
						<a href="{$href_base}{id}" alt="{$edit_popup}{id}"  title="{$edit_popup}{id}">	
							<input type="checkbox" disabled="1" checked="1"/>
						</a>
					</xsl:if>
				</td>
				<td><a href="{$href_base}{id}" alt="{$edit_popup}{id}"  title="{$edit_popup}{id}"><xsl:value-of select="owner"/></a></td>
				<td>
					<a class="href" href="/?call=site_nbk.edit_account_comment&amp;id={id}"><xsl:value-of select="comment"/>
						<xsl:choose>
							<xsl:when test="account_comment!=''">редактировать</xsl:when>
							<xsl:otherwise>добавить</xsl:otherwise>
						</xsl:choose>
					</a>
				</td>
				<td><a href="{$href_base}{id}" alt="{$edit_popup}{id}"  title="{$edit_popup}{id}"><xsl:value-of select="debt"/></a></td>
				<td><a href="{$href_base}{id}" alt="{$edit_popup}{id}"  title="{$edit_popup}{id}"><xsl:value-of select="balance"/></a></td>
				<td><a href="{$href_base}{id}" alt="{$edit_popup}{id}"  title="{$edit_popup}{id}"><xsl:value-of select="charges"/></a></td>
				<td><a href="{$href_base}{id}" alt="{$edit_popup}{id}"  title="{$edit_popup}{id}"><xsl:value-of select="control_summ"/></a></td>
				<td><a href="{$href_base}{id}" alt="{$edit_popup}{id}"  title="{$edit_popup}{id}">
					<xsl:choose>
						<xsl:when test="debt_date_formatted='00.0000'">-</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="debt_date_formatted"/>
						</xsl:otherwise>
					</xsl:choose>
				</a></td>
				<td><a href="{$href_base}{id}" alt="{$edit_popup}{id}"  title="{$edit_popup}{id}">
					<xsl:choose>
						<xsl:when test="pay_date_formatted='00.0000'">-</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="pay_date_formatted"/>
						</xsl:otherwise>
					</xsl:choose>
				</a></td>
				<td>
					<a class="href" href="/?call=site_nbk.edit_comment&amp;id={id}">
						<xsl:choose>
							<xsl:when test="comment!=''">редактировать</xsl:when>
							<xsl:otherwise>добавить</xsl:otherwise>
						</xsl:choose>
					</a>
				</td>
				<td>
					<center><a class="remove" href="/?call=site_nbk.remove&amp;id={id}"><xsl:value-of select="comment"/>Х</a></center>
				</td>
			</tr>
		</xsl:for-each>
	</table>
	<xsl:call-template name="debt_nav"/>
	<p>
		<a href="/?call=site_nbk.edit">добавить должника</a>
	</p>
	<p>
		<a href="/?call=site_nbk.generate&amp;is_default=1">загрузить файл со списком должников</a>
	</p>
</xsl:template>

<xsl:template name="debt_nav">
	<div class="page_nav">
		<xsl:variable name="href_nav">/?call=site_nbk<xsl:if test="argument/order!=''">&amp;order=<xsl:value-of select="argument/order"/></xsl:if><xsl:if test="argument/count!=''">&amp;count=<xsl:value-of select="argument/count"/></xsl:if><xsl:if test="argument/search!=''">&amp;search=<xsl:value-of select="argument/search"/></xsl:if><xsl:if test="argument/filter!=''">&amp;filter=<xsl:value-of select="argument/filter"/></xsl:if></xsl:variable>
		<xsl:call-template name="shownavigation">
			<xsl:with-param name="obj_count" select="__num_rows"/>
			<xsl:with-param name="page_size" select="__page_size"/>
			<xsl:with-param name="page" select="__page"/>
			<xsl:with-param name="url"><xsl:value-of select="$href_nav"/></xsl:with-param>
		</xsl:call-template>
		<span class="page_select">
			страница:<select name="page">
				<xsl:value-of select="_page_select_html" disable-output-escaping="yes"/>
			</select>
		</span>
		<form method="post" action="/?call=site_nbk.get" enctype="multipart/form-data">
			<input type="hidden" value="{argument/order}" name="order"/>
			<input type="hidden" value="{argument/filter}" name="filter"/>
			<span class="page_size">
				строк на странице:<input type="text" size="1" value="{__page_size}" name="count"/>
			</span>
			<span class="search_field">
				быстрый поиск:<input type="text" size="15" value="{argument/search}" name="search"/>
			</span>
			<input type="submit" value="Ok"/>
		</form>
	</div>
</xsl:template>

<xsl:template match="root/module/item[_module_name='site_nbk' and (_method_name='generate')]">
	<form method="post" action="admin.php?call=site_nbk.generate" enctype="multipart/form-data">
		<table class="nbk_admin_file">
			<tr>
				<td>Путь к файлу со списком:</td>
				<td>
					<input type="file" name="path"/>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<center>
						<input type="submit" value="Сгенерировать"/>
					</center>
				</td>
			</tr>
		</table>
	</form>
</xsl:template>

</xsl:stylesheet>