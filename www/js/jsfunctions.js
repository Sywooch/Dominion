window.onload = function() {
  // Открытие/закрытие окошек
  $("a.product_block_icon, a.phone_icon, a.pseudo, a.what_is_it").click(function() {
    $(".dialog_box").hide();
    $(this).nextAll(".dialog_box").show();
  });
  $("a.close_icon").click(function() {
//    $(this).parents(".dialog_box").hide();
    $(this).parent().hide();
  });
  $(document).keyup(function(e) {
    if (e.keyCode == 27) {
      $(".dialog_box").hide();
    };
  });
  // Выравнивание блоков разделов каталога по высоте на главной странице
  var catalog_chapter_box_height = 0;
  $("#catalog_chapters div").each(function() {
    if($(this).height() > catalog_chapter_box_height) {
      catalog_chapter_box_height = $(this).height();
    }
  });
  $("#catalog_chapters div").height(catalog_chapter_box_height);
  // Спрятать/показать форму подбора похожих товаров
  $("#same_products > h3 a").click(function() {
    $("#same_products_form").slideToggle();
    $(this).toggleClass("expanded");
    return false;
  });
  //alert("test");
  // Спрятать/показать группы полей подбора товара по параметрам
  $("#catalog_compare_products_form h3 a").click(function() {
    $(this).parent().next().slideToggle();
    $(this).toggleClass("expanded");
    return false;
  });
  // Бегунки
//  $(".jquery_slider").slider({
//    range:true,
//    values:[10,80]    
//  });
  $(".ui-slider-handle").append("<span></span>");
  $(".ui-slider-handle span").last().addClass("last");
}
