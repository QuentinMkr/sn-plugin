<?php

class Savage_Note_AdminMenu
{
    protected static $instance;

    public function __construct()
    {

        add_action('admin_menu', array($this, 'admin_menu'));
        add_filter('set-screen-option', [$this, 'table_set_option'], 11, 3);

    }

    public static function get_instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function admin_menu()
    {
        global $sample_page_lots;
        global $sample_page_articles;
        global $sample_page_purchase;

        add_menu_page(
            __('Savage Note', 'savage-note'),
            __('Savage Note', 'savage-note'),
            'manage_options',
            'savage-note',
            [$this, 'admin_dashboard'],
            SAVAGE_NOTE_URL . 'admin/assets/img/savage-note.png',
            4
        );
        add_submenu_page( 'savage-note', 'API', 'Paramètres', "manage_options", "savage-note", [$this, 'admin_dashboard']);
        $options = get_option( 'sn_options' );

        if(isset($options['api_key']) && !empty($options['api_key'])){
            $sample_page_articles = add_submenu_page("savage-note", "Import par article", "Import par article", "manage_options", "sn-articles", [$this, 'articles']);
            $sample_page_lots = add_submenu_page("savage-note", "Import par lot", "Import par lot", "manage_options", "sn-lots", [$this, 'lots']);
            $sample_page_purchase= add_submenu_page("savage-note", "Acheter des lots", "Acheter des lots", "manage_options", "sn-purchase", [$this, 'purchase']);
            add_action("load-$sample_page_articles", [$this, "sample_screen_options_articles"]);
            add_action("load-$sample_page_lots", [$this, "sample_screen_options_lots"]);
            add_action("load-$sample_page_purchase", [$this, "sample_screen_options_purchase"]);
        }

    }

    public function admin_dashboard(){
        ?>
        <h2>Paramètres</h2>
        <form action="options.php" method="post">
            <?php
            settings_errors();
            settings_fields('sn_options');
            do_settings_sections('savage-note');
            submit_button('Sauvegarder');
            ?>
        </form>
        <?php
    }

    public function articles(){
        $tableArticles = new Savage_Note_ListArticlesTable();
        $src = SAVAGE_NOTE_URL . 'admin/assets/img/cropped-savage-note-favicon-32x32.png';
        ?>
        <div class="wrap">
            <div class="header-sn-plugin">
                <h2>Savage Note
                    <img src="<?php echo esc_url( $src ); ?>">
                </h2>
            </div>
            
        
                <form method="get" >
                    <input type="hidden" name="page" value="<?php echo esc_attr( $_REQUEST['page'] ) ?>"/>

                    <?php

                        $tableArticles->prepare_items();

                        $tableArticles->search_box('Recherche', 'search_id');

                        $tableArticles->display(); 

                    ?>
                    
                </form>

        </div>
        <?php
    }

    public function lots(){
        $table = new Savage_Note_ListTable();
        $src = SAVAGE_NOTE_URL . 'admin/assets/img/cropped-savage-note-favicon-32x32.png';

        ?>
        <div class="wrap">
            <div class="header-sn-plugin">
                <h2>Savage Note
                    <img src="<?php echo esc_url( $src ); ?>">
                </h2>
            </div>
            
        
            <form method="get">

                <input type="hidden" name="page" value="<?php echo esc_attr( $_REQUEST['page'] ) ?>"/>

                <?php

                    $table->prepare_items();

                    $table->search_box('Recherche', 'search_id');

                    $table->display(); 

                ?>

            </form>

            
        </div>
        <?php
    }

    public function purchase(){
        $tablePurchase = new Savage_Note_ListPurchaseTable();
        $src = SAVAGE_NOTE_URL . 'admin/assets/img/cropped-savage-note-favicon-32x32.png';

        $api = new Savage_Note_Api();
        $credits = $api->get('/credits');
        ?>
        <div class="wrap">
            <div class="header-sn-plugin">
                <h2>Savage Note
                    <img src="<?php echo esc_url( $src ); ?>">
                </h2>
            </div>
            <div style="font-size:0.9rem">Mes crédits : 
            <?php 
                if($credits !== NULL ){
                    echo "<span class='sn-credits'>" . esc_html($credits) . " crédit(s)</span>";
                }else{
                    echo "<span class='sn-credits'>0 crédit</span>";
                }
                
                
            ?> 
                <a href="https://www.savage-note.com/credits/" target="_blank" class="sn-purchase-link">
                    <span class="dashicons dashicons-insert dashicons-insert-sn"></span>
                </a>
            </div> 
            <form method="get">
                <input type="hidden" name="page" value="<?php echo esc_attr( $_REQUEST['page'] ) ?>"/>

                <?php

                    $tablePurchase->prepare_items();

                    $tablePurchase->search_box('Recherche', 'search_id');

                    $tablePurchase->display(); 

                ?>

            </form>

            
        </div>
        <?php
    }

    function sample_screen_options_lots() {
 
        global $sample_page_lots;
        global $table_lots;
     
        $screen_lots = get_current_screen();
     
        if(!is_object($screen_lots) || $screen_lots->id != $sample_page_lots)
            return;
     
        $args = array(
            'label' => __('Lots par page', 'savage-note'),
            'default' => 10,
            'option' => 'lots_par_page'
        );
        add_screen_option( 'per_page', $args );
    
        $table_lots = new Savage_Note_ListTable();
    
    }

    function sample_screen_options_articles() {
 
        global $sample_page_articles;
        global $table;
     
        $screen = get_current_screen();
     
        if(!is_object($screen) || $screen->id != $sample_page_articles)
            return;
     
        $args = array(
            'label' => __('Articles par page', 'savage-note'),
            'default' => 10,
            'option' => 'articles_per_page'
        );
        add_screen_option( 'per_page', $args );
    
        $table = new Savage_Note_ListArticlesTable();
    
    }

    function sample_screen_options_purchase() {
 
        global $sample_page_purchase;
        global $table_purchase;
     
        $screen = get_current_screen();
     
        if(!is_object($screen) || $screen->id != $sample_page_purchase)
            return;
     
        $args = array(
            'label' => __('Lots par page', 'savage-note'),
            'default' => 10,
            'option' => 'purchase_per_page'
        );
        add_screen_option( 'per_page', $args );
    
        $table_purchase = new Savage_Note_ListPurchaseTable();
    
    }
    
    function table_set_option($status, $option, $value) {
        return $value;
    }

}