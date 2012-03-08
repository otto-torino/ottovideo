var loading = "<img src='img/ajax-loader.gif' alt='loading...'>";
var requestCache = new Array();

function sendGet(url, data, ref_id, load_id) {

	var req = new Request.HTML({
		url:url,
		method: 'get',
		data:data,
		onRequest: function() {
			if(!load_id) document.getElementById(ref_id).innerHTML = loading;
			else document.getElementById(load_id).innerHTML = loading;
		},
		onComplete: function(responseTree, responseElements, responseHTML, responseJavaScript) {
			document.getElementById(ref_id).innerHTML = responseHTML;
			if(!(!load_id) && load_id!=ref_id) document.getElementById(load_id).innerHTML = '';
		}
	}).send();
}

/* per GET che non restituiscono niente */
function sendGetAction(url, data) {

	var req = new Request.HTML({
		url:url,
		method: 'get',
		data:data,							
		onComplete: function(responseTree, responseElements, responseHTML, responseJavaScript) {
		}
	}).send();
}

function sendPost(url, data, ref_id, load_id, script, callback, params) {

	var ref = $type(ref_id)=='element' ? ref_id : $(ref_id);
	var load = $type(load_id)=='element' ? load_id : $(load_id);
	
	var req = new Request.HTML({
		evalScripts:!script,
		url:url,
		method: 'post',
		data:data,
		onRequest: function() {
			if(load) load.innerHTML = loading;
		},
		onComplete: function(responseTree, responseElements, responseHTML, responseJavaScript) {
			ref.innerHTML = responseHTML;
			if(script) eval(responseJavaScript);
			if((load) && load!=ref) document.getElementById(load).innerHTML = '';
			if(callback) callback(params);
		}
	}).send();
}

/* per POST che non restituiscono niente */
function sendPostAction(url, data, callback, params) {

	var req = new Request.HTML({
		url:url,
		method: 'post',
		data:data,							
		onComplete: function(responseTree, responseElements, responseHTML, responseJavaScript) {
			if(callback) callback(params);
		}
	}).send();
}

/*
 * Ajax requests function
 * performs post and get asynchronous requests
 *
 * Arguments
 * method - (string) The method can be either post or get
 * url - (string) The requested url
 * data - (string) The datas of the request in the form 'var1=value1&var2=value2'
 * target - (mixed) The element DOM Object or the element id of the DOM element that have to be updated with the request response
 *
 * Options (object)
 * cache - (bool default to false) Whether to cache the request result or not
 * cacheTime - (int default 3600000 [1hr]) The time in milliseconds to keep the request in cache
 * load - (mixed default null) The element DOM Object or the element id of the DOM element to use to show the loading image
 * script - (bool default false) True if scripts have to be executed, false if not
 * callback - (function dafault null) The function to call after the request has been executed
 * callback_params - (string default null) The params passed to the callback function
 *
 * if the called method has to return an error, must return a string like:
 * request error:Error description
 * this way the method is not executed and an alert is displayed with the message "Error description"
 *
 */
function ajaxRequest(method, url, data, target, options) {
	var opt = {
		cache: false,
		cacheTime: 3600000,
		load: null,
		script: false,
		callback: null,
		callback_params: null
	};
	$extend(opt, options);
	target = $type(target)=='element'
		? target
		: $chk($(target))
			? $(target)
			: null;
	
	if(opt.cache && $defined(requestCache[url+data]) && ($time() - requestCache[url+data][0] < opt.cacheTime)) {
		target.set('html', requestCache[url+data][1]); 
		return true;
	}

	var opt_load = $chk(opt.load)? ($type(opt.load)=='element'?opt.load:$(opt.load)):null;
	var request = new Request.HTML({
		evalScripts: opt.script,
		url: url,
		method:	method,
		data: data,
		onRequest: function() {
			if(opt_load) opt_load.set('html', loading); 
		},
		onComplete: function(responseTree, responseElements, responseHTML, responseJavaScript) {
			if(opt_load) opt_load.set('html', ''); 
			rexp = /request error:(.*)/;
			var err_match = rexp.exec(responseHTML);
			if($chk(err_match)) alert(err_match[1]);
			else {
				if(target) target.set('html', responseHTML);
				if(opt.cache) requestCache[url+data] = new Array($time(), responseHTML);
				if(opt.callback && opt.callback_params) opt.callback(opt.callback_params);
				else if($chk(opt.callback)) opt.callback(); 
			}
		}
	}).send();

}

