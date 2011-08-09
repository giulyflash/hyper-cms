<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="utf-8" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />

<xsl:template match="/root/module/item" priority="-0.75">
	<xsl:value-of select="_module_name"/>.<xsl:value-of select="_method_name"/>: шаблон не найден<br/>
	<a href="{/root/session/call/item[position()=2]}">Назад</a><br/>
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
	<!--TODO test this -->
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
	<xsl:if test="root/error/*/*/item">
		<a href="{/root/session/call/item[position()=2]}">Назад</a>
	</xsl:if>
</xsl:template>

<xsl:template name="_message">
	<xsl:for-each select="root/message/item">
		<div class="message_box">
			<xsl:value-of select="." disable-output-escaping="yes"/>
		</div>
	</xsl:for-each> 
</xsl:template>

<xsl:template name="shownavigation">
	<xsl:param name="obj_count"/>
	<xsl:param name="page_size"/>
	<xsl:param name="page"/>
	<xsl:param name="url"/>
	<!--<xsl:param name="max_page_count" select="5"/>-->
	<xsl:param name="page_indent" select="3"/>
	<!--<xsl:value-of select="concat($obj_count,'|',$page_size,'|',$page,'|',$url)"/>-->
	<xsl:variable name="page_new"><xsl:if test="not($page) or $page=''">1</xsl:if><xsl:value-of select="$page"/></xsl:variable>
	<xsl:variable name="page_count" select="ceiling($obj_count div $page_size)"/>
	<xsl:variable name="overflow_check" select="(-2*$page_indent + $page_count) &gt; 0"/>
	<xsl:variable name="page_start">
		<xsl:choose>
			<xsl:when test="($page_count - $page_new) &lt; $page_indent and $overflow_check">
				<xsl:value-of select="-2*$page_indent + $page_count"/>
			</xsl:when>
			<xsl:when test="(-1*$page_indent+$page_new) &gt; 0  and $overflow_check">
				<xsl:value-of select="-1*$page_indent+$page_new"/>
			</xsl:when>
			<xsl:otherwise>1</xsl:otherwise>
		</xsl:choose>
	</xsl:variable>
	<xsl:if test="$obj_count &gt; $page_size">
		<div class="pagenavigation">
			<xsl:if test="$page_new &gt; 1">
				<span class="toback" alt="назад" title="назад">
					<a href="{$url}&amp;page={-1+$page_new}">
						&lt;
					</a>
				</span>
				<xsl:if test="$page_start &gt; 1">
					<span class="toback" alt="1" title="1">
						<a href="{$url}&amp;page=1">1</a>
					</span>...
				</xsl:if>
			</xsl:if>
			<xsl:call-template name="show_nav">
				<xsl:with-param name="page_count" select="$page_count"/>
				<xsl:with-param name="url" select="$url"/>
				<xsl:with-param name="page" select="$page_start"/>
				<xsl:with-param name="page_lim" select="$page_start+2*$page_indent"/>
				<xsl:with-param name="curr_page" select="$page_new"/>
			</xsl:call-template>
			<xsl:if test="$page_new &lt; $page_count">
				<xsl:if test="$page_start+2*$page_indent &lt; $page_count">
					...<span class="forward" alt="{$page_count}" title="{$page_count}">
						<a href="{$url}&amp;page={$page_count}">
							<xsl:value-of select = "$page_count"/>
						</a>
					</span>
				</xsl:if>
				<span class="forward" alt="вперед" title="вперед">
					<a href="{$url}&amp;page={$page_new+1}">
						&gt;
					</a>
				</span>
			</xsl:if>
		</div>
	</xsl:if>
</xsl:template>

<xsl:template name="show_nav">
	<xsl:param name="url"/>
	<xsl:param name="page" select="1"/>
	<xsl:param name="page_count"/>
	<xsl:param name="curr_page"/>
	<xsl:param name="page_lim"/>
	<xsl:choose>
		<xsl:when test="$page=$curr_page">
			<span class="curr">
				<xsl:value-of select="$page"/>
			</span>
		</xsl:when>
		<xsl:otherwise>
			<span>
				<a href="{$url}&amp;page={$page}">
					<xsl:value-of select="$page"/>
				</a>
			</span>
		</xsl:otherwise>
	</xsl:choose>
	<xsl:if test="$page_count &gt; $page and $page_lim &gt; $page">
		<xsl:call-template name="show_nav">
			<xsl:with-param name="page" select="$page+1"/>
			<xsl:with-param name="page_count" select="$page_count"/>
			<xsl:with-param name="url" select="$url"/>
			<xsl:with-param name="curr_page" select="$curr_page"/>
			<xsl:with-param name="page_lim" select="$page_lim"/>
		</xsl:call-template>
	</xsl:if>
</xsl:template>

</xsl:stylesheet>