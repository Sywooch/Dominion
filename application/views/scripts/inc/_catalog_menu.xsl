<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE xsl:stylesheet SYSTEM "../symbols.ent">
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

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
                            <!--<xsl:apply-templates select="//catalogue-menu/catalogue" mode="parent"/>-->
                        <!--</ul>-->
                    <!--</li>-->
                <!--</ul>-->
            <!--</div>-->
        <!--</div>-->

    <!--</xsl:template>-->

    <!--Apply template catalogue parent-->
    <xsl:template match="catalogue" mode="parent">
        <xsl:param name="child_mutex" select="0"/>

        <li class="" catalog_id="{@catalog_id}">
            <xsl:choose>
                <xsl:when test="boolean(catalogue[count(*) > 0])">
                    <a href="{url}" class="parent">
                        <span>
                            <xsl:value-of select="name"/>
                        </span>
                    </a>

                    <div class="third-level">
                        <div class="third-level-wraper" style="background-image:url(i/catalog_img.jpg)">
                            <div class="col">
                                <xsl:if test="position() = last()">
                                    <xsl:attribute name="class">col last</xsl:attribute>
                                </xsl:if>
                                <div class="catalog-box">
                                    <h3>
                                        <xsl:value-of select="name"/>
                                    </h3>
                                    <ul>
                                        <xsl:apply-templates select="catalogue" mode="parent">
                                            <xsl:with-param name="child_mutex" select="1"/>
                                        </xsl:apply-templates>
                                    </ul>
                                    <span class="show-more">Показать еще</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </xsl:when>
                <xsl:otherwise>
                    <a href="{url}">
                        <xsl:choose>
                            <xsl:when test="$child_mutex = 1">
                                <xsl:value-of select="name"/>
                            </xsl:when>
                            <xsl:otherwise>
                                <span>
                                    <xsl:value-of select="name"/>
                                </span>
                            </xsl:otherwise>
                        </xsl:choose>
                    </a>
                </xsl:otherwise>
            </xsl:choose>
        </li>
    </xsl:template>


    <xsl:template name="catalog_button">

        <script type="text/javascript" src="/js/menu.js"></script>

        <div class="catalog-button-wrap">
            <div>
                <ul class="nav first-level">
                    <li>
                        <a class="catalog-button">
                            <span>Каталог товаров</span>
                        </a>

                        <ul class="second-level">
                            <li>
                                <a href="#">
                                    <span>Крупная бытовая техника</span>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <span>Встраиваемая техника для кухни</span>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <span>Ноутбуки и компьютерная техника</span>
                                </a>
                                <div class="third-level">
                                    <div class="third-level-wraper" style="background-image:url(i/catalog_img.jpg)">
                                        <div class="col">
                                            <div class="catalog-box">
                                                <h3>Компьютерная техника</h3>
                                                <ul>
                                                    <li>
                                                        <a href="#">Ноутбуки</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Планшеты</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Ноутбуки</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Планшеты</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Ноутбуки</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Планшеты</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Ноутбуки</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Планшеты</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                </ul>
                                                <span class="show-more">Показать еще</span>
                                            </div>
                                            <div class="catalog-box">
                                                <h3>Компьютерная техника</h3>
                                                <ul>
                                                    <li>
                                                        <a href="#">Ноутбуки</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Планшеты</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Ноутбуки</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Планшеты</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Ноутбуки</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Планшеты</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Ноутбуки</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Планшеты</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                </ul>
                                                <span class="show-more">Показать еще</span>
                                            </div>
                                            <div class="catalog-box">
                                                <h3>Компьютерная техника</h3>
                                                <ul>
                                                    <li>
                                                        <a href="#">Ноутбуки</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Планшеты</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Ноутбуки</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Планшеты</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Ноутбуки</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Планшеты</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Ноутбуки</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Планшеты</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                </ul>
                                                <span class="show-more">Показать еще</span>
                                            </div>
                                        </div>

                                        <div class="col">
                                            <div class="catalog-box">
                                                <h3>Компьютерная техника</h3>
                                                <ul>
                                                    <li>
                                                        <a href="#">Ноутбуки</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Планшеты</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Ноутбуки</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Планшеты</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Ноутбуки</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Планшеты</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Ноутбуки</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Планшеты</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                </ul>
                                                <span class="show-more">Показать еще</span>
                                            </div>
                                            <div class="catalog-box">
                                                <h3>Компьютерная техника</h3>
                                                <ul>
                                                    <li>
                                                        <a href="#">Ноутбуки</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Планшеты</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Ноутбуки</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Планшеты</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Ноутбуки</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Планшеты</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Ноутбуки</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Планшеты</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                </ul>
                                                <span class="show-more">Показать еще</span>
                                            </div>
                                            <div class="catalog-box">
                                                <h3>Компьютерная техника</h3>
                                                <ul>
                                                    <li>
                                                        <a href="#">Ноутбуки</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Планшеты</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Ноутбуки</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Планшеты</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Ноутбуки</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Планшеты</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Ноутбуки</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Планшеты</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                </ul>
                                                <span class="show-more">Показать еще</span>
                                            </div>
                                        </div>

                                        <div class="col last">
                                            <div class="catalog-box">
                                                <h3>Компьютерная техника</h3>
                                                <ul>
                                                    <li>
                                                        <a href="#">Ноутбуки</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Планшеты</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Ноутбуки</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Планшеты</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Ноутбуки</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Планшеты</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Ноутбуки</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Планшеты</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                </ul>
                                                <span class="show-more">Показать еще</span>
                                            </div>
                                            <div class="catalog-box">
                                                <h3>Компьютерная техника</h3>
                                                <ul>
                                                    <li>
                                                        <a href="#">Ноутбуки</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Планшеты</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Ноутбуки</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Планшеты</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Ноутбуки</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Планшеты</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Ноутбуки</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Планшеты</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">Мониторы</a>
                                                    </li>
                                                </ul>
                                                <span class="show-more">Показать еще</span>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>

                    </li>


                </ul>
            </div>

        </div>

    </xsl:template>
</xsl:stylesheet>