<script type="text/javascript">
(function($){
    'use strict';

    $(window).load(function() {
        let order_total      = $('input[name=order-total]').val();
        let available_wallet = $('input[name=available-wallet]').val();

        if(available_wallet < order_total) {
            $('#use-wallet').hide();
            setTimeout(() => {
                $('.beli-sekarang .submit-button').text('<?php echo __('SALDO TIDAK MENCUKUPI', 'sejoli-wallet'); ?>');
            }, 5000)
        } else {
            $('#use-wallet').show();
            setTimeout(() => {
                $('#use-wallet').trigger('click');
                $('.beli-sekarang .submit-button').text('<?php echo __('PROSES SEKARANG', 'sejoli-wallet'); ?>');
            }, 5000)
        }

        setTimeout(() => {
            $('.beli-sekarang .submit-button').attr('disabled','disabled');
        }, 5000)
    });

    $(document).ready(function(){
        $('.metode-pembayaran').remove();

        $(document).on('sejoli:calculate', function(){

            $('.beli-sekarang .submit-button').attr('disabled','disabled');

            let total       = $('.beli-sekarang .total-holder').text();
            let getTotalVal = total.replace(/\D/g, "");

            if( getTotalVal ) {

                $('input[name=order-total]').val(getTotalVal)

                let order_total      = $('input[name=order-total]').val();
                let available_wallet = $('input[name=available-wallet]').val();
                let hasil            = available_wallet - order_total;

                if( hasil > 0 ) {
                    $('.use-wallet').show();
                    $('.beli-sekarang .submit-button').text('<?php echo __('PROSES SEKARANG', 'sejoli-wallet'); ?>');
                } else {
                    $('.use-wallet').hide();
                    $('.beli-sekarang .submit-button').text('<?php echo __('SALDO TIDAK MENCUKUPI', 'sejoli-wallet'); ?>');
                }

            }

        });

        $('body').on('change', '#use-wallet', function(){
            sejoliSaCheckout.getCalculateAfterUseWallet();
        });
    });
})(jQuery);
</script>
