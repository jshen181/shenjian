jQuery(document).ready(function($) {
    $('.secret-submit').on('click', function() {
        var container = $(this).closest('.secret-container');
        var key = container.data('key');
        var password = container.find('#pwbox').val();
        
        $.ajax({
            url: secret_vars.ajax_url,
            type: 'POST',
            data: {
                action: 'secret_verify',
                nonce: secret_vars.nonce,
                key: key,
                password: password,
                content: container.next('.secret-content').html()
            },
            success: function(response) {
                if (response.success) {
                    container.find('form').hide();
                    container.find('.secret-content').html(response.data.content).show();
                } else {
                    alert(response.data.message);
                }
            }
        });
    });
});