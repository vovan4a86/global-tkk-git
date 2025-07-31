const max_file_size = 2097152;

let Cart = {
	add: function (id, count, callback) {
		sendAjax('/ajax/add-to-cart',
			{id, count}, (result) => {
				if (typeof callback == 'function') {
					callback(result);
				}
			});
	},

	update: function (id, count, callback) {
		sendAjax('/ajax/update-to-cart',
			{id, count}, (result) => {
				if (typeof callback == 'function') {
					callback(result);
				}
			});
	},

	edit:  function (id, count, callback) {
		sendAjax('/ajax/edit-cart-product',
			{id, count}, (result) => {
				if (typeof callback == 'function') {
					callback(result);
				}
			});
	},

	remove: function (id, callback) {
		sendAjax('/ajax/remove-from-cart',
			{id: id}, (result) => {
				if (typeof callback == 'function') {
					callback(result);
				}
			});
	},

	purge: function (callback) {
		sendAjax('/ajax/purge-cart',
			{}, (result) => {
				if (typeof callback == 'function') {
					callback(result);
				}
			});
	},

}

$(function(){
	//iCheck for checkbox and radio inputs
    $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
      checkboxClass: 'icheckbox_minimal-blue',
      radioClass: 'iradio_minimal-blue'
    });

    $(document).on('click', '.popup-ajax', function(e){
    	e.preventDefault();
    	popupAjax($(this).attr('href'));
    });
});

function sendAjax(url, data, callback, type){
	data = data || {};
	if (typeof type == 'undefined') type = 'json';
	$.ajax({
		type: 'post',
		url: url,
		data: data,
		// processData: false,
		// contentType: false,
		dataType: type,
		beforeSend: function(request) {
	        return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
	    },
		success: function(json){
			if (typeof callback == 'function') {
				callback(json);
			}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown){
			alert('Не удалось выполнить запрос! Ошибка на сервере.');
		},
	});
}

function sendAjaxWithFile(url, data, callback, type){
	data = data || {};
	if (typeof type == 'undefined') type = 'json';
	$.ajax({
		type: 'post',
		url: url,
		data: data,
		processData: false,
		contentType: false,
		dataType: type,
		beforeSend: function(request) {
			return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
		},
		success: function(json){
			if (typeof callback == 'function') {
				callback(json);
			}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown){
			alert('Не удалось выполнить запрос! Ошибка на сервере.');
		},
	});
}

function sendFiles(url, data, callback, type){
	if (typeof type == 'undefined') type = 'json';
	$.ajax({
        url: url,
        type: 'POST',
        data: data,
        cache: false,
        dataType: type,
        processData: false, // Don't process the files
        contentType: false, // Set content type to false as jQuery will tell the server its a query string request
        beforeSend: function(request) {
	        return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
	    },
        success: function(json, textStatus, jqXHR)
        {
            if (typeof callback == 'function') {
				callback(json);
			}
        },
        error: function(jqXHR, textStatus, errorThrown)
        {
            alert('Не удалось выполнить запрос! Ошибка на сервере.');
        }
    });
}

function renderImage(file, callback){
	var reader = new FileReader();
	reader.onload = function(event){
		if (typeof callback == 'function') {
			callback(event.target.result);
		}
	};
	reader.readAsDataURL(file);
}

function applyFormValidate(form, ErrMsg){
	$(form).find('.invalid').attr('title', '').removeClass('invalid');
	for (var key in ErrMsg) {
		$(form).find('[name="'+urldecode(key)+'"]').addClass('invalid').attr('title', urldecode(ErrMsg[key].join(' ')));
	}
	$(form).find('.invalid').eq(0).trigger('focus');
}

function siteClearCache(elem, e) {
	e.preventDefault();
	const url = $(elem).attr('href');
	$('.fa-refresh').addClass('rotate');
	sendAjax(url, {}, function (json) {
		if (json.success) {
			alert('Кэш очищен! 🤨');
		}
		$('.fa-refresh').removeClass('rotate');
	})
}

function updateSearchIndex(elem, e) {
	e.preventDefault();
	if (!confirm('Обновляем поисковый индекс?')) return false;
	const url = $(elem).attr('href');
	$('.search-index').addClass('rotate');
	sendAjax(url, {}, function (json) {
		if (json.success) {
			alert('Поисковый индекс обновлен!');
			// location.reload();
		}
		$('.search-index').removeClass('rotate');
	})
}

let autoHideMsgNextId = 0;
function autoHideMsg(color, text, time){
	if (typeof time == 'undefined') time = 5000;
	var id = 'auto-hide-msg-'+(autoHideMsgNextId++);
	var msg = '<span id="'+id+'" class="auto-hide-msg text-'+color+'">'+text+'</span>';
	setTimeout(function(){ $('#'+id).fadeOut(500, function(){ $(this).remove(); }); }, time);
	return msg;
}

function startCkeditor(id){
	if (typeof CKEDITOR == 'undefined') {
		$('head').append('<script type="text/javascript" src="/adminlte/plugins/ckeditor/ckeditor.js"></script>');
	}

	var editor = CKEDITOR.replace(
		id,
		{
            filebrowserBrowseUrl : '/admin/laravel-filemanager?type=Files',
            filebrowserImageBrowseUrl : '/admin/laravel-filemanager?type=Images',
            filebrowserFlashBrowseUrl : '/admin/laravel-filemanager?type=Flash',
            filebrowserUploadUrl : '/admin/laravel-filemanager/upload?type=Files',
            filebrowserImageUploadUrl : '/admin/laravel-filemanager/upload?type=Images',
            filebrowserFlashUploadUrl : '/admin/laravel-filemanager/upload?type=Flash',
			// contentsCss : '/static/css/all.css',
			toolbar : [
				{ name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ 'Source', '-', /*'Save', 'NewPage',*/ 'Preview', 'Print', /*'-', 'Templates'*/ ] },
				{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
				{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ], items: [ 'Find', 'Replace', '-', 'SelectAll', '-', 'Scayt' ] },
				//{ name: 'forms', items: [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ] },
				{ name: 'others', items: [ '-' ] },
				{ name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] },
				//{ name: 'about', items: [ 'About' ] },
				'/',
				{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', /*'CreateDiv',*/ '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', /* 'BidiLtr', 'BidiRtl', 'Language' */] },
				{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
				{ name: 'insert', items: [ 'Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe' ] },
				'/',
				{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
				{ name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
				{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },
			],
            removePlugins: 'magicline',
            allowedContent: true
		}
	);

	editor.on( 'change', function( evt ) {
	    editor.updateElement();
	});
}

function popup(content){
	$('body').append('<div class="modal"><div class="modal-dialog"><div class="modal-content">' +
		'<div class="modal-header"><a href="#" class="popup-close close" onclick="return popupClose(this)"><span class="glyphicon glyphicon-remove"></span></a></div>'+
		'<div class="modal-body">' +
		content+'</div></div></div></div>')
	$('.modal').fadeIn(300);
}

function popupClose(el){
	if(typeof(el) !== 'undefined'){
        $(el).closest('.modal').fadeOut(300, function(){ $(this).remove(); });
	} else {
        $('.modal').fadeOut(300, function(){ $(this).remove(); });
	}


	return false;
}

function popupImage(src){
	popup('<img class="img-polaroid popup-image" src="'+src+'"/>');
}

function popupVideo(src){
	popup('<iframe class="mfp-iframe" src="'+src+'?rel=0&amp;autoplay=1" frameborder="0" allowfullscreen="" width="560" height="315"></iframe>');
}

function popupAjax(url){
	sendAjax(url, {}, function(html){
		popup(html);
	}, 'html');
}

function urldecode(str) {
   return decodeURIComponent((str+'').replace(/\+/g, '%20'));
}

function postDelete(elem, confirm_msg, parent, e){
    e.preventDefault();
    if (!confirm(confirm_msg)) return false;
    var url = $(elem).attr('href');
    sendAjax(url, {}, function(json){
        if (typeof json.success != 'undefined' && json.success == true) {
            $(elem).closest(parent).fadeOut(300, function(){ $(this).remove(); });
        }
    });
    return false;
}

function init_autocomplete(el){
    $(el).autocomplete({
        serviceUrl: $(el).data('url'),
        ajaxSettings: {
            dataType: 'json',
        },
        paramName: 'tag_name',
        minLength: 3,
        transformResult: function (response) {
            return {
                suggestions: $.map(response.data, function (dataItem) {
                    return {
                        value: dataItem.name, data: {
                            id: dataItem.id,
                        }
                    };
                })
            };
        }
    });

    $(el).on('keypress', function(e){
        if(e.keyCode == 13)
        {
            $(el).closest('.input-group').find('button').trigger('click');
            return false;
        }
    });
}

function debounce(func, wait, immediate) {
	let timeout;

	return function executedFunction() {
		const context = this;
		const args = arguments;

		const later = function() {
			timeout = null;
			if (!immediate) func.apply(context, args);
		};

		const callNow = immediate && !timeout;

		clearTimeout(timeout);

		timeout = setTimeout(later, wait);

		if (callNow) func.apply(context, args);
	};
}

function resetForm(form) {
	$(form).trigger('reset');
	$(form).find('.err-msg-block').remove();
	$(form).find('.has-error').remove();
	$(form).find('.invalid').attr('title', '').removeClass('invalid');
}

function sendRequest(frm, e) {
	e.preventDefault();
	var form = $(frm);
	var data = form.serialize();
	var url = form.attr('action');
	sendAjax(url, data, function (json) {
		if (typeof json.errors !== 'undefined') {
			let focused = false;
			for (var key in json.errors) {
				if (!focused) {
					form.find('#' + key).focus();
					focused = true;
				}
				form.find('#' + key).after('<span class="has-error">' + json.errors[key] + '</span>');
			}
			form.find('.sending__title').after('<div class="err-msg-block has-error">Заполните, пожалуйста, обязательные поля.</div>');
		} else {
			resetForm(form);
			alert('Форма отправлена!');
			// form.parent().find('.is-close').click();
			// popup('Спасибо, ваш вопрос отправлен. Сообщение об ответе придет на эл. почту.');
			// Fancybox.show([{ src: '#confirm', type: 'inline' }], {
			//     mainClass: 'popup--main popup--thanks'
			// });
		}
	});
}

function sendCallback(frm, e) {
	e.preventDefault();
	var form = $(frm);
	var data = form.serialize();
	var url = form.attr('action');
	sendAjax(url, data, function (json) {
		if (typeof json.errors !== 'undefined') {
			let focused = false;
			for (var key in json.errors) {
				if (!focused) {
					form.find('#' + key).focus();
					focused = true;
				}
				form.find('#' + key).after('<span class="has-error">' + json.errors[key] + '</span>');
			}
			form.find('.sending__title').after('<div class="err-msg-block has-error">Заполните, пожалуйста, обязательные поля.</div>');
		} else {
			resetForm(form);
			form.find('.is-close').click();
			Fancybox.show([{ src: '#request-done', type: 'inline' }], {
				mainClass: 'popup--custom popup--complete',
				template: { closeButton: closeBtn },
				hideClass: 'fancybox-zoomOut'
			});
		}
	});
}

function search(frm, e) {
	e.preventDefault();
	var form = $(frm);
	var data = form.serialize();
	var url = form.attr('action');
	sendAjax(url, data, function (json) {
		if (typeof json.errors !== 'undefined') {
			let focused = false;
			for (var key in json.errors) {
				if (!focused) {
					form.find('#' + key).focus();
					focused = true;
				}
				// form.find('#' + key).after('<span class="has-error">' + json.errors[key] + '</span>');
			}
			// form.find('.sending__title').after('<div class="err-msg-block has-error">Заполните, пожалуйста, обязательные поля.</div>');
		} else {
			// resetForm(form);
			location.href = res.redirect;
		}
	});
}
