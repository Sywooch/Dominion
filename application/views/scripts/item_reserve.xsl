<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE xsl:stylesheet SYSTEM "symbols.ent">
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:import href="_base.xsl"/>

 <xsl:template match="//page"> 
	<script type="">
		$("#reserve_form").validate({
			errorLabelContainer: $("#reserve_form div.errhold"),
			submitHandler: function(form) {
				data = $(form).serialize();
				var utl = $(form).attr('action');
				$.post(utl, data, function(data){
					$('.modal_dialog_box').html(data);
					setTimeout("$('#fancybox-close').trigger('click')", 2000);
				});
			},
			rules: {
			  reserve_email: {
				required: true,
				minlength: 2
			  }
			},
			messages: {
			  reserve_email: "Поле E-mail пустое",
			},
			onkeyup: false
		  });
	</script>
	<div class="modal_dialog_box">
		<form id="reserve_form" action="/item/reserve/id/{item_id}/" method="post" class="dialog_content">
			<input type="hidden" name="reserve_error" id="reserve_error" value="{error}"/>
			<div class="errhold"></div>
			<div>
				<xsl:value-of select="system_message" disable-output-escaping="yes"/>
			</div>
			<div>
				<label for="reserve_email">E-mail</label>
				<input id="reserve_email" name="reserve_email" type="email"/>
			</div>
			<input type="submit" value="Сделать заявку"/>
		</form>
	</div>
 </xsl:template>

</xsl:stylesheet>