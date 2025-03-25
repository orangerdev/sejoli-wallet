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
                // $('#use-wallet').trigger('click');
                $('.beli-sekarang .submit-button').text('<?php echo __('BUAT PESANAN', 'sejoli-wallet'); ?>');
            }, 3500)
        } else {
            $('#use-wallet').hide();
            setTimeout(() => {
                $('.beli-sekarang .submit-button').attr('disabled','disabled');
                $('.beli-sekarang .submit-button').text('<?php echo __('SALDO TIDAK MENCUKUPI', 'sejoli-wallet'); ?>');
            }, 3500)
        }

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

        setTimeout(() => {
            $('.beli-sekarang .submit-button').attr('disabled','disabled');
        }, 3500)
    });

    $(document).ready(function(){
        $('.metode-pembayaran').remove();

        $(document).on('keyup', '#apply_coupon', function(){
            setTimeout(() => {
                $('.beli-sekarang .submit-button').attr('disabled','disabled');

                let total       = $('.total-bayar .total-holder:last').text();
                let getTotalVal = total.replace(/\D/g, "");

                if( getTotalVal ) {

                    $('input[name=order-total]').val(getTotalVal)

                    let order_total      = $('input[name=order-total]').val();
                    let available_wallet = $('input[name=available-wallet]').val();
                    let hasil            = available_wallet - order_total;
                    
                    if( hasil > 0 ) {
                        $('.use-wallet').show();
                        $('.beli-sekarang .submit-button').text('<?php echo __('BUAT PESANAN', 'sejoli-wallet'); ?>');
                        $('.beli-sekarang .submit-button').attr('disabled','disabled');
                    } else {
                        $('.use-wallet').hide();
                        $('.beli-sekarang .submit-button').text('<?php echo __('SALDO TIDAK MENCUKUPI', 'sejoli-wallet'); ?>');
                        $('.beli-sekarang .submit-button').attr('disabled','disabled');
                    }
                    
                    if(order_total === '0' || order_total === '00'){
                        $('.wallet-option').hide();
                        $('.use-wallet').hide();
                        $('.metode-pembayaran').hide();
                        $('input[name="payment_gateway"]').prop('checked', false);
                        $('.beli-sekarang .submit-button').removeAttr('disabled','disabled');
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
                $('.beli-sekarang .submit-button').attr('disabled','disabled');

                let total       = $('.total-bayar .total-holder:last').text();
                let getTotalVal = total.replace(/\D/g, "");

                if( getTotalVal ) {

                    $('input[name=order-total]').val(getTotalVal)

                    let order_total      = $('input[name=order-total]').val();
                    let available_wallet = $('input[name=available-wallet]').val();
                    let hasil            = available_wallet - order_total;

                    if( hasil > 0 ) {
                        $('.use-wallet').show();
                        $('.beli-sekarang .submit-button').text('<?php echo __('BUAT PESANAN', 'sejoli-wallet'); ?>');
                        $('.beli-sekarang .submit-button').attr('disabled','disabled');
                    } else {
                        $('.use-wallet').hide();
                        $('.beli-sekarang .submit-button').text('<?php echo __('SALDO TIDAK MENCUKUPI', 'sejoli-wallet'); ?>');
                        $('.beli-sekarang .submit-button').attr('disabled','disabled');
                    }

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
                        $('.beli-sekarang .submit-button').attr('disabled','disabled');
                    }

                }
            }, 5000)
        });

        $('body').on('change', '#use-wallet', function(){
            sejoliSaCheckoutRenew.getCalculateAfterUseWallet();

            var use_wallet = document.getElementsByName('use-wallet');

            for (var i = 0, length = use_wallet.length; i < length; i++) {
                if (use_wallet[i].checked) {
                    setTimeout(() => {
                        $('.beli-sekarang .submit-button').attr('disabled','disabled');

                        let total       = $('.total-bayar .total-holder:last').text();
                        let getTotalVal = total.replace(/\D/g, "");

                        if( getTotalVal ) {

                            $('input[name=order-total]').val(getTotalVal)

                            let order_total      = $('input[name=order-total]').val();
                            let available_wallet = $('input[name=available-wallet]').val();
                            let hasil            = available_wallet - order_total;

                            if( hasil > 0 ) {
                                $('.use-wallet').show();
                                $('.beli-sekarang .submit-button').text('<?php echo __('BUAT PESANAN', 'sejoli-wallet'); ?>');
                                $('.beli-sekarang .submit-button').removeAttr('disabled','disabled');
                            } else {
                                $('.use-wallet').hide();
                                $('.beli-sekarang .submit-button').text('<?php echo __('SALDO TIDAK MENCUKUPI', 'sejoli-wallet'); ?>');
                                $('.beli-sekarang .submit-button').attr('disabled','disabled');
                            }

                        }
                    }, 1500)

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
