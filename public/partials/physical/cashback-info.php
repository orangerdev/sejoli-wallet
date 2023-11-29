<tr>
    <td colspan='2' style="width: 70%; vertical-align: middle;">
        <?php if(false === $product->enable_quantity) : ?>
        <p><?php _e('Potensi cashback yang anda dapatkan', 'sejoli'); ?></p>
        <?php else : ?>
        <p><?php _e('Potensi cashback yang anda dapatkan per satu item', 'sejoli'); ?></p>
        <?php endif; ?>
    </td>
    <td style="text-align: right;"><?php echo sejolisa_price_format($product->cashback['amount']); ?></td>
</tr>
