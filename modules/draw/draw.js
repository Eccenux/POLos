var drawTestMode = $('#draw-test-mode').is(":visible");

$('#draw-starter').click(function(){
	$('#draw-starter').button('disable');
	drawStart();
});

/**
 * Start draw proccess.
 */
function drawStart() {
	var drawData = [];
	$('#draw-lists section').each(function(){
		var item = this;
		drawData.push({
			saveId : parseInt(item.getAttribute('data-draw-profile')),
			min : 1,
			max : parseInt(item.getAttribute('data-draw-persons')),
			n : parseInt(item.getAttribute('data-draw-invites'))
		});
		// stop early on in test-mode
		if (drawTestMode && drawData.length >= 3) {
			return false;
		}
	});
	var drawRemaining = drawData.length;
	var drawErrors = 0;
	drawMany(drawData, function(drawItem, random, signature) {
		console.log(drawRemaining, drawItem, random, signature);
		drawRemaining--;
		if (drawRemaining <= 0) {
			drawDone(drawErrors, drawData.length);
		}
	}, function(drawItem) {
		console.error('draw failed for item: ', drawItem);
		drawRemaining--;
		if (drawRemaining <= 0) {
			drawDone(drawErrors, drawData.length);
		}
	});
}

/**
 * Run after all draws finished.
 * @param {Number} drawErrors
 * @param {Number} total
 */
function drawDone(drawErrors, total) {
	if (drawErrors > 0) {
		alert(
			'Część losowań ('+drawErrors +'/'+ total+') nie powiodło się!'
			+'\n\n'
			+'Sprawdź połączenie z Internetem i spróbuj ponownie. Informacje techniczne znajdują się w konsoli JS.'
		);
	} else {
		alert(
			'Wszystkie losowania ('+total+') zostały zakończone pomyślnie.'
		);
	}
}

/**
 * Draw many sets of integers.
 * @param {Array} drawData Array of draw items.
 * @param {Function} onSuccess (item, random, signature); `random.data` contains the integers.
 * @param {Function} onFail (item)
 */
function drawMany(drawData, onSuccess, onFail) {
	$(drawData).each(function(){
		var item = this;
		console.log(item);
		randomApi.drawIntegers(item.min, item.max, item.n, false)
			.done(function(random, signature){
				// return value
				onSuccess(item, random, signature);
			})
			.fail(function(){
				// return error
				onFail(item);
			})
		;
	});
}
