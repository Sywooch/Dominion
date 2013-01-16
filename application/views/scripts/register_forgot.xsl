<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE xsl:stylesheet SYSTEM "symbols.ent">
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:import href="_main.xsl"/>

<xsl:template match="error_messages">
  <xsl:value-of select="err_mess"/><br />
</xsl:template>

<xsl:template match="data">			
		<xsl:if test="error_messages!=''">
			<table width="40%" border="0" cellpadding="0" cellspacing="0" align="center">
			  <tr>
				<td height="30" align="center" valign="middle" class="error"><xsl:apply-templates select="error_messages"/></td>
			  </tr>
			</table>
		  </xsl:if>
		<form action="/register/forgot/" method="post">
			<div class="reg">
				<p class="regtitle">Все поля, <span class="star">отмеченные *</span> обязательны для заполнения.</p>
				<div class="both">
					<label for="login"><span class="star">*</span>Email:</label>
					<div class="input-holder nec" >
						<input type="text" id="login"  class="textinp" name="email" value="{email_err}"/>
					</div>
				</div>
				<div class="add-comment">
					<table>
						<tr>
							<td></td>
							<td></td>
							<td><input type="image" src="/i/send.png" /></td>
							<td></td>
						</tr>
					</table>
				</div>
			</div>
		</form>
</xsl:template>

</xsl:stylesheet>	