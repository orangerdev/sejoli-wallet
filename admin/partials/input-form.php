<div class="wrap">
    <h1><?php _e('Form Perubahan Saldo', 'sejoli'); ?></h1>
    <form id='sejoli-add-wallet-data-form' action="" method="post">
        <table class='form-table' role='presentation'>
            <tbody>
                <tr>
                    <th scope='row'>
                        <?php _e('User', 'sejoli'); ?>
                    </th>
                    <td>
                        <select id='sejoli-user-options' name="data[user_id]" class='regular-text sejoli-wallet-field' required></select>
                        <p class="description" id="sejoli-user">Cari user sesuai dengan nama lengkap atau alamat email</p>
                    </td>
                </tr>
                <tr>
                    <th scope='row'>
                        <?php _e('Saldo', 'sejoli'); ?>
                    </th>
                    <td>
                        <input type="number" name="data[wallet]" class='regular-text sejoli-wallet-field' required value='0'/>
                        <p class="description" id="sejoli-wallet">Diisi dengan pass yang anda gunakan di sejoli.id</p>
                    </td>
                </tr>
                <tr>
                    <th scope='row'>
                        <?php _e('Operasi', 'sejoli'); ?>
                    </th>
                    <td>
                        <select class='sejoli-wallet-field' name="data[operation]" required data-default='add'>
                            <option value="add"><?php _e('Tambah Saldo', 'sejoli'); ?></option>
                            <option value="reduce"><?php _e('Kurangi Saldo', 'sejoli'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope='row'>
                        <?php _e('Bisa Dicairkan?', 'sejoli'); ?>
                    </th>
                    <td>
                        <select class='sejoli-wallet-field' name="data[refundable]" required data-default='1'>
                            <option value="1"><?php _e('Bisa', 'sejoli'); ?></option>
                            <option value=""><?php _e('Tidak', 'sejoli'); ?></option>
                        </select>
                        <p class="description" id="sejoli-refundabel"><?php _e('Jika memilih bisa dicairkan, maka data yang dimasukkan akan berupa CASHBACK, sebaliknya akan berupa POIN', 'sejoli'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope='row'>
                        <?php _e('Catatan', 'sejoli'); ?>
                    </th>
                    <td>
                        <textarea class='sejoli-wallet-field' name="data[note]" rows="8" cols="80"></textarea>
                    </td>
                </tr>
            </tbody>
        </table>
        <p class='submit'>
            <button type="submit" name="button" class='button button-primary' id='sejoli-add-button'><?php _e('Proses Perubahan Saldo', 'sejoli'); ?></button>
        </p>
        <?php wp_nonce_field('sejoli-add-wallet-data', 'noncekey'); ?>
    </form>
</div>
<script type="text/javascript">
(function($){

    'use strict';

    $(document).ready(function(){

        sejoli.helper.select_2(
            "#sejoli-user-options",
            sejoli_admin.user.select.ajaxurl,
            sejoli_admin.user.placeholder
        );

        $('#sejoli-add-wallet-data-form').submit(function(e){

            let formData = new FormData($(this)[0]),
                submitButton = $('#sejoli-add-button'),
                notice = $('.sejoli-reset-data-response');

            formData.append('action', 'add-input-wallet-data');

            console.log(formData);

            $.ajax({
                type: 'POST',
                data:   formData,
                url:    '<?php echo admin_url('admin-ajax.php'); ?>',
                contentType: false,
                processData: false,
                dataType:   'json',
                beforeSend: function() {
                    submitButton.attr('disabled', true);
                    notice.show()
                        .removeClass('notice-error notice-success')
                        .addClass('notice-info')
                        .html('<p><?php _e('Sedang melakukan proses penambahan data pada wallet...', 'sejoli'); ?></p>');

                }, success: function(response) {

                    if(response.success) {
                        notice.show()
                            .removeClass('notice-info notice-error')
                            .addClass('notice-success').html(response.message);
                    } else {
                        notice.show()
                            .removeClass('notice-info notice-success')
                            .addClass('notice-error').html(response.message);
                    }

                    submitButton.attr('disabled', false);
                }
            });

            return false;
        });

    });

})(jQuery);
</script>
