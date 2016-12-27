function editorInit(el) {
	var editorToolbar;

	if (device.desktop()) {
		editorToolbar = [
			['action', ['undo', 'redo']],
			['style', ['style']],
			['style2', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
			['font', ['fontsize', 'height']],
			['color', ['color']],
			['paragraph', ['ol', 'ul', 'paragraph']],
			['table', ['table']],
			['insert', ['picture', 'link', 'video']],
			['misk', ['codeview']],
		];
	} else {
		editorToolbar = [
			['action', ['undo', 'redo']],
			['style', ['style']],
			['style2', ['bold', 'italic', 'underline']],
			['font', ['fontsize']],
			['paragraph', ['ul', 'paragraph']],
			['insert', ['picture', 'link', 'video']],
		];
	}

	el.summernote({
		// lang: 'ru-RU',
		minHeight: '300px',
		toolbar: editorToolbar,
		prettifyHtml: true
	});
}

function editorDestroy(el) {
	el.summernote('destroy');
	el.val('');
}

jQuery(document).ready(function($) {

	// Editor design fix
	$('body').on('focus', '.note-editable', function() {
		$(this).closest('.note-editor').addClass('active');
	}).on('blur', '.note-editable', function() {
		$(this).closest('.note-editor').removeClass('active');
	});

});