$(function() {
	$("#import-form").submit(function() {
		var order = [];
		$("#import-sortable-columns li").each(function(){
			var $li = $(this);
			if ($li.hasClass('ignore-column')) {
				order.push('-');
			} else {
				order.push($li.attr('data-column'));
			}
		});
		$('#import-order').val(order.join(','));

		return true;	// OK, submit
		//return false;	// fail
	});
});
