<?php
    sejoli_header();
    $amount = 0;
    $wallet_response = sejoli_get_user_wallet_data();

    if(false !== $wallet_response['valid']) :
        $amount = $wallet_response['wallet']->available_cash;
    endif;
?>
<h2 class="ui header"><?php _e('Pencairan Dana', 'sejoli'); ?></h2>
<form class="ui form">
    <div class="field">
        <label><?php _e('Total Pencairan', 'sejoli'); ?></label>
        <div class="ui right labeled input">
            <label for="amount" class="ui label">Rp. </label>
            <input type="number" placeholder="" id="amount" value='<?php echo $amount; ?>' name='amount' />
        </div>
    </div>
    <div class="ui info message">
        <?php _e('Untuk pengisian rekening penerima, akan lebih baik jika anda mengisi lebih dari 1 rekening yang anda miliki untuk mempermudah kami melakukan pencairan dana', 'sejoli'); ?>
    </div>
    <div class="field">
        <label><?php _e('Rekening penerima', 'sejoli'); ?></label>
        <textarea name="information" rows="8" cols="80"></textarea>
    </div>
    <?php wp_nonce_field('sejoli-request-fund', 'noncekey'); ?>
    <button class="fluid ui blue big button" type="submit"><?php _e('Kirim permintaan pencairan', 'sejoli'); ?></button>
</form>
<?php sejoli_footer();
