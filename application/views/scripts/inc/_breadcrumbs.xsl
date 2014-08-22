<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE xsl:stylesheet SYSTEM "../symbols.ent">
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                xmlns:xsL="http://www.w3.org/1999/XSL/Transform">
    <xsl:template name="breadcrumbs">

        <xsl:if test="count(breadcrumbs) &gt; 0">
            <ul class="breadcrumb">
                <li>
                    <a href="/" class="pseudo">
                        <span>Главная</span>
                    </a>
                </li>

                <xsL:choose>
                    <xsl:when test="count(//breadcrumbs/breadcrumbs) &gt; 0">
                        <xsl:apply-templates select="//breadcrumbs/breadcrumbs" mode="new_catalog">
                            <xsl:sort select="position()" data-type="number" order="descending"/>
                        </xsl:apply-templates>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:apply-templates select="//breadcrumbs" mode="new_catalog">
                            <xsl:sort select="position()" data-type="number" order="ascending"/>
                        </xsl:apply-templates>
                    </xsl:otherwise>
                </xsL:choose>
            </ul>
        </xsl:if>

    </xsl:template>

    <xsl:template match="breadcrumbs" mode="new_catalog">
        <li>
            <xsl:choose>
                <xsl:when test="current()[url]">
                    <a href="{url}" class="pseudo">
                        <span>
                            <xsl:value-of select="name"/>
                        </span>
                    </a>
                </xsl:when>
                <xsl:otherwise>
                    <span>
                        <xsl:value-of select="name"/>
                    </span>
                </xsl:otherwise>
            </xsl:choose>
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