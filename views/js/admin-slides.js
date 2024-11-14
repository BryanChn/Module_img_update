$(document).ready(function () {
    // HelperList génère une table avec la classe 'table'
    var $table = $(".table");

    if ($table.length) {
        var $tbody = $table.find("tbody");

        $tbody.sortable({
            axis: "y",
            handle: ".dragHandle",
            helper: fixHelper,
            update: function (event, ui) {
                var orders = $(this).sortable("toArray");
                var positions = [];
                var token = $table.data("token");

                orders.forEach(function (order, index) {
                    positions.push({
                        id_slide: order.replace("tr_", "").replace("_", ""),
                        position: index + 1,
                    });
                });

                $.ajax({
                    type: "POST",
                    url:
                        currentIndex +
                        "&token=" +
                        token +
                        "&action=updateSlidesPosition",
                    data: {
                        positions: JSON.stringify(positions),
                    },
                    success: function (response) {
                        try {
                            var result = JSON.parse(response);
                            if (result.success) {
                                showSuccessMessage(
                                    s2iUpdateImgConfig.successMessage
                                );
                            } else {
                                showErrorMessage(
                                    s2iUpdateImgConfig.errorMessage
                                );
                            }
                        } catch (e) {
                            showErrorMessage(s2iUpdateImgConfig.errorMessage);
                        }
                    },
                });
            },
        });
    }

    var fixHelper = function (e, ui) {
        ui.children().each(function () {
            $(this).width($(this).width());
        });
        return ui;
    };
});
