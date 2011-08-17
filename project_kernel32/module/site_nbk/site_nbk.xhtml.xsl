<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="utf-8" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />

<xsl:template match="root/module/item[_module_name='site_nbk' and (_method_name='get' or _method_name='search')]">
	<xsl:variable name="href_order"><xsl:if test="argument/order and argument/order!='num' and argument/order!=''">&amp;order=<xsl:value-of select="argument/order"/></xsl:if></xsl:variable>
	<xsl:variable name="href_page"><xsl:if test="argument/page and argument/page!=1 and argument/page!=''">&amp;page=<xsl:value-of select="argument/page"/></xsl:if></xsl:variable>
	<xsl:variable name="href_count"><xsl:if test="argument/count and argument/count!=_default_page_count and argument/count!=''">&amp;count=<xsl:value-of select="argument/count"/></xsl:if></xsl:variable>
	<xsl:variable name="href_search"><xsl:if test="argument/search!=''">&amp;search=<xsl:value-of select="argument/search"/></xsl:if></xsl:variable>
	<xsl:variable name="href_filter"><xsl:if test="argument/filter and argument/filter!=''">&amp;filter=<xsl:value-of select="argument/filter"/></xsl:if></xsl:variable>
	<xsl:variable name="href_column"><xsl:if test="argument/column and argument/column!=''">&amp;column=<xsl:value-of select="argument/column"/></xsl:if></xsl:variable>
	<script type="text/javascript">_default_page_count = <xsl:value-of select="_default_page_count"/>;</script>
	<div class="curtain"></div>
	<xsl:call-template name="debt_nav">
		<xsl:with-param name="href_order" select="$href_order"/>
		<xsl:with-param name="href_page" select="$href_page"/>
		<xsl:with-param name="href_count" select="$href_count"/>
		<xsl:with-param name="href_search" select="$href_search"/>
		<xsl:with-param name="href_filter" select="$href_filter"/>
		<xsl:with-param name="href_column" select="$href_column"/>
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
							<a href="/?call={../../_module_name}{$href_count}{$href_filter}{$href_search}{$href_page}{$href_column}&amp;order={order}" alt='{$sort_base_text}"{title}"{$desc_name}' title='{$sort_base_text}"{title}"{$desc_name}'>
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
						<xsl:for-each select="*">
							<xsl:if test="name()!='id'">
								<td>
									<xsl:choose>
										<xsl:when test="name()='num'">
											<center>
												<a href="{$href_base}{id}" alt="{$edit_popup}{id}"  title="{$edit_popup}{id}"><xsl:value-of select="."/></a>
											</center>
										</xsl:when>
										<xsl:when test="name()='acc_comm' or name()='comment'">
											<xsl:variable name="account_field_name"><xsl:if test="name()='acc_comm'">_account</xsl:if>_comment</xsl:variable>
											<a class="href" href="/?call={_module_name}.edit{$account_field_name}{$href_order}{$href_page}{$href_count}{$href_search}{$href_filter}{$href_column}&amp;id={id}"><xsl:value-of select="comment"/>
												<xsl:choose>
													<xsl:when test=".!=''">редактировать</xsl:when>
													<xsl:otherwise>добавить</xsl:otherwise>
												</xsl:choose>
											</a>
										</xsl:when>
										<xsl:when test="name()='privatizated'">
											<xsl:if test=".=1">
												<a href="{$href_base}{id}" alt="{$edit_popup}{id}"  title="{$edit_popup}{id}">
													<input type="checkbox" disabled="1" checked="1"/>
												</a>
											</xsl:if>
										</xsl:when>
										<xsl:when test="name()='debt_date' or name()='pay_date'">
											<xsl:choose>
												<xsl:when test=".='00.0000'">-</xsl:when>
												<xsl:otherwise>
													<xsl:value-of select="."/>
												</xsl:otherwise>
											</xsl:choose>
										</xsl:when>
										<xsl:otherwise>
											<a href="{$href_base}{id}" alt="{$edit_popup}{id}"  title="{$edit_popup}{id}"><xsl:value-of select="."/></a>
										</xsl:otherwise>
									</xsl:choose>
								</td>
							</xsl:if>
						</xsl:for-each>
						<td>
							<center><a class="remove" href="/?call={../_module_name}.remove&amp;id={id}"><xsl:value-of select="comment"/>Х</a></center>
						</td>
					</tr>
				</xsl:for-each>
			</table>
			<!-- <xsl:call-template name="debt_nav">
				<xsl:with-param name="href_order" select="$href_order"/>
				<xsl:with-param name="href_page" select="$href_page"/>
				<xsl:with-param name="href_count" select="$href_count"/>
				<xsl:with-param name="href_search" select="$href_search"/>
				<xsl:with-param name="href_filter" select="$href_filter"/>
				<xsl:with-param name="href_column" select="$href_column"/>
			</xsl:call-template> -->
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
	<xsl:param name="href_column"/>
	<xsl:variable name="nav_href" select="concat('/?call=',_module_name,$href_count,$href_filter,$href_order,$href_search)"/>
	<table class="page_nav">
		<tr>
			<td>
				<span class="filter">
					<img src="module/site_nbk/img/filter.png" alt="Фильтры" title="Фильтры"/>
					<xsl:call-template name="nbk_edit">
						<xsl:with-param name="href_order" select="$href_order"/>
						<xsl:with-param name="href_page" select="$href_page"/>
						<xsl:with-param name="href_count" select="$href_count"/>
						<xsl:with-param name="href_search" select="$href_search"/>
						<xsl:with-param name="href_filter" select="$href_filter"/>
						<xsl:with-param name="href_column" select="$href_column"/>
						<xsl:with-param name="type">filter</xsl:with-param>
					</xsl:call-template>
				</span>
				<a href="/?call={_module_name}{$href_order}{$href_count}{$href_column}">
					<img src="module/site_nbk/img/filter_delete.png" alt="Очистить фильтры и поиск" title="Очистить фильтры и поиск"/>
				</a>
				<span class="column">
					<img src="module/site_nbk/img/column.png" alt="Выбрать колонки" title="Выбрать колонки"/>
					<xsl:call-template name="nbk_edit">
						<xsl:with-param name="href_order" select="$href_order"/>
						<xsl:with-param name="href_page" select="$href_page"/>
						<xsl:with-param name="href_count" select="$href_count"/>
						<xsl:with-param name="href_search" select="$href_search"/>
						<xsl:with-param name="href_filter" select="$href_filter"/>
						<xsl:with-param name="href_column" select="$href_column"/>
						<xsl:with-param name="type">column</xsl:with-param>
					</xsl:call-template>
				</span>
				<a href="/?call={_module_name}{$href_order}{$href_page}{$href_count}{$href_search}{$href_filter}">
					<img src="module/site_nbk/img/column_delete.png" alt="Очистить выбор колонок" title="Очистить выбор колонок"/>
				</a>
				<a href="/?call={_module_name}{$href_page}{$href_count}{$href_search}{$href_filter}{$href_column}">
					<img src="module/site_nbk/img/sort_delete.png" alt="Отменить сортировку" title="Отменить сортировку"/>
				</a>
				<a href="/?call={_module_name}.import">
					<img src="module/site_nbk/img/excel_import.png" alt="Импорт" title="Импорт"/>
				</a>
				<a href="/?call={_module_name}.export">
					<img src="module/site_nbk/img/excel_export.png" alt="Экспорт" title="Экспорт"/>
				</a>
			</td>
			<!-- 
			<td>
				<xsl:call-template name="shownavigation">
					<xsl:with-param name="obj_count" select="__num_rows"/>
					<xsl:with-param name="page_size" select="__page_size"/>
					<xsl:with-param name="page" select="__page"/>
					<xsl:with-param name="url" select="$nav_href"/>
				</xsl:call-template>
			</td>
			 -->
			<td>
				<form method="post" action="/?call={_module_name}{$href_order}{$href_column}{$href_filter}" enctype="multipart/form-data">
					<xsl:if test="_page_select_html">
						<span class="page_select">
							страница:
							<xsl:choose>
								<xsl:when test="__page &gt; 1">
									<a rel="prev" class="page_nav_control" href="{$nav_href}&amp;page={-1+__page}" accesskey="["  alt="назад ALT+B" title="назад (стрелка влево, ALT+[ )">&#8678;</a>
								</xsl:when>
								<xsl:otherwise>
									<a class="page_nav_control">&#160;&#160;&#160;</a>
								</xsl:otherwise>
							</xsl:choose>
							<select name="page" autofocus="autofocus">
								<xsl:value-of select="_page_select_html" disable-output-escaping="yes"/>
							</select>
							<xsl:choose>
								<xsl:when test="__page &lt; __max_page">
									<a rel="next" class="page_nav_control" href="{$nav_href}&amp;page={__page+1}" accesskey="]"  alt="вперед ALT+F" title="вперед (стрелка вправо, ALT+] )">&#8680;</a>
								</xsl:when>
								<xsl:otherwise>
									<a class="page_nav_control">&#160;&#160;&#160;</a>
								</xsl:otherwise>
							</xsl:choose>
						</span>
					</xsl:if>
					<div>
						<span>
							строк на странице:<input type="text" size="1" value="{__page_size}" name="count"/>
						</span>
						<xsl:variable name="search_value"><xsl:choose>
							<xsl:when test="argument/search"></xsl:when>
						</xsl:choose></xsl:variable>
						<span>
							<input type="text" size="15" placeholder="быстрый поиск" value="{argument/search}" name="search"/>
						</span>
						<input type="submit" value="Ok"/>
					</div>
				</form>
			</td>
		</tr>
	</table>
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

<xsl:template match="root/module/item[_module_name='site_nbk' and ( _method_name='edit')]">
	<xsl:variable name="href_order"><xsl:if test="argument/order and argument/order!='num' and argument/order!=''">&amp;order=<xsl:value-of select="argument/order"/></xsl:if></xsl:variable>
	<xsl:variable name="href_page"><xsl:if test="argument/page and argument/page!=1 and argument/page!=''">&amp;page=<xsl:value-of select="argument/page"/></xsl:if></xsl:variable>
	<xsl:variable name="href_count"><xsl:if test="argument/count and argument/count!=_default_page_count and argument/count!=''">&amp;count=<xsl:value-of select="argument/count"/></xsl:if></xsl:variable>
	<xsl:variable name="href_search"><xsl:if test="argument/search!=''">&amp;search=<xsl:value-of select="argument/search"/></xsl:if></xsl:variable>
	<xsl:variable name="href_filter"><xsl:if test="argument/filter and argument/filter!=''">&amp;filter=<xsl:value-of select="argument/filter"/></xsl:if></xsl:variable>
	<xsl:variable name="href_column"><xsl:if test="argument/column and argument/column!=''">&amp;column=<xsl:value-of select="argument/column"/></xsl:if></xsl:variable>
	<xsl:call-template name="nbk_edit">
		<xsl:with-param name="href_order" select="$href_order"/>
		<xsl:with-param name="href_page" select="$href_page"/>
		<xsl:with-param name="href_count" select="$href_count"/>
		<xsl:with-param name="href_search" select="$href_search"/>
		<xsl:with-param name="href_filter" select="$href_filter"/>
		<xsl:with-param name="href_column" select="$href_column"/>
	</xsl:call-template>
</xsl:template>

<xsl:template name="nbk_filter_value">
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

<xsl:template name="nbk_column_value">
	<input type="checkbox" name="column[{name()}]">
		<xsl:if test="active">
			<xsl:attribute name="checked">1</xsl:attribute>
		</xsl:if>
	</input>
</xsl:template>

<xsl:template name="nbk_editor_value">
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

<xsl:template name="nbk_edit">
	<xsl:param name="href_order"/>
	<xsl:param name="href_page"/>
	<xsl:param name="href_count"/>
	<xsl:param name="href_search"/>
	<xsl:param name="href_filter"/>
	<xsl:param name="href_column"/>
	<xsl:param name="type">edit</xsl:param>
	<div>
		<xsl:choose>
			<xsl:when test="$type='filter'">
				<img class="div_logo" src="module/site_nbk/img/filter.png" alt="Фильтрация выборки" title="Фильтрация выборки"/>
			</xsl:when>
			<xsl:when test="$type='column'">
				<img class="div_logo column_logo" src="module/site_nbk/img/column.png" alt="Выбор колонок" title="Выбор колонок"/>
			</xsl:when>
		</xsl:choose>
		<div>
			<xsl:variable name="action"><xsl:choose>
				<xsl:when test="$type='filter'"><xsl:value-of select="$href_column"/></xsl:when>
				<xsl:when test="$type='column'"><xsl:value-of select="$href_filter"/></xsl:when>
				<xsl:otherwise><xsl:value-of select="concat($href_column,$href_filter)"/></xsl:otherwise>
			</xsl:choose></xsl:variable>
			<xsl:choose>
				<xsl:when test="$type='filter'">
					<h4>Фильтрация выборки</h4>
				</xsl:when>
				<xsl:when test="$type='column'">
					<h4>Выбор колонок</h4>
				</xsl:when>
				<xsl:otherwise>
					<h4>Редактирование записи №<xsl:value-of select="num"/></h4>
				</xsl:otherwise>
			</xsl:choose>
			<form class="nbk_editor_table {_method_name}" action="/?call={_module_name}{$href_order}{$href_page}{$href_count}{$href_search}{$action}" enctype="multipart/form-data" method="post">
				<table class="nbk_filter">
					<xsl:for-each select="field_raw/*">
						<tr>
							<td>
								<xsl:value-of select="title"/>:
							</td>
							<td>
								<xsl:if test="type='date'">
									<xsl:attribute name="class">date</xsl:attribute>
								</xsl:if>
								<xsl:choose>
									<xsl:when test="$type='filter'"> 
										<xsl:call-template name="nbk_filter_value"/>
									</xsl:when>
									<xsl:when test="$type='column'"> 
										<xsl:call-template name="nbk_column_value"/>
									</xsl:when>
									<xsl:otherwise>
										<xsl:call-template name="nbk_editor_value"/>
									</xsl:otherwise>
								</xsl:choose>
							</td>
						</tr>
					</xsl:for-each>
					<tr>
						<td colspan="2">
							<xsl:if test="$type='filter' or $type='column'">
								<input type="submit" class="drop" value="сбросить"/>
							</xsl:if>
							<input type="submit" value="ок"/>
							<input type="submit" class="cancel" value="отмена"/>
						</td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</xsl:template>

</xsl:stylesheet>