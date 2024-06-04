<script type="text/javascript">
(function($){
    'use strict';

    $(document).ready(function(){

        $('body').on('change', '#use-wallet', function(){
            sejoliSaCheckout.getCalculateAfterUseWallet();

            // $('.kode-diskon-form .alert-holder').hide();
        });
    });
})(jQuery);
</script>
