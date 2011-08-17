<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="utf-8" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />

<xsl:template match="root/module/item[_module_name='user' and _method_name='_admin']">
	В разработке
</xsl:template>

<xsl:template match="root/module/item[_module_name='user' and _method_name='form']" name="user_form">
	<xsl:variable name="admin_mode"><xsl:if test="/root/meta/admin_mode">admin.php</xsl:if></xsl:variable>
	<xsl:choose>
		<xsl:when test="/root/session/user_info">
			<div class="user_container logined">
				Вы вошли как <span><xsl:value-of select="/root/session/user_info/login"/></span>.
				<a class="logout" href="/{$admin_mode}?call=user.logout">Выйти</a>
			</div>
		</xsl:when>
		<xsl:otherwise>
			<form action="/{$admin_mode}?call=user.login" method="post">
				<div class="user_container">
					<div class="input_container">
						<p>
							<input placeholder="логин" name="login" autocomplete="off"/>
						</p>
						<p>
							<input placeholder="пароль" type="password" name="password"/>
						</p>
					</div>
					<xsl:if test="not(_config/registration) or config/registration!=''">
						<p>
							<a href="/?call=user.register_form">регистрация</a>
						</p>
					</xsl:if>
					<p>
						<input class="button" type="submit" value="Войти"/>
						<a href="/admin.php?call=user.reset_password" class="reset_password">забыли пароль?</a>
					</p>
				</div>
			</form>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template match="root/module/item[_module_name='user' and _method_name='login']">
	<xsl:if test="not(/root/module/item[_module_name='user' and _method_name='form'])">
		<xsl:call-template name="user_form"/>
	</xsl:if>
</xsl:template>

</xsl:stylesheet>