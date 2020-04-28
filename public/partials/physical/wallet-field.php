<tr>
    <td colspan='3'>
        <h4><?php _e('Dana di dompet', 'sejoli'); ?></h4>
        <p>
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
    </td>
</tr>
