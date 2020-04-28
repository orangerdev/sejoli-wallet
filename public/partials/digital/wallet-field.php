<tr>
    <th colspan='2'>
        <div class="ui info message">
            <div class="header">
                <?php _e('Dana di dompet', 'sejoli'); ?>
            </div>
            <p style='line-heigh:40px;'>
                <label for="use-wallet">
                    <input id='use-wallet' type="checkbox" name="use-wallet" value="">
                    <span>
                    <?php
                        printf(
                            __('Dana yang tersedia %s, ini menggunakan dana yang ada untuk pembayaran?', 'sejoli'),
                            sejolisa_price_format($wallet->available_total)
                        );
                    ?>
                </label>
            </p>
        </div>
    </th>
</tr>
