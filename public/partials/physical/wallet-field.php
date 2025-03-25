<tr>
    <td colspan="2" style="text-align: left; padding: 20px 10px 20px 10px;">
        <div class="ui info message wallet-option">
            <div class="header">
                <?php _e('Dana di dompet', 'sejoli'); ?>
            </div>
            <p style='line-heigh:40px;'>
                <label for="use-wallet">
                    <input id='use-wallet' class='use-wallet' type="checkbox" name="use-wallet" value="">
                    <input id='order-total' type="hidden" name="order-total" value="<?php echo $get_total; ?>">
                    <input id='order-rawtotal' type="hidden" name="order-rawtotal" value="">
                    <input id='available-wallet' type="hidden" name="available-wallet" value="<?php echo $wallet->available_total; ?>">
                    <span>
                    <?php
                    if( $wallet->available_total > $get_total ) :
                        
                        printf(
                            __('Dana yang tersedia %s, ingin menggunakan dana yang ada untuk pembayaran?', 'sejoli'),
                            sejolisa_price_format($wallet->available_total)
                        );

                    elseif ( true !== $using_wallet_only && $wallet->available_total < $get_total ) :

                        printf(
                            __('Dana yang tersedia %s, ingin menggunakan dana yang ada untuk pembayaran?', 'sejoli'),
                            sejolisa_price_format($wallet->available_total)
                        );

                    elseif ( true === $using_wallet_only && $wallet->available_total < $get_total ) :

                        printf(
                            __('Dana yang tersedia %s, dana tidak cukup untuk digunakan pembayaran!', 'sejoli'),
                            sejolisa_price_format($wallet->available_total)
                        );

                    endif;
                    ?>
                </label>
            </p>
        </div>
    </td>
</tr>
