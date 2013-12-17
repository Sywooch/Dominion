<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE xsl:stylesheet SYSTEM "../symbols.ent">
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template match="credit_calculator[@bank = 'renesans']">

        <link href="/css/renesans.css" rel="stylesheet" type="text/css"/>
        <script src="/js/renesans.js" type="text/javascript"/>

        <!--Base-->
        <div class="calculator">
            <h1>Кредитный калькулятор</h1>
            <a href="#">
                <img src="logo.jpg" width="215" height="30" alt=""/>
            </a>
            <div style="clear: both "/>
            <form action="/ajax/send/bank/renesans/" class="form1" id="form1">
                <div class="column">
                    <label>Цена товара:</label>
                    <input type="text" name="price" id="price" size="28" maxlength="256" value=""
                           onChange="return Pay();"/>
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
                    <label class="italic" id="type">Легкий</label>
                </div>
                <div style="clear: both; border-bottom:1px solid #cccccc"/>
                <div class="column">
                    <label class="f12">% ставка:</label>
                    <label class="italic" id="rate"/>
                </div>
                <div class="column w175">
                    <label class="f12">Ежемесячная комиссия:</label>
                    <label class="italic" id="fee"/>
                </div>
                <div class="column w90">
                    <label class="f12">Страховка:</label>
                    <label class="italic" id="belay"/>
                </div>
                <div style="clear: both; border-bottom:1px solid #cccccc"/>
                <div>
                    <h4>Ваш ежемесячный платеж:</h4>
                    <label class="price" id="pay"/>
                    <span id="cash" style="display:none;">грн/месяц</span>
                    <input type="button" class="button" name="application" size="28" maxlength="256"
                           value="Подать заявку" onClick="return SendForm();"/>
                </div>
            </form>
            <script type="text/javascript">
                Pay();
            </script>
            <div style="clear: both;"/>
            <div class="info">
                <p>Расчеты калькулятора являются справочными.</p>
                <p>Точная сумма может незначительно отличатся от рассчитанной на сайте.</p>
            </div>
            <form action="#" class="form2" style="display:none;" id="sendForm">
                <label>ФИО:</label>
                <input type="text" name="name" size="28" maxlength="256" value="" id="name"/>
                <div style="clear: both;"/>
                <label>Дата рождения:</label>
                <select name="year" style="width:85px;" id="year"/>
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
                <div style="clear: both;"/>
                <label>Идентификационный код:</label>
                <input type="text" name="code" size="28" maxlength="256" value="" id="inn"/>
                <div style="clear: both;"/>
                <label>Адрес:</label>
                <input type="text" name="address" size="28" maxlength="256" value="" id="address"/>
                <div style="clear: both;"/>
                <label>Номер телефона:</label>
                <input type="text" name="phone" size="28" maxlength="256" value="" id="phone"/>
                <div style="clear: both;"/>
                <label>Электронная почта:</label>
                <input type="text" name="email" size="28" maxlength="256" value="" id="email"/>
                <div style="clear: both;"/>
                <input type="submit" value="Отправить заявку" name="but" class="button"
                       onClick="return SendMail(this.form)"/>
                <div style="clear: both;"/>
            </form>
        </div>
    </xsl:template>
</xsl:stylesheet>
