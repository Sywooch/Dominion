/**
 * Описание обекта
 * @param {Object} selector
 * @param {Object} sortSelect
 */
function sorting(selector, sortSelect) {
    this.input = $(selector);
    this.sortSelect = $(sortSelect);
    this.deselectAll_BTN = '';
    this.selectAll_BTN = '';
    this.sortOptions = new Array(2);
}

/**
 * Очиста фильтра сортировки
 */
sorting.prototype.clearInput = function() {
    this.input.val('');
}

/**
 * Метод переопределения указателей на загруженные option
 * @return this
 */
sorting.prototype.loadOptions = function() {
    //Очищаем значения масивa
    this.sortOptions[0] = new Array();
    this.sortOptions[1] = new Array();
    var optGroup = this.sortSelect.find("option");
    this.kolvo = optGroup.length;
    for (i=0; i < this.kolvo; i++) {
        this.sortOptions[0][i] = $(optGroup[i]).text();
        this.sortOptions[1][i] = $(optGroup[i]).val();
    }
    return this;
}
/**
 * Фильтрация значений
 * @param {Object} str
 */
sorting.prototype.ssort = function(str) {
    var k = 0;
    inSelect = this.sortSelect;
    this.sortSelect.html("");
    var optText = this.sortOptions[0];
    var optVal = this.sortOptions[1];
    $(optText).each(function(i, thisVal) {
        thisVal = thisVal.toLowerCase();
        result = thisVal.indexOf(str);
        if (result >= 0) {
            k++;
            newElem = $("<option/>").attr({
                value: optVal[i]
            }).text(optText[i]);
            $(newElem).appendTo(inSelect);
        }
    })
    this.kolvo = k;
};
/**
 * Выделение опшинов
 */
sorting.prototype.sselect = function() {
    this.sortSelect.find("option").attr('selected', 'selected');
}
/**
 * Снятие выделения с опшинов
 */
sorting.prototype.deselect = function() {
    this.sortSelect.find("option").removeAttr('selected');
}
/**
 * Аякс загрузка опшинов
 * @param {Object} f
 * @param {Object} name
 * @param {Object} qw
 * @param {Object} parm
 *
 */
function chan(f, name, qw, parm) {
    // тестовая ссылка
    //ajaxlink = "/admin/ajax/opt.htm";

    //Рабочая ссылка
    qw = encodeURI(qw);
    ajaxlink = "selector2.php?q=" + parm + "&sel=" + qw;

    var size  = $(name).attr('size');
    //Запрос
    $(name).load(ajaxlink, function(data) {
        if(size==0){
            $('<option value="0" selected="selected">-- укажите товар ---</option>').prependTo(name);
        }
        isAdminSorting.loadOptions().clearInput();
    //    isAdminSorting.selectAll_BTN.text("Выделить все " + isAdminSorting.kolvo + " позиций");
    });


}

/**
 * Аякс загрузка опшинов
 *
 * @param {String} name
 * @param {String} catalogId
 *
 */
function getOptions(name, catalogId) {

    //    qw = encodeURI(qw);
    ajaxlink = "getICatAjax.php?catId=" + catalogId;

    var size  = $(name).attr('size');
    //Запрос
    $(name).load(ajaxlink, function(data) {
        if(size==0){
            $('<option value="0" selected="selected">-- укажите товар ---</option>').prependTo(name);
        }
        isAdminSorting.loadOptions().clearInput();
    //    isAdminSorting.selectAll_BTN.text("Выделить все " + isAdminSorting.kolvo + " позиций");
    });


}

$(document).ready(function() {

    isAdminSorting = new sorting($("#sorting"), $("#filterfield"));
    isAdminSorting.loadOptions().clearInput();
    //  isAdminSorting.deselectAll_BTN = $("<a href='#' class='sel_btn'>Убрать все</a>").insertAfter(isAdminSorting.sortSelect);
    //  isAdminSorting.selectAll_BTN = $("<a href='#'  class='sel_btn'>Выделить все " + isAdminSorting.kolvo + " позиций </a>").insertAfter(isAdminSorting.sortSelect);


    isAdminSorting.input.keyup(function() {
        str = $(this).val().toLowerCase();
        isAdminSorting.ssort(str);
    //    $(isAdminSorting.selectAll_BTN).text("Выделить все " + isAdminSorting.kolvo + " позиций ")
    });
    //  isAdminSorting.selectAll_BTN.click(function(event) {
    //    event.preventDefault();
    //    isAdminSorting.sselect();
    //  });
    //  isAdminSorting.deselectAll_BTN.click(function(event) {
    //    event.preventDefault();
    //    isAdminSorting.deselect();
    //  });

//    var size  = $('#filterfield').attr('size');
//    if(size==0){
        $("#filterfield").change(function(){
            var price = $('#filterfield option:selected').attr('price');
            var purchase_price = $('#filterfield option:selected').attr('purchase_price');
            var currency_id = $('#filterfield option:selected').attr('currency_id');

            $("#PRICE").val(price);
            $("#PURCHASE_PRICE").val(purchase_price);
            $("#CURRENCY_ID").text(currency_id);
        });
//    }

});

