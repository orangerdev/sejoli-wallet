<?php

namespace Sejoli_Wallet\Front;

class Member {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

    /**
     * Menu position
     * @since   1.0.0
     * @var     integer
     */
    protected $menu_position = 1;

    /**
     * Registered point member menu list
     * @since   1.0.0
     * @var     array
     */
    protected $member_menu = array();

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

        $this->member_menu = array(
                                'link'    => 'javascript::void(0)',
                                'label'   => __('Saldo Anda', 'sejoli'),
                                'icon'    => 'money bill alternate icon',
                                'class'   => 'item',
                                'submenu' => array(
                                    'point' => array(
                                        'link'    => site_url('member-area/your-wallet'),
                                        'label'   => __('History Transaksi', 'sejoli'),
                                        'icon'    => '',
                                        'class'   => 'item',
                                        'submenu' => array()
                                    ),
                                    'reward' => array(
                                        'link'    => site_url('member-area/request-fund'),
                                        'label'   => __('Pencairan Dana', 'sejoli'),
                                        'icon'    => '',
                                        'class'   => 'item',
                                        'submenu' => array()
                                    )
                                )
                            );
	}

    /**
     * Register member area menu
     * Hooked via filter sejoli/member-area/menu, priority 11
     * @since   1.0.0
     * @param   array  $menu
     * @return  array
     */
    public function register_menu( array $menu ) {

        $menu = array_slice($menu, 0, $this->menu_position, true) +
                $this->member_menu +
                array_slice($menu, $this->menu_position, count($menu) - 1, true);

        return $menu;
    }

    /**
     * Add point menu to menu backend area
     * Hooked via filter sejoli/member-area/backend/menu, priority 1111
     * @since   1.0.0
     * @param   array   $menu
     * @return  array
     */
    public function add_menu_in_backend(array $menu) {

        $point_menu = array(
            'title'  => __('Saldo Anda', 'sejoli'),
            'object' => 'sejoli-user-wallet',
            'url'    => site_url('member-area/your-wallet')
        );

        // Add point menu in selected position
        $menu   =   array_slice($menu, 0, $this->menu_position, true) +
                    array('your-wallet' => $point_menu) +
                    array_slice($menu, $this->menu_position, count($menu) - 1, true);

        return $menu;
    }

    /**
     * Display link list for point member link
     * Hooked via filter sejoli/member-area/menu-link, priority 1
     * @since   1.0.0
     * @param   string  $output
     * @param   object  $object
     * @param   array   $args
     * @param   array   $setup
     * @return  string
     */
    public function display_link_list_in_menu($output, $object, $args, $setup) {

        if('sejoli-user-wallet' === $object->object) :
            // YES IM LAZY
            extract($args);

            ob_start();
            ?>
            <div class="master-menu">
                <a href="javascript:void(0)" class='item'>
                    <i class='money bill alternate icon'></i>
                    <?php echo $object->post_title; ?>
                </a>
                <ul class="menu">
                <?php foreach( $this->member_menu['submenu'] as $submenu ) : ?>
                    <li>
                        <a href="<?php echo $submenu['link']; ?>" class="<?php echo $submenu['class']; ?>">
                        <?php if( !empty( $submenu['icon'] ) ) : ?>
                        <i class="<?php echo $submenu['icon']; ?>"></i>
                        <?php endif; ?>
                        <?php echo $submenu['label']; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
                </ul>
            </div>
            <?php

            $item_output = ob_get_contents();
            ob_end_clean();

            return $item_output;
        endif;

        return $output;
    }

    /**
     * Set template file for point menu template
     * Hooked via sejoli/template-file, priority 111
     * @since   1.0.0
     * @param   string  $file
     * @param   string  $view_request
     */
    public function set_template_file(string $file, string $view_request) {

        if('your-wallet' === $view_request) :

            return SEJOLI_WALLET_DIR . 'template/your-wallet.php';

        elseif('request-fund' === $view_request) :

            return SEJOLI_WALLET_DIR . 'template/request-fund.php';

        endif;

        return $file;
    }
}
