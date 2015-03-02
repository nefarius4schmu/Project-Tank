/**
* Project Tank Webpage
* basic ajax handler
* @author Steffen Lange
*/
(function(_this, $){
	
	if(_this["ajax"]) return;
	
	var _ajaxStatusInternal = "internal";
	
	function _ajaxSuccess(callback, data, value, content){
		callback({
			success:true,
			data:data,
			value:value,
			content:content,
		});
	}
	
	function _ajaxError(callback, data, value, textStatus, errorThrown){
		callback({
			success:false,
			data:data,
			value:value,
			status:textStatus,
			message:errorThrown,
		});
	}
	
	function _ajaxCall(method, url, data, value, callback){
		return $.ajax({
			method:method,
			url:url,
			data:data
		}).fail(function(jqXHR, textStatus, errorThrown){
			_ajaxError(callback, data, value, textStatus, errorThrown);
		});
	}
	
	_this.ajax = {
		
		json:
		function(method, url, data, value, callback){
			var xhr = _ajaxCall(method, url, data);
			xhr.done(function(msg){
				if(msg == "") return _ajaxSuccess(callback, data, value, null);
				var content = null;
				try{
					content = $.parseJSON(msg);
				}catch(e){
					return _ajaxError(callback, data, value, _ajaxStatusInternal, e.message+"\n"+msg);
				}
				return _ajaxSuccess(callback, data, value, content);
			});
			return xhr;
		}
		
	};
	
	
}(this, jQuery));