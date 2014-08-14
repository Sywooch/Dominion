<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE xsl:stylesheet SYSTEM "../symbols.ent">
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template name="breadcrumbs">

        <xsl:if test="count(breadcrumbs) &gt; 0">
            <ul class="breadcrumb">
                <li>
                    <a href="/" class="pseudo">
                        <span>Главная</span>
                    </a>
                </li>

                <xsl:apply-templates select="//breadcrumbs/crumbs[url]" mode="new_catalog"/>
                <xsl:apply-templates select="//breadcrumbs/crumbs[not(url)]" mode="last"/>
            </ul>
        </xsl:if>

    </xsl:template>

    <xsl:template match="crumbs" mode="new_catalog">
        <li>
            <a href="{url}" class="pseudo">
                <span>
                    <xsl:value-of select="name"/>
                </span>
            </a>
        </li>
    </xsl:template>

    <xsl:template match="crumbs" mode="last">
        <li>
            <xsl:value-of select="name"/>
        </li>
    </xsl:template>


    <xsl:template match="breadcrumbs">
        <xsl:if test="position()!=last()">
            <li>
                <a class="pseudo" href="javascript:void(0);">
                    <xsl:if test="count(breadcrumbs) = 0">
                        <xsl:attribute name="href">
                            <xsl:value-of select="url"/>
                        </xsl:attribute>
                    </xsl:if>
                    <span>
                        <xsl:value-of select="name"/>
                    </span>
                </a>
                <xsl:if test="count(breadcrumbs) &gt; 0">
                    <div class="dialog_box breadcrumb">
                        <a href="#" class="close_icon">Close</a>
                        <ul class="menu dialog_content">
                            <xsl:apply-templates select="breadcrumbs" mode="sub"/>
                        </ul>
                    </div>
                </xsl:if>
            </li>
        </xsl:if>
    </xsl:template>

    <xsl:template match="breadcrumbs" mode="sub">
        <li>
            <a href="{url}">
                <xsl:if test="@id='brands'">
                    <xsl:attribute name="style">color: #ED1C24</xsl:attribute>
                </xsl:if>
                <xsl:value-of select="name"/>
            </a>
        </li>
    </xsl:template>

</xsl:stylesheet>