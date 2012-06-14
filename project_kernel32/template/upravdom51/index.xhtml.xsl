<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="utf-8" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />
<xsl:template match="/">
<html><head>
	<link href="/template/upravdom51/img/template.css" rel="stylesheet" type="text/css"/>
	<xsl:call-template name="_head"/>
</head>
<body>

<div id="templatemo_document_wrapper">
	<table id="templatemo_wrapper">
		<tr id="templatemo_menu">
			<td></td>
			<td>
				<xsl:call-template name="_call">
					<xsl:with-param name="position">top</xsl:with-param>
				</xsl:call-template>
				<!--<ul>
					<li><a href="http://www.templatemo.com/templates/templatemo_324_green_home/index.html" class="current">Home</a></li>
					<li><a href="http://www.templatemo.com/templates/templatemo_324_green_home/services.html">Services</a></li>
					<li><a href="http://www.templatemo.com/templates/templatemo_324_green_home/blog.html">Blog</a></li>
					<li><a href="http://www.templatemo.com/templates/templatemo_324_green_home/gallery.html">Gallery</a></li>
					<li><a href="http://www.templatemo.com/templates/templatemo_324_green_home/contact.html">Contact</a></li>
				</ul>-->
			</td>
			<td></td>
		</tr> <!-- end of templatemo_menu -->
		<tr id="templatemo_header">
			<td></td>
			<td>
				<!--<div id="site_title">
					<a href="/">
						<h1>Управляющая <span>Компания</span></h1>
					</a>
				</div>-->    
				<div id="header_content">
					<!--<p>г. Мурманск, ул. Василия Пупкина д. 19</p>-->
					<xsl:call-template name="_call">
						<xsl:with-param name="position">head</xsl:with-param>
					</xsl:call-template>
					<!--<a href="#" class="more">Контакты</a>-->
				</div>
			</td>
			<td></td>
		</tr>
		
		<tr id="templatemo_main">
			<td></td>
			<td class="content">
				<xsl:call-template name="_call"/>
			</td>
			<td></td>
		</tr>
		
		<tr id="templatemo_footer_wrapper">
			<td id="templatemo_footer" colspan="3">
				<xsl:call-template name="_call">
					<xsl:with-param name="position">bottom</xsl:with-param>
				</xsl:call-template>
				<!--<a href="http://www.templatemo.com/templates/templatemo_324_green_home/index.html">Home</a> | <a href="http://www.templatemo.com/templates/templatemo_324_green_home/services.html">Services</a> | <a href="http://www.templatemo.com/templates/templatemo_324_green_home/blog.html">Blog</a> | <a href="http://www.templatemo.com/templates/templatemo_324_green_home/gallery.html">Gallery</a> | <a href="http://www.templatemo.com/templates/templatemo_324_green_home/contact.html">Contact</a><br><br>
				Copyright © 2048 <a href="http://www.templatemo.com/templates/templatemo_324_green_home/#">Your Company Name</a> | <a href="http://www.iwebsitetemplate.com/" target="_parent">Website Templates</a> by <a href="http://www.templatemo.com/" target="_parent">Free CSS Templates</a>-->
			</td>
		</tr>
	</table> <!-- end of wrapper -->
</div>

</body>
</html>
<!-- This document saved from http://www.templatemo.com/templates/templatemo_324_green_home/ -->
</xsl:template>
</xsl:stylesheet>
