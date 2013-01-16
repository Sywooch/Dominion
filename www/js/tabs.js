$(document).ready(function(){
  function strpos( haystack, needle, offset){ // Find position of first occurrence of a string   
    var i = haystack.indexOf( needle, offset ); // returns -1
    return i >= 0 ? i : false;
  }
      
  if ($(".tabs.content li").length) {
    tabs(".tabs.content");
  }
  else {
    $("#tab_content").hide();
  }
  function tabs(tab) {
    $(tab).find("li a").click(function(event) {
      event.preventDefault();
    });
    if($(tab).find("li[class='active']").length > 0){
      var activeTab = $(tab).find("li[class='active']").attr("id");
    }
    else{
      var activeTab = $(tab).find("li:first").addClass("active").attr("id");
    }
    
//    var thold = $(tab).parents('.tabsholder');
    var thold = $('#tab_content');
    if ($(thold).find(".tab_content").length) {
      $(thold).find(".tab_content").hide();
      $(thold).find(".tab_content").css({
        "visibility": "visible"
      });
      $(thold).find("." + activeTab).show();
      $(tab).find("li").click(function() {        
        activeTab = $(this).attr("id");
        section = $(this).attr("section");

        currentUrlParts = window.location.href.split("#");
        path = currentUrlParts[0]+'#tab='+section;
        window.location.href = path;
        
        $(thold).find(".tab_content").hide();
        $(thold).find("." + activeTab).show();
        $(tab).find("li").removeClass("active");
        $(this).addClass("active");
      })
    }
  }
  
  var currint_url = window.location.href;
  if(strpos(currint_url, '#tab=', 0)){
    var out = currint_url.match(/.+tab=(.+)/);
    if(out[1]){
      $('li[section="'+out[1]+'"]').trigger('click');
    }
  }
});