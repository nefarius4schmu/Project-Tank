(function(_this, $){

	// init ddslick elements
	$("select.data-select").each(function(){
		var id = $(this).attr("id");
		var name = $(this).attr("name");
		$(this).ddslick();
		$("#"+id).find(".dd-selected-value").attr("name", name);
	});
	
}(this, jQuery));