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
                <div class="third-level-wraper" style="background-image:url({image_menu}); width: {image_menu/@width}; height: {image_menu/@height}">
                    <xsl:choose>
                        <xsl:when test="boolean(catalogue/catalogue[count(*) > 0])">
                            <xsl:apply-templates select="catalogue" mode="multi"/>
                        </xsl:when>
                        <xsl:otherwise>
                            <div class="col">
                                <div class="catalog-box">
                                    <ul>
                                        <xsl:apply-templates select="catalogue" mode="child_one"/>
                                    </ul>
                                    <span class="show-more">Показать еще</span>
                                </div>
                            </div>
                        </xsl:otherwise>
                    </xsl:choose>
                </div>
            </div>
        </li>
    </xsl:template>

    <!--Apply template catalogue parent-->
    <!--<xsl:template match="catalogue" mode="parent">-->
    <!--<li class="" catalog_id="{@catalog_id}">-->

    <!--<a href="{url}" class="parent">-->
    <!--<span>-->
    <!--<xsl:value-of select="name"/>-->
    <!--</span>-->

    <!--<div class="third-level">-->
    <!--<div class="third-level-wraper" style="background-image:url(i/catalog_img.jpg)">-->
    <!--&lt;!&ndash;<xsl:apply-templates select="catalogue/catalogue" mode="child_one"/>&ndash;&gt;-->
    <!--&lt;!&ndash;<xsl:apply-templates select="catalogue" mode="child_one"/>&ndash;&gt;-->
    <!--<xsl:choose>-->
    <!--<xsl:when test="boolean(catalogue/catalogue/catalogue)">-->
    <!--&lt;!&ndash;<xsl:apply-templates select="catalogue">&ndash;&gt;-->
    <!--</xsl:when>-->
    <!--<xsl:otherwise>-->
    <!--<xsl:apply-templates select="catalogue" mode="child_one"/>-->
    <!--</xsl:otherwise>-->
    <!--</xsl:choose>-->
    <!--</div>-->
    <!--</div>-->
    <!--</a>-->
    <!--</li>-->
    <!--</xsl:template>-->

    <!--Generate catalogue child-->
    <xsl:template match="catalogue" mode="child_one">
        <li catalog_id="{@catalog_id}">
            <a href="{url}">
                <xsl:value-of select="name"/>
            </a>
        </li>
    </xsl:template>

    <xsl:template match="catalogue" mode="multi">
        <div class="col">

            <div class="catalog-box">
                <h3>
                    <xsl:value-of select="name"/>
                </h3>
                <ul>
                    <xsl:apply-templates select="catalogue" mode="child_one"/>
                </ul>
                <span class="show-more">Показать еще</span>
            </div>
        </div>
    </xsl:template>

    <!--<xsl:template name="catalog_button">-->

    <!--<script type="text/javascript" src="/js/menu.js"></script>-->

    <!--<div class="catalog-button-wrap">-->
    <!--<div>-->
    <!--<ul class="nav first-level">-->
    <!--<li>-->
    <!--<a class="catalog-button">-->
    <!--<span>Каталог товаров</span>-->
    <!--</a>-->

    <!--<ul class="second-level">-->
    <!--<li>-->
    <!--<a href="#">-->
    <!--<span>Крупная бытовая техника</span>-->
    <!--</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">-->
    <!--<span>Встраиваемая техника для кухни</span>-->
    <!--</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">-->
    <!--<span>Ноутбуки и компьютерная техника</span>-->
    <!--</a>-->
    <!--<div class="third-level">-->
    <!--<div class="third-level-wraper" style="background-image:url(i/catalog_img.jpg)">-->
    <!--<div class="col">-->
    <!--<div class="catalog-box">-->
    <!--<h3>Компьютерная техника</h3>-->
    <!--<ul>-->
    <!--<li>-->
    <!--<a href="#">Ноутбуки</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Планшеты</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Ноутбуки</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Планшеты</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Ноутбуки</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Планшеты</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Ноутбуки</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Планшеты</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--</ul>-->
    <!--<span class="show-more">Показать еще</span>-->
    <!--</div>-->
    <!--<div class="catalog-box">-->
    <!--<h3>Компьютерная техника</h3>-->
    <!--<ul>-->
    <!--<li>-->
    <!--<a href="#">Ноутбуки</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Планшеты</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Ноутбуки</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Планшеты</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Ноутбуки</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Планшеты</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Ноутбуки</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Планшеты</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--</ul>-->
    <!--<span class="show-more">Показать еще</span>-->
    <!--</div>-->
    <!--<div class="catalog-box">-->
    <!--<h3>Компьютерная техника</h3>-->
    <!--<ul>-->
    <!--<li>-->
    <!--<a href="#">Ноутбуки</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Планшеты</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Ноутбуки</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Планшеты</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Ноутбуки</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Планшеты</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Ноутбуки</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Планшеты</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--</ul>-->
    <!--<span class="show-more">Показать еще</span>-->
    <!--</div>-->
    <!--</div>-->

    <!--<div class="col">-->
    <!--<div class="catalog-box">-->
    <!--<h3>Компьютерная техника</h3>-->
    <!--<ul>-->
    <!--<li>-->
    <!--<a href="#">Ноутбуки</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Планшеты</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Ноутбуки</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Планшеты</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Ноутбуки</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Планшеты</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Ноутбуки</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Планшеты</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--</ul>-->
    <!--<span class="show-more">Показать еще</span>-->
    <!--</div>-->
    <!--<div class="catalog-box">-->
    <!--<h3>Компьютерная техника</h3>-->
    <!--<ul>-->
    <!--<li>-->
    <!--<a href="#">Ноутбуки</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Планшеты</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Ноутбуки</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Планшеты</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Ноутбуки</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Планшеты</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Ноутбуки</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Планшеты</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--</ul>-->
    <!--<span class="show-more">Показать еще</span>-->
    <!--</div>-->
    <!--<div class="catalog-box">-->
    <!--<h3>Компьютерная техника</h3>-->
    <!--<ul>-->
    <!--<li>-->
    <!--<a href="#">Ноутбуки</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Планшеты</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Ноутбуки</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Планшеты</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Ноутбуки</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Планшеты</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Ноутбуки</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Планшеты</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--</ul>-->
    <!--<span class="show-more">Показать еще</span>-->
    <!--</div>-->
    <!--</div>-->

    <!--<div class="col last">-->
    <!--<div class="catalog-box">-->
    <!--<h3>Компьютерная техника</h3>-->
    <!--<ul>-->
    <!--<li>-->
    <!--<a href="#">Ноутбуки</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Планшеты</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Ноутбуки</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Планшеты</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Ноутбуки</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Планшеты</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Ноутбуки</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Планшеты</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--</ul>-->
    <!--<span class="show-more">Показать еще</span>-->
    <!--</div>-->
    <!--<div class="catalog-box">-->
    <!--<h3>Компьютерная техника</h3>-->
    <!--<ul>-->
    <!--<li>-->
    <!--<a href="#">Ноутбуки</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Планшеты</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Ноутбуки</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Планшеты</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Ноутбуки</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Планшеты</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Ноутбуки</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Планшеты</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--<li>-->
    <!--<a href="#">Мониторы</a>-->
    <!--</li>-->
    <!--</ul>-->
    <!--<span class="show-more">Показать еще</span>-->
    <!--</div>-->

    <!--</div>-->
    <!--</div>-->
    <!--</div>-->
    <!--</li>-->
    <!--</ul>-->

    <!--</li>-->


    <!--</ul>-->
    <!--</div>-->

    <!--</div>-->

    <!--</xsl:template>-->
</xsl:stylesheet>