$(document).ready(function(){
	var attr = $("#all_attr .attr");
	var attr_btn = $("#all_attr .attr .attr_btn");
	var attr_hold = $("#all_attr .attr .attr_hold");
	var v_time = 250;
	var thisBtn;
	if(attr.length>0){
		$(attr_btn).each(function(){
				if (!$(this).hasClass('active')){
					ind = $(attr_btn).index(this);
					$(attr_hold[ind]).hide();
				}
			});
		$(attr_btn).click(function(){
										thisBtn = $(this);
										if ($(thisBtn).next(":visible").length>0){
											$(thisBtn).next().slideUp(v_time,function(){
													$(thisBtn).toggleClass("active")
												});
											
										}else{
											$(thisBtn).toggleClass("active").next().slideDown(v_time);
										}
									});
	}
	
	var attr_chk = $("#all_attr .attr .attr_hold input:checkbox");
	var attr_chk_td = $(attr_chk).parent("td");
	var attr_chk_fs = $(attr_chk_td).find("fieldset");
	var attr_radio = $(attr_chk_fs).find("input:radio");
	change_color_td = function(ind, col){
		$(attr_chk_td[ind]).removeAttr('class').addClass(col);
		$(attr_chk_fs[ind]).find('label').removeClass('active');
		col_class = '.'+col;
		$(attr_chk_fs[ind]).find('p'+col_class+' label').addClass('active');
	};
	change_active_attr = function(ind, col){
		$(attr_chk_td[ind]).removeAttr('class').addClass(col);
		$(attr_chk_fs[ind]).toggleClass('active');
	}
	$(attr_chk).click(function(){
			checked = $(this).attr('checked');
			aIND = $(attr_chk).index(this);
			color = $(attr_chk_fs[aIND]).find('input:checked').parent('p').attr('class');
			if(checked){
				change_active_attr(aIND, color);
				$(attr_chk_fs[aIND]).find('input').removeAttr('disabled');
				$(attr_chk_fs[aIND]).find('input:checked').next('label').toggleClass('active');
			}else{
				change_active_attr(aIND, '');
				$(attr_chk_fs[aIND]).find('input').attr({'disabled':'disabled'});
				$(attr_chk_fs[aIND]).find('input:checked').next('label').toggleClass('active');
			}
		});
	$(attr_radio).change(function(){
		this_fs = $(this).parents('fieldset');
		aIND = $(attr_chk_fs).index(this_fs);
		color = $(attr_chk_fs[aIND]).find('input:checked').parent('p').attr('class');
		change_color_td(aIND, color);
	});
});

