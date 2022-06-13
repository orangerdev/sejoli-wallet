<script type="text/javascript">
(function($){
    'use strict';

    $(document).ready(function(){

        $(document).on('sejoli:calculate', function(){
            console.log('ahoy');
        });

        $('body').on('change', '#use-wallet', function(){
            sejoliSaCheckout.getCalculate();
        });
    });
})(jQuery);
</script>
