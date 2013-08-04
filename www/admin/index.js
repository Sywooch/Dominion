$(document).ready(function(){
    
  $("input[name=update]").click(function () {
     $(this).attr({
           disabled: "disabled",
           value: "Идет перестройка"
         });
     _button =  $(this);

     $.get("/search/update/", function(data){
         alert('Index restruct');
         _button.attr({
           disabled: "",
           value: "Сгенерировать"
         });
     });
  });    
});