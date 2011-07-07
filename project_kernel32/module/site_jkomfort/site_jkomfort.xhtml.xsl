<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="utf-8" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />

<xsl:template match="root/module/item[_module_name='site_jkomfort' and _method_name='_admin']">
	<ul>
		<li>
			<a href="admin.php?call=site_jkomfort.generate&amp;is_default=1">список домов</a>
		</li>
		<li>
			<a href="admin.php?call=site_jkomfort.generate_works&amp;is_default=1">список услуг по домам</a>
		</li>
	</ul>
</xsl:template> 

<xsl:template match="root/module/item[_module_name='site_jkomfort' and (_method_name='generate' or _method_name='generate_works')]">
	<form method="post" action="admin.php?call=site_jkomfort.{_method_name}" enctype="multipart/form-data">
		<table>
			<tr>
				<td>Путь к Файлу со списком:</td>
				<td>
					<input type="file" name="path"/>
				</td>
			</tr>
			<xsl:choose>
				<xsl:when test="_method_name='generate'">
					<tr>
						<td>Имя статьи со списком домов:</td>
						<td>
							<input type="text" name="main_article_title" value="Примерная расшифровка платы" style="width:100%"/>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<a href="/module/site_jkomfort/house_list_template.xls">Скачать шаблон списка домов</a>
						</td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
					<tr>
						<td colspan="2">
							<a href="/module/site_jkomfort/work_list_template.xls">Скачать шаблон списка работ</a>
						</td>
					</tr>
					
				</xsl:otherwise>
			</xsl:choose>
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