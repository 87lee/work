function init() {
	var host = get_query_string("host");
	if (host) {
		var arr = host.split(":");
		$("stb_ip").value = arr[0];
		$("stb_port").value = arr[1];
		start2();
	}
	var id = get_query_string("id");
	if (id) {
		$("mode1").style.display = "none";
		$("mode2").style.display = "";

		load_info(id);
	}
	show_report();
}
var http = null;
var task = 0;
function stop() {
	result.innerText = "";
	if (http) {
		http.onreadystatechange = null;
		http.abort();
		http = null;
	}
	if (task) {
		clearTimeout(task);
		task = 0;
	}
}
function clear() {
}
function start2() {
	stop();
	update();
}
function update() {
	task = 0;
	http = ajax2("http://" + $("stb_ip").value + ":" + $("stb_port").value + "/report", ajax_state, parse_report);
}
function parse_report(text) {
	if (text.length) {
		$("stb_ip").disabled = true;
		$("stb_port").disabled = true;
	}
	result.innerText = result.innerText + " " + text.length;
	http = null;
	var a = text.split("\n");
	add_report_list(a);
	if ($("repeat").checked) {
		task = setTimeout(update, 1000);
	}
};
function add_report_list(a) {
	var n = report_table.children.length;
	for (var i = 0 ; i < a.length; ++i) {
		var l = a[i];
		if (l.length < 2) {
			continue;
		}
		var j = l.indexOf(":{");
		if (j <= 0) {
			console.log(l);
			continue;
		}
		add_report(parseInt(l.substr(0, j)), l.substr(j + 1));
	}
	if ($("scroll").checked && n != report_table.children.length) {
		tail.scrollIntoView();
	}
}
var report_color = {
	open_url_report: "blue",
	timing_report: "",
	preroll_report: "green",
	play_error_report: "red",
	buffering_report: "yellow",
	close_url_report: "gray"
};
var report_type = {
	open_url_report: "打开",
	timing_report: "计时",
	preroll_report: "播放",
	play_error_report: "错误",
	buffering_report: "缓冲",
	close_url_report: "关闭"
};
var action_type = {
	0: "程序启动",
	1: "上下键",
	2: "左右键",
	3: "菜单",
	4: "数字",
	5: "Single超时",
	6: "Multi-HD超时",
	7: "Multi-SD超时",
	8: "Multi-ALL超时",
	9: "Letv解析失败",
	10: "Single解析失败",
	11: "Multi失败切P2P",
	12: "Letv超时",
	13: "Letv缓冲",
	14: "缓冲3次",
	15: "上次用过P2P",
	16: "缓冲太久",
	17: "播放器出错",
	18: "画面静止",
	19: "循环一周",
	20: "蓝天白云",
	21: "没有其他源",
	22: "回播加载失败",
	23: "已超过一分钟",

	31: "向上↑",
	32: "向下↓",
	33: "向右→",
	34: "向左←",
	35: "进入",//回播
	36: "退出",//回播
	37: "等待超时",
	38: "网络断开",
	39: "网络恢复",
	40: "菜单",//换源

	100: "程序退出"
};
var mode_color = {
	0: "gray",
	3: "green",
	4: "blue",
	5: "gray",
	6: "gray",
	7: "gray",
	8: "yellow",
	9: "yellow"
};
var mode_type = {
	0: "Wait",
	3: "Letv",
	4: "Single",
	5: "Multi-HD",
	6: "Multi-SD",
	7: "Multi-ALL",
	8: "P2P",
	9: "Letv-So"
}
var open_type = {
	1: "换台",
	2: "换源",
	3: "重开",
	4: "回播",
	5: "等待",
}
var error_type = {
	1: "解析",
	2: "下载",
	3: "播放"
}

function find_type(list, value) {
	var s = list[value];
	return s ? s : value;
}
var special_list = {};
var report_list = {};
var extra_list = {};
var channelSession, urlSession;
var channelIndex, urlIndex;
var channelTime, urlTime;
var lastUrl = "";
var lastTime = 0;
var commom_key = {
	channelSession: 1,
	urlSession: 1,
	desc: 1,
	geo: 1,
	id: 1,
	platform: 1,
	report: 1,
	sn: 1,
	url: 1,
	seq: 1,
	version: 1,
	os: 1,
	model: 1,
};
var ls_err = {
	0: "成功",
	400: "不支持",
	413: "stream_id不存在",
	416: "重定向stream_id",
	417: "splatid无权限",
	418: "缺少expect",
	419: "splatid无效",
	420: "缺少stream_id",
	421: "UserAgent错误",
	422: "不允许path",
	423: "1代密钥(key)错误",
	424: "tm不合法",
	425: "1代密钥(key)和splatid不匹配",
	426: "缺少splatid",
	428: "lstm不合法",
	429: "2代密钥(linkshell)错误",
	432: "mac黑名单",
	433: "2代密钥(linkshell)算法失效",
};

function add_report(t, text) {
	if (report_list[t]) {
		return;
	}
	var obj;
	try {
		eval("obj=" + text);
	}
	catch (e) {
		return;
	}
	report_list[t] = obj;

	if (channelSession != obj.channelSession) {
		var tr = add_tr(report_table);
		var th = add_th(tr, "时间");
		th.style.width = "165px";
		add_th(tr, "频道名称");
		add_th(tr, "停留时间");
		add_th(tr, "持续时间");
		add_th(tr, "时差");
		add_th(tr, "事件");
		add_th(tr, "序号");
		add_th(tr, "摘要");
	}
	else if (urlSession != obj.urlSession) {
		var tr = add_tr(report_table);
		tr.className = "sp";
	}
	var i = report_table.children.length;
	var tr = add_tr(report_table);
	add_td(tr, format_date(new Date(t), true));
	if (channelSession != obj.channelSession) {
		channelSession = obj.channelSession;
		channelTime = t;
		channelIndex = i;
		lastUrl = "";
		var td = add_td(tr, obj.id);
		if (obj.id == except_channel) {
			td.style.backgroundColor = "#f88";
			td.title = excpet_event;
		}
		add_td(tr);
	}
	else {
		report_table.children[channelIndex].children[1].rowSpan = i - channelIndex + 1;
		report_table.children[channelIndex].children[2].rowSpan = i - channelIndex + 1;
		report_table.children[channelIndex].children[2].innerText = format_duration(t - channelTime);
	}

	if (urlSession != obj.urlSession) {
		urlSession = obj.urlSession;
		urlTime = t;
		urlIndex = i;
		lastUrl = "";
		add_td(tr);
	}
	else {
		var j = (urlIndex == channelIndex) ? 3 : 1;
		report_table.children[urlIndex].children[j].rowSpan = i - urlIndex + 1;
		report_table.children[urlIndex].children[j].innerText = format_duration(t - urlTime);
	}
	var diff = t - lastTime;
	if (lastTime) {
		add_td(tr, format_duration(diff));
	}
	else {
		add_td(tr);
	}
	lastTime = t;

	var td = add_td(tr, find_type(report_type, obj.report));
	td.className = find_type(report_color, obj.report);
	add_td(tr, obj.seq);

	td = add_td(tr);
	td.style.textAlign = "left";
	if (obj.report == "open_url_report" || obj.report == "close_url_report") {
		add_span(td, find_type(action_type, obj.action), (obj.action > 4 && obj.action < 30) ? "red" : "");
		add_span(td, find_type(open_type, obj.type));
		add_span(td, obj.report == "open_url_report" ? "打开" : "关闭");
		add_span(td, find_type(mode_type, obj.mode), find_type(mode_color, obj.mode));
		if (obj.close_time) {
			add_span(td, "关闭:" + obj.close_time, obj.close_time > 500 ? "red" : "blue");
		}
	}
	if (obj.report == "timing_report") {
		add_span(td, "步骤:" + obj.step);
		add_span(td, "耗时:" + obj.time, obj.time > 300 ? "red" : obj.time > 150 ? "yellow" : obj.time > 50 ? "blue" : "");
		diff -= obj.time;
		add_span(td, "误差:" + diff, (diff < 0 || diff > 50) ? "red" : (diff > 20 ? "yellow" : ""));
		if (!obj.data) {
			add_span(td, "无内容");
		}
		else if (obj.step == "linkshell") {
			var data = obj.data;
			add_link2(add_span(td, ""), "长度:" + data.length, data);

			var ps = data.split("&");
			for (var i in ps) {
				var b = ps[i].split("=");
				if (b[0] == "lstm") {
					var tm = base62_to_int(b[1]);
					var age = parseInt(tm + 5 * 60 - t / 1000);
					var span = add_span(td, "时效:" + format_age(age), age > 60 ? "" : age > 0 ? "gray" : "red");
					span.title = format_time(tm);
				}
				else if (b[0] == "lsbv") {
					add_span(td, "bv:" + base62_to_int(b[1]));
				}
				else if (b[0] == "lssv") {
					var c = b[1].split("_");
					var splatid = null;
					var ver = null;
					var salt = "salt:" + base62_to_int(c[0]);
					var title = [salt];
					for (var j = 1 ; j < c.length; ++j) {
						var n = c[j].charAt(0);
						var v = c[j].substr(1);
						if (n == "M") {
							title.push("mac:" + hex_array_from_byte_array(base62_to_byte_array(v)).join(":"));
						}
						else if (n == "L") {
							title.push("ip:" + base62_to_byte_array(v).join("."));
						}
						else if (n == "V") {
							ver = "ver:" + base62_to_int(v);
							title.push(ver);
						}
						else if (n == "S") {
							splatid = "splatid:" + base62_to_int(v);
							title.push(splatid);
						}
						else if (n == "T") {
							title.push("type:" + base62_to_int(v));
						}
						else if (n == "I") {
							title.push("pip:" + base62_to_int(v));
						}
						else if (n == "P") {
							title.push("package:" + v);
						}
						else {
							title.push(n + ":" + v);
						}
					}
					var span = add_span(td, splatid || ver || salt);
					span.title = title.join("\n").replace(/:/g, " = ");
				}
			}
		}
		else if (obj.step == "cdekey") {
			var data = obj.data;
			add_link2(add_span(td, ""), "长度:" + data.length, data);

			var ps = data.split("&");
			for (var i in ps) {
				var b = ps[i].split("=");
				if (b[0] == "cdetm") {
					var tm = parseInt(b[1]);
					var age = parseInt(tm + 5 * 60 - t / 1000);
					var span = add_span(td, "时效:" + format_age(age), age > 60 ? "" : age > 0 ? "gray" : "red");
					span.title = format_time(tm);
				}
				else if (b[0] == "cde") {
					add_span(td, "cde:" + b[1]);
				}
			}
		}
		else {
			add_span(td, "长度:" + obj.data);
		}
	}
	//if (obj.report == "close_url_report") {
	//	add_span(td, find_type(action_type, obj.action));
	//}
	if (obj.report == "preroll_report") {
		var extra = {};
		extra.preroll_time = obj.load_time + obj.open_time;
		extra.play_time = obj.render_start;
		if (extra.play_time < obj.buffer_start) {
			extra.play_time = obj.buffer_start;
		}
		if (extra.play_time < obj.buffer_end) {
			extra.play_time = obj.buffer_end;
		}
		if (extra.play_time < obj.decode_size) {
			extra.play_time = obj.decode_size;
		}
		if (extra.play_time < obj.prepare_time) {
			extra.play_time = obj.prepare_time;
		}
		extra.total_time = obj.parse_time + extra.preroll_time + obj.close_time + extra.play_time;
		extra_list[t] = extra;

		add_span(td, "总耗时:" + extra.total_time, extra.total_time > 4000 ? "red" : "gray");
		var real_time = t - obj.urlSession;
		diff = real_time - extra.total_time;
		var span = add_span(td, "误差:" + diff, (diff < 0 || diff > 200) ? "red" : diff > 100 ? "yellow" : "");
		span.title = "实际耗时:" + real_time;
		if (obj.parse_time) {
			add_span(td, "解析:" + obj.parse_time, obj.parse_time > 500 ? "red" : "green");
		}
		var span = add_span(td, "预读:" + extra.preroll_time, extra.preroll_time > 2000 ? "red" : "yellow");
		span.title = "打开:" + obj.open_time + "\n下载:" + obj.load_time;
		add_span(td, "比例:" + obj.progress + "%", obj.progress > 100 ? "red" : "");
		add_span(td, "速率:" + obj.rate + "K", obj.rate < 256 ? "red" : "");
		if (obj.close_time) {
			add_span(td, "关闭:" + obj.close_time, obj.close_time > 500 ? "red" : "blue");
		}
		span = add_span(td, "播放:" + extra.play_time, (!extra.play_time || !obj.decode_size || !obj.render_start || extra.play_time > 1000) ? "red" : "blue");
		span.title = "准备:" + parseInt(obj.prepare_time) + "\n缓冲:" + obj.buffer_start + "\n解码:" + obj.decode_size + "\n结束:" + obj.buffer_end + "\n播放:" + obj.render_start;
	}
	if (obj.report == "buffering_report") {
		// 次数 下载总量 网络差 缓冲时间 剩余数据 积压数据 速率
		var extra = {};
		extra.network_diff = obj.duration - obj.system_time;
		extra.buffer_time = obj.system_time - obj.player_time;
		extra.actual_diff = obj.diff - obj.progress * 100;
		extra_list[t] = extra;

		add_span(td, "次数:" + obj.buffer_count);
		add_span(td, "下载量:" + format_duration(obj.duration), obj.duration < 10000 ? "red" : "gray");
		var span = add_span(td, "网络差:" + format_duration(extra.network_diff), "red");
		span.title = "下载量:" + format_duration(obj.duration) + "\n-系统时间:" + format_duration(obj.system_time);
		span = add_span(td, "累计缓冲:" + format_duration(extra.buffer_time), "blue");
		span.title = " 系统时间:" + format_duration(obj.system_time) + "\n-播放时间:" + format_duration(obj.player_time);
		span = add_span(td, "剩余:" + format_duration(obj.diff), "yellow");
		span.title = "下载量:" + format_duration(obj.duration) + "\n-播放时间:" + format_duration(obj.player_time);
		span = add_span(td, "积压:" + obj.progress + "%", obj.progress ? "red" : "");
		if (obj.progress) {
			span.title = span.title = "实际剩余:" + format_duration(extra.actual_diff);
		}
		add_span(td, "速率:" + obj.rate + "K", obj.rate ? "red" : "");
	}
	if (obj.report == "play_error_report") {
		add_span(td, find_type(error_type, obj.step));
		if (obj.step == 2) {
			add_span(td, "比例:" + obj.code1 + "%", parseInt(obj.code1) > 0 ? "red" : "");
			add_span(td, "速率:" + obj.code2 + "K", parseInt(obj.code2) > 0 ? "red" : "");
		}
		else if (obj.step == 3) {
			add_span(td, "what:" + obj.code1);
			add_span(td, "extra:" + obj.code2);
		}
		else {
			var span = add_span(td, obj.code1);
			var x = (obj.code1 + "").split(":");
			if (x.length == 2 && x[0] == "gslb") {
				span.title = ls_err[x[1]] || "";
			}
			add_span(td, obj.code2);
		}
	}
	if (obj.url && obj.url.length) {
		var u = parse_url(obj.url);
		if (u.query.ntm) {
			var age = parseInt(u.query.ntm - t / 1000);
			var span = add_span(td, "时效:" + format_age(age), age > 7200 ? "" : age > 3600 ? "gray" : "red");
			span.title = format_time(u.query.ntm);
		}
	}
	else {
		add_span(td, "缺少url字段", "red");
	}
	if (obj.url && lastUrl != obj.url) {
		if (obj.report != "timing_report") {
			lastUrl = obj.url;
		}
		var u = parse_url(obj.url);
		if (u.path[0] == "play" && u.query.enc == "base64") {
			var gslb = byte_array_to_string(base64_to_byte_array(u.query.url));
			u = parse_url(gslb);
			add_link2(add_span(td, ""), "splatid:" + u.query.splatid, gslb);
		}
		add_link(add_span(td, ""), obj.url);
	}
	tr.onmouseover = function () {
		show_detail(t);
	};
	var special = {};
	for (var i in obj) {
		if (commom_key[i]) {
			continue;
		}
		special[i] = obj[i];
		delete obj[i];
	}
	special_list[t] = special;
}
function add_link(span, url) {
	add_link2(span, url.substr(0, 40), url);
}
function add_link2(span, text, url) {
	var a = add_a(span, text, "javascript:copy_url('" + url + "')");
	a.title = url.replace("?", "\n?").replace(/&/g, "\n&");
}
function show_detail(t) {
	report_content.innerHTML = "";
	extra_content.innerHTML = "";
	special_content.innerHTML = "";
	var obj = report_list[t];
	dump_json(report_content, "commmon", obj);
	var special = special_list[t];
	dump_json(special_content, obj.report, special);
	var extra = extra_list[t];
	if (extra) {
		dump_json(extra_content, "extra", extra);
	}

	report_geo.innerText = obj.geo;
	report_desc.innerText = obj.desc;

	var channelSession = parseInt(obj.channelSession);
	var urlSession = parseInt(obj.urlSession);
	report_time.innerText = t;
	report_time2.innerText = format_date(new Date(t), true);
	report_time3.innerText = format_duration(t - urlSession);
	report_channelSession.innerText = obj.channelSession;
	report_channelSession2.innerText = format_date(new Date(channelSession), true);
	report_urlSession.innerText = obj.urlSession;
	report_urlSession2.innerText = format_date(new Date(urlSession), true);
	report_urlSession3.innerText = format_duration(urlSession - channelSession);
	report_platform.innerText = obj.platform;
	report_os.innerText = obj.os;
	report_model.innerText = obj.model;

	report_version.innerText = obj.version;
	report_sn.innerText = obj.sn;
	report_id.innerText = obj.id;
	report_seq.innerText = obj.seq;

	if (obj.report == "preroll_report") {
		parse_time.innerText = special.parse_time;
		open_time.innerText = special.open_time;
		load_time.innerText = special.load_time;
		prepare_time.innerText = parseInt(special.prepare_time);
		preroll_time.innerText = extra.preroll_time;
		preroll_progress.innerText = special.progress + "%";
		preroll_rate.innerText = special.rate + "K";
		close_time.innerText = special.close_time;
		buffer_start.innerText = special.buffer_start;
		buffer_end.innerText = special.buffer_end;
		decode_size.innerText = special.decode_size;
		render_start.innerText = special.render_start;
		prepare_time.innerText = parseInt(special.prepare_time);
		play_time.innerText = extra.play_time;
		total_time.innerText = extra.total_time;
	}
	if (obj.report == "buffering_report") {
		buffer_count.innerText = special.buffer_count;
		duration.innerText = format_duration(special.duration);
		network_diff.innerText = format_duration(extra.network_diff);
		system_time.innerText = format_duration(special.system_time);
		buffer_time.innerText = format_duration(extra.buffer_time);
		player_time.innerText = format_duration(special.player_time);
		player_diff.innerText = format_duration(special.diff);
		actual_diff.innerText = format_duration(extra.actual_diff);
		buffer_progress.innerText = special.progress + "%";
		buffer_rate.innerText = special.rate + "K";
	}
	show_report(obj.report);
}
function show_report(type) {
	common_report.style.display = type ? "" : "none";
	preroll_report.style.display = type == "preroll_report" ? "" : "none";
	buffering_report.style.display = type == "buffering_report" ? "" : "none";
}
function open_report_url() {
	window.open("http://" + $("stb_ip").value + ":8089/report", "_blank");
}

function add_info_title(table, text) {
	var tr = add_tr(table);
	add_td(tr, "");
	add_th(tr, text);
}
function add_info_field(table, name, text, title) {
	var tr = add_tr(table);
	add_th(tr, name);
	var td = add_td(tr, text);
	if (title) {
		td.title = title;
	}
}

function add_info_time(table, name, time, ref) {
	if (!time) {
		return add_info_field(table, name, "无效");
	}
	if (ref) {
		return add_info_field(table, name, format_date(new Date(time), true), format_duration(time - ref));
	}
	return add_info_field(table, name, format_date(new Date(time), true));
}
function load_info(id) {
	var url = "http://121.42.58.242:20000/userReport/" + id + ".log";
	report_url.innerText = id;
	report_url.href = url;
	ajax2(url, ajax_state, parse_info);
}

var except_channel, excpet_event;
function parse_info(text) {
	try {
		eval("var obj=" + text);
	}
	catch (e) {
		alert(e.message);
		return;
	}

	var content = obj.content;
	add_info_title(basic_table, "基本信息");
	add_info_field(basic_table, "sn", obj.sn);
	add_info_field(basic_table, "model", obj.model);
	add_info_field(basic_table, "vendorID", obj.vendorID);
	add_info_field(basic_table, "mac", obj.mac);
	add_info_field(basic_table, "wifiMac", obj.wifiMac);
	add_info_field(basic_table, "p2p", content.sn);
	add_info_field(basic_table, "版本", content.version);
	add_info_field(basic_table, "频道", content.channel);
	add_info_field(basic_table, "症状", content.event);
	except_channel = content.channel;
	excpet_event = content.event;

	add_info_title(basic_table, "乐视g3");
	add_info_field(basic_table, "请求时间", format_time(content.g3_time));
	add_info_field(basic_table, "ip", content.g3_ip);
	add_info_field(basic_table, "geo", content.g3_region);
	add_info_field(basic_table, "desc", content.g3_desc);
	add_info_field(basic_table, "gslb", (content.gslb_ip || "").split(",").join("\n"));

	add_info_title(basic_table, "接口更新");
	add_info_field(basic_table, "开机时间", format_duration(content.up_time));
	add_info_time(basic_table, "参考时间", content.ref_time);
	add_info_time(basic_table, "系统时间", content.os_time, content.ref_time);
	add_info_time(basic_table, "启动时间", content.start_time, content.ref_time);
	add_info_time(basic_table, "配置文件", content.config_time, content.os_time);
	add_info_time(basic_table, "首次频道", content.live1_time, content.os_time);
	add_info_time(basic_table, "非首次", content.live2_time, content.os_time);
	add_info_time(basic_table, "升级", content.updateApp_time, content.os_time);
	add_info_time(basic_table, "开机广告", content.bootad_time, content.os_time);
	add_info_time(basic_table, "频道广告", content.disad_time, content.os_time);
	add_info_time(basic_table, "服务列表", content.upsrc_time, content.os_time);

	add_info_title(config_table, "配置文件");
	try {
		var data = base64_to_byte_array(content.config);
		var key = byte_array_from_string("YZV3141592653589");
		var iv = byte_array_from_string("PLANCKH413566743");
		var out = aes_cbc_decrypt(data, key, iv);
		var tail = aes_tail(out);
		if (tail) {
			out = out.slice(0, out.length - tail);
		}
		eval("var config = " + byte_array_to_string(out));
		for (var i in config) {
			add_info_field(config_table, i, config[i].split(",").join("\n"));
		}
	}
	catch (e) {
		bad_config.innerText = content.config;
		add_info_field(config_table, "解析出错", e.message);
	}

	add_info_title(channel_table, "频道更新");
	var type = content.type;
	for (var i in type) {
		add_info_time(channel_table, i, type[i], content.os_time);
	}

	add_info_title(epg_table, "EPG更新");
	var epg = content.epg;
	for (var i in epg) {
		add_info_time(epg_table, i, epg[i], content.os_time);
	}

	add_report_list(content.report);
}

function toggle_info() {
	if ($("info_div").style.display == "none") {
		$("info_div").style.display = "";
	}
	else {
		$("info_div").style.display = "none";
	}
}