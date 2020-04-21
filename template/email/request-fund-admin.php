<p>
<?php
    printf(
        __('%s telah melakukan permintaan pencairan dana sebesar %s.', 'sejoli'),
        '{{buyer-name}}',
        '{{request-fund}}'
    );
?>
</p>
<p>
<?php
    printf(
        __('%s meminta untuk dicairkan melalui instruksi di bawah ini : <br /> %s', 'sejoli'),
        '{{buyer-name}}',
        '{{instruction}}'
    );
?>
</p>
