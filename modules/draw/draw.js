var drawTestMode = $('#draw-test-mode').is(":visible");

$('#draw-continue').button().button('disable');
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
			item : item,
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
	function onResponse() {
		drawRemaining--;
		if (drawRemaining <= 0) {
			drawDone(drawErrors, drawData.length);
		}
	}
	drawMany(drawData, function(drawItem, random, signature) {
		//console.log(drawRemaining, drawItem, random, signature);
		drawSave(drawItem.item, drawItem.saveId, random, signature, function(){
			onResponse();
		}, function(){
			drawErrors++;
			onResponse();
		});
	}, function(drawItem) {
		console.error('[drawMany] draw failed for item: ', drawItem);
		drawErrors++;
		onResponse();
	});
}

/**
 * Save draw results.
 * @param {Number} saveId
 * @param {Object} random
 * @param {String} signature
 * @param {Function} onSuccess (responseText)
 * @param {Function} onFail (responseText)
 */
function drawSave(drawSection, saveId, random, signature, onSuccess, onFail) {
	//debugger;
	var ids = [];
	saveIdFromSection = drawSection.getAttribute('data-draw-profile');
	var elements = drawSection.querySelectorAll('li');
	for (var i=0; i<random.data.length; i++) {
		try {
			var no = random.data[i];
			ids.push(elements[no-1].getAttribute('data-person'));
		} catch (e) {
			console.error('[drawSave] unable to translate person-no to id', elements.length, no, saveIdFromSection);
			console.error(e);
		}
	}
	$.ajax('?mod=draw&a=save&display=raw', {
		'method':'post',
		'data':{
			'profile': saveId,
			'persons': ids.join(','),
			'verification': JSON.stringify({
				'random' : random,
				'signature' : signature
			})
	}})
	.done(function(response) {
		console.log("[drawSave] saved; response: ", response);
		onSuccess(response);
	})
	.fail(function(ajaxCall) {
		console.error("[drawSave] saving failed; response: ", ajaxCall.responseText);
		onFail(ajaxCall.responseText);
	})
	;
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
		// can continue
		$('#draw-continue').button('enable');
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
