<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE xsl:stylesheet SYSTEM "../symbols.ent">
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="catalogue-menu" mode="menu">

        <script type="text/javascript" src="/js/menu.js"></script>

        <div class="catalog-button-wrap">
            <div>
                <ul class="nav first-level">
                    <li>
                        <a class="catalog-button">
                            <span>Каталог товаров</span>
                        </a>

                        <ul class="second-level">
                            <xsl:apply-templates select="catalogue" mode="parent"/>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>

    </xsl:template>

    <!--Apply template catalogue parent-->
    <xsl:template match="catalogue" mode="parent">

        <li class="" catalog_id="{@catalog_id}" xid="parentLi">

            <a href="{url}" class="parent">
                <span>
                    <xsl:value-of select="name"/>
                </span>
            </a>

            <div class="third-level">
                <div class="third-level-wraper"
                     style="background-image:url({image_menu}); width: {image_menu/@width}; height: {image_menu/@height}">
                    <div class="col">
                        <xsl:apply-templates select="catalogue[position() mod 3 = 1]" mode="multi"/>
                    </div>
                    <div class="col">
                        <xsl:apply-templates select="catalogue[position() mod 3 = 2]" mode="multi"/>
                    </div>
                    <div class="col last">
                        <xsl:apply-templates select="catalogue[position() mod 3 = 0]" mode="multi"/>
                    </div>
                </div>
            </div>
        </li>
    </xsl:template>

    <!--Generate catalogue child-->
    <xsl:template match="catalogue" mode="child_one">
        <li catalog_id="{@catalog_id}">
            <a href="{url}">
                <xsl:value-of select="name"/>
            </a>
        </li>
    </xsl:template>

    <xsl:template match="catalogue" mode="multi">
        <div class="catalog-box">
            <h3>
                <xsl:value-of select="name"/>
            </h3>
            <ul>
                <xsl:apply-templates select="catalogue" mode="child_one"/>
            </ul>
            <xsl:if test="count(current()/catalogue) &gt; 8">
                <span class="show-more">Показать еще</span>
            </xsl:if>
        </div>
    </xsl:template>

</xsl:stylesheet>