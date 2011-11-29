<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="utf-8" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />

<xsl:template match="root/module/item[_module_name='article' and (_method_name='get')]">
	<xsl:if test="title">
		<h1><xsl:value-of select="title"/></h1>
	</xsl:if>
	<div class="article">
		<xsl:value-of select="text" disable-output-escaping="yes"/>
	</div>
	<!-- temp -->
	<xsl:if test="/root/session/user_info and id">
		<p class="article_edit">
			<a class="edit_article" href="/admin.php?call=article.edit&amp;id={id}" target="_blank" title="редактировать" alt="редактировать">
				<img src="module/base_module/img/pencil.png"/>&#160;<span>редактировать</span>
			</a>
		</p>
	</xsl:if> 
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
					<tr>
						<td class="table_title">
							Дата:
						</td>
						<td>
							<xsl:call-template name="_base_create_date"/>
						</td>
					</tr>
					<tr>
						<td class="table_title">
							Категория:
						</td>
						<td>
							<xsl:variable name="category_id"><xsl:choose>
								<xsl:when test="id"><xsl:value-of select="category_id"/></xsl:when>
								<xsl:otherwise><xsl:value-of select="_argument/category_id"/></xsl:otherwise>
							</xsl:choose></xsl:variable>
							<select name="category_id" autocomplete='off'>
								<xsl:call-template name="_get_category_list">
									<xsl:with-param name="id" select="$category_id"/>
								</xsl:call-template>
							</select>
						</td>
					</tr>
					<tr>
						<td class="table_title">
							Редактор связей:
						</td>
						<td>
							<xsl:call-template name="link_editor_href">
								<xsl:with-param name="method_name">get</xsl:with-param>
							</xsl:call-template>
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

<xsl:template match="root/module/item[_module_name='article' and _method_name='get_category']">
	<xsl:choose>
		<xsl:when test="/root/meta/content_type='json_html'">
			<xsl:call-template name="article_base">
				<xsl:with-param name="need_ul">0</xsl:with-param>
			</xsl:call-template>
		</xsl:when>
		<xsl:otherwise>
			<div class="nested_items {_module_name} {_method_name} {_config/category_type}">
				<xsl:call-template name="article_base"/>
			</div>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template name="article_base">
	<xsl:param name="need_ul">1</xsl:param>
	<xsl:param name="module_name" select="_module_name"/>
	<xsl:choose>
		<xsl:when test="item or items">
			<xsl:choose>
				<xsl:when test="$need_ul=1">
					<ul>
						<xsl:call-template name="article_core">
							<xsl:with-param name="module_name" select="$module_name"/>
						</xsl:call-template>
						<xsl:if test="active=1 and not(items)">
							<xsl:call-template name="base_no_items"/>
						</xsl:if>
					</ul>
				</xsl:when>
				<xsl:otherwise>
					<xsl:call-template name="article_core">
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

<xsl:template name="article_core">
	<xsl:param name="module_name" select="_module_name"/>
	<xsl:variable name="admin_mode"><xsl:if test="/root/meta/admin_mode=1">admin.php</xsl:if></xsl:variable>
	<xsl:for-each select="item">
		<li>
			<xsl:if test="active=1">
				<xsl:attribute name="class">active</xsl:attribute>
			</xsl:if>
			<div class="item_cont">
				<a class="_ajax" href="/{$admin_mode}?call={$module_name}.get_category&amp;title={translit_title}" alt="{title}" title="{title}">
					<span class="folder_icon"></span>
					<span class="text"><xsl:value-of select="title"/></span>
				</a>
			</div>
			<xsl:if test="items or item">
				<ul>
					<xsl:call-template name="article_core">
						<xsl:with-param name="module_name" select="$module_name"/>
					</xsl:call-template>
				</ul>
			</xsl:if>
		</li>
	</xsl:for-each>
	<xsl:if test="items">
		<li class="items">
			<ul class="items">
				<xsl:for-each select="items/item">
					<li>
						<a href="/?call=article.get&amp;title={translit_title}" alt="{title}" title="{title}">
							<h4><xsl:value-of select="title"/></h4>
						</a>
						<xsl:if test="thumb_path">
							<img class="article_thumb" src="thumb_path"/>
						</xsl:if>
						<div class="text">
							<xsl:value-of select="preview"/>
							<xsl:if test="preview!='' and substring(string-length(preview)-1, 1, preview)!='.'">...</xsl:if>
							<a href="/?call=article.get&amp;title={translit_title}" alt="{title}" title="{title}">
								читать полностью &#8594;
							</a>
						</div>
					</li>
				</xsl:for-each>
			</ul>
		</li>
	</xsl:if>
</xsl:template>

<xsl:template match="root/module/item[_module_name='article' and _method_name='edit_category']">
	<form class="{_module_name}" method="post" action="admin.php?call={_module_name}.save_category">
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
				<td class="first">
					Вместо категории отображать статью:
				</td>
				<td>
					<select name="article_redirect">
						<option/>
						<xsl:for-each select="article/*">
							<option value="{name()}">
								<xsl:if test="name() = ../../article_redirect">
									<xsl:attribute name="selected">selected</xsl:attribute>
								</xsl:if>
								<xsl:value-of select="."/>
							</option>
						</xsl:for-each>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<input type="submit" value="Сохранить"/>
				</td>
			</tr>
		</table>
	</form>
</xsl:template>

</xsl:stylesheet>