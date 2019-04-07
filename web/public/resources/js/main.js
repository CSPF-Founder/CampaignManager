


$(document).ready(function(){
    $.ReStable({rowHeaders : false,keepHtml: true});
});
function redirect(url,msg) { showError(msg); window.location.href=url;}
function redirectToLogin(url) {
    if(window.confirm("You have no longer logged in, you will be redirected to Login page.."))
    {
        window.location.href=url;
    }
}
function showModalMessage(message,title){
    setTimeout(function(){
        $("#app-msg-box").find(".modal-title").text(title);
        $("#app-msg-box").find(".modal-body").text(message);
        $("#app-msg-box").modal({
            backdrop: true
        });
    }, 600); //600 milli seconds wait before displaying the box
}
function showError(message, title) {
    if(!title){
        title="Error";
    }
    console.log(message);
    if(message instanceof Array){
        var messages = message;
        message = "";
        for(var i=0; i< messages.length; i++){
            message += messages[i] + "\n";
        }
    }
    $("#app-msg-box").removeClass("info-msg");
    $("#app-msg-box").removeClass("success-msg");
    $("#app-msg-box").addClass("error-msg");
    showModalMessage(message, title);
}
function showSuccess(message, title) {
    if(!title){
        title="Success";
    }
    $("#app-msg-box").removeClass("info-msg");
    $("#app-msg-box").removeClass("error-msg");
    $("#app-msg-box").addClass("success-msg");
    showModalMessage(message, title);
}
function showInfo(message,title) {
    if(!title){
        title="Info";
    }
    $("#app-msg-box").removeClass("success-msg");
    $("#app-msg-box").removeClass("error-msg");
    $("#app-msg-box").addClass("info-msg");
    showModalMessage(message, title);
}
$(document).ready(function() {

    $(".delete-selected-rows").on("click", function (e) {
        e.preventDefault();
        if (!confirm('Are you sure want to Delete?')) {
            return;
        }

        var table_div = $(this).closest(".table-div");
        var id_list = [];
        $(table_div).find("input[name='table_records']:checked").each(function () {
            id_list.push($(this).val());
        });

        if (id_list.length === 0) {
            showError('No row selected!');
            return;
        }

        $.post("delete", {"id_list": id_list}, function (res) {
                if (res.redirect) {
                    redirectToLogin(res.redirect);
                }
                if (res.error) {
                    showError(res.error);
                } else if (res.success) {
                    $.each(res.deleted_id_list, function (index,value) {
                        var row = table_div.find('[data-row-id="' + value + '"]');
                        if(row){
                            row.remove();
                        }
                    } );
                    showSuccess(res.success);
                } else {
                    showError("Unexpected Error !");
                }
            }
            , "json").fail(function (response) {
            showError('Error occurred');
        });
    });
});


function createHiddenInputElement(name, value) {
    var hiddenField = document.createElement("input");
    hiddenField.setAttribute("type", "hidden");
    hiddenField.setAttribute("name", name);
    hiddenField.setAttribute("value", value);
    return hiddenField;
}

function virtualFormSubmit(path, params, method) {
    //Reference: http://ctrlq.org/code/19233-submit-forms-with-javascript
    method = method || "post";
    var form = document.createElement("form");
    form.setAttribute("method", method);
    form.setAttribute("action", path);
    for (var name in params) {
        if (params.hasOwnProperty(name)) {
            var value = params[name];
            if (value && value instanceof Array) {
                for (var i = 0; i < value.length; i++) {
                    hiddenField = createHiddenInputElement(name, value[i]);
                    form.appendChild(hiddenField);
                }
            } else {
                hiddenField = createHiddenInputElement(name, value);
                form.appendChild(hiddenField);
            }
        }
    }
    document.body.appendChild(form);
    form.submit();
}