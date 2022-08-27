 
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
                $.each(response, function (index, value) {
                    if($stateValue == value.state_name){
                        $stateSelect.append(`<option selected value="` + value.state_name + `">` + value.state_name + `</option>`);
                    } else {
                        $stateSelect.append(`<option value="` + value.state_name + `">` + value.state_name + `</option>`);
                    }
                });
            }
        });
    }


});
