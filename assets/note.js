jQuery(document).ready(function($) {

// Var =============================================

	// Editor
	var editor      = $('#editor');
	var editorTitle = $('#editor .note-title');
	var editorText  = $('#editor .note-text');

	// Share modal
	var share       = $('#share');
	var sharePrompt = $('.share-prompt');
	var shareForm   = $('.share-form');
	var id          = share.data('id');

	// Api call
	function ajaxApi(m,d,c) {
		$.post('api/' + m, d, c);
	}

// Event ===========================================

	// Save/Update note
	$('.note-save').on('click', function(event) {
		event.preventDefault();
		var valid  = true;
		if (editorTitle.val().length > 100) {
			valid = false;
			editorTitle.parent().addClass('has-error').find('.help-block').html('Title is too long');
			editorTitle.focus(function() {
				editorTitle.parent().removeClass('has-error').find('.help-block').html('');
			});
		}
		if (!editorText.val()) {
			valid = false;
			editorText.parent().addClass('has-error').find('.help-block').html('Text must not be empty');
			$('.note-editable').focus(function(event) {
				editorText.parent().removeClass('has-error').find('.help-block').html('');
			});
		}
		if (valid) {
			editor.submit();
		}
	});

	// Share
	$('.share-access').on('click', function() {
		ajaxApi('note.share', {id: id, shared: 1}, function() {
			sharePrompt.hide();
			shareForm.show();
		});
	});
	$('.share-close').on('click', function() {
		ajaxApi('note.share', {id: id, shared: 0}, function() {
			share.on('hidden.bs.modal', function() {
				sharePrompt.show();
				shareForm.hide();
			}).modal('hide');
		});
	});
	$('.share-copy').on('click', function() {
		new Clipboard('.share-copy');
	});

});