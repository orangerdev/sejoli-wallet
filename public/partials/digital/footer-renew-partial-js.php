<script type="text/javascript">
(function($){
    'use strict';

    $(document).ready(function(){

        let order_total = $('input[name=order-total]').val();

        if(order_total === '0' || order_total === '00'){
            $('.wallet-option').hide();
            $('.use-wallet').hide();
            $('.metode-pembayaran').hide();
            $('input[name="payment_gateway"]').prop('checked', false);
        } else {
            $('.wallet-option').show();
            $('.use-wallet').show();
            $('.metode-pembayaran').show();
            $('input[name="payment_gateway"]').prop('checked', true);
        }

        $(document).on('keyup', '#apply_coupon', function(){
            setTimeout(() => {
                // $('.beli-sekarang .submit-button').attr('disabled','disabled');

                let total       = $('.total-bayar .total-holder:last').text();
                let getTotalVal = total.replace(/\D/g, "");

                if( getTotalVal ) {

                    $('input[name=order-total]').val(getTotalVal)

                    let order_total      = $('input[name=order-total]').val();
                    let available_wallet = $('input[name=available-wallet]').val();
                    let hasil            = available_wallet - order_total;
                    
                    // if( hasil > 0 ) {
                        $('.use-wallet').show();
                        $('.beli-sekarang .submit-button').text('<?php echo __('BUAT PESANAN', 'sejoli-wallet'); ?>');
                        // $('.beli-sekarang .submit-button').attr('disabled','disabled');
                    // } else {
                    //     $('.use-wallet').hide();
                    //     $('.beli-sekarang .submit-button').text('<?php echo __('SALDO TIDAK MENCUKUPI', 'sejoli-wallet'); ?>');
                    //     $('.beli-sekarang .submit-button').attr('disabled','disabled');
                    // }
                    
                    if(order_total === '0' || order_total === '00'){
                        $('.wallet-option').hide();
                        $('.use-wallet').hide();
                        $('.metode-pembayaran').hide();
                        $('input[name="payment_gateway"]').prop('checked', false);
                    } else {
                        $('.wallet-option').show();
                        $('.use-wallet').show();
                        $('.metode-pembayaran').show();
                        $('input[name="payment_gateway"]').prop('checked', true);
                    }

                }
            }, 5000)
        });

        $(document).on('click','.hapus-kupon',function(e){
            e.preventDefault();
            
            setTimeout(() => {
                // $('.beli-sekarang .submit-button').attr('disabled','disabled');

                let total       = $('.total-bayar .total-holder:last').text();
                let getTotalVal = total.replace(/\D/g, "");

                if( getTotalVal ) {

                    $('input[name=order-total]').val(getTotalVal)

                    let order_total      = $('input[name=order-total]').val();
                    let available_wallet = $('input[name=available-wallet]').val();
                    let hasil            = available_wallet - order_total;

                    // if( hasil > 0 ) {
                        $('.use-wallet').show();
                        $('.beli-sekarang .submit-button').text('<?php echo __('BUAT PESANAN', 'sejoli-wallet'); ?>');
                        // $('.beli-sekarang .submit-button').attr('disabled','disabled');
                    // } else {
                    //     $('.use-wallet').hide();
                    //     $('.beli-sekarang .submit-button').text('<?php echo __('SALDO TIDAK MENCUKUPI', 'sejoli-wallet'); ?>');
                    //     $('.beli-sekarang .submit-button').attr('disabled','disabled');
                    // }

                    if(order_total === '0' || order_total === '00'){
                        $('.wallet-option').hide();
                        $('.use-wallet').hide();
                        $('.metode-pembayaran').hide();
                        $('input[name="payment_gateway"]').prop('checked', false);
                    } else {
                        $('.wallet-option').show();
                        $('.use-wallet').show();
                        $('.metode-pembayaran').show();
                        $('input[name="payment_gateway"]').prop('checked', true);
                    }

                }
            }, 5000)
        });

        $(document).on('sejoli:calculate', function(){
            console.log('ahoy');
        });

        $('body').on('change', '#use-wallet', function(){
            sejoliSaCheckout.getCalculate();

            setTimeout(() => {
                let total = $('.total-bayar .total-holder:last').text();
                let getTotalVal = total.replace(/\D/g, "");

                if( getTotalVal ) {

                    $('input[name=order-total]').val(getTotalVal)

                    let order_total      = $('input[name=order-total]').val();

                    if(order_total === '0' || order_total === '00'){
                        $('.metode-pembayaran').hide();
                        $('input[name="payment_gateway"]').prop('checked', false);
                    } else {
                        $('.metode-pembayaran').show();
                        $('input[name="payment_gateway"]').prop('checked', true);
                    }

                }
            }, 1500)    
        });

    });
})(jQuery);
</script>
