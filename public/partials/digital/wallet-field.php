<tr>
    <th colspan='2'>
        <div class="ui info message">
            <div class="header">
                <?php _e('Dana di dompet', 'sejoli'); ?>
            </div>
            <p style='line-heigh:40px;'>
                <label for="use-wallet">
                    <input id='use-wallet' class='use-wallet' type="checkbox" name="use-wallet" value="">
                    <input id='order-total' type="hidden" name="order-total" value="<?php echo $get_total; ?>">
                    <input id='available-wallet' type="hidden" name="available-wallet" value="<?php echo $wallet_data['wallet']->available_total; ?>">
                    <span>
                    <?php
                        printf(
                            __('Dana yang tersedia %s, ingin menggunakan dana yang ada untuk pembayaran?', 'sejoli'),
                            sejolisa_price_format($wallet->available_total)
                        );
                    ?>
                </label>
            </p>
        </div>
    </th>
</tr>
