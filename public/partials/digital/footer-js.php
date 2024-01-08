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
            }, 4000)
        } else {
            $('#use-wallet').hide();
            setTimeout(() => {
                $('.beli-sekarang .submit-button').attr('disabled','disabled');
                $('.beli-sekarang .submit-button').text('<?php echo __('SALDO TIDAK MENCUKUPI', 'sejoli-wallet'); ?>');
            }, 4000)
        }

        setTimeout(() => {
            $('.beli-sekarang .submit-button').attr('disabled','disabled');
        }, 4000)
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
                        $('.beli-sekarang .submit-button').text('<?php echo __('BUAT PESANAN', 'sejoli-wallet'); ?>');
                        $('.beli-sekarang .submit-button').attr('disabled','disabled');
                    } else {
                        $('.use-wallet').hide();
                        $('.beli-sekarang .submit-button').text('<?php echo __('SALDO TIDAK MENCUKUPI', 'sejoli-wallet'); ?>');
                        $('.beli-sekarang .submit-button').attr('disabled','disabled');
                    }

                }
            }, 4000)
        });

        $(document).on('click','.hapus-kupon',function(e){
            e.preventDefault();
            
            setTimeout(() => {
                $('.beli-sekarang .submit-button').attr('disabled','disabled');

                let total       = $('.total-bayar .total-holder').text();
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

                }
            }, 2500)
        });

        $('body').on('change', '#use-wallet', function(){
            sejoliSaCheckout.getCalculateAfterUseWallet();

            var use_wallet = document.getElementsByName('use-wallet');

            for (var i = 0, length = use_wallet.length; i < length; i++) {
                if (use_wallet[i].checked) {
                    setTimeout(() => {
                        $('.beli-sekarang .submit-button').attr('disabled','disabled');

                        let total       = $('.total-bayar .total-holder').text();
                        let getTotalVal = total.replace(/\D/g, "");

                        if( getTotalVal ) {

                            $('input[name=order-total]').val(getTotalVal)

                            let order_total      = $('input[name=order-total]').val();
                            let available_wallet = $('input[name=available-wallet]').val();
                            let hasil            = available_wallet - order_total;

                            if( hasil > 0 ) {
                                // $('.use-wallet').show();
                                $('.beli-sekarang .submit-button').text('<?php echo __('BUAT PESANAN', 'sejoli-wallet'); ?>');
                                $('.beli-sekarang .submit-button').attr('disabled','disabled');
                            } else {
                                // $('.use-wallet').hide();
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
    // $(document).ready(function(){
    //     $('.metode-pembayaran').remove();

    //     // $(document).on('sejoli:calculate', function(){
    //     //     alert("OK");

    //     //     $('.beli-sekarang .submit-button').attr('disabled','disabled');

    //     //     let total       = $('.beli-sekarang .total-holder').text();
    //     //     let getTotalVal = total.replace(/\D/g, "");

    //     //     if( getTotalVal ) {

    //     //         $('input[name=order-total]').val(getTotalVal)

    //     //         let order_total      = $('input[name=order-total]').val();
    //     //         let available_wallet = $('input[name=available-wallet]').val();
    //     //         let hasil            = available_wallet - order_total;

    //     //         if( hasil > 0 ) {
    //     //             $('.use-wallet').show();
    //     //             $('.beli-sekarang .submit-button').text('<?php echo __('BUAT PESANAN', 'sejoli-wallet'); ?>');
    //     //         } else {
    //     //             $('.use-wallet').hide();
    //     //             $('.beli-sekarang .submit-button').text('<?php echo __('SALDO TIDAK MENCUKUPI', 'sejoli-wallet'); ?>');
    //     //             $('.beli-sekarang .submit-button').attr('disabled','disabled');
    //     //         }

    //     //     }

    //     // });

    //     $('body').on('change', '#use-wallet', function(){
    //         sejoliSaCheckout.getCalculateAfterUseWallet();

    //         var use_wallet = document.getElementsByName('use-wallet');

    //         for (var i = 0, length = use_wallet.length; i < length; i++) {
    //             if (use_wallet[i].checked) {

    //                 setTimeout(() => {
    //                     let total       = $('.beli-sekarang .total-holder').text();
    //                     let getTotalVal = total.replace(/\D/g, "");

    //                     if( getTotalVal ) {

    //                         $('input[name=order-total]').val(getTotalVal)

    //                         let order_total      = $('input[name=order-total]').val();
    //                         let available_wallet = $('input[name=available-wallet]').val();
    //                         let hasil            = available_wallet - order_total;

    //                         if( hasil > 0 ) {
    //                             // $('.use-wallet').show();
    //                             $('.beli-sekarang .submit-button').text('<?php echo __('BUAT PESANAN', 'sejoli-wallet'); ?>');
    //                             setTimeout(() => {
    //                                 $('.beli-sekarang .submit-button').removeAttr('disabled','disabled');
    //                             }, 100);
    //                         } else {
    //                             // $('.use-wallet').hide();
    //                             $('.beli-sekarang .submit-button').text('<?php echo __('SALDO TIDAK MENCUKUPI', 'sejoli-wallet'); ?>');
    //                             setTimeout(() => {
    //                                 $('.beli-sekarang .submit-button').attr('disabled','disabled');
    //                             }, 100);
    //                         }

    //                     }
    //                     // $('.beli-sekarang .submit-button').removeAttr('disabled','disabled');
    //                 }, 1500);

    //                 break;
    //             } else {
    //                 setTimeout(() => {
    //                     $('.beli-sekarang .submit-button').attr('disabled','disabled');
    //                 }, 1500);

    //                 break;
    //             }
    //         }
    //     });
    // });
})(jQuery);
</script>
