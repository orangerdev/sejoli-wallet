<?php
    $date = date('Y-m-d', strtotime('-30day')) . ' - ' . date('Y-m-d');
?>
<div class="wrap">
    <h1 class="wp-heading-inline">
        <?php printf(__('Detil Poin User : %s', 'sejoli'), $user->display_name); ?>
	</h1>
    <div class="sejoli-table-wrapper">
        <div class='sejoli-form-action-holder'>

            <div class="sejoli-form-filter box" style='float:right;'>
                <button type="button" name="button" class='export-csv button'><?php _e('Export CSV', 'sejoli'); ?></button>
                <button type="button" name="button" class='button toggle-search'><?php _e('Filter Data', 'sejoli'); ?></button>
                <div class="sejoli-form-filter-holder sejoli-form-float">
                    <input type="text" class='filter' name="date-range" value="<?php echo $date; ?>" placeholder="<?php _e('Pencarian berdasarkan tanggal', 'sejoli'); ?>">
                    <select class="autosuggest filter" name="product_id"></select>
                    <select class="autosuggest filter" name='type'>
                        <option value=''><?php _e('Pilih tipe poin', 'sejoli'); ?></option>
                        <option value='in'><?php _e('Poin masuk', 'sejoli'); ?></option>
                        <option value='out'><?php _e('Poin keluar', 'sejoli'); ?></option>
                    </select>
                    <button type="button" name="button" class='button button-primary do-search'><?php _e('Cari Data', 'sejoli'); ?></button>
                    <!-- <button type="button" name="button" class='button button-primary reset-search'><?php _e('Reset Pencarian', 'sejoli'); ?></button> -->
                </div>
            </div>
        </div>
        <div class="sejoli-table-holder">
            <table id="sejoli-wallet" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th><?php _e('Tgl', 'sejoli'); ?></th>
                        <th><?php _e('Detil', 'sejoli'); ?></th>
                        <th><?php _e('Poin', 'sejoli'); ?></th>
                        <th><?php _e('Tipe', 'sejoli'); ?></th>
                        <th><?php _e('Status', 'sejoli'); ?></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                    <tr>
                        <th><?php _e('Tgl', 'sejoli'); ?></th>
                        <th><?php _e('Detil', 'sejoli'); ?></th>
                        <th><?php _e('Poin', 'sejoli'); ?></th>
                        <th><?php _e('Tipe', 'sejoli'); ?></th>
                        <th><?php _e('Status', 'sejoli'); ?></th>
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
            "select[name='product_id']",
            sejoli_admin.product.select.ajaxurl,
            sejoli_admin.product.placeholder
        );

        sejoli.helper.daterangepicker("input[name='date-range']");

        sejoli.helper.filterData();

        sejoli_table = $('#sejoli-wallet').DataTable({
            language: dataTableTranslation,
            searching: false,
            processing: false,
            serverSide: true,
            ajax: {
                type: 'POST',
                url: sejoli_admin.wallet.single_table.ajaxurl,
                data: function(data) {

                    data.__sejoli_ajax = 'single-wallet-table';
                    data.nonce = sejoli_admin.wallet.single_table.nonce;
                    data.user_id = sejoli_admin.wallet.single_table.user_id;
                    data.backend  = true;
                }
            },
            pageLength : 50,
            lengthMenu : [
                [50, 100, 200],
                [50, 100, 200],
            ],
            order: [
                [ 0, "desc" ]
            ],
            columnDefs: [
                {
                    targets: [1, 2, 3],
                    orderable: false
                },{
                    targets: 0,
                    width: '80px',
                    data : 'created_at',
                    className: 'center'
                },{
                    targets: 1,
                    data: 'detail',
                },{
                    targets: 2,
                    width: '80px',
                    data : 'point',
                    className: 'center'
                },{
                    targets: 3,
                    width: '80px',
                    data : 'refundable',
                    className: 'center',
                    render: function(data, meta, full) {
                        if('out' === full.type) {
                            return '-';
                        } else if( true === data ) {
                            return '<label class="ui blue label">Cash</label>';
                        } else {
                            return '<label class="ui purple label">Point</label>';
                        }
                    }
                },{
                    targets: 4,
                    width:  '80px',
                    data: 'type',
                    className: 'center',
                    render: function(data) {
                        if('in' === data) {
                            return '<label class="ui green label">Tambah</label>';
                        } else {
                            return '<label class="ui yellow label">Kurang</label>';
                        }
                    }
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

    });
})(jQuery);
</script>
