function ajax_submit(url, data) {
    var ladda = Ladda.create(document.activeElement);
    jQuery.ajax(location.href, {
        method: 'POST',
        data: data,
        processData: false,
        contentType: false,
        beforeSend: function() {
            ladda.start();
        },
        success: function (response) {
            ladda.remove();
            $('.modal').modal('hide');
            response = JSON.parse(response);
            if(response.status === 'success') {
                location.reload();
            } else {
                alert(response.info);
            }

        }
    });
}

function show_modal(tag, params) {
    var modal_obj = $(tag);
    for(var key in params) {
        modal_obj.find(key).val(params[key]);
    }
    modal_obj.modal();
}

function create_dir(name) {
    var form_data = new FormData();
    form_data.append("action", "create");
    form_data.append("name", Base64.encode(name));
    ajax_submit(location.href, form_data);
}

function rename_file(name, name_new) {
    var form_data = new FormData();
    form_data.append("action", "rename");
    form_data.append("name", Base64.encode(name));
    form_data.append("name_new", Base64.encode(name_new));
    ajax_submit(location.href, form_data);
}

function delete_file(name) {
    var form_data = new FormData();
    form_data.append("action", "delete");
    form_data.append('name', Base64.encode(name));
    ajax_submit(location.href, form_data);
}

function upload_file(obj) {
    var data = new FormData();
    data.append("action", "upload");
    data.append("upload", obj.files[0]);
    var progress = $('#uploadProgress');
    var progressbar = $('#uploadProgressBar');
    var filename = $('#uploadFileName');
    progress.modal({
        keyboard: false,
        backdrop: 'static'
    });
    filename.text(obj.files[0].name);
    jQuery.ajax(location.href, {
        method: 'POST',
        data: data,
        processData: false,
        contentType: false,
        progress: function(e) {
            if(e.lengthComputable) {
                var progress = Math.round((e.loaded / e.total) * 100).toString();
                progressbar.attr('aria-valuenow', progress).css('width', progress + '%').text(progress + '%');
            }
            else {
                console.warn('Content Length not reported!');
            }
        },
        success: function (response) {
            progress.modal('hide');
            console.log(response);
            response = JSON.parse(response);
            if(response.status === 'success') {
                location.reload();
            } else {
                alert(response.info);
            }
        }
    });
}

function user_auth(form_obj) {
    var form_data = new FormData(form_obj);
    if(form_data.get('username').length + form_data.get('password').length === 0) {
        alert_msg('Username and Password are required', '#alertText', 5000);
        return false;
    }
    jQuery.ajax(location.href, {
        method: 'POST',
        data: form_data,
        processData: false,
        contentType: false,
        success: function (response) {
            response = JSON.parse(response);
            if(response.status === 'success') {
                Cookies.set('user', response.user);
                Cookies.set('token', response.token);
                location.reload();
            } else {
                alert_msg('Login Error', '#alertText', 5000);
            }
        }
    });
}

function user_logout() {
    Cookies.remove('user');
    Cookies.remove('token');
    location.reload();
}

function alert_msg(msg, tag, timeout) {
    var alert_text = $(tag);
    alert_text.text(msg);
    alert_text.parent('.alert').css('opacity', '0.05');
    alert_text.parent('.alert').css('display', 'block');
    alert_text.parent('.alert').css('display');
    alert_text.parent('.alert').css('opacity', '1');
    setTimeout(function () {
        alert_text.parent('.alert').css('opacity', '0.05');
        setTimeout(function () {
            alert_text.text('');
            alert_text.parent('.alert').css('display', 'none');
        }, 150);
    }, timeout);
}

function key_return_click(tag_id) {
    if(window.event.keyCode === 13){
        if (document.getElementById(tag_id)!=null){
            document.getElementById(tag_id).click();
        }
    }
}