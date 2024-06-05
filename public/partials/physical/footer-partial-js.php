<script type="text/javascript">
(function($){
    'use strict';

    $(document).ready(function(){
        $('body').on('change', '#use-wallet', function(){
            sejoliSaCheckoutFisik.func.getCalculateAfterUseWallet();
        });
    });
})(jQuery);
</script>
