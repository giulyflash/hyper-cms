<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="utf-8" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />
<xsl:template match="/">
<html>
<head>
	<meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
	<link href="/css/main.css" rel="stylesheet" type="text/css"/>
	<script type="text/javascript" src="/extensions/jquery/jquery-1.5.min.js"/>
	<script type="text/javascript" src="/extensions/jquery/jquery.form.js"/>
	<script type="text/javascript" src="/js/board.js"/>
	<script type="text/javascript" src="/js/dragndrop.js"/>
	<script type="text/javascript" src="/js/main.js"/>
	<script type="text/javascript" src="/js/simple_chat.js"/>		
</head>
<body>
	<div class="wrapper" id="wrapper">
		<div class="left">
			<div class="header">Доступные доски<span>▾</span></div>
			<!-- ▴▾ -->
			<div class="popup">
				<div id="board_container">
					<xsl:call-template name="call">
						<xsl:with-param name="class">board</xsl:with-param>
						<xsl:with-param name="method">get_data</xsl:with-param>
					</xsl:call-template>
				</div>
				<div id="create_board">Новая доска</div>
				<div id="output"></div>
			</div>
		</div>
		<div class="right">
			<div class="logout">
				<a href="/?call=user.logout">выйти</a>
			</div>
			<div class="header">Инструменты<span>▾</span></div>
			<div class="popup">
				<xsl:call-template name="call">
					<xsl:with-param name="class">board</xsl:with-param>
					<xsl:with-param name="method">get_instruments</xsl:with-param>
				</xsl:call-template>
				<div class="chat">
					<h4>Чат</h4>
					<div class="chat_cont">
						<ul>
						</ul>
					</div>
					<textarea></textarea>
					<button>Отправить</button>
				</div>
			</div>
		</div>
		<div class="center" id="center">
			<xsl:apply-templates select="root/errors"/>
		</div>
	</div>
</body>
</html>
</xsl:template>
</xsl:stylesheet>