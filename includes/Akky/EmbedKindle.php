<?php

class Akky_EmbedKindle
{
    const CLASS_NAME = __CLASS__;
    const PLUGIN_KEY = 'akky_embed_kindle';
    const DB_OPTION_KEY = self::PLUGIN_KEY;
    const DISPLAY_NAME = 'Embed Kindle';

    public function __construct()
    {
        $dummy = __( 'Embed Kindle', self::PLUGIN_KEY );
        $dummy = __( 'Embed Kindle Setting', self::PLUGIN_KEY );
        $this->hook();
    }

    /**
     * set hooks on WordPress
     */
    public function hook()
    {
        add_action(
            'admin_init',
            array( self::CLASS_NAME, 'initializeI18n' )
        );
        add_shortcode(
            'embed_kindle',
            array( &$this, 'shortCode')
         );
        add_action(
            'wp_print_scripts',
            array( &$this, 'addScripts')
        );
        if ( is_admin() ) {
            add_action(
                'admin_menu',
                array( &$this, 'registerMenu')
            );
            add_action(
                'admin_init',
                array( &$this, 'registerSettings')
            );
        }
        register_uninstall_hook(
            self::PLUGIN_KEY ,
            array( 'self', 'uninstall')
        );
    }

    public static function initializeI18n()
    {
        load_plugin_textdomain(
            self::PLUGIN_KEY,
            false,
            dirname(dirname(dirname( plugin_basename( __FILE__ ) ))) . '/languages/'
        );
    }

    public function shortCode( $params )
    {
        static $readerId = 0; $readerId++;
        if ( !array_key_exists('asin', $params) ) {
            return false;
        }
        $asin = $params['asin'];
        // 'plausible' ASIN
        //  http://stackoverflow.com/questions/2123131/determine-if-10-digit-string-is-valid-amazon-asin
        if ( !preg_match( "/^B\d{3}\w{6}|\d{9}(X|\d)$/", $asin ) ) {
            return false;
        }

        $affiliateId = 'akimotojp-22';
        $db_options = get_option( self::DB_OPTION_KEY );
        if ( array_key_exists('affiliate_id', $db_options) ) {
            $affiliateId = $db_options['affiliate_id'];
        }
        if ( array_key_exists('id', $params) ) {
            $affiliateId = $params['id'];
        }

        $embed_size_w = get_option( 'embed_size_w' );
        $width = 600;
        if ($embed_size_w > 0) {
            $width = $embed_size_w;
        }
        $embed_size_h = get_option( 'embed_size_h' );
        $height = 800;
        if ($embed_size_h > 0) {
            $height = $embed_size_h;
        }

        $html = <<<END_OF_HTML
<div id='kindleReaderDiv$readerId'></div>
<script type="text/javascript">
KindleReader.LoadSample({
   containerID: 'kindleReaderDiv$readerId', width: '$width', height: '$height',
   asin: '$asin', assoctag: '$affiliateId'});
</script>
END_OF_HTML;

        $html .= '<div class="embed-kindle-warning" style="color: grey">(' . __('Not displayed if no sample available in your country', self::PLUGIN_KEY) . ')</div>';

        return $html;
    }

    public static function uninstall()
    {
        delete_option(self::DB_OPTION_KEY);
    }

    public function addScripts()
    {
        wp_deregister_script( 'KindleReader' );
        wp_register_script( 'KindleReader', 'http://kindleweb.s3.amazonaws.com/app/KindleReader-min.js');
        wp_enqueue_script( 'KindleReader' );
    }

    public function registerMenu()
    {
        add_options_page(
            __( self::DISPLAY_NAME . ' Setting', self::PLUGIN_KEY ),
            __( self::DISPLAY_NAME, self::PLUGIN_KEY ),
            'administrator',
            self::PLUGIN_KEY,
            array( &$this, 'callbackRenderForm')
        );
    }

    public function callbackRenderForm()
    {
        $unique_name = self::PLUGIN_KEY;
        $save_i18n = self::PLUGIN_KEY;

        echo '<div class="wrap">';
        echo '<div class="icon32" id="icon-options-general"></div>';
        echo '<h2>' . __( self::DISPLAY_NAME . ' Setting', self::PLUGIN_KEY ), '</h2>';
        echo '<div class="' . self::PLUGIN_KEY . '">';
        settings_errors();
        echo '<form action="options.php" method="post">';
        settings_fields( self::PLUGIN_KEY );
        do_settings_sections( self::PLUGIN_KEY );
        submit_button();
        echo '</form>';
        echo '</div>';
        echo '</div>';
    }

    public function registerSettings()
    {
        register_setting(
            self::PLUGIN_KEY,
            self::DB_OPTION_KEY,
            array( &$this, 'validate')
        );
        $this->registerSectionAffiliateId();
    }

    protected function registerSectionAffiliateId()
    {
        $sectionName = 'embed_kindle_affiliate_id';

        add_settings_section(
            $sectionName,
            __( 'Amazon Affiliate ID', self::PLUGIN_KEY ),
            array( &$this, 'sectionAffiliateId' ),
            self::PLUGIN_KEY
        );

        add_settings_field(
            'affiliate_id',
            __( 'Your ID', self::PLUGIN_KEY ),
            array( &$this, 'fieldAffiliateId' ),
            self::PLUGIN_KEY,
            $sectionName
        );
    }

    public function sectionAffiliateId()
    {
        echo '<p>' . __( 'You may set your Amazon affiliate ID', self::PLUGIN_KEY ) . '</p>';
    }

    public function fieldAffiliateId()
    {
        $options = get_option(self::DB_OPTION_KEY);
        echo '<input name="' . self::DB_OPTION_KEY . '[affiliate_id]"' . ' id="affiliate_id" type="text"' . ' size="64" maxlength="64"' . ' value="' . $options['affiliate_id'] . '" />';
    }

    public function validate( $input )
    {
        return $input;
    }

}
