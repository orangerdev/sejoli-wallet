<?php
    printf(
        __('Pencairan dana sebesar %s oleh %s telah dibatalkan.', 'sejoli'),
        '{{request-fund}}',
        '{{buyer-name}}'
    );
?>&nbsp;


<?php
    printf(
        __('Dana sebesar %s telah dikembalikan ke saldo.', 'sejoli'),
        '{{request-fund}}'
    );
