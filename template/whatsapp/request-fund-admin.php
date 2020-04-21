<?php
    printf(
        __('%s telah melakukan permintaan pencairan dana sebesar %s.', 'sejoli'),
        '{{buyer-name}}',
        '{{request-fund}}'
    );
?>.


<?php
    printf(
        __('%s meminta untuk dicairkan melalui instruksi di bawah ini', 'sejoli'),
        '{{buyer-name}}'
    );
?>.

<?php echo '{{instruction}}'; ?>.
