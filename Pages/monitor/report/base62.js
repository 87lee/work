var base62_char = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
function base62_format_input(input) {
	return input.replace(/[^A-Za-z0-9]/g, "");
}

function base62_to_int(input) {
	input = base62_format_input(input);
	var ret = 0;
	for (var i = 0; i < input.length; ++i) {
		var c = input.charAt(i);
		var value = base62_char.indexOf(c);
		if (value == -1) {
			continue;
		}
		ret = ret * 62 + value;
	}
	return ret;
}
function base62_from_int(value) {
	var ls = "";
	while (value) {
		var l = value % 62;
		ls = base62_char[l] + ls;
		value = (value - l) / 62;
	}
	return ls;
}


function base62_to_byte_array(input) {
	input = base62_format_input(input);
	var ret = [0];
	for (var i = 0; i < input.length; ++i) {
		var c = input.charAt(i);
		var value = base62_char.indexOf(c);
		if (value == -1) {
			continue;
		}
		for (var j = 0; j < ret.length; ++j) {
			ret[j] *= 62;
		}
		ret[0] += value;
		for (j = 0; j < ret.length; ++j) {
			var h = ret[j] >> 8;
			ret[j] &= 0xFF;
			if (!h) {
				continue;
			}
			if (j + 1 == ret.length) {
				ret.push(0);
			}
			ret[j + 1] += h;
		}
	}
	return ret.reverse();
}

function base62_from_byte_array(data) {
	var ret = [0];
	for (var i = 0; i < data.length; ++i) {
		for (var j = 0; j < ret.length; ++j) {
			ret[j] <<= 8;
		}
		var value = data[i];
		ret[0] += value;
		for (j = 0; j < ret.length; ++j) {
			var l = ret[j] % 62;
			var h = (ret[j] - l) / 62;
			ret[j] = l;
			if (!h) {
				continue;
			}
			if (j + 1 == ret.length) {
				ret.push(0);
			}
			ret[j + 1] += h;
		}
	}
	var ls = "";
	for (var j = 0; j < ret.length; ++j) {
		ls = base62_char[ret[j]] + ls;
	}
	return ls;
}
