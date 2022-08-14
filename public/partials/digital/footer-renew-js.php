<script type="text/javascript">
(function($){
    'use strict';

    $(window).load(function() {
        let order_total      = $('input[name=order-total]').val();
        let available_wallet = $('input[name=available-wallet]').val();
        let hasil            = available_wallet - order_total;

        if( hasil > 0 ) {
            $('#use-wallet').show();
            setTimeout(() => {
                $('#use-wallet').trigger('click');
                $('.beli-sekarang .submit-button').text('<?php echo __('PROSES SEKARANG', 'sejoli-wallet'); ?>');
            }, 2000)
        } else {
            $('#use-wallet').hide();
            setTimeout(() => {
                $('.beli-sekarang .submit-button').text('<?php echo __('SALDO TIDAK MENCUKUPI', 'sejoli-wallet'); ?>');
            }, 2000)
        }

        setTimeout(() => {
            $('.beli-sekarang .submit-button').attr('disabled','disabled');
        }, 2000)
    });

    $(document).ready(function(){
        $('.metode-pembayaran').remove();

        $(document).on('keyup', '#apply_coupon', function(){
            setTimeout(() => {
                $('.beli-sekarang .submit-button').attr('disabled','disabled');

                let total       = $('.total-bayar .total-holder').text();
                let getTotalVal = total.replace(/\D/g, "");

                if( getTotalVal ) {

                    $('input[name=order-total]').val(getTotalVal)

                    let order_total      = $('input[name=order-total]').val();
                    let available_wallet = $('input[name=available-wallet]').val();
                    let hasil            = available_wallet - order_total;
                    // alert(hasil);

                    if( hasil > 0 ) {
                        $('.use-wallet').show();
                        $('.beli-sekarang .submit-button').text('<?php echo __('PROSES SEKARANG', 'sejoli-wallet'); ?>');
                        $('.beli-sekarang .submit-button').attr('disabled','disabled');
                    } else {
                        $('.use-wallet').hide();
                        $('.beli-sekarang .submit-button').text('<?php echo __('SALDO TIDAK MENCUKUPI', 'sejoli-wallet'); ?>');
                        $('.beli-sekarang .submit-button').attr('disabled','disabled');
                    }

                }
            }, 3000)
        });

        $('body').on('change', '#use-wallet', function(){
            sejoliSaCheckoutRenew.getCalculateAfterUseWallet();

            setTimeout(() => {
                $('.beli-sekarang .submit-button').removeAttr('disabled','disabled');
            }, 3000)
        });
    });
})(jQuery);
</script>
