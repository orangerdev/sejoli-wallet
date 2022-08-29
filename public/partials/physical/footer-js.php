<script type="text/javascript">
(function($){
    'use strict';

    $(window).load(function() {
        setTimeout(() => {
            let order_total      = $('input[name=order-total]').val();
            let getOrderTotalVal = order_total.match(/\d/g).join('')

            document.getElementById("order-total").value = getOrderTotalVal;
            let available_wallet = $('input[name=available-wallet]').val();

            if(available_wallet < getOrderTotalVal) {
                $('.use-wallet').hide();
                setTimeout(() => {
                    $('.beli-sekarang .submit-button').text('<?php echo __('SALDO TIDAK MENCUKUPI', 'sejoli-wallet'); ?>');
                }, 1500)
            } else {
                $('.use-wallet').show();
                setTimeout(() => {
                    $('#use-wallet').trigger('click');
                    $('.beli-sekarang .submit-button').text('<?php echo __('BUAT PESANAN', 'sejoli-wallet'); ?>');
                }, 1500)
            }

            $('.beli-sekarang .submit-button').attr('disabled','disabled');
        }, 2000)
    });

    $(document).ready(function(){
        $('.metode-pembayaran').remove();

        $(document).on('sejoli:calculate', function(){

            let total       = $('.total-holder').text();
            let getTotalVal = total.match(/\d/g).join('')
            
            document.getElementById("order-rawtotal").value = getTotalVal;

            let order_total      = $('input[name=order-rawtotal]').val();
            let available_wallet = document.getElementById('available-wallet').value;

            let hasil = available_wallet - order_total;

            if( hasil > 0 ) {
                $('.use-wallet').show();
                $('.beli-sekarang .submit-button').text('<?php echo __('BUAT PESANAN', 'sejoli-wallet'); ?>');
            } else {
                $('.use-wallet').hide();
                $('.beli-sekarang .submit-button').text('<?php echo __('SALDO TIDAK MENCUKUPI', 'sejoli-wallet'); ?>');
            }

        });

        $('body').on('change', '#use-wallet', function(){
            sejoliSaCheckoutFisik.func.getCalculateAfterUseWallet();
            
            var use_wallet = document.getElementsByName('use-wallet');

            for (var i = 0, length = use_wallet.length; i < length; i++) {
                if (use_wallet[i].checked) {
                    setTimeout(() => {
                        $('.beli-sekarang .submit-button').removeAttr('disabled','disabled');
                    }, 1500);

                    break;
                } else {
                    setTimeout(() => {
                        $('.beli-sekarang .submit-button').attr('disabled','disabled');
                    }, 1500);

                    break;
                }
            }
        });
    });
})(jQuery);
</script>
