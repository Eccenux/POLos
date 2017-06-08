/**
 * Form value definition helper.
 * @param {DrawHistoryValue} config
 */
function DrawHistoryValue(config) {
	this.label = '' + config.label;
	this.value = '' + (typeof config.value === 'function' ? config.value() : config.value);
	this.shortValue = this.value;
	if (typeof config.shortValue === 'function') {
		this.shortValue = config.shortValue(this.value);
	} else if (typeof config.shortValue === 'string') {
		this.shortValue = config.shortValue;
	}
}
DrawHistoryValue.firstLetter = function (value) {
	return (!value.length) ? "" : value.substr(0, 1) + ".";
};
DrawHistoryValue.shortWords = function (value) {
	return (!value.length) ? "" : value.replace(/[ .,]+/g, ' ').replace(/\s*-\s*/g, '-').replace(/([^ -]{2,3})[^ -]*/g, '$1.');
};
DrawHistoryValue.range = function (from, to) {
	if (to.length) {
		if (!from.length) {
			from = '0';
		}
		return from + "-" + to;
	} else if (from.length) {
		return from + "+";
	}
	return "";
};