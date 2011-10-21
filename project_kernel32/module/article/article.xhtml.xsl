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
					<tr>
						<td class="table_title">
							Дата:
						</td>
						<td>
							<xsl:call-template name="article_create_date"/>
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
			<xsl:variable name="category_id"><xsl:choose>
				<xsl:when test="id"><xsl:value-of select="category_id"/></xsl:when>
				<xsl:otherwise><xsl:value-of select="_argument/category_id"/></xsl:otherwise>
			</xsl:choose></xsl:variable>
			<input type="hidden" value="{$category_id}" name="category_id"/>
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
			<xsl:call-template name="article_core">
				<xsl:with-param name="module_name" select="$module_name"/>
			</xsl:call-template>
		</li>
	</xsl:for-each>
	<xsl:if test="items">
		<li class="items">
			<ul class="items">
				<xsl:for-each select="items/item">
					<li>
						<a href="{path}" alt="{title}" title="{title}">
							<xsl:if test="not(thumb_path) or thumb_path=''">
								<xsl:attribute name="class">default_thumb</xsl:attribute>
							</xsl:if>
							<xsl:variable name="thumb_path">
								<xsl:choose>
									<xsl:when test="thumb_path and thumb_path!=''"><xsl:value-of select="thumb_path"/></xsl:when>
									<xsl:otherwise><xsl:value-of select="/root/module/item[_module_name=$module_name]/_config/default_thumb"/></xsl:otherwise>
								</xsl:choose>
							</xsl:variable>
							<!-- <img src="{$thumb_path}" alt="{title}" title="{title}"/> -->
							<span class="title"><xsl:value-of select="title"/></span>
						</a>
					</li>
				</xsl:for-each>
			</ul>
		</li>
	</xsl:if>
</xsl:template>

<xsl:template name="article_create_date">
	<xsl:param name="create_date" select="create_date"/>
	<!-- 2011-10-21 12:01:45 -->
	<xsl:variable name="year" select="substring-before($create_date,'-')"/>
	<xsl:variable name="temp1" select="substring-after($create_date,'-')"/>
	<xsl:variable name="month" select="substring-before($temp1,'-')"/>
	<xsl:variable name="temp2" select="substring-after($temp1,'-')"/>
	<xsl:variable name="day" select="substring-before($temp2,' ')"/>
	<xsl:variable name="temp3" select="substring-after($temp2,' ')"/>
	<xsl:variable name="hour" select="substring-before($temp3,':')"/>
	<xsl:variable name="temp4" select="substring-after($temp3,':')"/>
	<xsl:variable name="minute" select="substring-before($temp4,':')"/>
	<xsl:variable name="second" select="substring-after($temp4,':')"/>
	<!-- <p><xsl:value-of select="$create_date"/></p>
	<p><xsl:value-of select="$year"/></p>
	<p><xsl:value-of select="$month"/></p>
	<p><xsl:value-of select="$day"/></p>
	<p><xsl:value-of select="$hour"/></p>
	<p><xsl:value-of select="$minute"/></p>
	<p><xsl:value-of select="$second"/></p> -->
	<select name="create_date[y]" autocomplete="off">
		<xsl:call-template name="__article_date_loop">
			<xsl:with-param name="var" select="2010"/>
			<xsl:with-param name="max" select="2040"/>
			<xsl:with-param name="selected" select="$year"/>
		</xsl:call-template>
	</select>
	<select  name="create_date[m]" autocomplete="off">
		<xsl:call-template name="__article_date_loop">
			<xsl:with-param name="var" select="1"/>
			<xsl:with-param name="max" select="12"/>
			<xsl:with-param name="selected" select="$month"/>
			<xsl:with-param name="month" select="1"/>
		</xsl:call-template>
	</select>
	<select  name="create_date[d]" autocomplete="off">
		<xsl:call-template name="__article_date_loop">
			<xsl:with-param name="var" select="1"/>
			<xsl:with-param name="max" select="31"/>
			<xsl:with-param name="selected" select="$day"/>
		</xsl:call-template>
	</select>
	&#160;&#160;
	<select name="create_date[h]" autocomplete="off">
		<xsl:call-template name="__article_date_loop">
			<xsl:with-param name="var" select="0"/>
			<xsl:with-param name="max" select="23"/>
			<xsl:with-param name="selected" select="$hour"/>
		</xsl:call-template>
	</select>
	:
	<select name="create_date[i]" autocomplete="off">
		<xsl:call-template name="__article_date_loop">
			<xsl:with-param name="var" select="0"/>
			<xsl:with-param name="max" select="59"/>
			<xsl:with-param name="selected" select="$minute"/>
		</xsl:call-template>
	</select>
	:
	<select name="create_date[s]" autocomplete="off">
		<xsl:call-template name="__article_date_loop">
			<xsl:with-param name="var" select="0"/>
			<xsl:with-param name="max" select="59"/>
			<xsl:with-param name="selected" select="$second"/>
		</xsl:call-template>
	</select>
</xsl:template>

<xsl:template name="__article_date_loop">
	<xsl:param name="var"/>
	<xsl:param name="max"/>
	<xsl:param name="selected"/>
	<xsl:param name="month">0</xsl:param>
	<option value="{$var}">
		<xsl:if test="$var = $selected">
			<xsl:attribute name="selected">1</xsl:attribute>
		</xsl:if>
		<xsl:choose>
			<xsl:when test="$month='1'">
				<xsl:choose>
					<xsl:when test="$var='1'">Январь</xsl:when>
					<xsl:when test="$var='2'">Февраль</xsl:when>
					<xsl:when test="$var='3'">Март</xsl:when>
					<xsl:when test="$var='4'">Апрель</xsl:when>
					<xsl:when test="$var='5'">Май</xsl:when>
					<xsl:when test="$var='6'">Июнь</xsl:when>
					<xsl:when test="$var='7'">Июль</xsl:when>
					<xsl:when test="$var='8'">Август</xsl:when>
					<xsl:when test="$var='9'">Сентябрь</xsl:when>
					<xsl:when test="$var='10'">Октябрь</xsl:when>
					<xsl:when test="$var='11'">Ноябрь</xsl:when>
					<xsl:when test="$var='12'">Декабрь</xsl:when>
				</xsl:choose>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$var"/>
			</xsl:otherwise>
		</xsl:choose>
	</option>
	<xsl:if test = "$var &lt; $max">
		<xsl:call-template name="__article_date_loop">
			<xsl:with-param name="var" select="$var+1"/>
			<xsl:with-param name="max" select="$max"/>
			<xsl:with-param name="selected" select="$selected"/>
			<xsl:with-param name="month" select="$month"/>
		</xsl:call-template>
	</xsl:if>
</xsl:template>

</xsl:stylesheet>