(function ($) {
    if (!window.Be) {
        window.Be = {};
    }
    Be.CloneWidgets = {
        init: function () {
            $("body").on("click", ".widget-control-actions .clone-me", Be.CloneWidgets.Clone);
            Be.CloneWidgets.Bind();
        },
        Bind: function () {
            $("#widgets-right").off("DOMSubtreeModified", Be.CloneWidgets.Bind);

            $(".widget-control-actions:not(.meks-cloneable)").each(function () {
                var $widget = $(this);
                var $clone = $("<a>");

                $clone.addClass("clone-me meks-clone-action").attr("title", be_clone_widgets.title).attr("href", "#").html(be_clone_widgets.text);

                $widget.addClass("meks-cloneable");

                $clone.insertAfter($widget.find(".alignleft .widget-control-remove")).before(" | ");
            });

            $("#widgets-right").on("DOMSubtreeModified", Be.CloneWidgets.Bind);
        },
        Clone: function (ev) {
            ev.preventDefault();
            ev.stopPropagation();

            var $original = $(this).parents(".widget");
            var widgetId = $original.find("input.widget-id").val();
            var sidebarId = $original.closest("div.widgets-sortables").attr("id");

            if (!sidebarId) {
                sidebarId = "wp_inactive_widgets";
            }

            var $button = $(this);
            $button.prop("disabled", true).text("复制中");

            $.ajax({
                url: ajaxurl,
                type: "POST",
                data: {
                    action: "be_clone_widget",
                    nonce: be_clone_widgets.nonce,
                    widget_id: widgetId,
                    sidebar_id: sidebarId
                },
                success: function (response) {
                    if (response.success) {
                        var newWidgetId = response.data.new_widget_id;
                        window.location.reload();
                    } else {
                        alert("复制失败: " + response.data);
                        $button.prop("disabled", false).text(be_clone_widgets.text);
                    }
                },
                error: function () {
                    alert("复制失败，请重试");
                    $button.prop("disabled", false).text(be_clone_widgets.text);
                }
            });
        }
    };

    $(Be.CloneWidgets.init);
})(jQuery);