var ptList;

(function($){

var $document = $(document);

// --------------------------------------------------------------------

/**
 * P&T List
 */

ptList = function($ul){

	var $lastLi = $('> li:last-child', $ul),
		$input = $('input', $lastLi),
		name = $input.attr('name');

	$('> li:not(:last-child)', $ul).each(function(){
		new ptListItem($(this), $input);
	});

	var initSortable = function(){
		$ul.sortable({
			axis: 'y',
			items: 'li:not(:last-child)'
		});
	}
	initSortable();

	$input.keydown(function(event){
		switch(event.keyCode){
			case 13: // return
				var val = $input.val();
				if (val) {
					val = val.replace(/"/g, '\"');
					var $li = $('<li><span>'+val+'</span></li>').insertBefore($lastLi),
					 	$hidden = $('<input type="hidden" name="'+name+'" value="'+val+'" />').appendTo($li);
					new ptListItem($li, $input);
					initSortable();
				}
				break;
			case 27: // esc
				if ($input.val()) {
					event.stopPropagation();
				}
				break;
			case 38: // up
				$('span', $lastLi.prev()).focus();
			default:
				return;
		}

		$input.val('');
		event.preventDefault();
	});
};


/**
 * List Item
 */
var ptListItem = function($li, $mainInput){

	var $span = $('span:first', $li).attr('tabindex', 0),
		$input = $('input:first', $li),
		$text,
		val = $span.html();

	var removeItem = function(){
		$('span,input[type=text]', $li.next()).focus();
		$li.remove();
	}

	var editItem = function(){
		$li.addClass('input');
		$text = $('<input type="text" class="field" style="width: 100%" />').val(val.replace('&amp;', '&'));
		$span.replaceWith($text);
		$text.focus();

		$text.keydown(function(event){
			switch(event.keyCode){
				case 38: // up
					if ($('span', $li.prev()).focus().length) {
						saveItem($text.val());
					}
					break;
				case 40: // down
					saveItem($text.val());
					$('span,input[type=text]', $li.next()).focus();
					break;
				case 13: // return
					saveItem($text.val());
					$mainInput.focus();
					break;
				case 27: // esc
					saveItem(val);
					$span.focus();
					event.stopPropagation();
					break;
				default:
					return;
			}

			event.preventDefault();
		});

		$text.blur(function(){
			saveItem($text.val());
		});
	};

	var saveItem = function(newVal) {
		if (!newVal) {
			removeItem();
			return;
		}

		if (newVal != val) {
			val = newVal;
			$span.html(val);
			$input.val(val.replace(/"/g, '\"'));
		}

		$li.removeClass('input');
		$text.replaceWith($span);
		bindEvents();
	};

	var bindEvents = function(){
		$span.dblclick(editItem);

		$span.keydown(function(event){
			switch(event.keyCode) {
				case 38: // up
					$('span', $li.prev()).focus();
					break;
				case 40: // down
					$('span,input[type=text]', $li.next()).focus();
					break;
				case 13: // return
					editItem();
					break;
				case 8: // delete
					removeItem();
					break;
				default:
					return;
			}

			event.preventDefault();
		});
	};

	bindEvents();
};


})(jQuery);
