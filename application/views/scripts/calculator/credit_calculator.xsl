<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE xsl:stylesheet SYSTEM "../symbols.ent">
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:import href="../_base.xsl"/>
    <!--<xsl:output encoding="UTF-8" indent="yes" omit-xml-declaration="yes" method="xml"/>-->
    <xsl:template match="credit_calculator[@bank = 'renesans']">
        <link href="/css/renesans.css" rel="stylesheet" type="text/css"/>
        <script src="/js/renesans.js" type="text/javascript"/>

        <div class="calculator">
            <div class="agree">
                <div>
                    <input type="checkbox" name="checkme" id="agree"/>
                    <p>Разрешаю использование своих данных в
                        <a href="/images/article/assignment.pdf" target="_blank" class="marketing">маркетинговых целях.</a>
                    </p>
                </div>
            </div>
            <h1>Кредитный калькулятор</h1>
            <a href="#">
                <img src="/images/renesans_logo.jpg" width="215" height="30" alt=""/>
            </a>
            <div style="clear: both "></div>
            <form action="#" class="form1" id="form1" method="post">
                <div class="column">
                    <label>Цена товара:</label>
                    <input type="text" name="price" id="price" size="28" maxlength="256" value="{@price}" readonly="readonly"/>
                </div>
                <div class="column w175">
                    <label>Срок кредита:</label>
                    <select name="time" id="time" onChange="return Pay();">
                        <option value="3">3 месяца</option>
                        <option value="6">6 месяца</option>
                        <option value="10">10 месяца</option>
                        <option value="12">12 месяца</option>
                        <option value="15">15 месяца</option>
                        <option value="18">18 месяца</option>
                        <option value="20">20 месяца</option>
                        <option value="24">24 месяца</option>
                    </select>
                </div>
                <div class="column w90">
                    <label>Вид кредита:</label>
                    <label class="italic" id="type">Доступный</label>
                </div>
                <div style="clear: both; border-bottom:1px solid #cccccc; margin:0"></div>
                <div class="column">
                    <label class="f12">% ставка:</label>
                    <label class="italic" id="rate"></label>
                </div>
                <div class="column w175">
                    <label class="f12">Ежемесячная комиссия:</label>
                    <label class="italic" id="fee"></label>
                </div>
                <div class="column w90">
                    <label class="f12">Страховка:</label>
                    <label class="italic" id="belay"></label>
                </div>
                <div style="clear: both; border-bottom:1px solid #cccccc; margin:0"></div>
                <div>
                    <h4>Ваш ежемесячный платеж:</h4>
                    <label class="price" id="pay"></label>
                    <span id="cash" style="display:none;">грн/месяц</span>
                    <input type="button" class="button" name="application" size="28" maxlength="256"
                           value="Подать заявку" onClick="return SendForm();"/>
                </div>
            </form>

            <script type="text/javascript">
                Pay();
            </script>

            <div style="clear: both;"></div>
            <div class="info">
                <p>Расчеты калькулятора являются справочными.</p>
                <p>Точная сумма может незначительно отличатся от рассчитанной на сайте.</p>
                <p>АТ "БАНК РЕНЕСАНС КАПІТАЛ" Ліцензія НБУ №222 від 17.10.2011.</p>
            </div>
            <form action="#" class="form2" style="display:none;" id="sendForm">
                <input type="hidden" name="item_id" value="{@item_id}"/>
                <label>ФИО:</label>
                <input type="text" name="name" size="28" maxlength="256" value="" id="name"/>
                <div style="clear: both;"></div>
                <label>Дата рождения:</label>
                <select name="year" style="width:85px;" id="year"></select>

                <select name="month" style="width:110px;" id="month">
                    <option value="01">Январь</option>
                    <option value="02">Февраль</option>
                    <option value="03">Март</option>
                    <option value="04">Апрель</option>
                    <option value="05">Май</option>
                    <option value="06">Июнь</option>
                    <option value="07">Июль</option>
                    <option value="08">Август</option>
                    <option value="09">Сентябрь</option>
                    <option value="10">Октябрь</option>
                    <option value="11">Ноябрь</option>
                    <option value="12">Декабрь</option>
                </select>
                <select name="day" style="width:55px;" id="day">
                    <option>1</option>
                    <option>2</option>
                    <option>3</option>
                    <option>4</option>
                    <option>5</option>
                    <option>6</option>
                    <option>7</option>
                    <option>8</option>
                    <option>9</option>
                    <option>10</option>
                    <option>11</option>
                    <option>12</option>
                    <option>13</option>
                    <option>14</option>
                    <option>15</option>
                    <option>16</option>
                    <option>17</option>
                    <option>18</option>
                    <option>19</option>
                    <option>20</option>
                    <option>21</option>
                    <option>22</option>
                    <option>23</option>
                    <option>24</option>
                    <option>25</option>
                    <option>26</option>
                    <option>27</option>
                    <option>28</option>
                    <option>29</option>
                    <option>30</option>
                    <option>31</option>
                </select>
                <div style="clear: both;"></div>
                <label>Идентификационный код:</label>
                <input type="text" name="code" size="28" maxlength="256" value="" id="inn"/>
                <div style="clear: both;"></div>
                <label>Адрес:</label>
                <input type="text" name="address" size="28" maxlength="256" value="" id="address"/>
                <div style="clear: both;"></div>
                <label>Номер телефона:</label>
                <input type="text" name="phone" size="28" maxlength="256" value="" id="phone"/>
                <div style="clear: both;"></div>
                <label>Электронная почта:</label>
                <input type="text" name="email" size="28" maxlength="256" value="" id="email"/>
                <div style="clear: both;"></div>
                <input type="submit" value="Отправить заявку" name="but" id="goButton" class="button"
                       onClick="return SendMail(this.form)"/>
                <div style="clear: both;"></div>
            </form>
        </div>

    </xsl:template>
</xsl:stylesheet>
