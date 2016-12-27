jQuery(document).ready(function($) {

// UI ===============================================================

	// Bootstrap fix
	$('body').on('mousedown', 'button[data-dismiss=modal]', function(event) {
		event.preventDefault();
		$(this).closest('.modal').modal('hide');
	});
	$('.modal').on('hidden.bs.modal', function (e) {
		if ($('.modal').hasClass('in')) {
			$('body').addClass('modal-open');
		}
	});

	// Tooltip
	$('[data-toggle="tooltip"]').tooltip();


	// Masonry
	var grid = $('.notes, .search').masonry({
		itemSelector: '.grid-item',
		columnWidth: '.grid-item',
		percentPosition: true
	});
	function updateGrid() {
		$('body').waitForImages(function() {
			grid.masonry('reloadItems').masonry('layout');
		});
	}
	updateGrid();

	// Sortable
	var sortable;
	if (device.desktop()) {
		sortable = dragula([$('.notes')[0]], {
			moves: function(el, container, handle) {
				if (!$(el).hasClass('note-load') && !$(el).hasClass('note-new')) {
					return el;
				}
			}
		});
	}

	// Notification
	function notify(o) {
		$('.notification-text').html(o.t);
		if (o.i) {
			$('.notification-icon')
				.attr('class', 'notification-icon fa fa-fw')
				.addClass(o.i);
		}
		$('#notification').stop().fadeIn();
		setTimeout(function(){
			$('#notification').fadeOut();
		}, 1500);
	}
	var n = {
		note: {
			s: {
				t: 'Note successfully saved',
				i: 'fa-check'
			},
			u: {
				t: 'Note successfully updated',
				i: 'fa-edit'
			},
			d: {
				t: 'Note successfully deleted',
				i: 'fa-trash'
			},
			o: {
				t: 'Sort order saved',
				i: 'fa-th-large'
			},
		},
		share: {
			l: {
				t: 'Link copied to clipboard',
				i: 'fa-copy'
			},
			a: {
				t: 'Access to the note is open',
				i: 'fa-unlock'
			},
			c: {
				t: 'Access to the note is closed',
				i: 'fa-lock'
			},
		},
		param: {
			s: {
					t: 'Settings saved',
					i: 'fa-cog'
			},
			e: 'Enter valid url'
		},
		edit: {
			m: 'Title is too long',
			h: 'Title must not be empty',
			t: 'Text must not be empty'
		}
	};

// App ==============================================================

	// Api call
	function ajaxApi(m,d,c) {
		$.post('api/' + m, d, c);
	}

	// Editor modal
	var editor       = $('#editor');
	var editorTitle  = $('#editor .note-title');
	var editorText   = $('#editor .note-text');
	var editorSave   = $('#editor .note-save');

	function openEditor(title,txt) {
		editorDestroy(editorText);
		editorTitle.val(title);
		editorText.val(txt);
		editorInit(editorText);
		$('.modal').modal('hide');
		editor.modal('show');
	}

	// Viewer modal
	var viewer       = $('#viewer');
	var viewerTtile  = $('#viewer .note-title');
	var viewerText   = $('#viewer .note-text');
	var viewerDate   = $('#viewer .note-date');

	function openNote(id,title,txt,date) {
		viewerTtile.html(title);
		viewerText.html(txt);
		viewerDate.html(date);
		viewer.find('button').data('id', id);
		$('.modal').modal('hide');
		viewer.modal('show');
	}

	// Share modal
	var share        = $('#share');
	var sharePrompt  = $('.share-prompt');
	var shareForm    = $('.share-form');

	// Delete modal
	var deletePrompt = $('#delete');
	var deleteYes    = $('.delete-yes');

	// Load notes
	var loadCount  = 20;
	var loadOffset = 20;

	function scrollLoad() {
		var dh = $(document).height();
		var wh = $(window).height();
		var st = $(window).scrollTop();
		if (dh-wh == Math.floor(st)) {
			ajaxApi('note.load', {offset: loadOffset, count: loadCount}, function(d) {
				if (d) {
					loadOffset += loadCount;
					$(d).insertBefore('.notes .note-new');
					updateGrid();
				} else {
					$(window).off('scroll', scrollLoad);
				}
			});
		}
	}

	if ($('.note-item').length >= loadCount) {
		$(window).on('scroll', scrollLoad);
	}


	// New note
	if (device.desktop()) {
		$('.note-new').on('click', function(event) {
			event.preventDefault();
			editorSave.data('action', 'note.save');
			openEditor(null,null);
		});
	}

	// Open note
	if (device.desktop()) {
		$('body').on('click', '.note-item', function(event) {
			event.preventDefault();
			var id = $(this).data('id');
			ajaxApi('note.json', {id: id}, function(d) {
				var r = JSON.parse(d);
				openNote(r.id, r.title, r.txt, r.date);
			});
		});
	}

	// Edit note
	$('body').on('click', '.note-edit', function(event) {
		event.preventDefault();
		event.stopPropagation();
		if (device.desktop()) {
			var id = $(this).data('id');
			editorSave.data({
				id: id,
				action: 'note.update'
			});
			if ($(this).parents('#viewer').length) {
				openEditor(viewerTtile.html(), viewerText.html());
			} else {
				ajaxApi('note.json', {id: id}, function(d) {
					var r = JSON.parse(d);
					openEditor(r.title, r.txt);
				});
			}
		} else {
			window.location.href = $(this).data('href');
		}
	});
	editor.on('hide.bs.modal', function() {
		$('.popover').hide();
	});

	// Save/Update note
	editorSave.on('click', function() {
		var id = $(this).data('id');
		var action = $(this).data('action');
		var valid  = true;
		if (editorTitle.val().length > 100) {
			valid = false;
			editorTitle.parent().addClass('has-error').find('.help-block').html(n.edit.m);
			editorTitle.focus(function() {
				editorTitle.parent().removeClass('has-error').find('.help-block').html('');
			});
		}
		if (!editorText.val()) {
			valid = false;
			editorText.parent().addClass('has-error').find('.help-block').html(n.edit.t);
			$('.note-editable').focus(function() {
				editorText.parent().removeClass('has-error').find('.help-block').html('');
			});
		}
		if (valid) {
			ajaxApi(action, {
				id:    id,
				title: editorTitle.val(),
				txt:   editorText.summernote('code')
			}, function(d) {
				if (action == 'note.save') {
					id = null;
					$(d).insertBefore('.grid-item:first');
					notify(n.note.s);
				} else {
					var i = $('#note'+id);
					$(d).insertBefore(i);
					i.remove();
					notify(n.note.u);
				}
				updateGrid();
				ajaxApi('note.json', {id: id}, function(r) {
					var r = JSON.parse(r);
					openNote(r.id, r.title, r.txt, r.date);
				});
			});
		}
	});

	// Delete note
	$('body').on('click', '.note-delete', function(event) {
		event.preventDefault();
		event.stopPropagation();
		var id = $(this).data('id');
		deleteYes.data('id', id);
		deletePrompt.modal('show');
	});
	deleteYes.on('click', function() {
		var id = $(this).data('id');
		ajaxApi('note.delete', {id: id}, function() {
			$('#note'+id).remove();
			updateGrid();
			$('.modal').modal('hide');
			notify(n.note.d);
		});
	});

	// Share note
	$('body').on('click', '.note-share', function(event) {
		event.preventDefault();
		event.stopPropagation();
		var id = $(this).data('id');
		var shared = $(this).data('shared');
		var url = window.location.href + id;
		$('.share-access, .share-close').data('id', id);
		$('.share-url').val(url);
		if (shared) {
			sharePrompt.hide();
			shareForm.show();
		} else {
			sharePrompt.show();
			shareForm.hide();
		}
		share.modal('show');
	});
	$('.share-access').on('click', function() {
		var id = $(this).data('id');
		ajaxApi('note.share', {id: id, shared: 1}, function() {
			$('#note'+id+' .note-share').data('shared', 1);
			sharePrompt.hide();
			shareForm.show();
			notify(n.share.a);
		});
	});
	$('.share-close').on('click', function() {
		var id = $(this).data('id');
		ajaxApi('note.share', {id: id, shared: 0}, function() {
			$('#note'+id+' .note-share').data('shared', 0);
			share.modal('hide');
			notify(n.share.c);
		});
	});
	$('.share-copy').on('click', function() {
		var c = new Clipboard('.share-copy');
		notify(n.share.l);
	});

	// Save order
	if (device.desktop()) {
		sortable.on('drop', function(el) {
			var next = $(el).next('.note-item').data('pos');
			var prev = $(el).prev('.note-item').data('pos');
			next = (next) ? next : prev;
			prev = (prev) ? prev : next;
			var d = {
				id:  $(el).data('id'),
				pos: $(el).data('pos'),
			};
				 if (d.pos > Math.max(next, prev)) {d.new = prev;}
			else if (d.pos < Math.min(next, prev)) {d.new = next;}
			var max = Math.max(d.pos, d.new);
			var min = Math.min(d.pos, d.new);
			var add = (d.pos > d.new) ? 1 : -1;
			$('.note-item').each(function(i, el) {
				var pos = $(el).data('pos');
				if (pos >= min && pos <= max && !$(el).hasClass('gu-mirror')) {
					pos = ($(el).is('#note'+d.id)) ? d.new : pos+add;
					$(el).data('pos', pos);
				}
			});
			updateGrid();
			ajaxApi('note.sort', d, function() {
				notify(n.note.o);
			});
		}).on('shadow', function() {
			updateGrid();
		});
	}

	// Quick search
	var searchTimeout;
	$('.note-search-field').on('keyup', function() {
		var s = $(this).val();
		if (s.length > 100) {
			$(this).val(s.substr(0,100));
		}
		clearTimeout(searchTimeout);
		searchTimeout = setTimeout(function() {
			if (s) {
				$('.notes').hide();
				$('.search').show();
				ajaxApi('note.search', {search: s}, function(d) {
					$('.search').html(d);
					updateGrid();
				});
			} else {
				$('.notes').show();
				$('.search').html('').hide();
			}
		},500);
	});
	$('#search').on('hidden.bs.collapse', function() {
		$('.notes').show();
		$('.search').html('').hide();
	});

// Settings =========================================================

	// Settings
	var settings  = $('#settings form');
	var paramSave = $('.param-save');
	var bgColor   = $('#settings input[name="bg[color]"]');
	var bgImage   = $('#settings input[name="bg[image]"]');
	var bgParam   = $('#settings input[name="bg[param]"]');

	$('.bg-image-clear').on('click', function(event) {
		event.preventDefault();
		bgImage.val('');
		settings.trigger('change');
	});

	function bgUpdate() {
		var bc = bgColor.parent().find(':checked').val();
		var bp = bgParam.parent().find(':checked').val();
		var bi = bgImage.val();
		var b = $('body');
		var c = b.attr('class');
		c = c.replace(/(bgcolor.|bgtiled|bgfilled)/g, '').trim();
		b.attr('class', c);
		b.addClass(bc);
		b.addClass(bp);
		b.css('background-image', 'url('+ bi +')');
	}

	// Update settings
	settings.on('change input', function() {
		var i = bgImage.val();
		var valid = i.match(/^http(s|)\:\/\//) && i.match(/(\.jpg|\.jpeg|\.png|\.gif)$/) || !i;
		if (valid) {
			paramSave.removeClass('disabled');
			bgImage.parents('.form-group').removeClass('has-error').find('.help-block').html('');
			bgUpdate();
		} else {
			paramSave.addClass('disabled');
			bgImage.parents('.form-group').addClass('has-error').find('.help-block').html(n.param.e);
			bgImage.focus(function() {
				$(this).parents('.form-group').removeClass('has-error').find('.help-block').html('');
			});
		}
	});

	// Save settings
	paramSave.on('click', function(event) {
		event.preventDefault();
		if (!$(this).hasClass('disabled')) {
			ajaxApi('user.settings', settings.serialize(), function() {
				paramSave.addClass('disabled');
				$('.modal').modal('hide');
				notify(n.param.s);
			});
		}
	});

});