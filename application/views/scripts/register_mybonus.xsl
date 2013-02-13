<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE xsl:stylesheet SYSTEM "symbols.ent">
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:import href="_main.xsl"/>
	<xsl:template match="data">
		<h1>
			<xsl:apply-templates select="docinfo/name"/>
		</h1>
		<div class="content_block">
			<ul class="tabs menu">
				<li>
					<a href="/register/myorders/">Заказы</a>
				</li>
				<li>
					<a href="/register/account/">Редактировать данные</a>
				</li>
				<li class="active">Бонусная программа</li>
			</ul>
			<div class="kabinet_tab">
				<xsl:apply-templates select="docinfo/txt"/>
				<div class="personal_status">
					Ваше накопление: <span class="sum">
							<xsl:value-of select="format-number(user_summ, '### ##0', 'european')"/>&#160;грн.</span> 
					<xsl:if test="present_discount/name != ''">		
					   | Ваш статус: 
						<a href="/ajax/popup/mode/userdiscount/id/{present_discount/@id}/" class="popup status">
							<xsl:attribute name="style">background: url("/images/usr_disc/<xsl:value-of select="present_discount/image_small/@src"/>") no-repeat scroll right top transparent; color: <xsl:value-of select="next_discount/color"/></xsl:attribute>
							<xsl:value-of select="present_discount/name"/>
						</a>
					</xsl:if>
				</div>
				<div class="personal_status next">
					<span>Следующий предел: <span class="sum">
							<xsl:value-of select="format-number(next_discount/@min, '### ##0', 'european')"/>&#160;грн.</span>
					</span>
					<a href="/ajax/popup/mode/userdiscount/id/{next_discount/@id}/" class="popup status">
						<xsl:attribute name="style">background: url("/images/usr_disc/<xsl:value-of select="next_discount/image_small/@src"/>") no-repeat scroll right top transparent; color: <xsl:value-of select="next_discount/color"/> </xsl:attribute>
						<xsl:value-of select="next_discount/name"/>
					</a>
				</div>
			</div>
		</div>
	</xsl:template>
</xsl:stylesheet>
