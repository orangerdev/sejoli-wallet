<div class="wrap">
    <h1 class="wp-heading-inline">
        <?php _e('Data Permintaan Pencairan Dana', 'sejoli'); ?>
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
                        <th><?php _e('Tgl', 'sejoli'); ?></th>
                        <th><?php _e('User', 'sejoli'); ?></th>
                        <th><?php _e('Dana', 'sejoli'); ?></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                    <tr>
                        <th><?php _e('Tgl', 'sejoli'); ?></th>
                        <th><?php _e('User', 'sejoli'); ?></th>
                        <th><?php _e('Dana', 'sejoli'); ?></th>
                        <th></th>
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

        sejoli.helper.filterData();

        sejoli_table = $('#sejoli-wallet').DataTable({
            language: dataTableTranslation,
            searching: false,
            processing: false,
            serverSide: true,
            ajax: {
                type: 'POST',
                url: sejoli_admin.wallet.request_table.ajaxurl,
                data: function(data) {
                    data.filter = sejoli.var.search;
                    data.action = 'sejoli-request-fund-table';
                    data.nonce = sejoli_admin.wallet.request_table.nonce
                    data.backend  = true;
                }
            },
            pageLength : 50,
            lengthMenu : [
                [10, 50, 100, 200],
                [10, 50, 100, 200],
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
                    data: 'created_at',
                    width: '80px',
                    className: 'center',
                },{
                    targets: 1,
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
                    targets: 2,
                    width: '80px',
                    data: 'value',
                    className: 'center'
                },{
                    targets: 3,
                    width:  '140px',
                    data: 'meta_data',
                    className: 'center',
                    render: function(data, type, full) {

                        let tmpl = $.templates('#request-action');
                        data.id = full.id;
                        
                        return tmpl.render(data);
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

        $('body').on('click', '.request-information', function(){
            let content = $(this).parent().parent().find('.detail-request-information').html();

            $('.sejoli-request-information').find('.description').html(content);
            $('.sejoli-request-information').modal('show');
            return false;
        });

        function sejoli_update_request(request_id, type) {
            let confirmed = confirm('<?php _e('Apakah anda yakin akan melakukan ini. Aksi ini tidak bisa diulang kembali', 'sejoli'); ?>');

            if(confirmed) {
                $.ajax({
                    url : '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'POST',
                    data: {
                        action : 'sejoli-update-request',
                        request_id : request_id,
                        type : type,
                        noncekey: '<?php echo wp_create_nonce('sejoli-update-request'); ?>'
                    },
                    beforeSend:function() {
                        sejoli.helper.blockUI('#sejoli-wallet');
                    },success:function(response) {

                        sejoli.helper.unblockUI('#sejoli-wallet');
                        sejoli_table.ajax.reload();

                        alert(response.message);

                    }
                })
            }
        }

        $('body').on('click', '.confirm-request', function(){
            let request_id = $(this).data('request'),
                request_action = $(this).data('type');

            sejoli_update_request(request_id, request_action);

            return false;
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
<script id='request-action' type="text/x-jsrender">
<div class='button-holder'>
    {{if accepted }}
    <span class="ui small green label">
        <i class="checkmark icon"></i> {{:accepted}}
    </span>
    {{else}}
    <div class="ui icon buttons">
        <button class="ui button small red confirm-request" data-request='{{:id}}' data-type='reject'><i class="close icon"></i></button>
        <button class="ui button small green confirm-request" data-request='{{:id}}' data-type='accept'><i class="check icon"></i></button>
        <button class="ui button small blue request-information"><i class="info icon"></i></button>
    </div>
    <div class='detail-request-information' style='display:none'>{{:note}}</div>
    {{/if}}
</div>
</script>
<div class="sejoli-request-information ui small modal">
    <i class="close icon"></i>
    <div class="header">
        <?php _e('Informasi Transfer', 'sejoli'); ?>
    </div>
    <div class="content">
        <div class="description">
        </div>
    </div>
    <div class="actions" style='margin-bottom:20px;padding-bottom:30px;'>
        <div class="ui positive right labeled icon button">
            <?php _e('Ok', 'sejoli'); ?>
            <i class="checkmark icon"></i><br />
        </div>
    </div>
</div>
