<tr>
    <th colspan='2'>
        <div class="ui info message">
            <div class="header">
                <?php _e('Dana di dompet', 'sejoli'); ?>
            </div>
            <?php if($wallet_data['wallet']->available_cash > $product->price): ?>
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
            <?php else: ?>
                <p style='line-heigh:40px;'>
                <label for="use-wallet">
                    <span>
                    <?php
                        printf(
                            __('Dana yang tersedia %s, tidak cukup untuk melakukan pembelian ini.', 'sejoli'),
                            sejolisa_price_format($wallet->available_total)
                        );
                    ?>
                </label>
            </p>
            <script type="text/javascript">
                $(window).load(function() {
                    setTimeout(() => {
                        $('.beli-sekarang .submit-button').attr('disabled','disabled');
                    }, 5000)
                });
            </script>
            <?php endif; ?>
        </div>
    </th>
</tr>
