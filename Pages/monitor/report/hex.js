var hex_char = "0123456789abcdef";
var hex_char2 = "0123456789ABCDEF";
//0~15
function hex_get_value(c) {
	var j = hex_char.indexOf(c);
	if (j != -1) {
		return j;
	}
	j = hex_char2.indexOf(c);
	if (j != -1) {
		return j;
	}
	return 0;
}

function hex_format_input(input) {
	return input.replace(/[^A-Fa-f0-9 ]/g, " ");
}
function hex_from_int(value) {
	var ret = "";
	while (value) {
		var j = value & 0xF;
		ret = hex_char.charAt(j) + ret;
		value = (value - j) >>> 4;
	}
	return ret;
}


function hex_to_int(input) {
	input = hex_format_input(input);
	var ret = 0;
	for (var i = 0; i < input.length; ++i) {
		ret = (ret << 4) | hex_get_value(input.charAt(i));
	}
	return ret;
}
function hex_from_byte(b) {
	return hex_char.charAt((b >>> 4) & 0xF) + hex_char.charAt(b & 0xF);
}
function hex_to_byte(str) {
	return (hex_get_value(str.charAt(0)) << 4) | hex_get_value(str.charAt(1));
}
//var hex_array = ["4d", "7f"];
function hex_array_from_byte_array(a) {
	var ret = [];
	for (var j = 0; j < a.length; ++j) {
		ret.push(hex_from_byte(a[j]));
	}
	return ret;
}
//var byte_array = [0x4d, 0x7f];
function hex_array_to_byte_array(a) {
	var ret = [];
	for (var j = 0; j < a.length; ++j) {
		ret.push(hex_to_byte(a[j]));
	}
	return ret;
}
//hex_text = "0123456789abcdef";
function hex_array_from_hex_text(input) {
	var input = hex_format_input(input);
	var a = input.split(" ");
	var ret = [];
	for (var i = 0; i < a.length; ++i) {
		var x = a[i];
		var j = 0;
		if (x.length & 1) {
			ret.push("0" + x.charAt(0));
			++j;
		}
		while (j < x.length) {
			ret.push(x.substr(j, 2));
			j += 2;
		}
	}
	return ret;
}

//var string = "!@!@#!";
function byte_array_from_string(str) {
	var ret = [];
	for (var i = 0; i < str.length; ++i) {
		ret.push(str.charCodeAt(i));
	}
	return ret;
}
function byte_array_to_string(a) {
	var ret = "";
	for (var i = 0; i < a.length; ++i) {
		ret += String.fromCharCode(a[i]);
	}
	return ret;
}
//utf8_string
function byte_array_from_utf8_string(str) {
	var ret = [];
	for (var i = 0; i < str.length; ++i) {
		var c = str.charCodeAt(i);
		if (c < 0x80) {
			ret.push(c);//0xxxxxxx
		}
		else if (c < 0x800) {
			ret.push((c >> 6) | 0xc0);//110xxxxx
			ret.push((c & 0x3f) | 0x80);//10xxxxxx
		}
		else {
			ret.push((c >> 12) | 0xe0);//1110xxxx
			ret.push(((c >> 6) & 0x3f) | 0x80);//10xxxxxx
			ret.push((c & 0x3f) | 0x80);//10xxxxxx
		}
	}
	return ret;
}
function byte_array_to_utf8_string(data) {
	var i = 0;
	var str = "";
	while (i < data.length) {
		var c = data[i];
		if (c < 0x80) {//0xxxxxxx
			str += String.fromCharCode(c);
			++i;
		}
		else if (c < 0xc0) {//10xxxxxx
			++i;
		}
		else if (c < 0xe0) {//110xxxxx
			str += String.fromCharCode(((c & 0x1f) << 6) | (data[i + 1] & 0x3f));
			i += 2;
		}
		else if (c < 0xf0) {//1110xxxx
			str += String.fromCharCode(((c & 0xf) << 12) | ((data[i + 1] & 0x3f) << 6) | (data[i + 2] & 0x3f));
			i += 3;
		}
		else {
			return null;
		}
	}
	return str;
}

