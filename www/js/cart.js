$(document).ready(function(){
  
  $(".delete_icon").click(function(ev){ev.preventDefault();});
  $(".delete_icon").click(function(ev){
    var xid = $(this).attr('xid');
    
    if(confirm('Вы действительно хотите удалить товар из корзины?')){
      $.getJSON('/ajax/recountcart/', {id: xid, itm_count:'delete'}, function(data){        
        $('#item_row'+xid).remove();
                                            
        if(data.count == 0){
          $('#content_cart').html('<span>Ваша корзина пуста :(</span>');
          $('#content_cart').removeClass('content_block');
          $('#content_cart').removeClass('chapter_products');
          $('#content_cart').addClass('empty');
          $('#cart_text_block').remove();
        }
        else{
          $('#cart_total .price').html(number_format(data.itogo_summa));  
        }
        
        $("#cart").html(data.html);        
      });    
    }    
  });
  
  var valBefore;
  $(".quantity_form .count").focus(function(e){valBefore = $(this).val();});
  $(".quantity_form .count").keyup(function(e){
    if ((( 48 <= e.which) && (e.which <= 57)) || (( 96 <= e.which) && (e.which <= 105)) || (e.which == 8)||(e.which == 39)||(e.which == 37)||(e.which == 46)||(e.which == 27)||(e.which == 17) || (e.which == 16) || (e.which == 110)){
      var thisKolvo = $(this).val();
      var parent = $(this).parent();
      var id = $(this).attr('xid');
      
      if(thisKolvo < 0) thisKolvo = 1;
      
      $.getJSON('/ajax/recountcart/',{id:id, itm_count:thisKolvo},function(data){
        $(this).val(thisKolvo);
        $("#cart").html(data.html);
        $('#cart_total .price').html(number_format(data.itogo_summa));
        
      })
    }else{
        $(this).val(valBefore);
    }
  });
  
});