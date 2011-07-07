<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="utf-8" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />

<xsl:template match="root/module/item[_module_name='article' and (_method_name='get' or _method_name='get_by_title')]">
	<xsl:if test="title">
		<h1><xsl:value-of select="title"/></h1>
	</xsl:if>
	<div class="article">
		<xsl:value-of select="text" disable-output-escaping="yes"/>
	</div>
</xsl:template>

<xsl:template match="root/module/item[_module_name='article' and (_method_name='edit' or _method_name='save')]">
	<div class="article admin">
		<form method="post" action="admin.php?call=article.save">
			<input type="text" class="title" value="{title}" name="title"/>
			<span class="more_input">Дополнительно</span>
			<table class="more_input">
				<tbody>
					<xsl:if test = "translit_title">
						<tr>
							<td class="table_title">
								Ссылка:
							</td>
							<td>
								<xsl:variable name="short_href">/<xsl:value-of select = "translit_title"/></xsl:variable>
								<xsl:variable name="long_href">/?call=article.get&amp;name=translit_title&amp;value=<xsl:value-of select = "translit_title"/></xsl:variable>
								<a href="{$short_href}" target="_blank"><xsl:value-of select="$short_href"/></a>
							</td>
						</tr>
						<tr>
							<td class="table_title">
								Полная ссылка:
							</td>
							<td>
								<xsl:variable name="long_href">/?call=article.get&amp;field=translit_title&amp;value=<xsl:value-of select = "translit_title"/></xsl:variable>
								<a href="{$long_href}" target="_blank"><xsl:value-of select="$long_href"/></a>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<a href="admin.php?call=module_link.edit&amp;module=article&amp;params[field]=translit_title&amp;params[value]={translit_title}">
									Редактор связей
								</a>
							</td>
						</tr>
					</xsl:if>
					<tr>
						<td class="table_title">
							Заголовок транслитом:
						</td>
						<td>
							<input type="text" class="translit_title" name="translit_title" value="{translit_title}"/>
							<label for="generate_translit">Сгенерировать</label>
							<input id="generate_translit" type="checkbox"/>
						</td>
					</tr>
					<tr>
						<td class="table_title">
							Meta-keyword:
						</td>
						<td>
							<input type="text" class="keyword" value="{keyword}" name="keyword"/>
						</td>
					</tr>
					<tr>
						<td class="table_title">
							Meta-description:
						</td>
						<td>
							<input type="text" class="description" value="{description}" name="description"/>
						</td>
					</tr>
				</tbody>
			</table>
			<textarea id="article_text" name="text">
				<xsl:value-of select="text" disable-output-escaping="yes"/>
			</textarea>
			<!-- Теги (через запятую):
			<input class="input" value="" name="tag"/>
			<br/> -->
			<p class="delimiter"></p>
			<input type="submit" class="submit right" value="Сохранить"/>
			<label for="article_draft">Черновик:</label>
			<input type="submit" class="submit delete" value="Удалить" id="{id}"/>
			<input id="article_draft" type="checkbox" name="draft">
				<xsl:if test="draft=1">
					<xsl:attribute name="checked" value="1"/>
				</xsl:if>
			</input>
			<input type="hidden" value="{id}" name="id"/>
		</form>
	</div>
</xsl:template>

<xsl:template name="article_output">
	<li>
		<a href="/?call=article.get&amp;field=translit_title&amp;value={translit_title}">
			<xsl:value-of select="title"/>
		</a>
	</li>
</xsl:template>

<xsl:template match="item[_module_name='article' and _method_name='_admin']/item/items">
	<ul class="nested_items_list">
		<xsl:for-each select="item">
			<li>
				<a href="?call=article.edit&amp;id={id}">
					<img src="template/admin/images/folder_document.png"/>
					<xsl:value-of select="title"/>
				</a>
			</li>
		</xsl:for-each>
	</ul>
</xsl:template>

<xsl:template match="item[_module_name='article' and _method_name='_admin']/items">
	<ul class="nested_items_uncategorized">
		<xsl:for-each select="item">
			<li>
				<a href="?call=article.edit&amp;id={id}">
					<img src="template/admin/images/folder_document.png"/>
					<xsl:value-of select="title"/>
				</a>
			</li>
		</xsl:for-each>
	</ul>
</xsl:template>

</xsl:stylesheet>