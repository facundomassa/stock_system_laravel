//script para mover materiales 
$(document).ready(function () {
    var $quantityArticle = [],
        $tableMovement = $('#movement-t'),
        $tableArticle = $('#article-t'),
        $insertButtom = $('#insert'),
        $nameTx = $('#nameTx'),
        $typeTx = $('#typeTx'),
        $codeTx = $('#codeTx'),
        $filterButtom = $('#filter-b')

    $quantityArticle = Quantity();

    $tableMovement.find('input[type=checkbox]').change(function () {
        changeCheckbox(this);
    })

    function changeCheckbox(i) {
        if (i.checked == true) {
            $(i).closest('tr').addClass('table-dark');
        } else {
            $(i).closest('tr').removeClass('table-dark');
        }
    }

    function Quantity() {
        let $quantity = [];
        $tableMovement.find('tbody').find('input[class="id_article"]').each(function () {
            if (this.value > $quantity) {
                $quantity.push(this.value);
            }
        })
        return $quantity
    }

    $insertButtom.click(function () {
        $tableArticle.find('input[name=article]:checked').each(function () {
            let $value = this.value;
            let $stock = $(this).attr("data-stock");
            if (!$quantityArticle.includes($value)) {
                $quantityArticle.push($value);
                let $row = $(this).closest('tr').clone().appendTo($tableMovement)
                $row.find('td').last().remove()
                $row.find('td').last().remove()
                $row.append(`
                    <td><input type="number" name="${$value}[quantity]"></td>
                    <td>${$stock}</td>
                    <td class="text-center">
                        <input type="hidden" name="${$value}[id]">
                        <input class="id_article" type="hidden" name="${$value}[id_article]" value="${$value}">
                        <input class="expandCheckbox" type="checkbox" name="${$value}[delete]">
                    </td>
                `).find('input[type=checkbox]').change(function () {
                    changeCheckbox(this);
                });
            }

            this.checked = false;
        })
    })

    $typeTx.change(function (event) {
        event.preventDefault();
        filter();
    })
    typeTx.addEventListener('keypress', e => {
        if(e.keyCode == 13) {
          e.preventDefault();
        }
    })

    $nameTx.change(function (event) {
        event.preventDefault();
        filter();
    })
    nameTx.addEventListener('keypress', e => {
        if(e.keyCode == 13) {
          e.preventDefault();
        }
    })

    $codeTx.change(function (event) {
        event.preventDefault();
        filter();
    })
    codeTx.addEventListener('keypress', e => {
        if(e.keyCode == 13) {
          e.preventDefault();
        }
    })

    $filterButtom.click(function(event){
        event.preventDefault();
        filter();
    })

    function filter() {
        $.ajax({
            url: route,
            data: {
                'nameTx': $nameTx.val(),
                'typeTx': $typeTx.val(),
                'codeTx': $codeTx.val()
            },
            dataType: "json",
            method: "GET",
            success: function (result) {
                $tableArticle.find('tbody').empty();
                $(result).each(function (index) {
                    $tableArticle.find('tbody').append(`
                    <tr>
                        <td>${result[index].id}</td>
                        <td>${result[index].code}</td>
                        <td>${result[index].name}</td>
                        <td>${result[index].unit}</td>
                        <td>${result[index].type}</td>
                        <td class="text-center">
                            <input class="expandCheckbox" type="checkbox" name="article"
                                value="${result[index].id}">

                        </td>
                    </tr>
                    `)
                })
                console.log(result);
            }
        })
    }
});
