(function($) {
	jQuery(document).ready(function($) {
		$("body").on("click", "a.add_icobtn",
		function(e) {
			e.preventDefault();

			var widget_holder = $(this).closest('.widget-inside');
			var cloner = widget_holder.find('.icobtn_clone');

			widget_holder.find('.btn_container').append('<li>' + cloner.html() + '</li>');

			$(this).trigger('change');
		});

		$("body").on("click", ".remove-icobtn",
		function(e) {
			var delete_item = confirm('确定要删除？');
			delete_item ? $(this).closest('li').remove() : '';
			$('.btn-sortable').trigger('change');
		});

		$("body").on("mousedown", ".btn-sortable li, .wp-picker-holder",
		function(e) {
			$('.btn-sortable').trigger('change');
		});

		ico_btn_sortable();

		$(document).on('widget-added',
		function(e) {
			ico_btn_sortable();
		});

		$(document).on('widget-updated',
		function(e) {
			ico_btn_sortable();
		});

		/*  排序 */
		function ico_btn_sortable() {
			$(".btn-sortable").sortable({
				revert: false,
				cursor: "move",
				delay: 100,
				placeholder: "btn-sortable-drop"
			});
		}

		/* 上传 */
		function mw_media_select() {
			$(document).on('click', '.widgets-upload-btn',
			function(e) {
				e.preventDefault();
				var button = $(this);
				var image = wp.media().open().on('select',
				function(e) {
					var selected = image.state().get('selection').first();
					button.prev().val(selected.toJSON().url).trigger('change');
				});
			});
		}

		mw_media_select();

		if (wp.customize !== undefined) {
			$document.on('widget-updated', mw_media_select);
			$document.on('widget-added', mw_media_select);
		}

	});

})(jQuery);