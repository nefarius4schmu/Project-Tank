(function(_this, $){
 	
 	$(document).ready(function(){
 		// init callout buttons
		var jqBt = $(".bs-callout .callout-buttons .bt");
		jqBt.filter(".bt-collapse").click(onCalloutCollapse);
		jqBt.filter(".bt-close").click(onCalloutClose);
		
		// init navbar toggle
		$(".navbar-toggle").click(onNavbarToggleClick);
		$(".navbar-nav a").click(onNavbarLinkClick);
 	});
	
	
	
	function onCalloutCollapse(event){
		var jqBox = $(this).closest(".bs-callout");
		var isCollapsed = jqBox.hasClass("collapsed");
		jqBox.toggleClass("collapsed", !isCollapsed);
		if(isCollapsed) jqBox.find(".callout-content").show("slow")
		else jqBox.find(".callout-content").hide("slow")
		$(this).find("i").toggleClass("fa-chevron-down", !isCollapsed).toggleClass("fa-chevron-up", isCollapsed);
	}
	
	function onCalloutClose(event){
		$(this).closest(".bs-callout").hide("slow");
	}
	
	function onNavbarToggleClick(event){
		var target = $(this).data("target");
		var toggle = $(this).data("toggle");
		$(target).toggleClass(toggle);
	}
	
	function onNavbarLinkClick(event){
		if(!$(this).hasClass("dropdown")) return;
		$(this).closest(".navbar-nav").removeClass("dropdown");
	}
	
}(this, jQuery));