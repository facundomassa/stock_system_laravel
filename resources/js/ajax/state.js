$(document).ready(function () {

    var $countrySelect = $("#country"),
        $stateSelect = $("#state"),
        $countryValue = "",
        $stateValue = "";

    $countrySelect.change((e) => {
        if ($countrySelect.val() != "") {
            stateAjax();
        }
    })

    if ($countrySelect.find(":selected").val() != "") {
        stateAjax();
    }

    function stateAjax() {
        $countryValue = $countrySelect.val();
        $stateValue = $stateSelect.val();
        $stateSelect.empty();
        $stateSelect.append(`<option disabled selected value="">-Seleccionar una opcion-</option>`);
        $.ajax({
            type: "get",
            url: location.origin + "/api/state/" + $countryValue,
            success: function (response) {
                // The new API provides 'name' for the state name
                $.each(response, function (index, value) {
                    if($stateValue == value.name){
                        $stateSelect.append(`<option selected value="` + value.name + `">` + value.name + `</option>`);
                    } else {
                        $stateSelect.append(`<option value="` + value.name + `">` + value.name + `</option>`);
                    }
                });
            },
            error: function (xhr, status, error) {
                console.error("Error fetching states: ", error);
                $stateSelect.append(`<option value="">Error al cargar provincias</option>`);
            }
        });
    }
});
