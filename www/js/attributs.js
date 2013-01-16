$(document).ready(function(){
  
  if($('#same_products_div').length > 0){
    var item_id = $('#item_id').val();
    
    var url = '/item/getsimilaritem/id/'+item_id+'/';
    var attr = '';
    
    $.getJSON(url, {attr:''}, function(data){      
      $.each(data.vals, function(index, tov){
        val = tov.val;
        id = tov.id;
        
        if (val==0){
          $("#"+id).attr({'disabled':'disabled'});
          $("#"+id).parent().addClass('noactive');  
        }
        else{                      
          $("#"+id).removeAttr('disabled');
          $("#"+id).parent().removeClass('noactive');  
        }
      });
      
      $('#same_products_div').html(data.html);                  
    });
  }
  
  $('.attr_shorts').change(function(){
    var atr_str = '';
    var item_id = $('#item_id').val();
    
    $('input.attr_shorts:checked').each(function (i) {
      var val = $(this).val();
      var attr = $(this).attr('xid');
      atr_str+='a'+attr+'v'+val;
    });
    
    var url = '/item/getsimilaritem/id/'+item_id+'/';
    
    if(atr_str != ''){
      action = $('#category_path').val()+'at/'+atr_str+'/';
      $('#same_products_form').attr({'action':action});  
//      $('.applay_filters').show();
    }
    else{
      action = $('#category_path').val();
      $('#same_products_form').attr({'action':action});  
//      $('.applay_filters').hide();
    }
    
    $.getJSON(url, {attr:atr_str}, function(data){
      $.each(data.vals, function(index, tov){
        val = tov.val;
        id = tov.id;
        
        if (val==0){
          $("#"+id).attr({'disabled':'disabled'});
        }
        else{
          $("#"+id).removeAttr('disabled');
        }
      });
      
      $('#same_products_div').html(data.html);
    });
  });
  
  $(".applay_filters a.product_button").click(function(ev){ev.preventDefault();});
  $(".applay_filters a.product_button").click(function(ev){
    
    var action = $(this).parents('form').attr('action');
    window.location.href = action;
  });
  
});