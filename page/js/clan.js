/**
* Project Tank Webpage
* init clan page interactions
* @author Steffen Lange
*/
(function(_this, jQuery){
	
	var _jqMembersTable = $("#membersTable");
	var _jqMembersTableBody = $("#membersTableBody");
	
	$(document).ready(function(){
		initContent();
	});
	
	function initContent(){
		
//		initMembersTableEvents();
		// load stats table members list
		loadMembersStats();
	}
	
	function initMembersTableEvents(){
		_jqMembersTable.find("thead .header").click(onMembersTableSortClick);
	}
	
	function initMembersTable(data){
		if(!data.success) return console.log("get data failed");
		var members = data.data;
		var tmp = tmpMembersTableRow();
		
//		console.log(data);
		
		// append data for later functions
		setContent(_jqMembersTable, members);
		
		// sort
//		var sortData = getSortData(_jqMembersTable.find("thead"));
//		var sortBy = sortData.by;
//		
//		if(!sortData.by){
//			// print
		for(var key in members) if(members.hasOwnProperty(key)){
//				console.log(members[key]);
			var jq = $(fillTmp(tmp, members[key]));
			jq.appendTo(_jqMembersTableBody);
		}
//		}else{
//			var sdata = getSortedAssoc(sortData.by, members, "desc");
//			for(var i = 0; i < sdata.sorted.length; i++){
//				var key = sdata.assoc[sdata.sorted[i]];
//				var jq = $(fillTmp(tmp, members[key]));
//				jq.appendTo(_jqMembersTableBody);
//			}
//		}
		
		// init tablesort plugin
		_jqMembersTable.find("table").tablesorter();
		initMembersTableEvents();
	}

	/* events */
	
	function onMembersTableSortClick(event){
		console.log("click");
		var jqCols = _jqMembersTable.find("colgroup").removeClass("sorted");
		var jqCol = jqCols.eq($(this).index()).addClass("sorted");
//		toggleSort($(this));
//		sortMembersTable();
	}

	/* calls */
	
	function loadMembersStats(){
		toggleHidden(getjqLoader(_jqMembersTable), false);
//		console.log("call");
		if(!ajax) return console.error("missing ajax handler");
		var xhr = ajax.json("get", "get/?t=membersStats", {}, null, doneLoadMembersStats);
	}
	
	function doneLoadMembersStats(response){
		if(!response || !response.success) return failLoadMembersStats(response);
		console.log(response.content);
		
		initMembersTable(response.content);
		
		toggleHidden(getjqLoader(_jqMembersTable), true);
		toggleHidden(_jqMembersTable, false);
	}
	
	function failLoadMembersStats(response){
		console.log("failed to load members stats", response);
	}
	
	/* getter */
	
	function getjqLoader(jq){
		return $(jq.data("loader"));
	}
	
	function getSortedAssoc(skey, obj, direct){
		var arr = [];
		var data = {};
		var out = {};
		var isPath = ~skey.indexOf(".");
		var path = !isPath ? null : skey.split(".");
		// set helper
		for(var key in obj) if(obj.hasOwnProperty(key) && obj[key].hasOwnProperty(skey)){
			var value = !isPath ? obj[key][skey] : getObjectValueFromPath(path, obj[key]);
			arr.push(value);
			data[value] = key;			
		}
		// sort skey array
		arr.sort();
		if(direct == "desc") arr.reverse();
		
		// get out data
		return {
			sorted:arr,
			assoc:data,
		};
	}
	
	function getObjectValueFromPath(path, obj){
		if(typeof obj != "object") return obj;
		var key = path.shift();
		if(obj.hasOwnProperty(key))	return getObjectValueFromPath(path, obj[key]);
		else return NaN;
	}
	
	function getSortData(jq){
		var jqSorted = jq.find(".sort.sorted");
		
		return {
			by: jqSorted.data("sort"),
			direct: jqSorted.hasClass("sort-asc") ? "asc" : "desc",
		};
	}
	
	function getContent(jq){
		return jq.data("content");
	}
	
	
	/* setter */
	
	function setContent(jq, content){
		jq.data("content", content);
	}
	
	/* toggle */
	
	function toggleHidden(jq, state){
		return jq.toggleClass("hidden", state);
	}
	
	function toggleSort(jq){
		if(jq.hasClass("sort-asc")) jq.addClass("sorted sort-desc").removeClass("sort-asc");//direct = "desc";
		else if(!jq.hasClass("sort-desc")) jq.addClass("sorted sort-asc").removeClass("sort-desc");//direct = null;
		else jq.removeClass("sorted sort-asc sort-desc");
		jq.siblings().removeClass("sorted sort-asc sort-desc");
	}
	
	/* other */
	
	function fillTmp(tmp, data){
		for(var key in data) if(data.hasOwnProperty(key)){
			if(typeof data[key] == "object") tmp = fillTmp(tmp, data[key]);
			else{
				var re = new RegExp("{{"+key+"}}", "g");
				tmp = tmp.replace(re, data[key]);	
			}
		}
		return tmp;
	}
	
	function sortMembersTable(){
		var members = getContent(_jqMembersTable);
		var tmp = tmpMembersTableRow();
		var sortData = getSortData(_jqMembersTable.find("thead"));
		var sortBy = sortData.by;
//		console.log(members, sortData);
		
		if(!members) return;
		
		// clear old content
		_jqMembersTableBody.empty();
		
		if(!sortData.by){
			// print
			for(var key in members) if(members.hasOwnProperty(key)){
				var jq = $(fillTmp(tmp, members[key]));
				jq.appendTo(_jqMembersTableBody);
			}
		}else{
			// sort and print
			var sdata = getSortedAssoc(sortData.by, members, sortData.direct);
			for(var i = 0; i < sdata.sorted.length; i++){
				var key = sdata.assoc[sdata.sorted[i]];
				var jq = $(fillTmp(tmp, members[key]));
				jq.appendTo(_jqMembersTableBody);
			}
		}
		
		// append data for later functions
//		setContent(_jqMembersTable, members);
	}
	
	/* templates */
	
	function tmpMembersTableRow(){
		return "<tr><th data-wot='name'>{{name}}</th><td data-wot='role_i18n'>{{role_i18n}}</td><td data-wot='global'>{{global}}</td><td data-wot='battles'>{{battles}}</td><td data-wot='wins'>{{wins}}</td><td data-wot='winRatePerBattle'>{{winRatePerBattle}}</td><td data-wot='shots'>{{shots}}</td><td data-wot='hits'>{{hits}}</td><td data-wot='avgHitRatePerBattle'>{{avgHitRatePerBattle}}</td><td data-wot='damage'>{{damage}}</td><td data-wot='avgDamagePerBattle'>{{avgDamagePerBattle}}</td></tr>";
	}

}(this, jQuery));