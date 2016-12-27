jQuery(document).ready(function($) {

	// Api call
	function ajaxApi(m,d,c) {
		$.post('api/' + m, d, c);
	}

	// Form validation
	$('.submit').on('click', function(event) {

		event.preventDefault();

		var form   = $(this).parents('form');
		var action;

		switch (form.attr('id')) {
			case 'login-form':
				action = 'user.validateLogin';
				break;
			case 'registration-form':
				action = 'user.validateReg';
				break;
			case 'password-form':
				action = 'user.validatePass';
				break;
			case 'delete-form':
				action = 'user.validateDelete';
				break;
			default: break;
		}

		var f = {
			login   : form.find('input[name=login]'),
			pass    : form.find('input[name=pass]'),
			curpass : form.find('input[name=curpass]'),
			newpass : form.find('input[name=newpass]'),
			repass  : form.find('input[name=repass]'),
		}

		function showError(el, text) {
			var fg = el.parents('.form-group');
			fg.addClass('has-error').find('.help-block').html(text);
			fg.find('input').focus(function() {
				fg.removeClass('has-error').find('.help-block').html('');
			});
		}

		// Submit form
		ajaxApi(action, form.serialize(), function(d) {
			console.log(d);
			var r = JSON.parse(d);
			if (!$.isEmptyObject(r)) {
				for (var k in r) {
					showError(f[k], r[k]);
				}
			} else {
				form.submit();
			}
		});
	});

});