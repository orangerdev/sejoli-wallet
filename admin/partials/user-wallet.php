<div class="wrap">
    <h1 class="wp-heading-inline">
        <?php _e('Data Saldo User', 'sejoli'); ?>
	</h1>
    <div class="sejoli-table-wrapper">
        <div class='sejoli-form-action-holder'>

            <div class="sejoli-form-filter box" style='float:right;'>
                <button type="button" name="button" class='export-csv button'><?php _e('Export CSV', 'sejoli'); ?></button>
                <button type="button" name="button" class='button toggle-search'><?php _e('Filter Data', 'sejoli'); ?></button>
                <div class="sejoli-form-filter-holder sejoli-form-float">
                    <select class="autosuggest filter" name="user_id"></select>
                    <?php wp_nonce_field('search-user', 'sejoli-nonce'); ?>
                    <button type="button" name="button" class='button button-primary do-search'><?php _e('Cari Data', 'sejoli'); ?></button>
                    <!-- <button type="button" name="button" class='button button-primary reset-search'><?php _e('Reset Pencarian', 'sejoli'); ?></button> -->
                </div>
            </div>
        </div>
        <div class="sejoli-table-holder">
            <table id="sejoli-wallet" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th><?php _e('User', 'sejoli'); ?></th>
                        <th><?php _e('Cash Masuk', 'sejoli'); ?></th>
                        <th><?php _e('Poin Masuk', 'sejoli'); ?></th>
                        <th><?php _e('Penggunaan', 'sejoli'); ?></th>
                        <th><?php _e('Sisa Cash', 'sejoli'); ?></th>
                        <th><?php _e('Semua Sisa', 'sejoli'); ?></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                    <tr>
                        <th><?php _e('User', 'sejoli'); ?></th>
                        <th><?php _e('Cash Masuk', 'sejoli'); ?></th>
                        <th><?php _e('Poin Masuk', 'sejoli'); ?></th>
                        <th><?php _e('Penggunaan', 'sejoli'); ?></th>
                        <th><?php _e('Sisa Cash', 'sejoli'); ?></th>
                        <th><?php _e('Semua Sisa', 'sejoli'); ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<script type="text/javascript">

let sejoli_table;

(function( $ ) {
	'use strict';
    $(document).ready(function() {

        sejoli.helper.select_2(
            "select[name='user_id']",
            sejoli_admin.user.select.ajaxurl,
            sejoli_admin.user.placeholder
        );

        console.log(sejoli_admin.wallet);

        sejoli.helper.filterData();

        sejoli_table = $('#sejoli-wallet').DataTable({
            language: dataTableTranslation,
            searching: false,
            processing: true,
            serverSide: false,
            info: false,
            paging: false,
            ajax: {
                type: 'POST',
                url: sejoli_admin.wallet.table.ajaxurl,
                data: function(data) {
                    data.filter = sejoli.var.search;
                    data.action = 'sejoli-wallet-table';
                    data.nonce = sejoli_admin.wallet.table.nonce
                    data.backend  = true;
                }
            },
            pageLength : 50,
            lengthMenu : [
                [10, 50, 100, 200],
                [10, 50, 100, 200],
            ],
            order: [
                [ 5, "desc" ]
            ],
            columnDefs: [
                {
                    targets: [0],
                    orderable: false
                },{
                    targets: 0,
                    data : 'display_name',
                    render: function(data, type, full) {

                        let tmpl = $.templates('#user-detail');

                        return tmpl.render({
                            id : full.user_id,
                            display_name : full.display_name,
                            email : full.user_email,
                            detail_url: full.detail_url,
                        })
                    }
                },{
                    targets: 1,
                    width: '80px',
                    data: 'cash_value',
                    className: 'center'
                },{
                    targets: 2,
                    width: '80px',
                    data : 'point_value',
                    className: 'center'
                },{
                    targets: 3,
                    width:  '80px',
                    data: 'used_value',
                    className: 'center'
                },{
                    targets: 4,
                    width:  '80px',
                    data: 'available_cash',
                    className: 'center'
                },{
                    targets: 5,
                    width:  '80px',
                    data: 'available_total',
                    className: 'center'
                }
            ]
        });

        sejoli_table.on('preXhr',function(){
            sejoli.helper.blockUI('.sejoli-table-holder');
        });

        sejoli_table.on('xhr',function(){
            sejoli.helper.unblockUI('.sejoli-table-holder');
        });

        $(document).on('click', '.toggle-search', function(){
            $('.sejoli-form-filter-holder').toggle();
        });

        $(document).on('click', '.do-search', function(){
            sejoli.helper.filterData();
            sejoli_table.ajax.reload();
            $('.sejoli-form-filter-holder').hide();
        });

        $(document).on('click', '.export-csv', function(){
            sejoli.helper.filterData();
            
            $('<form action="' + sejoli_admin.wallet.export_prepare.ajaxurl + '" method="POST">')
                .append($('<input />').attr('type', 'hidden').attr('name', 'filter').val(sejoli.var.search))
                .append($('<input />').attr('type', 'hidden').attr('name', 'nonce').val(sejoli_admin.wallet.export_prepare.nonce))
                .appendTo($(document.body))
                .submit()
        });

    });
})(jQuery);
</script>
<script id='user-detail' type="text/x-jsrender">
<a type='button' class='ui mini button' href='{{:detail_url}}' target='_blank'>DETAIL</a> {{:display_name}}
<div style='line-height:220%'>
    <span class="ui purple label"><i class="envelope icon"></i>{{:email}}</span>
</div>
</script>
