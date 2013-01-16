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
	<form action="https://merchant.webmoney.ru/lmi/payment.asp" method="post" id="checkout">
		<input type="hidden" name="LMI_PAYEE_PURSE" id="purse" value="{purse_wmz}"/>
		<input type="hidden" name="LMI_PAYMENT_AMOUNT" value="{zakaz_cost}"/>
		<input type="hidden" name="LMI_SIM_MODE" value="2"/> 
		<input type="hidden" name="LMI_RESULT_URL" value="{site_url}cart/paymentresult/"/> 
		<input type="hidden" name="LMI_SUCCESS_URL" value="{site_url}cart/success/"/> 
		<input type="hidden" name="LMI_SUCCESS_METHOD" value="post"/> 
		<input type="hidden" name="LMI_FAIL_URL" value="{site_url}cart/fail/"/>  
		<input type="hidden" name="LMI_FAIL_METHOD" value="post"/>
		<input type="hidden" name="LMI_PAYMENT_DESC" value="Goods payment"/>   
		<input type="hidden" name="LMI_PAYMENT_NO" value="{payment_no}" />
	</form>
	
<script>document.getElementById('checkout').submit();</script>
</xsl:template>

</xsl:stylesheet>	