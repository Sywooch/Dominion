<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE xsl:stylesheet SYSTEM "symbols.ent">
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:import href="_main.xsl"/>

    <xsl:template match="search_result">
        <div class="teaser clearfix" id="pitm_{@item_id}">

            <div class="img_box_align">
                <div style="margin: 0 auto; width: {image_middle/@w}px;">
                    <a href="{href}">
                        <img alt="{brand_name} {name}" src="/images/it/{image_middle/@src}" width="{image_middle/@w}"
                             height="{image_middle/@h}"/>
                        <xsl:if test="@has_discount=1">
                            <span class="personal_product_status gold">
                                <xsl:attribute name="style">background: url("/images/usr_disc/<xsl:value-of
                                        select="sh_disc_img_small/@src"/>") no-repeat scroll 0 0 transparent;
                                </xsl:attribute>
                                User discount
                            </span>
                        </xsl:if>
                        <xsl:if test="discount_image/@src!=''">
                            <span class="sale">
                                <img src="/images/disc/{discount_image/@src}" alt="" width="{discount_image/@w}"
                                     height="{discount_image/@h}"/>
                            </span>
                        </xsl:if>
                    </a>
                </div>
            </div>

            <div class="main_content">
            <h3>
                <a href="{href}"><xsl:value-of select="brand_name"/>&#160;<xsl:value-of select="name"/>
                </a>
            </h3>
            <p>
                <xsl:value-of select="short_description" disable-output-escaping="yes"/>
            </p>
            <xsl:choose>
                <xsl:when test="@price1 &gt; 0">
                    <div class="price_box">
                        <span class="price old"><xsl:value-of select="format-number(@price, '### ##0', 'european')"/>&#160;<xsl:value-of
                                select="sname"/>
                        </span>
                        | <xsl:value-of select="format-number(@real_price, '### ##0', 'european')"/>&#160;<xsl:value-of
                            select="nat_sname"/>
                    </div>
                    <div class="price_box">
                        <span class="price personal">
                            <xsl:if test="@has_discount=1">
                                <xsl:attribute name="style">background: url("/images/usr_disc/<xsl:value-of
                                        select="sh_disc_img_big/@src"/>") no-repeat scroll 0 0 transparent;
                                </xsl:attribute>
                            </xsl:if>
                            <xsl:value-of select="format-number(@price1, '### ##0', 'european')"/>&#160;<xsl:value-of
                                select="sname"/>
                        </span>
                        | <xsl:value-of select="format-number(@real_price1, '### ##0', 'european')"/>&#160;<xsl:value-of
                            select="nat_sname"/>
                    </div>
                </xsl:when>
                <xsl:otherwise>
                    <div class="price_box">
                        <span class="price"><xsl:value-of select="format-number(@price, '### ##0', 'european')"/>&#160;<xsl:value-of
                                select="sname"/>
                        </span>
                        | <xsl:value-of select="format-number(@real_price, '### ##0', 'european')"/>&#160;<xsl:value-of
                            select="nat_sname"/>
                    </div>
                </xsl:otherwise>
            </xsl:choose>
            </div>
        </div>
    </xsl:template>

    <xsl:template name="section_url">
        /search/
        <xsl:if test="/page/data/query != ''"><xsl:value-of select="/page/data/query"/>/
        </xsl:if>
    </xsl:template>

    <xsl:template match="data">
        <h1>
            <xsl:value-of select="docinfo/name"/>
        </h1>
        <div class="chapter_products content_block">
            <xsl:choose>
                <xsl:when test="count(search_result) &gt; 0">
                    <xsl:apply-templates select="search_result"/>
                    <ul class="pager">
                        <xsl:apply-templates select="section"/>
                    </ul>
                </xsl:when>
                <xsl:otherwise>
                    <p>По Вашему запросу
                        <b>
                            <xsl:value-of select="query"/>
                        </b>
                        ничего не найдено
                    </p>
                </xsl:otherwise>
            </xsl:choose>

        </div>

    </xsl:template>
</xsl:stylesheet>