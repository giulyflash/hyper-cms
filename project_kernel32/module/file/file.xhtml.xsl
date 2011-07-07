<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="utf-8" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />

<xsl:template match="root/module/item[_module_name='file' and _method_name='_admin']">
	В разработке
</xsl:template>

<xsl:template match="root/module/item[_module_name='file' and _method_name='upload']">
	<!-- <xsl:call-template name="error"/>
	<xsl:call-template name="message"/> -->
	<xsl:for-each select="item">
		<xsl:call-template name="show_file"/>
	</xsl:for-each>
</xsl:template>

<xsl:template match="root/module/item[_module_name='file' and _method_name='get_list']">
	<script type="text/javascript">
		no_files_msg="<xsl:call-template name="no_files_msg"/>";
	</script>
	<div id="_background" tabindex="-1"></div>
	<form id="_wysiwyg_form">
		<div>
			<p>
				<span>Вставить в текстовый редактор:</span>
			</p>
			<p id="_href2wisiwig">
				<input type="text"></input><button>Ссылка</button>
			</p>
			<p id="_pdf2wisiwig">
				<input type="text"></input><button>PDF</button>
			</p>
			<p id="_img2wisiwig">
				<button>Картинка</button>
			</p>
			<p id="_preview2wisiwig">
				<button>Маленькая картинка</button>
			</p>
			<p id="_video2wisiwig">
				<button>Видеоплеер</button>
			</p>
		</div>
		<button class="cancel">Отмена</button>
	</form>
	<div id="article_file_list">
		<xsl:choose>
		<xsl:when test="item">
			<xsl:for-each select="item">
				<xsl:call-template name="show_file"/>
			</xsl:for-each>
		</xsl:when>
		<xsl:otherwise>
			<xsl:call-template name="no_files_msg" />
		</xsl:otherwise>
		</xsl:choose>		
	</div>
	<br/>
	<form id="article_upload_file" action="index.php" method="post" enctype="multipart/form-data">
		<div class="fieldset flash" id="fsUploadProgress">
		<span class="legend">Очередь загрузки</span>
		</div>
	<div id="divStatus">0 файлов загружено</div>
		<div>
			<span id="spanButtonPlaceHolder"></span>
			<input id="btnCancel" type="button" value="Cancel All Uploads" onclick="swfu.cancelQueue();" disabled="disabled" style="margin-left: 2px; font-size: 8pt; height: 29px;" />
		</div>
	</form>
	<!-- 
	<br/>
	<input id="video_input"/>
	<input type="submit" value="Добавить видео" onclick="add_video('video_input','article_admin_uploadSuccess')"/> -->
</xsl:template>

<xsl:template name="show_file">
<div class="article_file_2_wysiwyg" id="file2wisiwig_{id}">
	<xsl:if test="position()!=1">
		<p class="delimiter"></p>
	</xsl:if>
	<xsl:if test="name">
		<h3>
			<xsl:value-of select="concat(name,'.',extension)"/>
		</h3>
	</xsl:if>
	<xsl:if test="thumb_path" >
		<img ondragend="javascript:file2wysiwyg_dialog('{path}','{thumb_path}','{internal_type}','{name}'); return false;" src="{thumb_path}" title="{path}" alt="{path}"/>
		<br/>
	</xsl:if>
	<a href="#" onclick="javascript:file2wysiwyg_dialog('{path}','{thumb_path}','{internal_type}','{name}'); return false;">В редактор</a>|
	<a href="#" onclick="javascript:remove_file('{id}','file2wisiwig_{id}'); return false;">удалить</a>
</div>
</xsl:template>

<xsl:template name="no_files_msg">Список файлов пуст.</xsl:template>

</xsl:stylesheet>