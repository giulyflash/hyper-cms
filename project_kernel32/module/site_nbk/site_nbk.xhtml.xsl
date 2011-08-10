<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="utf-8" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />

<xsl:template match="root/module/item[_module_name='site_nbk' and (_method_name='get' or _method_name='search')]">
	<xsl:variable name="href_order"><xsl:if test="argument/order and argument/order!='num' and argument/order!=''">&amp;order=<xsl:value-of select="argument/order"/></xsl:if></xsl:variable>
	<xsl:variable name="href_page"><xsl:if test="argument/page and argument/page!=1 and argument/page!=''">&amp;page=<xsl:value-of select="argument/page"/></xsl:if></xsl:variable>
	<xsl:variable name="href_count"><xsl:if test="argument/count and argument/count!=20 and argument/count!=''">&amp;count=<xsl:value-of select="argument/count"/></xsl:if></xsl:variable>
	<xsl:variable name="href_search"><xsl:if test="argument/search!=''">&amp;search=<xsl:value-of select="argument/search"/></xsl:if></xsl:variable>
	<xsl:variable name="href_filter"><xsl:if test="argument/filter and argument/filter!=''">&amp;filter=<xsl:value-of select="argument/filter"/></xsl:if></xsl:variable>
	<xsl:call-template name="debt_nav">
		<xsl:with-param name="href_order" select="$href_order"/>
		<xsl:with-param name="href_page" select="$href_page"/>
		<xsl:with-param name="href_count" select="$href_count"/>
		<xsl:with-param name="href_search" select="$href_search"/>
		<xsl:with-param name="href_filter" select="$href_filter"/>
	</xsl:call-template>
	<xsl:choose>
		<xsl:when test="item">
			<table class="nbk_admin">
				<xsl:variable name="sort_base_text">сортировка по полю </xsl:variable>
				<tr>
					<xsl:for-each select="_field/*">
						<th>
							<xsl:variable name="desc_name"><xsl:if test="desc='desc'"> наоборот</xsl:if></xsl:variable>
							<xsl:variable name="anchor">order_<xsl:value-of select="name()"/></xsl:variable>
							<a href="/?call={../../_module_name}{$href_count}{$href_filter}{$href_search}{$href_page}&amp;order={order}#{$anchor}" alt='{$sort_base_text}"{title}"{$desc_name}' title='{$sort_base_text}"{title}"{$desc_name}' name="{$anchor}">
								<xsl:value-of select="title"/>
								<xsl:choose>
									 <xsl:when test="desc='desc'">&#160;▴</xsl:when>
									 <xsl:when test="desc='asc'">&#160;▾</xsl:when>
								</xsl:choose>
							</a>
						</th>
					</xsl:for-each>
					<th>
						Удалить
					</th>
				</tr>
				<xsl:variable name="href_base">/?call=<xsl:value-of select="_module_name"/>.edit&amp;id=</xsl:variable>
				<xsl:variable name="edit_popup">Редактировать должника № </xsl:variable>
				<xsl:for-each select="item">
					<tr>
						<!-- ▴▾ -->
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
							<a class="href" href="/?call={_module_name}.edit_account_comment&amp;id={id}"><xsl:value-of select="comment"/>
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
							<a class="href" href="/?call={_module_name}.edit_comment&amp;id={id}">
								<xsl:choose>
									<xsl:when test="comment!=''">редактировать</xsl:when>
									<xsl:otherwise>добавить</xsl:otherwise>
								</xsl:choose>
							</a>
						</td>
						<td>
							<center><a class="remove" href="/?call={../_module_name}.remove&amp;id={id}"><xsl:value-of select="comment"/>Х</a></center>
						</td>
					</tr>
				</xsl:for-each>
			</table>
			<xsl:call-template name="debt_nav">
				<xsl:with-param name="href_order" select="$href_order"/>
				<xsl:with-param name="href_page" select="$href_page"/>
				<xsl:with-param name="href_count" select="$href_count"/>
				<xsl:with-param name="href_search" select="$href_search"/>
				<xsl:with-param name="href_filter" select="$href_filter"/>
			</xsl:call-template>
		</xsl:when>
		<xsl:otherwise>
			<p class="empty">Записей не найдено</p>
		</xsl:otherwise>
	</xsl:choose>
	<p>
		<a href="/?call={_module_name}.edit">добавить должника</a>
	</p>
	<p>
		<a href="/?call={_module_name}.generate&amp;is_default=1">загрузить файл со списком должников</a>
	</p>
</xsl:template>

<xsl:template name="debt_nav">
	<xsl:param name="href_order"/>
	<xsl:param name="href_page"/>
	<xsl:param name="href_count"/>
	<xsl:param name="href_search"/>
	<xsl:param name="href_filter"/>
	<xsl:variable name="nav_href" select="concat('/?call=',_module_name,$href_count,$href_filter,$href_order,$href_search)"/>
	<div class="page_nav">
		<xsl:call-template name="shownavigation">
			<xsl:with-param name="obj_count" select="__num_rows"/>
			<xsl:with-param name="page_size" select="__page_size"/>
			<xsl:with-param name="page" select="__page"/>
			<xsl:with-param name="url" select="$nav_href"/>
		</xsl:call-template>
		<xsl:if test="_page_select_html">
			<span class="page_select">
				страница:<select name="page">
					<xsl:value-of select="_page_select_html" disable-output-escaping="yes"/>
				</select>
			</span>
		</xsl:if>
		<!-- /?call={_module_name}{$href_count}{$href_page}{$href_order}{$href_filter}{$href_search} -->
		<form method="post" action="/?call={_module_name}{$href_order}{$href_filter}" enctype="multipart/form-data">
			<a href="/?call={_module_name}.filter{$href_count}{$href_order}{$href_filter}">фильтры</a> | <a href="/?call={_module_name}{$href_count}{$href_order}">сбросить</a>
			<span class="page_size">
				строк на странице:<input type="text" size="1" value="{__page_size}" name="count"/>
			</span>
			<span class="default_sort">
				<a href="/?call={_module_name}{$href_count}{$href_filter}{$href_search}">сбросить сортировку</a>
			</span>
			<span class="search_field">
				быстрый поиск:<input type="text" size="15" value="{argument/search}" name="search"/>
			</span>
			<input type="submit" value="Ok"/>
		</form>
	</div>
</xsl:template>

<xsl:template match="root/module/item[_module_name='site_nbk' and (_method_name='generate')]">
	<form method="post" action="/?call={_module_name}.generate" enctype="multipart/form-data">
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

<xsl:template match="root/module/item[_module_name='site_nbk' and (_method_name='filter' or _method_name='edit')]">
	<xsl:variable name="action">/?call=<xsl:value-of select="_module_name"/><xsl:if test="_method_name='edit'">.save<xsl:if test="argument/filter!=''">&amp;filter=<xsl:value-of select="argumetn/filter"/></xsl:if></xsl:if><xsl:if test="argument/order!=''">&amp;order=<xsl:value-of select="argument/order"/></xsl:if><xsl:if test="argument/count and argument/count!=''">&amp;count=<xsl:value-of select="argument/count"/></xsl:if></xsl:variable>
	<form method="post" action="{$action}" enctype="multipart/form-data" class="filter_table">
		<xsl:if test="argument/id and argument/id!=''">
			<input type="hidden" value="{argument/id}" name="id"/>
		</xsl:if>
		<table class="nbk_filter">
			<xsl:for-each select="field/*">
				<tr>
					<td>
						<xsl:value-of select="title"/>:
					</td>
					<td>
						<xsl:if test="type='date'">
							<xsl:attribute name="class">date</xsl:attribute>
						</xsl:if>
						<xsl:choose>
							<xsl:when test="../../_method_name='filter'">
								<xsl:call-template name="filter_value"/>
							</xsl:when>
							<xsl:otherwise>
								<xsl:call-template name="editor_value"/>
							</xsl:otherwise>
						</xsl:choose>
					</td>
				</tr>
			</xsl:for-each>
			<tr><td colspan="2"><input type="submit" class="drop" value="сбросить"/><input type="submit" value="ок"/></td></tr>
		</table>
	</form>
</xsl:template>

<xsl:template name="filter_value">
	<xsl:choose>
		<xsl:when test="type='string' or type='text'">
			содержит: <input type="text" value="{value}" name="filter[{name()}]"/>
		</xsl:when>
		<xsl:when test="type='bool'">
			<select name="filter[{name()}]">
				<option></option>
				<option value="0">
					<xsl:if test="value='0'"><xsl:attribute name="selected">1</xsl:attribute></xsl:if>нет
				</option>
				<option value="1">
					<xsl:if test="value='1'"><xsl:attribute name="selected">1</xsl:attribute></xsl:if>да
				</option>
			</select>
		</xsl:when>
		<xsl:otherwise>
			<xsl:variable name="style"><xsl:if test="value/type=1">display:none</xsl:if></xsl:variable>
			<xsl:variable name="size"><xsl:choose>
				<xsl:when test="value/type=1">32</xsl:when>
				<xsl:otherwise>10</xsl:otherwise>
			</xsl:choose></xsl:variable>
			<span style="{$style}">с: </span><input type="text" size="{$size}" value="{value/min}" name="filter[{name()}][min]"/><span style="{$style}"> по: </span><input style="{$style}" type="text" value="{value/max}" name="filter[{name()}][max]" size="10"/><br/>
			<input class="filter_radio_type_switch" type="radio" id="{name()}_type0" name="filter[{name()}][type]" value="0">
				<xsl:if test="not(value/type) or value/type=0">
					<xsl:attribute name="checked">1</xsl:attribute>
				</xsl:if>
			</input><label for="{name()}_type0">интервал</label>
			<input class="filter_radio_type_switch" type="radio" id="{name()}_type1" name="filter[{name()}][type]" value="1">
				<xsl:if test="value/type=1">
					<xsl:attribute name="checked">1</xsl:attribute>
				</xsl:if>
			</input><label for="{name()}_type1">точное значение</label>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template name="editor_value">
	<xsl:choose>
		<xsl:when test="name()='num'">
			<xsl:value-of select="value"/>
		</xsl:when>
		<xsl:when test="type='bool'">
			<select name="filter[{name()}]">
				<option></option>
				<option value="0">
					<xsl:if test="value='0'"><xsl:attribute name="selected">1</xsl:attribute></xsl:if>нет
				</option>
				<option value="1">
					<xsl:if test="value='1'"><xsl:attribute name="selected">1</xsl:attribute></xsl:if>да
				</option>
			</select>
		</xsl:when>
		<xsl:when test="type='text'">
			<textarea type="text" name="filter[{name()}]">
				<xsl:value-of select="value"/>
			</textarea>
		</xsl:when>
		<xsl:otherwise>
			<input type="text" value="{value}" name="filter[{name()}]"/>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

</xsl:stylesheet>