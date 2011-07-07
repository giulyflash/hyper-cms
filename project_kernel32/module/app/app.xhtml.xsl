<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="utf-8" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />

<xsl:template match="/root/module/item" priority="-0.75">
	<xsl:value-of select="_module_name"/>.<xsl:value-of select="_method_name"/>: шаблон не найден<br/>
</xsl:template>

<xsl:template name="_call">
	<xsl:param name="position"><xsl:value-of select="root/meta/app_config/main_position_name"/></xsl:param>
	<xsl:if test="$position = root/meta/app_config/main_position_name">
		<xsl:call-template name="_error"/>
		<xsl:call-template name="_message"/>
	</xsl:if>
	<xsl:apply-templates select="root/module/item[_position=$position]"/>
</xsl:template>

<xsl:template name="_word">
	<xsl:param name="module"/>
	<xsl:param name="method"/>
	<xsl:param name="word"/>
	<xsl:variable name="path">/root/lang/<xsl:value-of select="$module"/>/<xsl:value-of select="$method"/>/<xsl:value-of select="$word"/></xsl:variable>
	<xsl:variable name="path_all">/root/lang/<xsl:value-of select="$module"/>/<xsl:value-of select="/root/meta/app_config/_for_all"/>/<xsl:value-of select="$word"/></xsl:variable>
	<xsl:value-of select="$path"/>
	<xsl:choose>
		<xsl:when test="$path">
			<xsl:value-of select="$path"/>
		</xsl:when>
		<xsl:otherwise>
			<xsl:value-of select="$path_all"/>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template name="_head">
	<xsl:for-each select="root/include/item">
		<xsl:value-of select="." disable-output-escaping="yes"/>
	</xsl:for-each>
	<xsl:if test="root/meta/app_config/page_title">
		<title><xsl:value-of select="root/meta/app_config/page_title"/></title>
	</xsl:if>
	<script type="text/javascript">
	function getCookie(c_name)
	{		
	if (document.cookie.length>0)
	  {
 		c_start=document.cookie.indexOf(c_name + "=");
 		if (c_start!=-1)
	    {
   		c_start=c_start + c_name.length+1;
	    c_end=document.cookie.indexOf(";",c_start);
   		if (c_end==-1) c_end=document.cookie.length;
	    return unescape(document.cookie.substring(c_start,c_end));
   		}
	  }
	return "";
	}
	var PHPSESSID = getCookie('PHPSESSID');
	</script>
</xsl:template>

<xsl:template name="_error">
	<xsl:for-each select="root/error/*/*/item">
		<div class="error_box">
			Error: <xsl:value-of select="class"/>
			<xsl:if test="method">.<xsl:value-of select="method"/></xsl:if>: 
			<xsl:if test="line and line!=''">
				line <xsl:value-of select="line"/>,
			</xsl:if>
			<xsl:value-of select="text" disable-output-escaping="yes"/>
		</div>
	</xsl:for-each> 
</xsl:template>

<xsl:template name="_message">
	<xsl:for-each select="root/message/item">
		<div class="message_box">
			<xsl:value-of select="." disable-output-escaping="yes"/>
		</div>
	</xsl:for-each> 
</xsl:template>

</xsl:stylesheet>