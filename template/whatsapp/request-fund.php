<?php
    printf(
        __('Anda telah melakukan permintaan pencairan dana sebesar %s.', 'sejoli'),
        '{{wallet-value}}'
    );
?>.


<?php _e('Anda telah memberikan instruksi pencairan sebagai berikut : ', 'sejoli'); ?>.

<?php echo '{{instruction}}'; ?>.
