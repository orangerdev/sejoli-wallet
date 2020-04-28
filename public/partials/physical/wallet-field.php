<tr>
    <td colspan='3' style='text-align:left;'>
        <div class="ui info message">
            <div class="header">
                <?php _e('Dana di dompet', 'sejoli'); ?>
            </div>
            <p style='line-height:40px;'>
                <label for="use-wallet">
                    <input id='use-wallet' type="checkbox" name="use-wallet" value="">
                    <span>
                        <?php
                            printf(
                                __('Dana yang tersedia %s, ingin menggunakan dana yang ada untuk pembayaran?', 'sejoli'),
                                sejolisa_price_format($wallet->available_total)
                            );
                        ?>
                    </span>
                </label>
            </p>
        </div>
    </td>
</tr>
