jQuery(document).ready(function($) {
    $(document).on('submit','form',function(e) {
        e.preventDefault();
        let form = $(this);
        $.ajax({
            type: $(this).attr('method'),
            url: $(this).attr('action'),
            data: form.serialize(),
            beforeSend: function() {
                form.find('[type="submit"]').prop("disabled",true);
            },
            success: function(data) {
                form.find('.form-input-error').remove();
                console.log(data);
            },
            error: function(error) {
                form.find('.form-input-error').remove();
                if(error.responseJSON) {
                    Object.entries(error.responseJSON.errors).forEach(([key, value]) => {
                        form.find('[name="'+key+'"]').parents(".form-input").append('<span class="form-input-error">'+value+'</span>');
                    });
                }
            },
            complete: function() {
                form.find('[type="submit"]').prop("disabled",false);
            },
        })
    });
})