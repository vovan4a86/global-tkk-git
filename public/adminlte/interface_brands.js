var brandImage = null;
function brandImageAttache(elem, e){
	$.each(e.target.files, function(key, file)
	{
		if(file['size'] > max_file_size){
			alert('Слишком большой размер файла. Максимальный размер 10Мб');
		} else {
			brandImage = file;
			renderImage(file, function (imgSrc) {
				var item = '<img class="img-polaroid" src="' + imgSrc + '" height="100" data-image="' + imgSrc + '" onclick="return popupImage($(this).data(\'image\'))">';
				$('#brand-image-block').html(item);
			});
		}
	});
	$(elem).val('');
}


function brandSave(form, e){
	e.preventDefault();

	var url = $(form).attr('action');
	var data = new FormData();
	$.each($(form).serializeArray(), function(key, value){
		data.append(value.name, value.value);
	});
	if (brandImage) {
		data.append('image', brandImage);
	}

	sendFiles(url, data, function(json){
		if (typeof json.errors != 'undefined') {
			applyFormValidate(form, json.errors);
			var errMsg = [];
			for (var key in json.errors) { errMsg.push(json.errors[key]);  }
			$(form).find('[type=submit]').after(autoHideMsg('red', urldecode(errMsg.join(' '))));
		}
		if (typeof json.redirect != 'undefined') document.location.href = urldecode(json.redirect);
		if (typeof json.msg != 'undefined') $(form).find('[type=submit]').after(autoHideMsg('green', urldecode(json.msg)));
		brandImage = null;
	});
}

function brandDel(elem, e, text = ''){
	e.preventDefault();
	if (!confirm(text)) return false;
	var url = $(elem).attr('href');
	sendAjax(url, {}, function(json){
		if (typeof json.success != 'undefined' && json.success == true) {
			$(elem).closest('tr').fadeOut(300, function(){ $(this).remove(); });
		}
	});
}