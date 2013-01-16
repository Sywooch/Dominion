<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE xsl:stylesheet SYSTEM "symbols.ent">
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:import href="_main.xsl"/>

<xsl:variable name="validate"><![CDATA[
<script type="text/javascript">
$().ready(function() { 
	$("#kabinet_personal_info_form").validate({
		errorLabelContainer: $("#kabinet_personal_info_form div.errhold"),
		rules: {
			captcha: {
				required: true,
				remote: "/ajax/caphainp/"
			},
			passwd: {
			  required: true,
			  minlength: 5
			},
			password_repeat: {
				required: true,
				minlength: 5,
				equalTo: "#passwd"
			},
			email: {
			  required: true,
			  email: true
			},
			name: {
				required: true,
				minlength: 2
			},
			address: {
				required: true,
				minlength: 2
			},
			phone: {
				required: true,
				minlength: 2
			}
		},
		messages: {
			passwd: {
			  required: "Пожалуйста, укажите пароль.",
			  minlength: "Пароль должен быть не менее 5 символво."
			},
			password_repeat: {
				required: "Поле Повтор пароля пустое",
				minlength: "Пароль должен быть не менее 5 символво.",
				equalTo: "Неверно указан повтор пароля"
			},
			name: "Поле Имя пустое",
			address: "Поле Адрес пустое",
			phone: "Поле Телефон пустое",
			email: "Поле E-mail пустое или заполнено не корректно",
			captcha: "Укажиет правильные символы на картинке."
		},
		onkeyup: false
	});
});
</script>]]>						
</xsl:variable>

<xsl:template match="data">				
	<h1><xsl:apply-templates select="docinfo/name" /></h1>
	
	<xsl:value-of select="$validate" disable-output-escaping="yes"/>
	
	<div class="content_block">
		<form method="post" action="/register/" id="kabinet_personal_info_form" class="text_block_form">
			<div class="errhold"></div>
			<p>
				Все поля, отмеченные <span class="asterisk">*</span>, обязательны для заполнения
			</p>
			<div>
				<label for="name">ФИО:<span class="asterisk">*</span></label>
				<input id="name" name="name" type="text"/>
			</div>
			<div>
				<label for="phone">Телефон для связи:<span class="asterisk">*</span></label>
				<input id="phone" name="phone" type="text"/>
			</div>
			<div>
				<label for="email">E-mail:<span class="asterisk">*</span></label>
				<input id="email" name="email" type="email"/>
			</div>
			<div>
				<label for="passwd">Пароль:<span class="asterisk">*</span></label>
				<input id="passwd" name="passwd" type="password"/>
			</div>
			<div>
				<label for="password_repeat">Повтор пароля:<span class="asterisk">*</span></label>
				<input id="password_repeat" name="password_repeat" type="password"/>
			</div>
			<div>
				<label for="comment">Комментарий:</label>
				<textarea id="comment" name="comment"></textarea>
			</div>
			<div class="captcha">
				<div class="clearfix">
					<label for="captcha_input">Впишите число:</label>
					<span id="capcha"></span>
					<input id="captcha" name="captcha" type="text"/>
					<script>reload();</script>
				</div>
				<a href="#" class="captcha_refresh_button" onclick="reload(); return false;">Обновить</a>
			</div>
			<input type="submit" value="Сохранить"/>
		</form>
	</div>
</xsl:template>

</xsl:stylesheet>	