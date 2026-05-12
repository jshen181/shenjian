jQuery(document).ready(function() {
	var lang = jQuery('html').attr('lang');
	var copy_text_label = '<i class="be be-clipboard"></i>';
	var copyButtonText = '复制';
	var copySucceedText = '复制成功';

	if (lang === 'zh-Hans') {
		copyButtonText = '复制';
		copySucceedText = '复制成功';
	} else if (lang === 'zh-TW') {
		copyButtonText = '複製';
		copySucceedText = '複製成功';
	} else if (lang === 'en-US') {
		copyButtonText = 'Copy Code';
		copySucceedText = 'Copy Success';
	}
	var copied_text_label = '<i class="dashicons dashicons-yes"></i>' + copySucceedText;
	var copyButton = '<div class="btn-clipboard bgt">' + copy_text_label + copyButtonText + '</div>';
	jQuery('pre').each(function() {
		jQuery(this).wrap('<div class="codebox"/>');
	});

	jQuery('div.codebox').prepend(jQuery(copyButton)).children('.btn-clipboard').show();
	var copyCode = new ClipboardJS('.btn-clipboard', {
		target: function(trigger) {
			return trigger.nextElementSibling;
		}
	});

	copyCode.on('success',
	function(event) {
		event.clearSelection();
		event.trigger.innerHTML = copied_text_label;
		jQuery('.dashicons-yes').closest('.codebox').addClass('pre-loading');
		window.setTimeout(function() {
			event.trigger.innerHTML = copy_text_label + copyButtonText;
			jQuery('.codebox').removeClass('pre-loading');
		},
		500);
	});

	copyCode.on('error',
	function(event) {
		window.setTimeout(function() {
			event.trigger.textContent = copy_text_label + copyButtonText;
		},
		2000);
	});

});