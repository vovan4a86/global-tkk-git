var catalogImage = null;
var previewImage = null;
var mass_images = [];
function catalogImageAttache(elem, e){
    $.each(e.target.files, function(key, file)
    {
        if(file['size'] > max_file_size){
            alert('Слишком большой размер файла. Максимальный размер 10Мб');
        } else {
            catalogImage = file;
            renderImage(file, function (imgSrc) {
                var item = '<img class="img-polaroid" src="' + imgSrc + '" height="100" data-image="' + imgSrc + '" onclick="return popupImage($(this).data(\'image\'))">';
                $('#article-image-block').html(item);
            });
        }
    });
    $(elem).val('');
}
function previewImageAttache(elem, e){
    $.each(e.target.files, function(key, file)
    {
        if(file['size'] > max_file_size){
            alert('Слишком большой размер файла. Максимальный размер 10Мб');
        } else {
            previewImage = file;
            renderImage(file, function (imgSrc) {
                var item = '<img class="img-polaroid" src="' + imgSrc + '" height="200" data-image="' + imgSrc + '" onclick="return popupImage($(this).data(\'image\'))">';
                $('#preview-image-block').html(item);
            });
        }
    });
    $(elem).val('');
}

function previewImageDel(el, e){
    e.preventDefault();
    if (!confirm('Удалить изображение?')) return false;
    var url = $(el).attr('href');
    sendAjax(url, {}, function(json){
        if (typeof json.success != 'undefined' && json.success === true) {
            $(el).closest('#preview-image-block').html('');
        }
    });
}

var doc = null;
function docAttache(elem, e){
    $.each(e.target.files, function(key, file)
    {
        if(file['size'] > max_file_size){
            alert('Слишком большой размер файла. Максимальный размер 2Мб');
        } else {
            doc = file;
            renderImage(file, function (imgSrc) {
                var item = '<img class="img-polaroid" src="' + imgSrc + '" height="100" data-image="' + imgSrc + '" onclick="return popupImage($(this).data(\'image\'))">';
                $('#action-image-block').html(item);
            });
        }
    });
    // $(elem).val('');
}

function update_order(form, e) {
    e.preventDefault();
    var button = $(form).find('[type="submit"]');
    button.attr('disabled', 'disabled');
    var url = $(form).attr('action');
    var data = $(form).serialize();
    sendAjax(url, data, function(json){
        button.removeAttr('disabled');
    });
}

function catalogContent(elem){
    //var url = $(elem).attr('href');
    //sendAjax(url, {}, function(html){
    //	$('#catalog-content').html(html);
    //}, 'html');
    //return false;
}

function catalogSave(form, e){
    var url = $(form).attr('action');
    var data = new FormData();
    $.each($(form).serializeArray(), function(key, value){
        data.append(value.name, value.value);
    });
    if (catalogImage) {
        data.append('image', catalogImage);
    }
    if (previewImage) {
        data.append('image_text_preview', previewImage);
    }
    sendFiles(url, data, function(json){
        if (typeof json.row != 'undefined') {
            if ($('#users-list tr[data-id='+json.id+']').length) {
                $('#users-list tr[data-id='+json.id+']').replaceWith(urldecode(json.row));
            } else {
                $('#users-list').append(urldecode(json.row));
            }
        }
        if (typeof json.errors != 'undefined') {
            applyFormValidate(form, json.errors);
            var errMsg = [];
            for (var key in json.errors) { errMsg.push(json.errors[key]);  }
            $(form).find('[type=submit]').after(autoHideMsg('red', urldecode(errMsg.join(' '))));
        }
        if (typeof json.redirect != 'undefined') document.location.href = urldecode(json.redirect);
        if (typeof json.msg != 'undefined') $(form).find('[type=submit]').after(autoHideMsg('green', urldecode(json.msg)));
        if (typeof json.success != 'undefined' && json.success === true) {
            catalogImage = null;
            previewImage = null;
        }
    });
    return false;
}

function catalogDel(elem){
    if (!confirm('Удалить раздел?')) return false;
    var url = $(elem).attr('href');
    sendAjax(url, {}, function(json){
        if (typeof json.msg != 'undefined') alert(urldecode(json.msg));
        if (typeof json.success != 'undefined' && json.success == true) {
            $(elem).closest('li').fadeOut(300, function(){ $(this).remove(); });
        }
    });
    return false;
}

function catalogFilterEdit(elem, e) {
    e.preventDefault();
    const filter_id = $(elem).closest('.form-group').find('input').val();
    const url = $(elem).attr('href');
    popupAjaxWithData(url, {filter_id});
}

function catalogFilterSaveData(form, e) {
    e.preventDefault();
    const url = $(form).attr('action');
    const data = $(form).serialize();
    sendAjax(url, data, function (json) {
        if (typeof json.success != 'undefined' && json.success === true) {
            popupClose();
            location.href = json.redirect;
        }
    });
}

function catalogFilterDelete(elem) {
    if (!confirm('Удаление фильтра также произойдет во всех вложенных разделах, а так же удалиться соответствующая характеристика у всех товаров разделов. Удаляем?')) return false;
    const filter_id = $(elem).closest('.form-group').find('input').val();
    const url = $(elem).attr('href');
    sendAjax(url, {filter_id}, function (json) {
        if (typeof json.msg != 'undefined') alert(urldecode(json.msg));
        if (typeof json.success != 'undefined' && json.success === true) {
            $(elem).closest('.filter').fadeOut(300, function () {
                $(this).remove();
            });
        }
    });
    return false;
}

function productSave(form, e){
    var url = $(form).attr('action');
    var data = $(form).serialize();
    sendAjax(url, data, function(json){
        if (typeof json.errors != 'undefined') {
            applyFormValidate(form, json.errors);
            var errMsg = [];
            for (var key in json.errors) { errMsg.push(json.errors[key]);  }
            $(form).find('[type=submit]').after(autoHideMsg('red', urldecode(errMsg.join(' '))));
        }
        if (typeof json.redirect != 'undefined') document.location.href = urldecode(json.redirect);
        if (typeof json.msg != 'undefined') $(form).find('[type=submit]').after(autoHideMsg('green', urldecode(json.msg)));
    });
    return false;
}

function productDel(elem){
    if (!confirm('Удалить товар?')) return false;
    var url = $(elem).attr('href');
    sendAjax(url, {}, function(json){
        if (typeof json.msg != 'undefined') alert(urldecode(json.msg));
        if (typeof json.success != 'undefined' && json.success == true) {
            $(elem).closest('tr').fadeOut(300, function(){ $(this).remove(); });
        }
    });
    return false;
}

function productImageUpload(elem, e){
    var url = $(elem).data('url');
    files = e.target.files;
    var data = new FormData();
    $.each(files, function(key, value)
    {
        if(value['size'] > max_file_size){
            alert('Слишком большой размер файла. Максимальный размер 2Мб');
        } else {
            data.append('images[]', value);
        }
    });
    $(elem).val('');

    sendFiles(url, data, function(json){
        if (typeof json.html != 'undefined') {
            $('.images_list').append(urldecode(json.html));
            if (!$('.images_list img.active').length) {
                $('.images_list .img_check').eq(0).trigger('click');
            }
        }
    });
}

function productDocUpload(elem, e){
    var url = $(elem).data('url');
    files = e.target.files;
    var data = new FormData();
    $.each(files, function(key, value)
    {
        if(value['size'] > max_file_size){
            alert('Слишком большой размер файла. Максимальный размер 2Мб');
        } else {
            data.append('docs[]', value);
        }
    });
    $(elem).val('');

    sendFiles(url, data, function(json){
        if (typeof json.html != 'undefined') {
            $('.docs_list').append(urldecode(json.html));
            if (!$('.docs_list img.active').length) {
                $('.docs_list .img_check').eq(0).trigger('click');
            }
        }
    });
}

function productDocDel(elem){
    if (!confirm('Удалить документ?')) return false;
    const url = $(elem).attr('href');
    sendAjax(url, {}, function(json){
        if (typeof json.msg != 'undefined') alert(urldecode(json.msg));
        if (typeof json.success != 'undefined' && json.success === true) {
            $(elem).closest('.images_item').fadeOut(300, function(){ $(this).remove(); });
        }
    });
    return false;
}

function productCheckImage(elem){
    $('.images_list img').removeClass('active');
    $('.images_list .img_check .glyphicon').removeClass('glyphicon-check').addClass('glyphicon-unchecked');

    $(elem).find('.glyphicon').removeClass('glyphicon-unchecked').addClass('glyphicon-check');
    $(elem).siblings('img').addClass('active');

    $('#product-image').val($(elem).siblings('img').data('image'));
    return false;
}

function productImageDel(elem){
    if (!confirm('Удалить изображение?')) return false;
    var url = $(elem).attr('href');
    sendAjax(url, {}, function(json){
        if (typeof json.msg != 'undefined') alert(urldecode(json.msg));
        if (typeof json.success != 'undefined' && json.success == true) {
            $(elem).closest('.images_item').fadeOut(300, function(){ $(this).remove(); });
        }
    });
    return false;
}

$(document).ready(function () {
    $('#pages-tree').jstree({
        "core": {
            "animation": 0,
            "check_callback": true,
            'force_text': false,
            "themes": {"stripes": true},
            'data': {
                'url': function (node) {
                    return node.id === '#' ? '/admin/catalog/get-catalogs' : '/admin/catalog/get-catalogs/' + node.id;
                }
            },
        },
        "plugins": ["contextmenu", "dnd", "state", "types"],
        "contextmenu": {
            "items": function ($node) {
                var tree = $("#tree").jstree(true);
                return {
                    "Create": {
                        "icon": "fa fa-plus text-blue",
                        "label": "Создать страницу",
                        "action": function (obj) {
                            // $node = tree.create_node($node);
                            document.location.href = '/admin/catalog/catalog-edit?parent=' + $node.id
                        }
                    },
                    "Edit": {
                        "icon": "fa fa-pencil text-yellow",
                        "label": "Редактировать страницу",
                        "action": function (obj) {
                            // tree.delete_node($node);
                            document.location.href = '/admin/catalog/catalog-edit/' + $node.id
                        }
                    },
                    "Remove": {
                        "icon": "fa fa-trash text-red",
                        "label": "Удалить страницу",
                        "action": function (obj) {
                            if (confirm("Действительно удалить страницу?")) {
                                var url = '/admin/catalog/catalog-delete/' + $node.id;
                                sendAjax(url, {}, function () {
                                    document.location.href = '/admin/catalog';
                                })
                            }
                            // tree.delete_node($node);
                        }
                    }
                };
            }
        }
    }).bind("move_node.jstree", function (e, data) {
        treeInst = $(this).jstree(true);
        parent =  treeInst.get_node( data.parent );
        var d = {
            'id':   data.node.id,
            'parent': (data.parent == '#')? 0: data.parent,
            'sorted': parent.children
        };
        sendAjax('/admin/catalog/catalog-reorder', d);
    }).on("activate_node.jstree", function(e,data){
        if(data.event.button == 0){
            window.location.href = '/admin/catalog/products/' + data.node.id;
        }
    });
});

function showHidden(elem) {
    let hidden = $('.action-hidden');
    if(elem.checked) {
        hidden.slideDown(300);
    } else {
        hidden.slideUp(300);
    }
}

function delProductChar(elem, e) {
    e.preventDefault();
    if (!confirm('Удалить характеристику?')) return false;
    $(elem).closest('.row').fadeOut(300, function(){ $(this).remove(); });
}

function addProductParam(link, e) {
    e.preventDefault();
    var conteiner = $(link).prev();
    var row = conteiner.find('.row:last');
    $newRow = $(document.createElement('div'));
    $newRow.addClass('row row-chars');
    $newRow.html(row.html());
    row.before($newRow);
}

function galleryItemEdit(elem, e){
    e.preventDefault();
    var url = $(elem).attr('href');
    popupAjax(url);
}

function galleryImageDataSave(form, e){
    e.preventDefault();
    var url = $(form).attr('action');
    var data = $(form).serialize();
    sendAjax(url, data, function(json){
        if (typeof json.success != 'undefined' && json.success == true) {
            popupClose();
        }
    });
}

//product params
function addParam(elem, e) {
    e.preventDefault();
    var name = $('.param-name'),
        value = $('.param-value');
    if(!name.val()){
        alert('Нужно заполнить название');
        return;
    }
    var data = {
        name: name.val(),
        value: value.val(),
    }
    var url = $(elem).attr('href');

    sendAjax(url, data, function(json){
        if(typeof json.row != 'undefined'){
            $('#param_list tbody').append(json.row);
            name.val('');
            value.val('');
        }
    });
}

function delParam(elem, e) {
    e.preventDefault();
    if(!confirm('Точно удалить этот параметр?')) return;
    var url = $(elem).attr('href');
    var row = $(elem).closest('tr');

    sendAjax(url, {}, function(json){
        if(typeof json.success != 'undefined'){
            $(row).fadeOut(300, function(){ $(this).remove(); });
        }
    });
}

function editParam(link, e) {
    e.preventDefault();
    var url = $(link).attr('href');
    sendAjax(url, {}, function (html) {
        popup(html);
    }, 'html');
}

function saveParam(form, e) {
    e.preventDefault();
    var url = $(form).attr('action');
    var data = $(form).serialize();
    var id = $(form).data('id');
    sendAjax(url, data, function (html) {
        popupClose();
        $('tr#param'+id).replaceWith(html);
    }, 'html');
}

//additional
function additionalCatalogDelete(btn, e) {
    e.preventDefault();
    if(!confirm('Действительно открепить дополнительный раздел')) return;

    $(btn).closest('.list-group-item').fadeOut(300, function(){ $(this).remove(); });
}

function additionalCatalogAdd(btn, e) {
    e.preventDefault();
    var $opt = $(btn).closest('.form-group').find('select option:selected');
    var id = $opt.attr('value');
    if(!id){
        alert('Нужно выбрать категорию');
        return;
    }
    var name = $opt.text().trim();
    var new_li = document.createElement('li');
    $(new_li).addClass('list-group-item');
    $(new_li).html($('.list-group.city_list li:last').html());
    $(new_li).find('input[type="hidden"]').val(id);
    $(new_li).find('span').html(name);
    $('.list-group.city_list').prepend(new_li);
}

//related
function attachRelated(btn, e){
    e.preventDefault();
    var cont = $(btn).closest('.input-group');
    var pub_id = cont.find('#pub_name_id').val();
    if(pub_id > 0){
        var url = '/admin/catalog/related-attach';
        sendAjax(url, {pub_id: pub_id}, function(json){
            if (typeof json.success != 'undefined' && json.success === true) {
                $('#list-group').before(json.row);
                cont.find('#pub_name_id').val('');
                cont.find('#pub_name').val('');
            }
        });
    } else {
        alert("Нужно выбрать товар");
    }

    return false;
}

function detachRelated(elem){
    if (!confirm('Открепить товар?')) return false;
    $(elem).closest('li').fadeOut(300, function(){ $(this).remove(); });
    return false;
}

//mass
function checkSelectProduct() {
    var selected = $('input.js_select:checked');
    if (selected.length) {
        $('.js-move-btn').removeAttr('disabled');
        $('.js-delete-btn').removeAttr('disabled');
        $('.js-add-btn').removeAttr('disabled');
    } else {
        $('.js-move-btn').attr('disabled', 'disabled');
        $('.js-delete-btn').attr('disabled', 'disabled');
        $('.js-add-btn').attr('disabled', 'disabled');
        $('.mass-images').hide('fast');
        $('.mass-images-list').empty();
        mass_images = [];
    }
}

function checkSelectAll() {
    $('input.js_select').prop('checked', true);
    checkSelectProduct();
}

function checkDeselectAll() {
    $('input.js_select').prop('checked', false);
    checkSelectProduct();
}

function moveProducts(btn, e) {
    e.preventDefault();
    var url = '/admin/catalog/move-products';
    var catalog_id = $('#moveDialog select').val();
    var items = [];
    var selected = $('input.js_select:checked');
    $(selected).each(function (n, el) {
        items.push($(el).val());
        $(el).closest('tr').animate({'backgroundColor': '#fb6c6c'}, 300);
    });
    sendAjax(url, {catalog_id: catalog_id, items: items}, function (json) {
        if (typeof json.success != 'undefined' && json.success == true) {
            $('#moveDialog').modal('hide');
            $(selected).each(function (n, el) {
                // $("#row td").animate({'line-height':0},1000).remove();
                // $(el).closest('tr').fadeOut(300, function(){ $(this).remove(); });
                $(el).closest('tr').children('td, th')
                    .animate({paddingBottom: 0, paddingTop: 0}, 300)
                    .wrapInner('<div />')
                    .children()
                    .slideUp(function () {
                        $(this).closest('tr').remove();
                    });
            })
        }
    })
    $('#moveDialog').modal('hide');
}

function deleteProducts(btn, e) {
    e.preventDefault();
    if (!confirm('Действительно удалить выбранные товары?')) return
    var url = '/admin/catalog/delete-products';
    var items = [];
    var selected = $('input.js_select:checked');
    $(selected).each(function (n, el) {
        items.push($(el).val());
        $(el).closest('tr').animate({'backgroundColor': '#fb6c6c'}, 300);
    });
    sendAjax(url, {items: items}, function (json) {
        if (typeof json.success != 'undefined' && json.success == true) {
            $(selected).each(function (n, el) {
                // $("#row td").animate({'line-height':0},1000).remove();
                // $(el).closest('tr').fadeOut(300, function(){ $(this).remove(); });
                $(el).closest('tr').children('td, th')
                    .animate({paddingBottom: 0, paddingTop: 0}, 300)
                    .wrapInner('<div />')
                    .children()
                    .slideUp(function () {
                        $(this).closest('tr').remove();
                    });
            })
        }
    })
}

function deleteProductsImage(btn, e, catalogId) {
    e.preventDefault();
    if (!confirm('Действительно удалить изображения у выбранных товаров?')) return
    let url = '/admin/catalog/delete-products-image';
    let redirect = '/admin/catalog/products/' + catalogId;
    let items = [];
    let selected = $('input.js_select:checked');
    $(selected).each(function (n, el) {
        items.push($(el).val());
        $(el).closest('tr').animate({'backgroundColor': '#ffc3c3'}, 300);
    });
    sendAjax(url, {items: items}, function (json) {
        if (typeof json.success != 'undefined' && json.success === true) {
            checkDeselectAll();
            location.href = redirect;
        }
    })
}

function addProductsImages(elem) {
    $('.mass-images').fadeIn(1000);
}

function massProductImageUpload(elem, e) {
    let url = $(elem).data('url');
    let files = e.target.files;
    // let data = new FormData();
    $.each(files, function (key, value) {
        if (value['size'] > max_file_size) {
            alert('Слишком большой размер файла. Максимальный размер 10Мб');
        } else {
            // data.append('mass_images[]', value);
            mass_images.push(value);
            renderImage(value, function (imgSrc) {
                let item = '<img class="img-polaroid" src="' + imgSrc + '" height="100" data-image="' + imgSrc + '" onclick="return popupImage($(this).data(\'image\'))">';
                $('.mass-images-list').append(item);
            });
        }
    });
    $(elem).val('');
    $('.send-images').removeAttr('disabled');
}

function sendAddedProductImages(elem, e) {
    const catalogId = $(elem).data('catalog-id');
    let url = $(elem).data('url');
    let redirect = '/admin/catalog/products/' + catalogId;
    let data = new FormData();
    $.each(mass_images, function ($i, file) {
        data.append('mass_images[]', file);
    });

    const selected = $('input.js_select:checked');
    $(selected).each(function (n, el) {
        data.append('product_ids[]', $(el).val())
        $(el).closest('tr').animate({'backgroundColor': '#c9c9c9'}, 300);
    });

    $('.send-images').attr('disabled', 'disabled');
    $('.mass-images-list').addClass('loading');
    const message = '<div class="msg">Загрузка картинок...</div>';
    $('.mass-images-list').append(message);

    // setTimeout(function(){
    //     $('.mass-images-list').removeClass('loading');
    //     $('.send-images').removeAttr('disabled');
    // }, 5000);

    sendFiles(url, data, function (json) {
        if (json.success) {
            // checkDeselectAll();
            location.href = redirect;
        } else {
            checkDeselectAll();
            $('.send-images').removeAttr('disabled');
        }
    });
}