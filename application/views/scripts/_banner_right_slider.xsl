<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE xsl:stylesheet SYSTEM "symbols.ent">
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="banner_right" mode="slider_buttons">
	<li><a href="#"><xsl:value-of select="position()"/></a></li>	
</xsl:template>

<xsl:template match="banner_right">
	<li>
		<xsl:variable name="target">
			<xsl:choose>
				<xsl:when test="@newwin='1'">_blank</xsl:when>
				<xsl:otherwise>_self</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
		<xsl:choose>
			<xsl:when test="@type='0'">
				<!-- Картинка -->
				<xsl:choose>
					<xsl:when test="url!=''">
						<a href="{url}" target="{$target}">
							<img src="/images/bn/{image/@src}" alt="" />
						</a>
					</xsl:when>
					<xsl:otherwise>
						<img src="/images/bn/{image/@src}" alt="" />
					</xsl:otherwise>
				</xsl:choose>
				<!-- Картинка -->
			</xsl:when>
			<xsl:when test="@type='1'">
				<!-- Флеш -->
				<xsl:choose>
					<xsl:when test="url!=''">
						<a href="{url}" target="{$target}" title="{alt}">
							<object type="application/x-shockwave-flash" data="/images/bn/{image/@src}"
								width="{image/@w}" height="{image/@h}">
								<param name="movie" value="/images/bn/{image/@src}" />
								<param value="transparent" name="wmode" />
							</object>
						</a>
					</xsl:when>
					<xsl:otherwise>
						<div style="z-index:1">
							<object type="application/x-shockwave-flash" data="/images/bn/{image/@src}"
								width="{image/@w}" height="{image/@h}">
								<param name="movie" value="/images/bn/{image/@src}" />
								<param value="transparent" name="wmode" />
							</object>
						</div>
					</xsl:otherwise>
				</xsl:choose>
				<!-- Флеш -->
			</xsl:when>
			<xsl:otherwise>
				<xsl:choose>
					<xsl:when test="description!=''">
						<xsl:apply-templates select="description"/>
					</xsl:when>
					<xsl:otherwise>
						<xsl:apply-templates select="alt" />
					</xsl:otherwise>
				</xsl:choose>
			</xsl:otherwise>
		</xsl:choose>
	</li>	
</xsl:template>


</xsl:stylesheet>