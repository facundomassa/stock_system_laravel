//script para mover materiales 
$(document).ready(function () {
    var $quantityArticle = [],
        $tableMovement = $('#movement-t'),
        $tableArticle = $('#article-t'),
        $insertButtom = $('#insert')

    $quantityArticle = Quantity();

    $tableMovement.find('input[type=checkbox]').change(function(){
        changeCheckbox(this);
    })

    function changeCheckbox(i){
        if(i.checked == true){
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

    console.log("Quantity = " + $quantityArticle);

    $insertButtom.click(function () {
        $tableArticle.find('input[name=article]:checked').each(function () {
            let $value = this.value;
            if(!$quantityArticle.includes($value)){
                $quantityArticle.push($value);
                let $row = $(this).closest('tr').clone().appendTo($tableMovement)
                $row.find('td').last().remove()
                $row.append(`
                    <td><input type="number" name="${$value}[quantity]"></td>
                    <td class="text-center">
                        <input type="hidden" name="${$value}[id]">
                        <input class="id_article" type="hidden" name="${$value}[id_article]" value="${$value}">
                        <input class="expandCheckbox" type="checkbox" name="${$value}[delete]">
                    </td>
                `).find('input[type=checkbox]').change(function(){
                    changeCheckbox(this);
                });
            }
            
            this.checked = false;
        })
    })

    
});
