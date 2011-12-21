<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="utf-8" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />
<xsl:template match="/">
<!--Dsigned by templatesperfect.com-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Free Law Lawyers Template</title>
		<link href="/style/style.css" rel="stylesheet" type="text/css" />
	</head>

	<body>
		<center>
			<div class="outer">
				<div id="logo-bg">
					<div class="name">Law Template </div>
					<div class="tag">Slogan will come here</div>
				</div>
				<div id="headimage"></div>

				<!--Header end -->
				<div style="clear:left"></div>

				<div id="rc1"></div>
				
				<xsl:call-template name="_call">
					<xsl:with-param name="position">top</xsl:with-param>
				</xsl:call-template>
				<div class="breadcrumbs">
					<xsl:call-template name="_call">
						<xsl:with-param name="position">breadcrumbs</xsl:with-param>
					</xsl:call-template>
				</div>
				<!-- <div id="bg">
					<div class="toplinks">
						<a
							href="http://www.cmgtechnologies.com/free-css-templates/business71/index.html">Homepage</a>
					</div>
					<div class="sap">|</div>
					<div class="toplinks">
						<a
							href="http://www.cmgtechnologies.com/free-css-templates/business71/index.html">About us</a>
					</div>
					<div class="sap">|</div>
					<div class="toplinks">
						<a
							href="http://www.cmgtechnologies.com/free-css-templates/business71/index.html">Products</a>
					</div>
					<div class="sap">|</div>
					<div class="toplinks">
						<a
							href="http://www.cmgtechnologies.com/free-css-templates/business71/index.html">Services</a>
					</div>
					<div class="sap">|</div>
					<div class="toplinks">
						<a
							href="http://www.cmgtechnologies.com/free-css-templates/business71/index.html">Contact us</a>
					</div>
				</div> -->
				<div id="rc2"></div>

				<!--Top nav end -->
				<div style="clear:left"></div>
				<div id="outer2">

					<div id="content">
						<xsl:call-template name="_call"/>
					</div>
					<div id="left-nav">
						<xsl:call-template name="_call">
							<xsl:with-param name="position">left</xsl:with-param>
						</xsl:call-template>

						<div id="news-bg">
							<span class="heading">News and Updates</span>
							<div class="date">27.07.2009</div>
							<div class="news-txt">Lorem ipsum dolor sit amet, consectetuer
								adipiscing elit. Curabitur nibh. Vestibulum ante ipsum primis in
								faucibus orci luctus et ultrices posuere cubilia Curae;
								Vestibulum sapien enim, cursus in, aliquam sit amet, convallis
								eget, metus. Duis dui mi, varius at, lacinia eget, ullamcorper
								et, tortor. Pellentesque ac pede. Lorem ipsum dolor</div>
							<div>
								<div style="float:right;">
									<a href="#">Read More</a>
								</div>
							</div>
						</div>
					</div>

					<div style="clear:left"></div>
				</div>

				<!--Bottom part -->
				<div id="bottom">
					<p>
						&#169; 2011 <a href="http://grosstech.ru">Grosstech</a>
					</p>
				</div>
			</div>
		</center>
	</body>
</html>

</xsl:template>
</xsl:stylesheet>