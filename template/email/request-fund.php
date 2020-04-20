<p>
<?php
    printf(
        __('Anda telah melakukan permintaan pencairan dana sebesar %s.', 'sejoli'),
        '{{wallet-value}}'
    );
?>
</p>
<p>
<?php
    printf(
        __('Anda telah memberikan instruksi pencairan sebagai berikut : <br /> %s', 'sejoli'),
        '{{instruction}}'
    );
?>
</p>
