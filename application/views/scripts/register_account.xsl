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
			name: "Поле Имя пустое",
			address: "Поле Адрес пустое",
			phone: "Поле Телефон пустое",
			email: "Поле E-mail пустое или заполнено не корректно",
		},
		onkeyup: false
	});
});
</script>]]>						
</xsl:variable>

<xsl:template match="data">
	<xsl:value-of select="$validate" disable-output-escaping="yes"/>
	
	<h1><xsl:apply-templates select="docinfo/name" /></h1>
	<ul class="tabs">
		<li>
			<a href="/register/myorders/">Заказы</a>
		</li>
		<li class="active">
			Редактировать данные
		</li>
		<li>
			<a href="/register/mybonus/">Бонусная программа</a>
		</li>
	</ul>
	<div class="content_block">
		<form method="post" action="/register/account/" id="kabinet_personal_info_form" class="text_block_form">
			<div class="errhold"></div>
			<p>
				Все поля, отмеченные <span class="asterisk">*</span>, обязательны для заполнения
			</p>
			<div>
				<label for="name">ФИО:<span class="asterisk">*</span></label>
				<input id="name" name="name" type="text" value="{member_data/name}"/>
			</div>
			<div>
				<label for="phone">Телефон для связи:<span class="asterisk">*</span></label>
				<input id="phone" name="phone" type="text" value="{member_data/telmob}"/>
			</div>
			<div>
				<label for="email">E-mail:<span class="asterisk">*</span></label>
				<input id="email" name="email" type="email" value="{member_data/email}"/>
			</div>
			<div>
				<label for="passwd">Пароль:<span class="asterisk">*</span></label>
				<input id="passwd" name="passwd" type="password"/>
			</div>
			<div>
				<label for="comment">Комментарий:</label>
				<textarea id="comment" name="comment"><xsl:value-of select="member_data/privateinfo"/></textarea>
			</div>
			<input type="submit" value="Сохранить" style="position:inherit; bottom:0; left:0;"/>
		</form>
	</div>

</xsl:template>
</xsl:stylesheet>