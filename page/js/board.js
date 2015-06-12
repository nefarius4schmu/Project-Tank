(function(_this, $){


    // init local dates
    $(".moment-date").each(function(){
        $(this).html(moment($(this).text()).format("hh:mm dddd, Do MMMM YYYY"));
    });

 	$(document).ready(function(){
 		// init callout buttons
		var jqBt = $(".bs-callout .callout-buttons .bt");
		jqBt.filter(".bt-collapse").click(onCalloutCollapse);
		jqBt.filter(".bt-close").click(onCalloutClose);
		
		// init navbar toggle
		$(".navbar-toggle").click(onNavbarToggleClick);
		$(".navbar-nav a").click(onNavbarLinkClick);

        $(".news-delete,.event-delete").click(onDeletePostClick);
 	});
	
	function onDeletePostClick(event){
        if(!confirm("Wollen Sie diesen Beitrag wirklich löschen?")) event.preventDefault();
    }
	
	function onCalloutCollapse(event){
		var jqBox = $(this).closest(".bs-callout");
		var isCollapsed = jqBox.hasClass("collapsed");
		jqBox.toggleClass("collapsed", !isCollapsed);
		if(isCollapsed) jqBox.find(".callout-content").show("slow");
		else jqBox.find(".callout-content").hide("slow");
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