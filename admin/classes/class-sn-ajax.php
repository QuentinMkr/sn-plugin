<?php


class Savage_Note_Ajax
{
    protected static $instance;

    private $options;
    private $api;
    private $helpers;

    public function __construct()
    {
        add_action('wp_ajax_snimport', [$this, 'snimport']);
        add_action('wp_ajax_snimportdraft', [$this, 'snimportdraft']);
        add_action('wp_ajax_snimportpublish', [$this, 'snimportpublish']);
        add_action('wp_ajax_snimportarticle', [$this, 'snimportarticle']);
        add_action('wp_ajax_snimportarticledraft', [$this, 'snimportarticledraft']);
        add_action('wp_ajax_snimportarticlepublish', [$this, 'snimportarticlepublish']);
        add_action('wp_ajax_snpurchaselot', [$this, 'snpurchaselot']);
        add_action('wp_ajax_savage_add_scheduled_post', [$this, 'add_scheduled_post']);

        $this->options = get_option('sn_options');

        require_once(SAVAGE_NOTE_PATH . 'admin/classes/class-sn-api.php');
        require_once(SAVAGE_NOTE_PATH . 'admin/classes/class-sn-helpers.php');

        $this->api = new Savage_Note_Api();
        $this->helpers = new Savage_Note_Helpers();
    }

    public static function get_instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    function snimport()
    {
        if(!isset($_POST['id'])){
            wp_send_json_error( ['success' => 0, 'msg' => 'No ID'], 403 );
        }else{
            
            $articles_per_lot = $this->api->get('/lots/' . absint( $_POST['id'] ) );
            foreach($articles_per_lot as $article){
                
                $this->helpers->insert_post($article);
                $this->set_new_site($article, '?lot=true');
            }
            wp_send_json_success(
                ['count' => count($articles_per_lot)]
            );

        }
    }

    function snimportdraft()
    {
        if(!isset($_POST['id'])){
            wp_send_json_error( ['success' => 0, 'msg' => 'No ID'], 403 );
        }else{
            
            $articles_per_lot = $this->api->get('/lots/' . absint( $_POST['id'] ) );
            foreach($articles_per_lot as $article){
                
                $this->helpers->insert_post($article, 'draft');
                $this->set_new_site($article, '?lot=true');
            }
            wp_send_json_success(
                ['count' => count($articles_per_lot)]
            );

        }
    }

    function snimportpublish()
    {
        if(!isset($_POST['id'])){
            wp_send_json_error( ['success' => 0, 'msg' => 'No ID'], 403 );
        }else{
            
            $articles_per_lot = $this->api->get('/lots/' . absint( $_POST['id'] ) );
            foreach($articles_per_lot as $article){
                
                $this->helpers->insert_post($article, 'publish');
                $this->set_new_site($article, '?lot=true');
            }
            wp_send_json_success(
                ['count' => count($articles_per_lot)]
            );

        }
    }

    function snimportarticle()
    {
        if(!isset($_POST['id_article'])){
            wp_send_json_error( ['success' => 0, 'msg' => 'No ID'], 403 );
        }else{
            $article = $this->api->get('/article/' . absint( $_POST['id_article']) ) ;

            $this->helpers->insert_post($article);

            $this->set_new_site($article);
            
            wp_send_json_success(
                ['article' => $article['title']]
            );
        }
    }

    function snimportarticledraft()
    {
        if(!isset($_POST['id_article'])){
            wp_send_json_error( ['success' => 0, 'msg' => 'No ID'], 403 );
        }else{
            $article = $this->api->get('/article/' . absint( $_POST['id_article'] ) );
            
            $this->helpers->insert_post($article, 'draft');
            $this->set_new_site($article);
            
            wp_send_json_success(
                ['article' => $article['title']]
            );
        }
    }

    function snimportarticlepublish()
    {
        if(!isset($_POST['id_article'])){
            wp_send_json_error( ['success' => 0, 'msg' => 'No ID'], 403 );
        }else{
            $article = $this->api->get('/article/' . absint( $_POST['id_article'] ) );
            
            $this->helpers->insert_post($article, 'publish');
            $this->set_new_site($article);
            
            wp_send_json_success(
                ['article' => $article['title']]
            );
        }
    }

    function snpurchaselot(){
        if(!isset($_POST['id_lot'])){
            wp_send_json_success(
                [
                    'msg' => 'Aucun identifiant de lot trouvé',
                    'success' => 0
                ]
            );
        }else if(!isset($_POST['price'])){
            wp_send_json_success(
                [
                    'msg' => 'Erreur dans le prix du lot',
                    'success' => 0
                ]
            );
        }
        else{
            
            $credits = $this->api->get('/credits');
            

                $args = [
                    'credits' => $credits,
                    'id_lot' => absint ( $_POST['id_lot'] ) ,
                    'version' => "2.1.0"
                ];

                $response = $this->api->post('/purchase', $args);

                wp_send_json_success(
                    [
                        'response' => $response,
                        'msg' => $response->msg,
                        'success' => $response->success,
                        'lot' => $response->lot,
                    ]
                );
            
        }
    }

    
    function set_new_site($article, $params = ''){
        $args = [
            'site_name' => SAVAGE_NOTE_SITE_NAME,
            'article' => $article,
            'site_url' => SAVAGE_NOTE_SITE_URL
        ];

        $response = $this->api->post('/article/site' . $params, $args);
    }

    function sn_wpseo_metadesc($desc){
        return $desc;
    }

    public function add_scheduled_post(){

        date_default_timezone_set(wp_timezone_string());


        if( !isset( $_POST['date'] ) || !isset( $_POST['time'] ) || !isset( $_POST['recurence'] ) || !isset( $_POST['element']) ){

            $error = new WP_Error( '001', 'Error in data provided');
            wp_send_json_error( $error );

        }

        $ids = $_POST['element'];

        $date = $_POST['date'];

        $time = $_POST['time'];

        $recurence = $_POST['recurence'];

        foreach($ids as $k => $id){

            // premier élément
            if( $k === array_key_first( $ids ) ){

                $publish_date = $date;

            }else{

                $d1 = new DateTime( $date );
                
                $d1->modify("+{$time} {$recurence}");

                $publish_date = $d1->format('Y-m-d H:i:s');
                $date = $d1->format('Y-m-d');
            }

            $article = $this->api->get('/article/' . absint( $id ) );

            if( date('Y-m-d H:i:s', time()) == $publish_date  ){
                $status = 'publish';
            }else{
                $status = 'future';
            }

            $this->helpers->insert_post( $article, $status, $publish_date);

        }

        wp_send_json_success( 'La planification est en cours, vous pouvez continuer votre navigation pendant ce temps !' );

    }

}
