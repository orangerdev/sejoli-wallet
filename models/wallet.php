<?php

namespace SEJOLI_WALLET\Model;

Class Wallet extends \SejoliSA\Model
{
    static protected $value        = 0;
    static protected $type         = 'out';
    static protected $refundable   = false;
    static protected $label        = NULL;
    static protected $valid_point  = true;
    static protected $table        = 'sejolisa_wallet';

    /**
     * Reset all property values
     * @since   1.0.0
     */
    static public function reset() {

        self::$value        = 0;
        self::$type         = 'out';
        self::$refundable   = false;
        self::$valid_point  = true;
        self::$label        = NULL;

        parent::reset();

        return new static;
    }

    /**
     * Set wallet value
     * @since   1.0.0
     * @param   integer     $value
     */
    static public function set_value($value) {

        self::$value = absint($value);

        return new static;
    }

    /**
     * Set point type value
     * @since   1.0.0
     * @param   string  $type
     */
    static public function set_type($type) {

        self::$type = (!in_array($type, array('in', 'out'))) ? 'out' : $type;

        return new static;
    }

    /**
     * Set refundable
     * @since   1.0.0
     * @param   boolean    $refundable
     */
    static public function set_refundable($refundable) {

        self::$refundable = boolval($refundable);

        return new static;
    }

    /**
     * Set wallet label
     * @since   1.0.0
     * @param   string     $label
     */
    static public function set_label($label) {

        self::$label = esc_html($label);

        return new static;
    }

    /**
     * Set if point is valid or not
     * @since   1.0.0
     * @param   boolean $valid_point
     */
    static public function set_valid_point($valid_point) {

        self::$valid_point = boolval($valid_point);

        return new static;
    }

    /**
     * Set filter args
     * @since  1.0.0
     * @param  array $args
     * @return array
     */
    static public function set_filter_args($args) {

        $filter = [];

        if(is_array($args) && 0 < count($args)) :

            foreach($args as $_filter) :

                if(
                    !empty($_filter['val']) &&
                    'sejoli-nonce' != $_filter['name'] &&
                    '_wp_http_referer' != $_filter['name']
                ) :

                    if('ID' == $_filter['name']) :
                        $filter[$_filter['name']] = explode(',', $_filter['val']);
                    else :
                        $filter[$_filter['name']] = $_filter['val'];
                    endif;

                endif;

            endforeach;

        endif;

        return $filter;

    }

    /**
     * Set table args
     * @since   1.0.0
     * @param   array $args
     * @return  array
     */
    static public function set_table_args(array $args) {

        $filter = NULL;
        $args   = wp_parse_args($args,[
            'start'  => 0,
            'length' => 10,
            'draw'   => 1,
            'filter' => [],
            'search' => []
        ]);

        $search = [[
            'name' => 'users',
            'val'  => isset($args['search']['value']) ? $args['search']['value'] : NULL,
        ]];

        $order = array(
            0 => [
            'column'=> 'ID',
            'sort'  => 'desc'
        ]);

        $columns = [];

        if(isset($args['columns'])) :
            foreach( $args['columns'] as $i => $_column ) :
                $columns[$i] = $_column['data'];
            endforeach;
        else :

            $columns['ID'] = 'desc';

        endif;

        if ( isset( $args['order'] ) && 0 < count( $args['order'] ) ) :
            $i = 0;
            foreach( $args['order'] as $_order ) :
                $order[$i]['sort']   = $_order['dir'];
                $order[$i]['column'] = $columns[$_order['column']];
                $i++;
            endforeach;
        endif;

        $filter = self::set_filter_args($args['filter']);

        return [
            'start'  => $args['start'],
            'length' => $args['length'],
            'draw'   => $args['draw'],
            'search' => $search,
            'order'  => $order,
            'filter' => $filter
        ];
        
    }

    /**
     * Validate property values based on action
     * @since   1.0.0
     * @since   1.1.0   ignore $order_id and $product validation if self::$label is manual
     */
    static protected function validate() {

        if(in_array(self::$action, array('add', 'reduce', 'request', 'manual-input'))) :

            if(empty(self::$value)) :
                self::set_valid(false);
                self::set_message( __('Value tidak boleh kosong', 'sejoli'));
            endif;

            if(empty(self::$type)) :
                self::set_valid(false);
                self::set_message( __('Tipe poin tidak valid', 'sejoli'));
            endif;

            if(empty(self::$label)) :
                self::set_valid(false);
                self::set_message( __('Label point tidak boleh kosong', 'sejoli'));
            endif;

        endif;

        if(in_array(self::$action, array('add', 'reduce', 'get-single', 'manual-input'))) :

            if(!is_a(self::$user, 'WP_User')) :
                self::set_valid(false);
                self::set_message( __('User tidak valid', 'sejoli'));
            endif;

        endif;

        if(in_array(self::$action, array('add', 'update-valid-point', 'get-single', 'reduce'))) :

            if(empty(self::$order_id)) :
                self::set_valid(false);
                self::set_message( __('Order ID tidak boleh kosong', 'sejoli'));
            endif;

        endif;

        if(in_array(self::$action, array('add'))) :

            if( !is_a(self::$product, 'WP_Post') || 'sejoli-product' !== self::$product->post_type) :
                self::set_valid(false);
                self::set_message( __('Produk tidak valid', 'sejoli'));
            endif;

        endif;

        if(in_array(self::$action, array('update-request-fund'))) :

            if(empty(self::$id)) :
                self::set_valid(false);
                self::set_message( __('ID tidak boleh kosong', 'sejoli'));
            endif;

        endif;

    }

    /**
     * Check existing cashback by order_id and user_id
     * @since   1.0.0
     * @return  boolean
     */
    static protected function check_existing_cashback() {

        global $wpdb;

        $table_name = $wpdb->prefix . self::$table;

        $query = $wpdb->prepare(
            "SELECT * FROM $table_name WHERE order_id = %d AND user_id = %d AND type = %s LIMIT 1",
            self::$order_id,
            self::$user->ID,
            self::$type
        );

        $data = $wpdb->get_row($query);

        return boolval($data);

    }

    /**
     * Add cashback
     * @since   1.0.0
     */
    static public function add_cashback() {

        self::set_action('add');
        self::validate();

        if (false !== self::$valid) :

            self::$type  = 'in';

            if (false === self::check_existing_cashback()) :

                global $wpdb;

                $table_name = $wpdb->prefix . self::$table;

                $wallet = [
                    'created_at'   => current_time('mysql'),
                    'order_id'     => self::$order_id,
                    'product_id'   => self::$product->ID,
                    'user_id'      => self::$user->ID,
                    'value'        => self::$value,
                    'type'         => 'in',
                    'label'        => self::$label,
                    'refundable'   => self::$refundable,
                    'valid_point'  => self::$valid_point,
                    'meta_data'    => serialize(self::$meta_data),
                ];

                $wpdb->insert(
                    $table_name,  
                    $wallet,       
                    [
                        '%s', '%d', '%d', '%d', '%d', '%s', '%s', '%d', '%d', '%s'
                    ]
                );

                $wallet['ID'] = $wpdb->insert_id;

                self::set_valid(true);
                self::set_respond('wallet', $wallet);

            else :

                self::set_valid(false);
                self::set_message(
                    sprintf(
                        __('Add cashback value for order %s and user %s already exists', 'sejoli-wallet'),
                        self::$order_id,
                        self::$user->ID
                    )
                );

            endif;

        endif;

        return new static;

    }

    /**
     * Add data manually by access confirmed user
     * @since   1.1.0
     */
    static public function manual_input() {

        self::set_action('manual-input');
        self::validate();

        if (false !== self::$valid) :

            global $wpdb;

            $table_name = $wpdb->prefix . self::$table;

            $wallet = [
                'created_at'   => current_time('mysql'),
                'order_id'     => 0,
                'product_id'   => 0,
                'user_id'      => self::$user->ID,
                'value'        => self::$value,
                'type'         => self::$type,
                'label'        => self::$label,
                'refundable'   => self::$refundable,
                'valid_point'  => self::$valid_point,
                'meta_data'    => serialize(self::$meta_data),
            ];

            $wpdb->insert(
                $table_name,  
                $wallet,       
                [
                    '%s', '%d', '%d', '%d', '%d', '%s', '%s', '%d', '%d', '%s'
                ]
            );

            $wallet['ID'] = $wpdb->insert_id;

            self::set_valid(true);
            self::set_respond('wallet', $wallet);

        endif;

        return new static;

    }

    /**
     * Get single user wallet cashback from an order
     * @since   1.0.0
     */
    static public function get_single_cashback() {

        self::set_action('get-single');
        self::validate();

        if (false !== self::$valid) :

            global $wpdb;

            $table_name = $wpdb->prefix . self::$table;

            $query = $wpdb->prepare(
                "SELECT * FROM $table_name WHERE order_id = %d AND user_id = %d AND valid_point = 1 LIMIT 1",
                self::$order_id,
                self::$user->ID
            );

            $wallet = $wpdb->get_row($query);

            if ($wallet) :

                self::set_valid(true);
                self::set_respond('wallet', $wallet);

            else :

                $query = $wpdb->prepare(
                    "SELECT * FROM $wpdb->prefix" . self::$table . " WHERE order_id = %d AND user_id = %d LIMIT 1",
                    self::$order_id,
                    self::$user->ID
                );

                $wallet = $wpdb->get_row($query);

                if ($wallet) :

                    self::set_valid(true);
                    self::set_respond('wallet', $wallet);

                else :

                    self::set_valid(false);

                endif;

            endif;

        endif;

        return new static;

    }

    /**
     * Get single point detail
     * @since   1.0.0
     */
    static public function get_point_detail() {

        self::set_action('get-detail');
        self::validate();

        if (false !== self::$valid) :

            global $wpdb;

            $table_name = $wpdb->prefix . self::$table;

            $query = $wpdb->prepare(
                "SELECT * FROM $table_name WHERE ID = %d LIMIT 1",
                self::$id
            );

            $point = $wpdb->get_row($query);

            if ($point) :

                self::set_valid(true);
                self::set_respond('point', $point);

            else :

                self::set_valid(false);

            endif;

        endif;

        return new static;

    }

    /**
     * Get points by filter
     * @since   1.0.0
     * @return  void
     */
    static public function get() {

        global $wpdb;

        $table_name = $wpdb->prefix . self::$table;

        $query = "
            SELECT wallet.*, user.display_name, user.user_email
            FROM {$table_name} AS wallet
            JOIN {$wpdb->users} AS user ON user.ID = wallet.user_id
        ";

        $query = self::set_filter_query($query);

        $query = self::set_orderby_query($query, null);

        $recordsTotal = $wpdb->get_var("SELECT COUNT(*) FROM {$table_name} AS wallet");

        $query = self::set_length_query($query);

        $wallet = $wpdb->get_results($query);

        if ($wallet) :
            self::set_respond('valid', true);
            self::set_respond('wallet', $wallet);
            self::set_respond('recordsTotal', $recordsTotal);
            self::set_respond('recordsFiltered', $recordsTotal);
        else:
            self::set_respond('valid', false);
            self::set_respond('wallet', []);
            self::set_respond('recordsTotal', 0);
            self::set_respond('recordsFiltered', 0);
        endif;

        return new static;

    }

    /**
     * Get available all user wallet
     * @since   1.0.0
     */
    static public function get_all_user_wallet() {

        global $wpdb;

        $table_name = $wpdb->prefix . self::$table;

        $query = "
            SELECT 
                wallet.user_id,
                user.display_name,
                user.user_email,
                SUM(CASE WHEN type = 'in' AND refundable = 1 THEN value ELSE 0 END) AS cash_value,
                SUM(CASE WHEN type = 'in' AND refundable = 0 THEN value ELSE 0 END) AS point_value,
                SUM(CASE WHEN type = 'out' THEN value ELSE 0 END) AS used_value,
                SUM(
                    CASE
                        WHEN type = 'in' AND refundable = 1 THEN value
                        WHEN type = 'in' AND refundable = 0 THEN 0
                        ELSE -value
                    END
                ) AS available_cash,
                SUM(CASE WHEN type = 'in' THEN value ELSE -value END) AS available_total
            FROM {$table_name} AS wallet
            INNER JOIN {$wpdb->users} AS user ON user.ID = wallet.user_id
            WHERE wallet.valid_point = 1
        ";

        $query = self::set_filter_query($query);

        $query .= "GROUP BY wallet.user_id 
            ORDER BY available_total DESC";

        $result = $wpdb->get_results($query);

        if ($result) :

            self::set_valid(true);
            self::set_respond('wallet', $result);

        else :

            self::set_valid(false);
            self::set_message(__('No point data', 'sejoli-wallet'));

        endif;

        return new static;

    }

    /**
     * Get user wallet data
     * @since   1.0.0
     */
    static public function get_user_wallet() {

        global $wpdb;

        $table_name = $wpdb->prefix . self::$table;

        $query = "
            SELECT
                wallet.user_id,
                SUM(CASE WHEN wallet.type = 'in' AND wallet.refundable = 1 THEN wallet.value ELSE 0 END) AS cash_value,
                SUM(CASE WHEN wallet.type = 'in' AND wallet.refundable = 0 THEN wallet.value ELSE 0 END) AS point_value,
                SUM(CASE WHEN wallet.type = 'out' THEN wallet.value ELSE 0 END) AS used_value,
                SUM(
                    CASE
                        WHEN wallet.type = 'in' AND wallet.refundable = 1 THEN wallet.value
                        WHEN wallet.type = 'in' AND wallet.refundable = 0 THEN 0
                        ELSE -wallet.value
                    END
                ) AS available_cash,
                SUM(CASE WHEN wallet.type = 'in' THEN wallet.value ELSE -wallet.value END) AS available_total
            FROM {$table_name} AS wallet
            WHERE wallet.valid_point = 1
            AND wallet.user_id = %d
            GROUP BY wallet.user_id
            LIMIT 1
        ";

        $query = $wpdb->prepare($query, self::$user_id);

        $query = $wpdb->get_row($query);

        if ($query) :
            self::set_valid(true);
            self::set_respond('wallet', $query);
        else :
            self::set_valid(false);
            self::set_message(sprintf(__('No wallet data for user %s', 'sejoli-wallet'), self::$user_id));
        endif;

        return new static;

    }

    /**
     * Reduce wallet
     * @since   1.0.0
     */
    static public function use_wallet() {

        self::set_action('reduce');
        self::validate();

        if (false !== self::$valid) :

            global $wpdb;

            $table_name = $wpdb->prefix . self::$table;

            $wallet = [
                'created_at'   => current_time('mysql'),
                'order_id'     => self::$order_id,
                'product_id'   => 0,
                'user_id'      => self::$user->ID,
                'value'        => self::$value,
                'label'        => self::$label,
                'type'         => 'out',
                'refundable'   => false,
                'meta_data'    => serialize(self::$meta_data),
                'valid_point'  => true
            ];

            $wpdb->insert(
                $table_name, 
                $wallet,                     
                [
                    '%s', '%d', '%d', '%d', '%f', '%s', '%s', '%d', '%s', '%d'
                ]
            );

            $wallet['ID'] = $wpdb->insert_id;

            self::set_valid(true);
            self::set_respond('wallet', $wallet);

        endif;

        return new static;

    }

    /**
     * Update valid point
     * @since
     */
    static public function update_valid_point() {

        self::set_action('update-valid-point');
        self::validate();

        if (false !== self::$valid) :

            global $wpdb;

            $table_name = $wpdb->prefix . self::$table;

            $data = [
                'valid_point' => self::$valid_point
            ];

            $where = [
                'order_id' => self::$order_id
            ];

            $updated = $wpdb->update(
                $table_name, 
                $data,                        
                $where,                       
                ['%d'],                       
                ['%d']                        
            );

            if ($updated !== false) :
                self::set_valid(true);
            else :
                self::set_valid(false);
            endif;

        endif;

        return new static;

    }

    /**
     * Request fund
     * @since   1.0.0
     */
    static public function request_fund() {

        self::set_action('request');
        self::validate();

        if (false !== self::$valid) :

            global $wpdb;

            $table_name = $wpdb->prefix . self::$table;

            $wallet = [
                'created_at'   => current_time('mysql'),
                'order_id'     => 0,
                'product_id'   => 0,
                'user_id'      => self::$user->ID,
                'value'        => self::$value,
                'label'        => self::$label,
                'type'         => 'out',
                'refundable'   => false,
                'meta_data'    => serialize(self::$meta_data),
                'valid_point'  => true
            ];

            $wpdb->insert(
                $table_name, 
                $wallet,                     
                [
                    '%s', '%d', '%d', '%d', '%f', '%s', '%s', '%d', '%s', '%d' 
                ]
            );

            $wallet['ID'] = $wpdb->insert_id;

            self::set_valid(true);
            self::set_respond('wallet', $wallet);

        endif;

        return new static;

    }

    /**
     * Cancel a request fund
     * @since   1.0.0
     */
    static public function update_request_fund() {

        self::set_action('update-request-fund');
        self::validate();

        if (false !== self::$valid) :

            global $wpdb;

            $table_name = $wpdb->prefix . self::$table;

            $data = [
                'valid_point' => self::$valid_point,
                'meta_data'   => serialize(self::$meta_data)
            ];

            $where = [
                'ID'    => self::$id,
                'label' => 'request',
                'type'  => 'out'
            ];

            $updated = $wpdb->update(
                $table_name, 
                $data,                        
                $where,                       
                ['%d', '%s'],                 
                ['%d', '%s', '%s']            
            );

            self::set_valid($updated !== false);

        endif;

        return new static;

    }

}
