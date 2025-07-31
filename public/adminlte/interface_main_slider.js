let sliderImage = null;

function sliderImageAttache(elem, e) {
    $.each(e.target.files, function (key, file) {
        if (file['size'] > max_file_size) {
            alert('Слишком большой размер файла. Максимальный размер 10Мб');
        } else {
            sliderImage = file;
            renderImage(file, function (imgSrc) {
                let item = '<img class="img-polaroid" src="' + imgSrc + '" height="100" data-image="' + imgSrc + '" onclick="return popupImage($(this).data(\'image\'))" alt="">';
                $('#slider-image-block').html(item);
            });
        }
    });
    $(elem).val('');
}

function sliderImageDel(elem) {
    if (!confirm('Удалить изображение?')) return false;
    var url = $(elem).attr('href');
    sendAjax(url, {}, function (json) {
        if (typeof json.msg != 'undefined') alert(urldecode(json.msg));
        if (typeof json.success != 'undefined' && json.success === true) {
            $(elem).closest('#slider-image-block').fadeOut(300, function () {
                $(this).empty();
                $(this).show();
            });
        }
    });
    return false;
}

function sliderSave(form, e) {
    e.preventDefault();
    const url = $(form).attr('action');
    let data = new FormData();
    $.each($(form).serializeArray(), function (key, value) {
        data.append(value.name, value.value);
    });
    if (sliderImage) {
        data.append('image', sliderImage);
    }

    sendFiles(url, data, function (json) {
        if (typeof json.row != 'undefined') {
            if ($('#users-list tr[data-id=' + json.id + ']').length) {
                $('#users-list tr[data-id=' + json.id + ']').replaceWith(urldecode(json.row));
            } else {
                $('#users-list').append(urldecode(json.row));
            }
        }
        if (typeof json.errors != 'undefined') {
            applyFormValidate(form, json.errors);
            var errMsg = [];
            for (var key in json.errors) {
                errMsg.push(json.errors[key]);
            }
            $(form).find('[type=submit]').after(autoHideMsg('red', urldecode(errMsg.join(' '))));
        }
        if (typeof json.redirect != 'undefined') document.location.href = urldecode(json.redirect);
        if (typeof json.msg != 'undefined') $(form).find('[type=submit]').after(autoHideMsg('green', urldecode(json.msg)));
        if (typeof json.success != 'undefined' && json.success === true) {
            sliderImage = null;
        }
        if (json.alert) $(form).find('[type=submit]').after(autoHideMsg('red', urldecode(json.alert)));
    });
    return false;
}

function sliderDel(elem, e){
    e.preventDefault();
    if (!confirm('Удалить слайд?')) return false;
    var url = $(elem).attr('href');
    sendAjax(url, {}, function(json){
        if (typeof json.success != 'undefined' && json.success === true) {
            $(elem).closest('tr').fadeOut(300, function(){ $(this).remove(); });
        }
    });
}

function featsUpload(elem, e){
    const url = $(elem).data('url');
    let data = new FormData();
    let files = e.target.files;
    $.each(files, function(key, file)
    {
        if(file['size'] > max_file_size){
            alert('Слишком большой размер файла. Максимальный размер 10Мб');
        } else {
            data.append($(elem).attr('name'), file);
        }
    });
    $(elem).val('');
    sendFiles(url, data, function(json){
        if (typeof json.html != 'undefined') {
            $(elem).closest('.slider-feats').find('.slider-feats-items').append(urldecode(json.html));
        }
    });
}

function featDelete(elem) {
    if (!confirm('Удалить преимущество?')) return false;
    const url = $(elem).attr('href');
    sendAjax(url, {}, function (json) {
        if (typeof json.msg != 'undefined') alert(urldecode(json.msg));
        if (typeof json.success != 'undefined' && json.success === true) {
            $(elem).closest('.images_item').fadeOut(300, function () {
                $(this).remove();
            });
        }
    });
    return false;
}

function featEdit(elem, e){
    e.preventDefault();
    const url = $(elem).attr('href');
    popupAjax(url);
}

function featDataSave(form, e){
    e.preventDefault();
    var url = $(form).attr('action');
    var data = $(form).serialize();
    sendAjax(url, data, function(json){
        if (typeof json.success != 'undefined' && json.success == true) {
            popupClose();
        }
    });
}
