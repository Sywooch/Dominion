$(document).ready(function(){
    
  $("input[name=update]").click(function () {
     $(this).attr({
           disabled: "disabled",
           value: "Идет генерация краткого описания ..."
         });
     _button =  $(this);

     $.get("/admin/short-description-update.php", function(data){
         alert('Краткое описание создано');
         _button.attr({
           disabled: "",
           value: "Сгенерировать"
         });
     });
  });
  
  $("input[name=update_images]").click(function () {
     $(this).attr({
           disabled: "disabled",
           value: "Идет обновление фотографий ..."
         });
     _button =  $(this);

     $.get("/admin/item-photo-cc-import.php", function(data){
         alert('Обновление фотографий завершено');
         _button.attr({
           disabled: "",
           value: "Обновить фотографии"
         });
     });
  });
  
  $("input[name=sitemap_update]").click(function () {
     $(this).attr({
           disabled: "disabled",
           value: "Идет перестройка урлов ..."
         });
     _button =  $(this);

     $.get("../sitemap.php", function(data){
         alert('Все описания обновлены');
         _button.attr({
           disabled: "",
           value: "Сгенерировать"
         });
     });
  });
  
  $("input[name=create_sef_url]").click(function () {
    $(this).attr({
      disabled: "disabled",
      value: "Идет перестройка урлов ..."
    });
    _button =  $(this);

    $.get("/admin/sef-url-update.php", function(data){
      alert('Все урлы обновлены');
      _button.attr({
        disabled: "",
        value: "Перестроить урлы"
      });
    });
  });
  
  $('input[name="load_images"]').click(function () {
    $(this).attr({
      disabled: "disabled",
      value: "Идет обновление фото ..."
    });
    _button =  $(this);

    $.get("/item_photo_import.php", function(data){
      $('#err_mess').html(data);
      alert('Все фото обновлены');
      
      _button.attr({
        disabled: "",
        value: "Обновить фото товаров"
      });
    });
  });
  
  $("#gen_type").change(function(){
     var id = $(this).val();
     if(id==4){
       $("#gen_catalog_id").removeAttr("disabled");
     }
     else{
       $("#gen_catalog_id").attr("disabled", "disabled");
     }
  });
  
  $("input[name=meta_generate]").click(function () {
     $(this).attr({
           disabled: "disabled",
           value: "Идет генерация описания"
         });
     _button =  $(this);
     
     var gen_type = $("#gen_type").val();
     var gen_catalog_id = $("#gen_catalog_id").val();
     $.post("/ajax/makemeta/",{gen_type:gen_type, gen_catalog_id:gen_catalog_id}, function(data){
         alert('Index restruct');
         _button.attr({
           disabled: "",
           value: "Сгенерировать"
         });
     });
  });

  $("input[name=run_attr_imp]").click(function () {
     $(this).attr({
           disabled: "disabled",
           value: "Идет импорт атрибутов"
         });
     _button =  $(this);

     var catalog_id = $('#catalog_id').val();
     $.ajax({
      url: 'ATTRIBUT_IMPORT.php',
      type:'POST',
      dataType:'json',
      data:{mode:"import", catalog_id:catalog_id},
      success: function(data) {
        $("input[name=run_attr_imp]").removeAttr('disabled');
        $("input[name=run_attr_imp]").attr({value: "Импорт атрибутов"});

         $('#content').html(data.html);
         $('#error').html(data.errors);
      }
    });
  });
  
  $("a.applay_attr").live("click", function(ev){ev.preventDefault();});
  $('a.applay_attr').live('click', function(){
      var parent = $(this).parent().parent();
      $(parent).find('input[type="checkbox"][class="id-checkbox"]').attr({"checked":"checked"});

      var jsonObj = [];

      var attr_id = $(parent).find('input[type="checkbox"][class="id-checkbox"]').val();
      var attr_grp_id = $(parent).find('select[name="ATTRIBUT_GROUP_ID"]').val();
      var view_attr_grp_id = $(parent).find('select[name="VIEW_ATTRIBUT_GROUP_ID"]').val();
      var type = $(parent).find('select[name="TYPE"]').val();
      var unit_id = $(parent).find('select[name="UNIT_ID"]').val();

      jsonObj.push({attrId:attr_id, attrGrpId: attr_grp_id, viewAttrGrpId:view_attr_grp_id, type:type, unitId:unit_id});
      processRow(jsonObj);
  });

  $("a.applay_all").live("click", function(ev){ev.preventDefault();});
  $('a.applay_all').live('click', function(){
      var parent = $(this).parents('form');
      var jsonObj = [];
      $('input[type="checkbox"][class="id-checkbox"]:checked').each(function (index, domEle) {
        var parent = $(this).parent().parent();

        var attr_id = $(parent).find('input[type="checkbox"][class="id-checkbox"]').val();
        var attr_grp_id = $(parent).find('select[name="ATTRIBUT_GROUP_ID"]').val();
        var view_attr_grp_id = $(parent).find('select[name="VIEW_ATTRIBUT_GROUP_ID"]').val();
        var type = $(parent).find('select[name="TYPE"]').val();
        var unit_id = $(parent).find('select[name="UNIT_ID"]').val();

        jsonObj.push({attrId:attr_id, attrGrpId: attr_grp_id, viewAttrGrpId:view_attr_grp_id, type:type, unitId:unit_id});        
      });

      processRow(jsonObj);
  });

  function processRow(jsonObj){
      $.ajax({
          url: 'ATTRIBUT_IMPORT.php',
          type:'POST',
          dataType:'json',
          data:{mode:"create", data:JSON.stringify(jsonObj)},
          success: function(data) {
            if (data.status==0) {
                $('#error').html('');
                $.each(data.id, function(key, value) {
                    var trId = '#tr'+value;
                    $(trId).html('<td colspan="7"><div class="success">Запись обработана успешно</div></td>');
                    $('div.success').fadeOut(2000, function (){
                        $(this).parent().parent().remove();
                    });
                    if ($('input[type="checkbox"][class="id-checkbox"]').length == 0) {
                        $('.applay_all').remove();
                    }
                });
            } else {
                $('#error').html(data.errors);
            }
          }
      });
  }

  $("a.look_attr_val").live("click", function(ev){ev.preventDefault();});
  $('a.look_attr_val').live('click', function(){
      var attrId = $(this).attr('xid');
      $.post('ATTRIBUT_IMPORT.php', {mode:"look_attr_val", attrId:attrId}, function(data){
          $.fancybox(data);
      });
  });
});
