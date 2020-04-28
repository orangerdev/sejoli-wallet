<tr>
    <th>
        <?php if('digital' === $product->type || false === $product->enable_quantity) : ?>
        <p><?php _e('Potensi cashback yang anda dapatkan', 'sejoli'); ?></p>
        <?php else : ?>
        <p><?php _e('Potensi cashback yang anda dapatkan per satu item', 'sejoli'); ?></p>
        <?php endif; ?>
    </th>
    <th><?php echo sejolisa_price_format($product->cashback['amount']); ?></th>
</tr>
