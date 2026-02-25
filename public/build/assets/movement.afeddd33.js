$(document).ready(function(){var a=[],c=$("#movement-t"),d=$("#article-t"),r=$("#insert"),o=$("#nameTx"),s=$("#typeTx"),l=$("#codeTx"),f=$("#filter-b");a=p(),c.find("input[type=checkbox]").change(function(){u(this)});function u(t){t.checked==!0?$(t).closest("tr").addClass("table-dark"):$(t).closest("tr").removeClass("table-dark")}function p(){let t=[];return c.find("tbody").find('input[class="id_article"]').each(function(){this.value>t&&t.push(this.value)}),t}r.click(function(){d.find("input[name=article]:checked").each(function(){let t=this.value,e=$(this).attr("data-stock");if(!a.includes(t)){a.push(t);let i=$(this).closest("tr").clone().appendTo(c);i.find("td").last().remove(),i.find("td").last().remove(),i.append(`
                    <td><input type="number" name="${t}[quantity]"></td>
                    <td>${e}</td>
                    <td class="text-center">
                        <input type="hidden" name="${t}[id]">
                        <input class="id_article" type="hidden" name="${t}[id_article]" value="${t}">
                        <input class="expandCheckbox" type="checkbox" name="${t}[delete]">
                    </td>
                `).find("input[type=checkbox]").change(function(){u(this)})}this.checked=!1})}),s.change(function(t){t.preventDefault(),n()}),typeTx.addEventListener("keypress",t=>{t.keyCode==13&&t.preventDefault()}),o.change(function(t){t.preventDefault(),n()}),nameTx.addEventListener("keypress",t=>{t.keyCode==13&&t.preventDefault()}),l.change(function(t){t.preventDefault(),n()}),codeTx.addEventListener("keypress",t=>{t.keyCode==13&&t.preventDefault()}),f.click(function(t){t.preventDefault(),n()});function n(){$.ajax({url:route,data:{refer,nameTx:o.val(),typeTx:s.val(),codeTx:l.val()},dataType:"json",method:"GET",success:function(t){d.find("tbody").empty(),$(t).each(function(e){d.find("tbody").append(`
                    <tr>
                        <td>${t[e].id}</td>
                        <td>${t[e].code}</td>
                        <td>${t[e].name}</td>
                        <td>${t[e].unit}</td>
                        <td>${t[e].type}</td>
                        <td>${t[e].stock}</td>
                        <td class="text-center">
                            <input class="expandCheckbox" type="checkbox" name="article"
                                value="${t[e].id}"
                                data-stock="${t[e].stock.quantity}">

                        </td>
                    </tr>
                    `)})}})}});
