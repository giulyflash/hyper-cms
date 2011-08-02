<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="utf-8" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />

<xsl:template match="root/module/item[_module_name='site_nbk' and _method_name='_admin']">
	<table class="nbk_admin">
		<xsl:variable name="href_sort">/?call=site_nbk&amp;order=</xsl:variable>
		<tr>
			<th>
				<a href="{href_sort}">
					№ п/п
				</a>
			</th>
			<th>
				<a href="{href_sort}">
					Лицевой счет
				</a>
			</th>
			<th>
				<a href="{href_sort}">
					Улица
				</a>
			</th>
			<th>
				<a href="{href_sort}">
					Дом
				</a>
			</th>
			<th>
				<a href="{href_sort}">
					Квартира
				</a>
			</th>
			<th>
				<a href="{href_sort}">
					Приватизация
				</a>
			</th>
			<th>
				<a href="{href_sort}">
					Владелец/Квартиросъемщик
				</a>
			</th>
			<th>
				<a href="{href_sort}">
					Комментарий к лицевому счету
				</a>
			</th>
			<th>
				<a href="{href_sort}">
					Долг на момент контроля
				</a>
			</th>
			<th>
				<a href="{href_sort}">
					Остаток на кон.мес. на момент контроля
				</a>
			</th>
			<th>
				<a href="{href_sort}">
					Начисления
				</a>
			</th>
			<th>
				<a href="{href_sort}">
					Оплата&lt;=суммы контроля
				</a>
			</th>
			<th>
				<a href="{href_sort}">
					Месяц начала задолженности
				</a>
			</th>
			<th>
				<a href="{href_sort}">
					Дата платежа
				</a>
			</th>
			<th>
				<a href="{href_sort}">
					Комментарий
				</a>
			</th>
			<th>
				<a href="{href_sort}">
					Удалить
				</a>
			</th>
		</tr>
		<xsl:variable name="href_base">/?call=site_nbk.edit&amp;id=</xsl:variable>
		<xsl:for-each select="item">
			<tr>
				<td><center><a href="{$href_base}{id}"><xsl:value-of select="num"/></a></center></td>
				<td><a href="{$href_base}{id}"><xsl:value-of select="account"/></a></td>
				<td><a href="{$href_base}{id}"><xsl:value-of select="street"/></a></td>
				<td><a href="{$href_base}{id}"><xsl:value-of select="house"/></a></td>
				<td><a href="{$href_base}{id}"><xsl:value-of select="flat"/></a></td>
				<td>
					<xsl:if test="privatizated=1">
						<a href="{$href_base}{id}">	
							<input type="checkbox" disabled="1" checked="1"/>
						</a>
					</xsl:if>
				</td>
				<td><a href="{$href_base}{id}"><xsl:value-of select="owner"/></a></td>
				<td>
					<a href="/?call=site_nbk.edit_account_comment&amp;id={id}"><xsl:value-of select="comment"/>
						<xsl:choose>
							<xsl:when test="account_comment!=''">редактировать</xsl:when>
							<xsl:otherwise>добавить</xsl:otherwise>
						</xsl:choose>
					</a>
				</td>
				<td><a href="{$href_base}{id}"><xsl:value-of select="debt"/></a></td>
				<td><a href="{$href_base}{id}"><xsl:value-of select="balance"/></a></td>
				<td><a href="{$href_base}{id}"><xsl:value-of select="charges"/></a></td>
				<td><a href="{$href_base}{id}"><xsl:value-of select="control_summ"/></a></td>
				<td><a href="{$href_base}{id}"><xsl:value-of select="debt_date"/></a></td>
				<td><a href="{$href_base}{id}"><xsl:value-of select="pay_date"/></a></td>
				<td>
					<a href="/?call=site_nbk.edit_comment&amp;id={id}"><xsl:value-of select="comment"/>
						<xsl:choose>
							<xsl:when test="comment!=''">редактировать</xsl:when>
							<xsl:otherwise>добавить</xsl:otherwise>
						</xsl:choose>
					</a>
				</td>
				<td>
					<center><a href="/?call=site_nbk.remove&amp;id={id}"><xsl:value-of select="comment"/>Х</a></center>
				</td>
			</tr>
		</xsl:for-each>
	</table>
	<div class="navigation">
		<xsl:call-template name="shownavigation">
			<xsl:with-param name="obj_count" select="__num_rows"/>
			<xsl:with-param name="page_size" select="__page_size"/>
			<xsl:with-param name="page" select="argument/page"/>
			<xsl:with-param name="url">/?call=site_nbk<xsl:if test="argument/order!=''">&amp;order=<xsl:value-of select="argument/order"/></xsl:if><xsl:if test="argument/count!=''">&amp;count=<xsl:value-of select="argument/count"/></xsl:if></xsl:with-param>
		</xsl:call-template>
	</div>
	<p>
		<a href="/?call=site_nbk.edit">добавить должника</a>
	</p>
	<p>
		<a href="/?call=site_nbk.generate&amp;is_default=1">загрузить файл со списком должников</a>
	</p>
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