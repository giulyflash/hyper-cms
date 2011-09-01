<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="utf-8" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />

<xsl:template match="root/module/item[_module_name='debtor_list' and (_method_name='get' or _method_name='search')]">
	<xsl:variable name="href_order"><xsl:if test="argument/order!='num' and argument/order!=''">&amp;order=<xsl:value-of select="argument/order"/></xsl:if></xsl:variable>
	<xsl:variable name="href_page"><xsl:if test="argument/page!=1 and argument/page!=''">&amp;page=<xsl:value-of select="argument/page"/></xsl:if></xsl:variable>
	<xsl:variable name="href_count"><xsl:if test="_count!='' and _count!=_default_count">&amp;count=<xsl:value-of select="_count"/></xsl:if></xsl:variable>
	<xsl:variable name="href_search"><xsl:if test="argument/search!=''">&amp;search=<xsl:value-of select="argument/search"/></xsl:if></xsl:variable>
	<xsl:variable name="href_filter"><xsl:if test="argument/filter!=''">&amp;filter=<xsl:value-of select="argument/filter"/></xsl:if></xsl:variable>
	<xsl:variable name="href_column"><xsl:if test="argument/column!=''">&amp;column=<xsl:value-of select="argument/column"/></xsl:if></xsl:variable>
	<xsl:variable name="href_zero_debt"><xsl:if test="argument/zero_debt!=''">&amp;zero_debt=1</xsl:if></xsl:variable>
	<xsl:variable name="href_table_name"><xsl:if test="argument/table_name!=''">&amp;table_name=<xsl:value-of select="argument/table_name"/></xsl:if></xsl:variable>
	<script type="text/javascript">_default_page_count = <xsl:value-of select="_default_count"/>;</script>
	<div class="curtain"></div>
	<xsl:call-template name="debt_nav">
		<xsl:with-param name="href_order" select="$href_order"/>
		<xsl:with-param name="href_page" select="$href_page"/>
		<xsl:with-param name="href_count" select="$href_count"/>
		<xsl:with-param name="href_search" select="$href_search"/>
		<xsl:with-param name="href_filter" select="$href_filter"/>
		<xsl:with-param name="href_column" select="$href_column"/>
		<xsl:with-param name="href_zero_debt" select="$href_zero_debt"/>
		<xsl:with-param name="href_table_name" select="$href_table_name"/>
	</xsl:call-template>
	<xsl:choose>
		<xsl:when test="item">
			<table class="nbk_admin">
				<xsl:variable name="sort_base_text">сортировка по полю </xsl:variable>
				<tr>
					<xsl:for-each select="_field/*[selected=1]">
						<th>
							<xsl:variable name="desc_name"><xsl:if test="desc='desc'"> наоборот</xsl:if></xsl:variable>
							<xsl:variable name="anchor">order_<xsl:value-of select="name()"/></xsl:variable>
							<a href="/?call={../../_module_name}{$href_count}{$href_zero_debt}{$href_table_name}{$href_filter}{$href_search}{$href_page}{$href_column}&amp;order={order}" alt='{$sort_base_text}"{title}"{$desc_name}' title='{$sort_base_text}"{title}"{$desc_name}'>
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
				<xsl:variable name="edit_href">/?call=<xsl:value-of select="_module_name"/>.edit<xsl:value-of select="concat($href_order,$href_page,$href_count,$href_search,$href_filter,$href_column,$href_zero_debt,$href_table_name)"/>&amp;id=</xsl:variable>
				<xsl:variable name="edit_popup">Редактировать записи № </xsl:variable>
				<xsl:for-each select="item">
					<tr>
						<xsl:for-each select="*">
							<xsl:if test="not(name()='id' or contains(name(),'__src'))">
								<td>
									<xsl:choose>
										<xsl:when test="name()='num'">
											<center>
												<a href="{$edit_href}{../id}" alt="{$edit_popup}{../id}"  title="{$edit_popup}{../id}"><xsl:value-of select="."/></a>
											</center>
										</xsl:when>
										<xsl:when test="name()='acc_comm' or name()='comment'">
											<xsl:variable name="account_field_name"><xsl:if test="name()='acc_comm'">_account</xsl:if>_comment</xsl:variable>
											<a href="{$edit_href}{../id}"><xsl:value-of select="comment"/>
												<xsl:choose>
													<xsl:when test=".!=''">редактировать</xsl:when>
													<xsl:otherwise>добавить</xsl:otherwise>
												</xsl:choose>
											</a>
										</xsl:when>
										<xsl:when test="name()='privatizated'">
											<xsl:if test=".=1">
												<a href="{$edit_href}{../id}" alt="{$edit_popup}{../id}"  title="{$edit_popup}{../id}">
													<input type="checkbox" disabled="1" checked="1"/>
												</a>
											</xsl:if>
										</xsl:when>
										<xsl:when test="name()='debt_date' or name()='pay_date'">
											<a href="{$edit_href}{../id}">
												<xsl:choose>
													<xsl:when test=".='00.0000' or .='00.00.0000'">-</xsl:when>
													<xsl:otherwise>
														<xsl:value-of select="."/>
													</xsl:otherwise>
												</xsl:choose>
											</a>
										</xsl:when>
										<xsl:otherwise>
											<a href="{$edit_href}{../id}" alt="{$edit_popup}{../id}"  title="{$edit_popup}{../id}"><xsl:value-of select="."/></a>
										</xsl:otherwise>
									</xsl:choose>
								</td>
							</xsl:if>
						</xsl:for-each>
						<td>
							<center><a class="remove" href="/?call={../_module_name}.remove&amp;id={id}{$href_order}{$href_page}{$href_count}{$href_search}{$href_filter}{$href_column}{$href_zero_debt}{$href_table_name}"><xsl:value-of select="comment"/>Х</a></center>
						</td>
					</tr>
				</xsl:for-each>
			</table>
			<p class="record_count">Результатов: <span><xsl:value-of select="__num_rows"/></span></p>
			<!-- <xsl:call-template name="debt_nav">
			</xsl:call-template> -->
		</xsl:when>
		<xsl:otherwise>
			<p class="message_box">Записей не найдено.</p>
		</xsl:otherwise>
	</xsl:choose>
	<!-- <p>
		<a href="/?call={_module_name}.edit">добавить должника</a>
	</p>
	<p>
		<a href="/?call={_module_name}.generate">загрузить файл со списком должников</a>
	</p> -->
</xsl:template>

<xsl:template name="debt_nav">
	<xsl:param name="href_order"/>
	<xsl:param name="href_page"/>
	<xsl:param name="href_count"/>
	<xsl:param name="href_search"/>
	<xsl:param name="href_filter"/>
	<xsl:param name="href_column"/>
	<xsl:param name="href_zero_debt"/>
	<xsl:param name="href_table_name"/>
	<xsl:variable name="nav_href" select="concat('/?call=',_module_name,$href_count,$href_filter,$href_order,$href_search,$href_column,$href_zero_debt)"/>
	<table class="page_nav">
		<tr>
			<td>
				<span class="filter">
					<img src="module/{_module_name}/img/filter.png" alt="Фильтры" title="Фильтры"/>
					<xsl:call-template name="nbk_edit">
						<xsl:with-param name="href_order" select="$href_order"/>
						<xsl:with-param name="href_page" select="$href_page"/>
						<xsl:with-param name="href_count" select="$href_count"/>
						<xsl:with-param name="href_search" select="$href_search"/>
						<xsl:with-param name="href_filter" select="$href_filter"/>
						<xsl:with-param name="href_column" select="$href_column"/>
						<xsl:with-param name="href_zero_debt" select="$href_zero_debt"/>
						<xsl:with-param name="href_table_name" select="$href_table_name"/>
						<xsl:with-param name="type">filter</xsl:with-param>
					</xsl:call-template>
				</span>
				<a href="/?call={_module_name}{$href_order}{$href_count}{$href_zero_debt}{$href_table_name}{$href_column}">
					<img src="module/{_module_name}/img/filter_delete.png" alt="Очистить фильтры и поиск" title="Очистить фильтры и поиск"/>
				</a>
				<span class="column">
					<img src="module/{_module_name}/img/column.png" alt="Выбрать колонки" title="Выбрать колонки"/>
					<xsl:call-template name="nbk_edit">
						<xsl:with-param name="href_order" select="$href_order"/>
						<xsl:with-param name="href_page" select="$href_page"/>
						<xsl:with-param name="href_count" select="$href_count"/>
						<xsl:with-param name="href_search" select="$href_search"/>
						<xsl:with-param name="href_filter" select="$href_filter"/>
						<xsl:with-param name="href_column" select="$href_column"/>
						<xsl:with-param name="href_zero_debt" select="$href_zero_debt"/>
						<xsl:with-param name="href_table_name" select="$href_table_name"/>
						<xsl:with-param name="type">column</xsl:with-param>
					</xsl:call-template>
				</span>
				<a href="/?call={_module_name}{$href_order}{$href_page}{$href_count}{$href_zero_debt}{$href_table_name}{$href_search}{$href_filter}">
					<img src="module/{_module_name}/img/column_delete.png" alt="Очистить выбор колонок" title="Очистить выбор колонок"/>
				</a>
				<a href="/?call={_module_name}.edit">
					<img src="module/{_module_name}/img/table_add.png" alt="добавить запись" title="добавить запись"/>
				</a>
				<a href="/?call={_module_name}{$href_page}{$href_count}{$href_zero_debt}{$href_table_name}{$href_search}{$href_filter}{$href_column}">
					<img src="module/{_module_name}/img/sort_delete.png" alt="Отменить сортировку" title="Отменить сортировку"/>
				</a>
				<a href="/?call={_module_name}.import{$href_order}{$href_page}{$href_count}{$href_search}{$href_filter}{$href_column}{$href_zero_debt}{$href_table_name}">
					<img src="module/{_module_name}/img/excel_import.png" alt="Импорт" title="Импорт"/>
				</a>
				<a href="/?call={_module_name}{$href_order}{$href_page}{$href_count}{$href_zero_debt}{$href_table_name}{$href_search}{$href_filter}{$href_column}&amp;export=1">
					<img src="module/{_module_name}/img/excel_export.png" alt="Экспорт" title="Экспорт"/>
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
				<form method="post" action="/?call={_module_name}{$href_order}{$href_filter}{$href_column}{$href_table_name}" enctype="multipart/form-data">
					<xsl:if test="_page_select_html">
						<span class="page_select">
							страница:
							<xsl:choose>
								<xsl:when test="__page &gt; 1">
									<a rel="prev" class="page_nav_control" href="{$nav_href}&amp;page={-1+__page}" accesskey="["  alt="страница назад (стрелка влево, ALT+[ )" title="страница назад (стрелка влево, ALT+[ )">&#8678;</a>
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
									<a rel="next" class="page_nav_control" href="{$nav_href}&amp;page={__page+1}" accesskey="]"  alt="страница вперед (стрелка вправо, ALT+] )" title="страница вперед (стрелка вправо, ALT+] )">&#8680;</a>
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
					<xsl:if test="argument/table_name='' or argument/table_name=_config/default_table">
						<input id="zero_debt_input" type="checkbox" name="zero_debt">
							<xsl:if test="argument/zero_debt and argument/zero_debt!=''">
								<xsl:attribute name="checked">checked</xsl:attribute>
							</xsl:if>
						</input>
						<label for="zero_debt_input">плательщики без долга</label>
					</xsl:if>
				</form>
			</td>
		</tr>
	</table>
</xsl:template>

<xsl:template match="root/module/item[_module_name='debtor_list' and (_method_name='import')]">
	<form method="post" action="/?call={_module_name}.{_method_name}&amp;is_default=0" enctype="multipart/form-data">
		<table class="nbk_admin_file">
			<tr>
				<td>Путь к файлу со списком:</td>
				<td>
					<input type="file" name="path" accept="application/x-excel"/>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<center>
						<input type="submit" value="Отмена" onclick="window.location='/?call={_module_name}';return false;"/>
						<input type="submit" value="Импортировать"/>
					</center>
				</td>
			</tr>
		</table>
	</form>
</xsl:template>

<xsl:template match="root/module/item[_module_name='debtor_list' and (_method_name='edit' or _method_name='save')]">
	<xsl:variable name="href_order"><xsl:if test="argument/order and argument/order!='num' and argument/order!=''">&amp;order=<xsl:value-of select="argument/order"/></xsl:if></xsl:variable>
	<xsl:variable name="href_page"><xsl:if test="argument/page and argument/page!=1 and argument/page!=''">&amp;page=<xsl:value-of select="argument/page"/></xsl:if></xsl:variable>
	<xsl:variable name="href_count"><xsl:if test="argument/count!=''">&amp;count=<xsl:value-of select="argument/count"/></xsl:if></xsl:variable>
	<xsl:variable name="href_search"><xsl:if test="argument/search!=''">&amp;search=<xsl:value-of select="argument/search"/></xsl:if></xsl:variable>
	<xsl:variable name="href_filter"><xsl:if test="argument/filter and argument/filter!=''">&amp;filter=<xsl:value-of select="argument/filter"/></xsl:if></xsl:variable>
	<xsl:variable name="href_column"><xsl:if test="argument/column and argument/column!=''">&amp;column=<xsl:value-of select="argument/column"/></xsl:if></xsl:variable>
	<xsl:variable name="href_zero_debt"><xsl:if test="argument/zero_debt and argument/zero_debt!=''">&amp;zero_debt=1</xsl:if></xsl:variable>
	<xsl:variable name="href_table_name"><xsl:if test="argument/table_name and argument/table_name!=''">&amp;table_name=<xsl:value-of select="argument/table_name"/></xsl:if></xsl:variable>
	<xsl:call-template name="nbk_edit">
		<xsl:with-param name="href_order" select="$href_order"/>
		<xsl:with-param name="href_page" select="$href_page"/>
		<xsl:with-param name="href_count" select="$href_count"/>
		<xsl:with-param name="href_search" select="$href_search"/>
		<xsl:with-param name="href_filter" select="$href_filter"/>
		<xsl:with-param name="href_column" select="$href_column"/>
		<xsl:with-param name="href_zero_debt" select="$href_zero_debt"/>
		<xsl:with-param name="href_table_name" select="$href_table_name"/>
	</xsl:call-template>
</xsl:template>

<xsl:template name="nbk_filter_value">
	<td>
		<xsl:value-of select="title"/>:
	</td>
	<td>
		<xsl:choose>
			<xsl:when test="type='string' or type='text' or type='string_parted'">
				содержит: <input type="text" value="{filter}" name="filter[{name()}]"/>
			</xsl:when>
			<xsl:when test="type='bool'">
				<select name="filter[{name()}]" autocomplete="off">
					<option></option>
					<option value="0">
						<xsl:if test="filter='0'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>нет
					</option>
					<option value="1">
						<xsl:if test="filter='1'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>да
					</option>
				</select>
			</xsl:when>
			<xsl:when test="type='enum'">
				<select name="filter[{name()}][]" autocomplete="off" multiple="multiple">
					<xsl:for-each select="val/item">
						<option value="{value}">
							<xsl:if test="selected">
								<xsl:attribute name="selected">selected</xsl:attribute>
							</xsl:if>
							<xsl:value-of select="value"/>
						</option>
					</xsl:for-each>
				</select>
			</xsl:when>
			<xsl:otherwise>
				<xsl:variable name="style"><xsl:if test="filter/type=1">display:none</xsl:if></xsl:variable>
				<xsl:variable name="size"><xsl:if test="filter/type=1">width:100%</xsl:if></xsl:variable>
				<span style="{$style}">с: </span>
				<input type="text" style="{$size}" value="{filter/min}" name="filter[{name()}][min]" size="10"/>
				<span style="{$style}"> по: </span>
				<input style="{$style}" type="text" value="{filter/max}" name="filter[{name()}][max]" size="10"/>
				<br/>
				<input  autocomplete="off" class="filter_radio_type_switch" type="radio" id="{name()}_type0" name="filter[{name()}][type]" value="0">
					<xsl:if test="not(filter/type) or filter/type=0">
						<xsl:attribute name="checked">checked</xsl:attribute>
					</xsl:if>
				</input><label for="{name()}_type0">интервал</label>
				<input  autocomplete="off" class="filter_radio_type_switch" type="radio" id="{name()}_type1" name="filter[{name()}][type]" value="1">
					<xsl:if test="filter/type=1">
						<xsl:attribute name="checked">checked</xsl:attribute>
					</xsl:if>
				</input><label for="{name()}_type1">точное значение</label>
			</xsl:otherwise>
		</xsl:choose>
	</td>
</xsl:template>

<xsl:template name="nbk_column_value">
	<td>
		<label for="column_{name()}"><xsl:value-of select="title"/></label>
	</td>
	<td>
		<input type="checkbox" name="column[{name()}]" id="column_{name()}">
			<xsl:if test="selected" autocomplete="off">
				<xsl:attribute name="checked">1</xsl:attribute>
			</xsl:if>
		</input>
	</td>
</xsl:template>

<xsl:template name="nbk_editor_value">
	<xsl:choose>
		<xsl:when test="name()='num'">
			<td>
				<xsl:value-of select="title"/>:
			</td>
			<td>
				<xsl:value-of select="value"/>
			</td>
		</xsl:when>
		<xsl:when test="type='bool'">
			<td>
				<xsl:value-of select="title"/>:
			</td>
			<td>
				<select name="value[{name()}]">
					<option></option>
					<option value="0">
						<xsl:if test="value='0'"><xsl:attribute name="selected">1</xsl:attribute></xsl:if>нет
					</option>
					<option value="1">
						<xsl:if test="value='1'"><xsl:attribute name="selected">1</xsl:attribute></xsl:if>да
					</option>
				</select>
			</td>
		</xsl:when>
		<xsl:when test="type='enum'">
			<td>
				<xsl:value-of select="title"/>:
			</td>
			<td>
				<select name="value[{name()}][]" autocomplete="off" multiple="multiple">
					<xsl:for-each select="val/item">
						<option value="{value}">
							<xsl:if test="selected">
								<xsl:attribute name="selected">selected</xsl:attribute>
							</xsl:if>
							<xsl:value-of select="value"/>
						</option>
					</xsl:for-each>
				</select>
			</td>
		</xsl:when>
		<xsl:when test="type='text'">
			<td colspan="2">
				<xsl:value-of select="title"/>:<br/>
				<textarea type="text" name="value[{name()}]" id="{name()}_editor">
					<xsl:value-of select="value"/>
				</textarea>
			</td>
		</xsl:when>
		<xsl:otherwise>
			<td>
				<xsl:value-of select="title"/>:
			</td>
			<td>
				<input type="text" value="{value}" name="value[{name()}]"/>
			</td>
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
	<xsl:param name="href_zero_debt"/>
	<xsl:param name="href_table_name"/>
	<xsl:param name="type">edit</xsl:param>
	<div class="editor {$type}">
		<xsl:choose>
			<xsl:when test="$type='filter'">
				<img class="div_logo" src="module/{_module_name}/img/filter.png" alt="Фильтрация выборки" title="Фильтрация выборки"/>
			</xsl:when>
			<xsl:when test="$type='column'">
				<img class="div_logo column_logo" src="module/{_module_name}/img/column.png" alt="Выбор колонок" title="Выбор колонок"/>
			</xsl:when>
		</xsl:choose>
		<xsl:variable name="action"><xsl:choose>
			<xsl:when test="$type='filter'"><xsl:value-of select="$href_column"/></xsl:when>
			<xsl:when test="$type='column'"><xsl:value-of select="$href_filter"/></xsl:when>
			<xsl:otherwise><xsl:value-of select="concat($href_column,$href_filter)"/></xsl:otherwise>
		</xsl:choose></xsl:variable>
		<xsl:variable name="method"><xsl:if test="$type='edit'">.save</xsl:if></xsl:variable>
		<form action="/?call={_module_name}{$method}{$href_order}{$href_page}{$href_count}{$href_zero_debt}{$href_table_name}{$href_search}{$action}" enctype="multipart/form-data" method="post">
			<xsl:if test="_field/id">
				<input type="hidden" name="id" value="{_field/id}"/>
				<input type="hidden" name="value[num]" value="{_field/num/value}"/>
			</xsl:if>
			<table class="nbk_input">
				<tr>
					<td colspan="2">
						<h4>
							<xsl:choose>
								<xsl:when test="$type='filter'">Фильтрация выборки</xsl:when>
								<xsl:when test="$type='column'">Выбор колонок</xsl:when>
								<xsl:otherwise><xsl:choose>
										<xsl:when test="_field/id">
											Редактирование записи № <xsl:value-of select="_field/id"/>
										</xsl:when>
										<xsl:otherwise>
											Добавление новой записи
										</xsl:otherwise>
								</xsl:choose></xsl:otherwise>
							</xsl:choose>
						</h4>
					</td>
				</tr>
				<xsl:for-each select="_field/*">
					<xsl:if test="not(name()='id' or contains(name(),'__src'))">
						<tr>
							<xsl:if test="type='date'"><xsl:attribute name="class">date</xsl:attribute></xsl:if>
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
						</tr>
					</xsl:if>
				</xsl:for-each>
				<tr>
					<td colspan="2">
						<xsl:if test="$type='filter' or $type='column'">
							<input type="submit" class="drop" value="сбросить"/>
						</xsl:if>
						<input type="submit" value="ок"/>
						<xsl:variable name="cancel">window.location='/?call=<xsl:value-of select="concat(_module_name,$href_order,$href_page,$href_count,$href_search,$action,$href_zero_debt,$href_table_name)"/>'; return false;</xsl:variable>
						<input type="submit" class="cancel" onclick="{$cancel}" value="отмена"/>
					</td>
				</tr>
			</table>
		</form>
	</div>
</xsl:template>

</xsl:stylesheet>