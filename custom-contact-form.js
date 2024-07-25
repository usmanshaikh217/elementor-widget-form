jQuery(document).ready(function($) {
    $('#custom-submission-form').on('submit', function(e) {
        e.preventDefault();

//         var formData = {
//             'action': 'custom_contact_form_submit',
//             'name': $('#name').val(),
//             'email': $('#email').val(),
//             'message': $('#message').val(),
//         };

		
		
		var formData = new FormData(this);
		formData.append('action', 'submit_custom_submission_form');
		
		// Check for file input
		var fileInput = $('#featured_image')[0];
		if (fileInput.files.length > 0) {
			var file = fileInput.files[0];
			var allowedTypes = ['image/jpeg', 'image/png'];

			if (allowedTypes.indexOf(file.type) === -1) {
				$('#form-message').html('<div class="alert alert-danger">Only JPG and PNG files are allowed.</div>');
				return;
			}

			formData.append('featured_image', file);
		}

        $.ajax({
            url: customForm.ajaxurl,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
			success: function(response) {
				if (response.success) {
					$('#form-message').html('<div class="alert alert-success">' + response.data.message + '</div>');
					$('#custom-submission-form')[0].reset();
				} else {
					$('#form-message').html('<div class="alert alert-danger">' + response.data.message + '</div>');
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				$('#form-message').html('<div class="alert alert-danger">An error occurred. Please try again.</div>');
				console.log(textStatus, errorThrown);
			}
        });
    });
});
