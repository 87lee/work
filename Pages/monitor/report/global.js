function $(id) {
	return document.getElementById(id);
}
function add_tag(parent, tag) {
	var e = document.createElement(tag);
	parent.appendChild(e);
	return e;
}
function add_input(parent, type) {
	var e = document.createElement("input");
	if (type) {
		e.type = type;
	}
	parent.appendChild(e);
	return e;
}

function add_tr(tbody) {
	return add_tag(tbody, "tr");
}
function add_td(tr, text, title) {
	var td = add_tag(tr, "td");
	if (text) {
		td.innerText = text;
	}
	if (title) {
		td.title = title;
	}
	return td;
}
function add_th(tr, text, title) {
	var td = add_tag(tr, "th");
	if (text) {
		td.innerText = text;
	}
	if (title) {
		td.title = title;
	}
	return td;
}
function add_a(td, text, href) {
	var a = add_tag(td, "a");
	if (text) {
		a.innerText = text;
	}
	if (href) {
		a.href = href;
	}
	return a;
}
function add_div(td, text, cls) {
	var div = add_tag(td, "div");
	if (text) {
		div.innerText = text;
	}
	if (cls) {
		div.className = cls;
	}
	return div;
}
function add_span(td, text, cls) {
	var span = add_tag(td, "span");
	span.innerText = text;
	if (cls) {
		span.className = cls;
	}
	return span;
}
function add_option(td, text, value) {
	var option = add_tag(td, "option");
	option.innerText = text;
	if (value) {
		option.value = value;
	}
	return option;
}
function ajax(url, cb, data, user, pass) {
	var http = new window.XMLHttpRequest();
	http.onloadend = function () {
		cb(http.responseText, http);
	}
	http.open(data ? "post" : "get", url, true, user, pass);
	http.send(data);
}
function ajax2(url, st, cb, data, user, pass, sync) {
	var http = new window.XMLHttpRequest();
	var time = (new Date()).getTime();
	http.onreadystatechange = function () {
		st(http.status, http.statusText, (new Date()).getTime() - time, http);
	}
	http.onloadend = function () {
		cb(http.responseText, http);
	}
	http.open(data ? "post" : "get", url, !sync, user, pass);
	http.send(data);
	return http;
}
function ajax_state(status, text, t) {
	if (text) {
		result.innerText = "HTTP " + status + " " + text + " [" + t + "]";
	}
	else {
		result.innerText = "Loading [" + t + "]";
	}
}
function eval_xml(text) {
	if (window.DOMParser) {
		var ps = new DOMParser();
		try {
			return ps.parseFromString(text, "text/xml");
		}
		catch (e) {
			return ps.parseFromString(text, "text/html");
		}
	}
	var doc = new ActiveXObject("MSXML2.DOMDocument");
	doc.loadXML(text);
	return doc.documentElement;
}

function dump_json(parent, name, value) {
	var li = add_tag(parent, "li");
	var type = typeof (value);
	add_span(li, "[");
	var span = add_span(li, type, "obj_type");
	if (type != "object" || value == null) {
		add_span(li, "] " + name + " = ");
		add_span(li, value, "obj_value");
		return;
	}
	add_span(li, "] " + name);
	var n = 0;
	for (var i in value) {
		++n;
	}
	if (n > 1) {
		span.innerText += "(" + n + ")";
	}
	var ul = add_tag(li, "ul");
	span.onclick = function () { toggle(ul) };
	span.className = "obj_type obj_list";
	for (var i in value) {
		dump_json(ul, i, value[i]);
	}
}
function dump_xml(parent, obj) {
	var li = add_tag(parent, "li");
	if (obj.nodeName) {
		add_span(li, "<");
		var span = add_span(li, obj.nodeName, "obj_type");
		add_span(li, ">");
	}
	if (obj.nodeValue) {
		add_span(li, obj.nodeValue.trim(), "obj_value");
	}
	var x = [];
	var childNodes = obj.childNodes;
	for (i = 0; i < childNodes.length; ++i) {
		var value = childNodes[i].nodeValue;
		if (value && !value.trim()) {
			continue;
		}
		x.push(childNodes[i]);
	}
	childNodes = x;
	var attributes = obj.attributes || [];
	if (!attributes.length) {
		if (!childNodes.length) {
			return;
		}
		if (childNodes.length == 1) {
			if ((childNodes[0].nodeName == "#text") || (childNodes[0].nodeName == "#cdata-section")) {
				var value = childNodes[0].nodeValue.trim();
				add_span(li, " " + value, "obj_value");
				return;
			}
		}
	}
	var ul = add_tag(li, "ul");
	span.onclick = function () { toggle(ul); }
	span.classList.add("obj_list");

	for (var i = 0; i < attributes.length; ++i) {
		var li2 = add_tag(ul, "li");
		add_span(li2, attributes[i].name + " = ");
		add_span(li2, attributes[i].value, "obj_value");
	}
	for (i = 0; i < childNodes.length; ++i) {
		dump_xml(ul, childNodes[i]);
	}
	var x = childNodes.length + attributes.length;
	if (x > 1) {
		span.innerText += "(" + x + ")";
	}
}

function eval_m3u8(text) {
	var lst = text.split("\n");
	var obj = [];
	for (var i in lst) {
		var line = lst[i];
		if (!line.length) {
			continue;
		}
		var j = line.indexOf("\r");
		if (j != -1) {
			line = line.substr(0, j);
		}
		if (!line.length) {
			continue;
		}
		if (line.charAt(0) != "#") {
			obj.push({ value: line });
			continue;
		}
		if (line.length == 1) {
			continue;
		}
		j = line.indexOf(":");
		if (j != -1) {
			obj.push({ name: line.substr(0, j), value: line.substr(j + 1) });
			continue;
		}
		obj.push({ name: line });
	}
	return obj;
}
function dump_m3u8(parent, obj) {
	for (var i in obj) {
		var line = obj[i];
		var li = add_tag(parent, "li");
		if (line.name) {
			var cls = null;
			var name = line.name;
			switch (name) {
				case "#EXTM3U":
				case "#EXTINF":
				case "#EXT-X-VERSION":
				case "#EXT-X-ALLOW-CACHE":
				case "#EXT-X-TARGETDURATION":
				case "#EXT-X-MEDIA-SEQUENCE":
				case "#EXT-X-PROGRAM-DATE-TIME":
				case "#EXT-X-BYTERANG":
				case "#EXT-X-KEY":
				case "#EXT-X-PLAYLIST-TYPE":
				case "#EXT-X-ENDLIST":
				case "#EXT-X-MEDIA":
				case "#EXT-X-STREAM-INF":
				case "#EXT-X-DISCONTINUITY":
				case "#EXT-X-I-FRAMES-ONLY":
				case "#EXT-X-I-FRAME-STREAM-INF":
					name = name.toLowerCase();
					cls = "obj_type";
			}
			add_span(li, name, cls);
		}
		if (line.name && line.value) {
			add_span(li, " = ");
		}
		if (line.value) {
			add_span(li, line.value, "obj_value");
		}
	}
}
function copy_url(url) {
	window.clipboardData.setData("text", url);
}
function nop() {

}

function add_url_tree(tree, url) {
	var i = url.indexOf("://");
	if (i == -1) {
		return;
	}
	i += 3;
	var proto = url.substr(0, i);
	if (!tree[proto]) {
		tree[proto] = { list: [], count: 0 };
	}
	var node = tree[proto];
	var j = url.indexOf("?", i);
	var a;
	if (j != -1) {
		path = url.substr(i, j - i).split("/");
	}
	else {
		path = url.substr(i).split("/");
	}
	for (i = 0; i < path.length; ++i) {
		var p = path[i];
		if (!p.length) {
			continue;
		}
		if (!node[p]) {
			node[p] = { list: [], count: 0 };
			++node.count;
		}
		node = node[p];
	}
	node.list.push(url);
}
function merge_url_tree(tree) {
	return;
	for (var i in tree) {
		if (i != "list") {
			merge_url_tree(tree[i]);
			continue;
		}
		if (!tree.list.length) {
			delete tree.list;
		}
		delete tree.count;
	}
}
function dump_url_tree(parent, tree) {
	for (var i in tree) {
		var li = add_tag(parent, "li");
		dump_url_tree2(li, i, tree[i]);
	}
}
function dump_url_tree2(li, name, tree) {
	var count = tree.count + tree.list.length;
	if (count == 1) {
		if (tree.list.length == 1) {
			add_span(li, tree.list[0], "obj_value");
			return;
		}
		for (var i in tree) {
			if (i == "list" || i == "count") {
				continue;
			}
			dump_url_tree2(li, name + "/" + i, tree[i]);
		}
		return;
	}

	add_span(li, "[");
	var span = add_span(li, name + "(" + count + ")", "obj_type obj_list");
	add_span(li, "] ");
	var ul = add_tag(li, "ul");
	span.onclick = function () {
		toggle(ul);
	}
	for (var i in tree) {
		if (i == "list" || i == "count") {
			continue;
		}
		var li = add_tag(ul, "li");
		dump_url_tree2(li, i, tree[i]);
	}
	for (var i in tree.list) {
		var li = add_tag(ul, "li");
		add_span(li, tree.list[i], "obj_value");
	}
}

function toggle(ul) {
	if (ul.style.display == 'none') {
		ul.style.display = "block";
	}
	else {
		ul.style.display = "none";
	}
}

function is_check(name) {
	var e = $(name);
	if (!e) {
		return false;
	}
	return e.checked;
}

function parse_query(query) {
	query = query.split("&");
	var ret = [];
	for (var i in query) {
		var q = query[i];
		var j = q.indexOf("=");
		if (j != -1) {
			ret[q.substr(0, j)] = q.substr(j + 1);
		}
		else {
			ret[q] = null;
		}
	}
	return ret;
}
function parse_url(url) {
	var ret = { proto: "", host: "", port: 0, path: [], query: {} };
	var a = url.split("?");
	if (a[1]) {
		ret.query = parse_query(a[1]);
	}
	url = a[0];
	var i = url.indexOf("://");
	ret.proto = url.substring(0, i);
	url = url.substring(i + 3);
	ret.path = url.split("/");
	var h = ret.path[0].split(":");
	ret.path.shift();
	ret.host = h[0];
	if (h[1]) {
		ret.port = parseInt(h[1]);
	}
	return ret;
}

String.prototype.reverse = function () {
	return this.split("").reverse().join("");
}
function format_time(t) {
	var d = new Date;
	d.setTime(t * 1000);
	return format_date(d);
}
function format_date(d, ms) {
	ms = ms ? ("." + fill_number(d.getMilliseconds(), 3)) : "";
	return (d.getYear() + 1900) + "/"
		+ fill_number(d.getMonth() + 1, 2) + "/"
		+ fill_number(d.getDate(), 2) + " "
		+ fill_number(d.getHours(), 2) + ":"
		+ fill_number(d.getMinutes(), 2) + ":"
		+ fill_number(d.getSeconds(), 2) + ms;
}
function fill_number(v, n) {
	var s = "" + v;
	while (s.length < n) {
		s = "0" + s;
	}
	return s;
}
function format_duration(t) {
	var sign = "";
	if (t < 0) {
		sign = "-";
		t = -t;
	}
	var d = Math.floor(t / 86400000);
	var h = Math.floor(t / 3600000) % 24;
	var m = Math.floor(t / 60000) % 60;
	var s = Math.floor(t / 1000) % 60;
	var ms = "." + fill_number(t % 1000, 3);

	if (d) {
		return sign + d + " "
		+ fill_number(h, 2) + ":"
		+ fill_number(m, 2) + ":"
		+ fill_number(s, 2) + ms;
	}
	else if (h) {
		return sign + h + ":"
		+ fill_number(m, 2) + ":"
		+ fill_number(s, 2) + ms;
	}
	else if (m) {
		return sign + m + ":"
		+ fill_number(s, 2) + ms;
	}
	else {
		return sign + s + ms;
	}
}
function format_age(t) {
	var sign = "";
	if (t < 0) {
		sign = "-";
		t = -t;
	}
	var d = Math.floor(t / 86400);
	var h = Math.floor(t / 3600) % 24;
	var m = Math.floor(t / 60) % 60;
	var s = t % 60;
	if (d) {
		return sign + d + " "
		+ fill_number(h, 2) + ":"
		+ fill_number(m, 2) + ":"
		+ fill_number(s, 2);
	}
	else if (h) {
		return sign + h + ":"
		+ fill_number(m, 2) + ":"
		+ fill_number(s, 2);
	}
	else if (m) {
		return sign + m + ":"
		+ fill_number(s, 2);
	}
	else {
		return sign + s;
	}
}
function ip_from_int(ip) {
	var a = Array(4);
	for (var i = 0; i < 4; ++i) {
		a[i] = ip >>> (8 * (3 - i)) & 0xFF;
	}
	return a.join(".");
}
function ip_to_int(ip) {
	var a = ip.split(".");
	return a[0] * 16777216 + a[1] * 65536 + a[2] * 256 + a[3] * 1;
}
var green = "#6c6", blue = "#aaf", yellow = "#fb6", red = "#f88", gray = "#ccc", offline = "#aaa", light = "#ddd";
function format_size(s) {
	s += "";
	var ret = "";
	while (s.length > 3) {
		ret = "," + s.substring(s.length - 3) + ret;
		s = s.substring(0, s.length - 3);
	}
	ret = s + ret;
	return ret;
}

function is_ip(ip) {
	var a = ip.split(".");
	if (!a.length || a.length > 4) {
		return false;
	}
	for (var i in a) {
		var x = parseInt(a[i]);
		if (x != a[i] || x > 255) {
			return false;
		}
	}
	return true;
}

function get_query_string(name) {
	var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
	var r = window.location.search.substr(1).match(reg);
	if (r != null) {
		return unescape(r[2]);
	}
	return null;
}