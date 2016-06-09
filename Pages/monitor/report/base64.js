var base64_char = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";

function base64_format_input(input) {
	input = input.replace(/[^A-Za-z0-9\+\/]/g, "");
	var pad = input.length % 4;
	if (pad == 1) {
		return input + "A==";
	}
	if (pad == 2) {
		return input + "==";
	}
	if (pad == 3) {
		return input + "=";
	}
	return input;
}

function base64_to_byte_array(input) {
	input = base64_format_input(input);
	var ret = [];
	var i = 0;
	while (i < input.length) {
		var a = base64_char.indexOf(input.charAt(i++));
		if (a == 64) {
			break;
		}
		var b = base64_char.indexOf(input.charAt(i++));
		if (b == 64) {
			break;
		}
		ret.push((a << 2) | (b >> 4));
		var c = base64_char.indexOf(input.charAt(i++));
		if (c == 64) {
			break;
		}
		ret.push(((b & 15) << 4) | (c >> 2));
		var d = base64_char.indexOf(input.charAt(i++));
		if (d == 64) {
			break;
		}
		ret.push(((c & 3) << 6) | d);
	}
	return ret;
}

function base64_from_byte_array(data) {
	var i = 0;
	var str = "";
	while (i < data.length) {
		var a = data[i++];
		str = str + base64_char[a >> 2];
		if (i == data.length) {
			return str + base64_char[(a & 3) << 4] + "==";
		}
		var b = data[i++];
		str = str + base64_char[((a & 3) << 4) | (b >> 4)];
		if (i == data.length) {
			return str + base64_char[(b & 15) << 2] + "=";
		}
		var c = data[i++];
		str = str + base64_char[((b & 15) << 2) | (c >> 6)] +
		base64_char[c & 63];
	}
	return str;
}

function base64_from_string(str) {
	return base64_from_byte_array(byte_array_from_string(str));
}
function base64_from_utf8_string(str) {
	return base64_from_byte_array(byte_array_from_utf8_string(str));
}