<?php
    sejoli_header();
    $amount = 0;
    $wallet_response = sejoli_get_user_wallet_data();

    if(false !== $wallet_response['valid']) :
        $amount = $wallet_response['wallet']->available_cash;
    endif;

    $currency_type = sejolisa_carbon_get_theme_option('sejoli_currency_type');

    if( $currency_type === "IDR" ) {
        $currency = 'Rp. ';
    } elseif( $currency_type === "MYR" ) {
        $currency = 'RM. ';
    }
?>
<h2 class="ui header"><?php _e('Pencairan Dana', 'sejoli'); ?></h2>
<form id='sejoli-request-fund' method="post" class="ui form">
    <div class="field">
        <label><?php _e('Total Pencairan', 'sejoli'); ?></label>
        <div class="ui right labeled input">
            <label for="amount" class="ui label"><?php echo $currency; ?> </label>
            <input type="number" placeholder="" id="amount" value='<?php echo $amount; ?>' name='amount' required />
        </div>
    </div>
    <div class="ui info message">
        <?php _e('Untuk pengisian rekening penerima, akan lebih baik jika anda mengisi lebih dari 1 rekening yang anda miliki untuk mempermudah kami melakukan pencairan dana', 'sejoli'); ?>
    </div>
    <div class="field">
        <label><?php _e('Rekening penerima *', 'sejoli'); ?></label>
        <textarea name="information" rows="8" cols="80" required></textarea>
    </div>
    <?php wp_nonce_field('sejoli-request-fund', 'noncekey'); ?>
    <div class="ui message sejoli-response-message" style='display:none'>

    </div>
    <button class="fluid ui blue big button" type="submit"><?php _e('Kirim permintaan pencairan', 'sejoli'); ?></button>
</form>
<script type="text/javascript">
(function($){

    let message = $('.sejoli-response-message');

    $('#sejoli-request-fund').submit(function(){
        let form = $(this)[0],
            data = new FormData(form);

        $.ajax({
            url : '<?php echo site_url('/sejoli-ajax/submit-request-fund'); ?>',
            data : data,
            type : 'POST',
            dataType: 'json',
            processData: false,
            contentType: false,
            beforeSend: function() {
                message.hide().html('').removeClass('success error');
                sejoli.block('#sejoli-request-fund');
            },
            success : function(response) {
                sejoli.unblock('#sejoli-request-fund');

                if(response.valid) {
                    message.html(response.message).addClass('success').show();
                } else {
                    message.html(response.message).addClass('error').show();
                }
            }
        });

        return false;
    });
})(jQuery);
</script>
<?php sejoli_footer();
