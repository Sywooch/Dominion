<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE xsl:stylesheet SYSTEM "../symbols.ent">
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template name="_top_banners_show_banners">
		<xsl:if test="count(banner_top) &gt; 0">
			<div id="header_slider">
				<xsl:if test="count(banner_top) &gt; 1">
					<ul class="slider_buttons">
						<xsl:apply-templates select="banner_top" mode="slider_buttons"/>
					</ul>
				</xsl:if>
				<xsl:choose>
					<xsl:when test="count(banner_top) = 1">
						<xsl:call-template name="_top_banners_without_wrapper"/>
					</xsl:when>
					<xsl:otherwise>
						<xsl:call-template name="_top_banners_without_wrapper"/>
					</xsl:otherwise>
				</xsl:choose>
				<!--<ul class="slider_holder">
					<xsl:apply-templates select="banner_top"/>
				</ul>-->
			</div>
		</xsl:if>
	</xsl:template>
	<xsl:template name="_top_banners_with_wrapper">
		<ul class="slider_holder">
			<xsl:apply-templates select="banner_top"/>
		</ul>
	</xsl:template>
	<xsl:template name="_top_banners_without_wrapper">
		<xsl:apply-templates select="banner_top"/>
	</xsl:template>
	<xsl:template match="banner_top" mode="slider_buttons">
		<li>
			<a href="#">
				<xsl:value-of select="position()"/>
			</a>
		</li>
	</xsl:template>
	<xsl:template match="banner_top[@type = '0']">
		<!-- Картинка -->
		<li>
			<xsl:choose>
				<xsl:when test="url!=''">
					<a href="{url}" target="_self">
						<xsl:if test="@newwin='1'">
							<xsl:attribute name="target">_blank</xsl:attribute>
						</xsl:if>
						<img src="/images/bn/{image/@src}" alt=""/>
					</a>
				</xsl:when>
				<xsl:otherwise>
					<img src="/images/bn/{image/@src}" alt=""/>
				</xsl:otherwise>
			</xsl:choose>
		</li>
		<!-- Картинка -->
	</xsl:template>
	<xsl:template match="banner_top[@type = '1']">
		<!-- Флеш -->
		<object id="flash_banner" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="{image/@w}" height="{image/@h}">
			<param name="movie" value="/images/bn/{image/@src}"/>
			<!--[if !IE]>-->
			<object type="application/x-shockwave-flash" data="/images/bn/{image/@src}" width="{image/@w}" height="{image/@h}">
				<param value="transparent" name="wmode"/>
			</object>
			<!--<![endif]-->
		</object>
		<!-- Флеш -->
	</xsl:template>
	
</xsl:stylesheet>
