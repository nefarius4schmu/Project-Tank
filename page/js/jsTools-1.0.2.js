/**
 * JS Tools
 * helper functions
 *
 * @version 1.0.2
 * @author Steffen lange
 */
(function($){
	'use strict';

    var tools = {
        template: function(temp, data){
            if(data){
                var re;
                // fill in template
                for (var key in data) if (data.hasOwnProperty(key)) {
                    re = new RegExp("{{" + key + "}}", "g");
                    temp = temp.replace(re, data[key] != null ? data[key] : "");
                }
                // clear unset data
                if (~temp.indexOf('{{')) {
                    re = new RegExp("{{.+?}}", "g");
                    temp = temp.replace(re, "");
                }
            }
            return $(temp);
        },
        empty: function(v){
            return v === null || v === "" || v == 0 || typeof v === 'undefined';
        },
        matchCount: function(string, subString, allowOverlapping){
            //src: http://stackoverflow.com/questions/4009756/how-to-count-string-occurrence-in-string
            string+=""; subString+="";
            if(subString.length<=0) return string.length+1;
            var n=0, pos=0;
            var step=(allowOverlapping)?(1):(subString.length);
            while(true){
                pos=string.indexOf(subString,pos);
                if(pos>=0){ n++; pos+=step; } else break;
            }
            return(n);
        },
        enclosing: function(haystack, needle){
            var re = new RegExp('('+needle+'.*?'+needle+')', 'g');
            var matches = haystack.match(re);
            return matches != null && matches.length > 0;
        },
    };

	var jso = {
		get: function(obj, path){
			for(var i = 0; i < path.length; i++)
				if(typeof obj === 'object' && obj.hasOwnProperty(path[i])) obj = path[i];
				else return;
			return obj;
		},
		set: function(obj, path, value){
			if(path.length > 1){
				var p = path.shift();
				if(obj[p] == null || typeof obj[p] !== 'object')
					obj[p] = {};
				jso.set(obj[p], path, value);
			}else{
				obj[path[0]] = value;

			}
		}
	};

    var jsh = {
        init: function($this, callback){
            $this.each(function(){

                var $self = $(this);
                var KEYS_TOGGLE = {
                    "CTRL": "ctrlKey",
                    "ALT": "altKey",
                    "SHIFT": "shiftKey",
                };
                var KEYS_CODED = {
                    "ENTER": 13,
                };

                // prepare hotkeys
                var hotkeys = $self.data("hk");
                if(!hotkeys) return;
                hotkeys = hotkeys.toUpperCase().split("+");

                var userKeys = {
                    code:null,
                    toggle:[],
                };
                var length = hotkeys.length;
                for(var i = 0; i < length; i++){
                    var key = hotkeys[i];
                    if(typeof KEYS_TOGGLE[key] !== 'undefined') userKeys.toggle.push(KEYS_TOGGLE[key]);
                    else if(typeof KEYS_CODED[key] !== 'undefined') userKeys.code = KEYS_CODED[key];
                    else userKeys.code = key.charCodeAt(0);
                }

                $self.keydown(function(event){
                    var state = userKeys.code == event.keyCode;
                    var l = userKeys.toggle.length;
                    for(var i = 0; i < l; i++){
                        var keyEvent = userKeys.toggle[i];
                        state = state && event[keyEvent];
                    }

                    if(state && callback) callback.call(this, event);
                });
            });
        }
    };

    /**
     * creates an event for setting object values
     * attributes: jso, jso-value
     * @param obj {object} scope where to add
     * @param path {string} jso data string
     * @param value
     * @returns {*}
     */
    $.fn.jsOption = function(obj, path, value){
        if(!path) return;
        path = typeof path === "string" ? path.split(".") : path;
        $(this).data("jso-value", value);
		
        if(typeof value === 'undefined') return jso.get(obj, path);
        else return jso.set(obj, path, value);

    };

    /**
     * creates an hotkey event on an jQuery element
     * attributes: data-hk
     * @param callback
     */
    $.fn.jsHotkey = function(callback){
        jsh.init($(this), callback);
    };

    // prepare global functions
    if(!window.jsTools) window["jsTools"] = tools;

}(jQuery));