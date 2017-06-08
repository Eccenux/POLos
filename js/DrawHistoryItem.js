/**
 * History item.
 *
 * @param {DrawHistoryItem} config
 */
function DrawHistoryItem(config) {
	this.formData = config.formData;
	this.actionName = config.actionName;
	this.actionData = config.actionData;

	this.time = new Date();
	if (typeof config.time === 'object') {	// assuming Date Object
		this.time = config.time;
	}
	if (typeof config.time === 'string') {	// assuming ISO or other Date recognized string
		this.time = new Date(config.time);
	}
	if (typeof config.time === 'number') {	// assuming UNIX timestamp
		this.time.setTime(config.time);
	}
}

/**
 * Render item.
 * 
 * @param {Boolean?} full If true then short forms should be avoided.
 * @returns {String} HTML to display.
 */
DrawHistoryItem.prototype.render = function(full){
	return ''
		+ this.renderTime(this.time, full)
		+ '; '
		+ this.renderAction(this.actionName, this.actionData, full)
		+ '; '
		+ this.renderForm(this.formData, full)
	;
};

/**
 * Render form data information.
 * @param {Object} formData
 * @param {Boolean?} full If true then short forms should be avoided.
 * @returns {String} html
 */
DrawHistoryItem.prototype.renderForm = function(formData, full){
	var html = "";
	html += '<b>'+formData.label+'</b>: ';
	// get non-empty values
	var values = [];
	for (var i = 0; i < formData.values.length; i++) {
		if (!formData.values[i].value.length) {
			continue;
		}
		values.push(formData.values[i]);
	}
	// no values?
	if (!values.length) {
		html += '&mdash;';
	}
	// render non-empty
	for (var i = 0; i < values.length; i++) {
		/**
		 * @type DrawHistoryValue
		 */
		var val = values[i];
		var className = (i+1 < values.length) ? '': 'last';
		if (full) {
			html += "<span class='profile-data "+className+"'"
				+ "title='"+val.label+': '+val.value+"'"
				+ ">"+val.value+"</span>"
			;
		} else {
			html += "<span class='profile-data "+className+"'"
				+ "title='"+val.label+': '+val.value+"'"
				+ ">"+val.shortValue+"</span>"
			;
		}
	}
	return html;
};

/**
 * Render action specific data.
 * @param {String} actionName
 * @param {Object} actionData
 * @param {Boolean?} full If true then short forms should be avoided.
 * @returns {String} HTML.
 */
DrawHistoryItem.prototype.renderAction = function(actionName, actionData, full) {
	var html = "";
	html = '<b>'+drawHistory.config.labels['action-'+actionName]+'</b>: ';
	// render action data
	if (actionName == 'RandomApi') {
		html += '<a href="#verify" data-RandomApi-result="'+JSON.stringify(actionData.result).replace(/"/g, "&quot;")+'">'
				+drawHistory.config.labels['action-RandomApi-verify']
			+'</a>'
			+' (nr '+actionData.random.serialNumber+')'
		;
	} else {
		html += actionData.grupName
			+ " (" + drawHistory.config.labels['action-GroupChange-for'] + " " + actionData.registrationId + ")"
		;
	}
	return html;
};

/**
 * Zero pad two digit number.
 * @param {Number} value One or two digit number.
 * @returns {String}
 */
DrawHistoryItem.prototype.formatTwoDigit = function(value){
	if (value < 10) {
		return "0" + value;
	}
	return "" + value;
};

/**
 * Redner time.
 * @param {Date} time
 * @param {Boolean?} full If true then short forms should be avoided.
 * @returns {String} HTML.
 */
DrawHistoryItem.prototype.renderTime = function(time, full){
	var html = '';
	if (full) {
		html += ''
			+ time.getFullYear()
			+ '-'
			+ this.formatTwoDigit(time.getMonth())
			+ '-'
			+ this.formatTwoDigit(time.getDate())
			+ ' '
		;
	}
	html += ''
		+ this.formatTwoDigit(time.getHours())
		+ ':'
		+ this.formatTwoDigit(time.getMinutes())
		+ ':'
		+ this.formatTwoDigit(time.getSeconds())
	;
	return html;
};
