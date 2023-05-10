<?php
require_once(SAVAGE_NOTE_PATH . 'admin/classes/class-sn-api.php');
require_once(SAVAGE_NOTE_PATH . 'admin/classes/class-sn-helpers.php');

class Savage_Note_ListArticlesTable extends WP_List_Table
{
    private $table_data;

    function get_columns()
    {
        $columns = array(
                'cb'            => '<input type="checkbox" />',
                'title' => __('Titre', 'sn-plugin'),
                'lot'          => __('Lot', 'sn-plugin'),
                'category'         => __('Catégorie', 'sn-plugin'),
                'tag'   => __('Thématique', 'sn-plugin'),
                'language'        => __('Langue', 'sn-plugin'),
                'nl'        => __('NextLevel', 'sn-plugin'),
                'site'        => __('Site', 'sn-plugin'),
        );
        return $columns;
    }

    function prepare_items()
    {
        $dir = wp_upload_dir();

        $data = []; 

        isset($_REQUEST['thematique']) && !empty($_REQUEST['thematique']) ? $data['tag'] = absint($_REQUEST['thematique']) : $data['tag'] = '';
        isset($_REQUEST['s']) && !empty($_REQUEST['s']) ? $data['search'] = sanitize_text_field($_REQUEST['s']) : $data['search'] = '';
        isset($_REQUEST['category']) && !empty($_REQUEST['category']) ? $data['category'] = absint($_REQUEST['category']) : $data['category'] = '';
        isset($_REQUEST['site']) && !empty($_REQUEST['site']) ? $data['site'] = sanitize_text_field($_REQUEST['site']) : $data['site'] = '';
        isset($_REQUEST['published']) && !empty($_REQUEST['published']) ? $data['published'] = sanitize_text_field($_REQUEST['published']) : $data['published'] = '';

        $api = new Savage_Note_Api();
        $this->process_bulk_action();

        $this->table_data = $api->get('/my-articles', $data);

        $columns = $this->get_columns();
        $hidden = ( is_array(get_user_meta( get_current_user_id(), 'managesavage-note_page_sn-articlescolumnshidden', true)) ) ? get_user_meta( get_current_user_id(), 'managesavage-note_page_sn-articlescolumnshidden', true) : array();
        
        $sortable = $this->get_sortable_columns();
        $primary  = 'title';
        $this->_column_headers = array($columns, $hidden, $sortable, $primary);
       
        if(!empty($this->table_data)){
            usort($this->table_data, array($this, 'usort_reorder'));
        }

        

        if(!empty($this->table_data)){
            /* pagination */
            $per_page = $this->get_items_per_page('articles_per_page', 10);
            $current_page = $this->get_pagenum();
            $total_items = count($this->table_data);
    
            $this->table_data = array_slice($this->table_data, (($current_page - 1) * $per_page), $per_page);
    
            $this->set_pagination_args(array(
                    'total_items' => $total_items, // total number of items
                    'per_page'    => $per_page, // items to show on a page
                    'total_pages' => ceil( $total_items / $per_page ) // use ceil to round up
            ));
        }
        
        !empty($this->table_data) ? 
        $this->items = $this->table_data :
        $this->items = [];
        
    }

    function column_default($item, $column_name)
    {
        return $item[$column_name];
    }

    function column_lot($item)
    {
        return $item['lot'];
    }

    function column_title($item){
        $actions = array(
            'import'      => sprintf('<a href="javascript:void"  data-id-article="%1$s" data-title="%2$s" class="snimportarticle">%3$s</a>', $item['id_article'], $item['title'], __('Importer', 'savage-note')),
            'import_draft'      => sprintf('<a href="javascript:void"  data-id-article="%1$s" data-title="%2$s" class="snimportarticledraft">%3$s</a>', $item['id_article'], $item['title'], __('Importer en brouillon', 'savage-note')),
            'import_publish'      => sprintf('<a href="javascript:void"  data-id-article="%1$s" data-title="%2$s" class="snimportarticlepublish">%3$s</a>', $item['id_article'], $item['title'], __('Importer et publier', 'savage-note')),
        );

        return sprintf('%1$s %2$s', $item['title'], $this->row_actions($actions));

    }

    function column_category($item)
    {
        return $item['category'];
    }

    function column_tag($item)
    {
        return $item['tag'];
    }

    function column_language($item)
    {
        $src_fr = SAVAGE_NOTE_URL . 'admin/assets/img/france.png';
        $src_en = SAVAGE_NOTE_URL . 'admin/assets/img/anglais.png';
        

        if($item['language'] == 'true'){
            return sprintf('<img src="%1$s"/>', $src_en);
        }else if($item['language'] == 'false'){
            return sprintf('<img src="%1$s"/>', $src_fr);
        }else if($item['lot_language'] == 'true'){
            return sprintf('<img src="%1$s"/>', $src_en);
        }else if($item['lot_language'] == 'false'){
            return sprintf('<img src="%1$s"/>', $src_fr);
        }
    }

    function column_nl($item)
    {
        $src = SAVAGE_NOTE_URL . 'admin/assets/img/nextlevel.png';

        if($item['nl'] == 'true'){
            return sprintf('<img src="%1$s" style="width:31px !important;"/>', $src);
        }else if($item['nl'] == 'false'){
            return '';
        }else if($item['lot_nextlevel'] == 'false'){
            return '';
        }else if($item['lot_nextlevel'] == 'true'){
            return sprintf('<img src="%1$s" style="width:31px !important;"/>', $src);
        }
    }

    function column_site($item)
    {
        $posted = $item['site_from_plugin'] == 'true' || $item['lot_site_from_plugin'] == 'true' ? 'posted' : 'notposted';
        
        if($item['posted_on_url'] == NULL){
            if($item['lot_posted_on_url'] == NULL){
                return ('Non posté');
            }else{
                return sprintf('<a class="%1$s" href="%2$s" target="_blank">%3$s</a>', $posted, $item['lot_posted_on_url'], $item['lot_posted_on_name']);
            }
        }else{
            return sprintf('<a class="%1$s" href="%2$s" target="_blank">%3$s</a>', $posted, $item['posted_on_url'], $item['posted_on_name']);
        }
    }


    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="element[]" value="%s" />',
            $item['id_article']
    );
    }
    
    function get_sortable_columns()
    {
        $sortable_columns = [
            'lot' => ['lot', true],
            'title' => ['title', true],
            'category' => ['category', true],
            'tag' => ['tag', true],
            'language' => ['lot_language', true],
            'nl' => ['lot_nextlevel', true],
            'site' => ['posted_on_name', true],
        ];
        return $sortable_columns;
    }

    function usort_reorder($a, $b)
    {
        
       $orderby = 'lot';
       $order = 'asc';

       if(!empty($_GET['orderby']))
       {
           $orderby = sanitize_text_field( $_GET['orderby'] );
       }

       if(!empty($_GET['order']))
       {
           $order = sanitize_text_field( $_GET['order'] );
       }

       $result = strcmp( $a[$orderby], $b[$orderby] );

       if($order === 'asc')
       {
           return $result;
       }

       return -$result;
    }

    function get_bulk_actions()
    {
            $actions = array(
                    'bulkimportarticles'    => __('Importer', 'savage-note'),
                    'bulkimportarticlesdraft' => __('Importer en brouillon', 'savage-note'),
                    'bulkimportarticlespublish' => __('Importer et publier', 'savage-note'),
            );
            return $actions;
    }

    function process_bulk_action()
    {
        $api = new Savage_Note_Api();
        $helpers = new Savage_Note_Helpers();
        $action = $this->current_action();

        switch ( $action ) {

            case 'bulkimportarticlespublish':
                if(isset($_REQUEST['element']) && !empty($_REQUEST['element'])){
                    $ids = array_map( 'absint', $_REQUEST['element'] );
                    foreach($ids as $id){
                        $article = $api->get('/article/' . $id);
                        $helpers->insert_post($article, 'publish');
                        $this->set_new_site($article, $api);
                    }
                }else{
                    return;
                }
                break;

            case 'bulkimportarticlesdraft':
                if(isset($_REQUEST['element']) && !empty($_REQUEST['element'])){
                    $ids = array_map( 'absint', $_REQUEST['element'] );
                    foreach($ids as $id){
                        $article = $api->get('/article/' . $id);
                        $helpers->insert_post($article, 'draft');
                        $this->set_new_site($article, $api);
                    }
                }else{
                    return;
                }
                break;
                

            case 'bulkimportarticles':
                if(isset($_REQUEST['element']) && !empty($_REQUEST['element'])){
                    $ids = array_map( 'absint', $_REQUEST['element'] );
                    foreach($ids as $id){
                        $article = $api->get('/article/' . $id);
                        $helpers->insert_post($article);
                        $this->set_new_site($article, $api);
                    }
                }else{
                    return;
                }
                break;

            default:
                // do nothing or something else
                return;
        }

        return;
    }

    
    function set_new_site($article, $api){

        $args = [
            'site_name' => SAVAGE_NOTE_SITE_NAME,
            'article' => $article,
            'site_url' => SAVAGE_NOTE_SITE_URL
        ];

        $response = $api->post('/article/site', $args);
    }

    function extra_tablenav( $which ) {

        $api = new Savage_Note_Api();

        $tags = $api->get('/tags');
        $categories = $api->get('/categories');
        $sites = $api->get('/sites');


		if ( $which == "top" ) : ?>

        <div class="actions alignleft"> 
            <select name="category" id="filter-category">
                <option value="">Catégorie</option>
                
                <?php

                    foreach($categories as $category){
                        if( isset( $_REQUEST['category'] ) ){
                            $selected = $category['id_category'] == $_REQUEST['category'] ? 'selected' : '';
                        }else{
                            $selected = '';
                        }
                          
                        echo "<option value='" . esc_attr( $category['id_category'] ) . "' " . esc_attr( $selected ) . ">" . esc_html( $category['category'] ) . "</option>";
                    }

                ?>

            </select>
            <input type="submit" value="Filtrer" class="button action" id="category-search" name="category-search">
		</div>
		<div class="actions alignleft">
            <select name="thematique" id="filter-thematique">
                <option value="">Thématique</option>
                
                <?php

                    foreach($tags as $tag){
                        if( isset( $_REQUEST['thematique'] ) ){
                            $selected = $tag['id_tag'] == $_REQUEST['thematique'] ? 'selected' : '';
                        }else{
                            $selected = '';
                        }
                          
                        echo "<option value='" . esc_attr( $tag['id_tag'] ) . "' " . esc_attr( $selected ) . ">" . esc_html( $tag['tag'] ) . "</option>";
                    }

                ?>

            </select>
            <input type="submit" value="Filtrer" class="button action" id="thematique-search" name="thematique-search">
		</div>

        <div class="actions alignleft">
            <select name="site" id="filter-site">
                <option value="">Site</option>
                
                <?php

                    foreach($sites as $site){
                        if( isset( $_REQUEST['site'] ) ){
                            $selected = $site == $_REQUEST['site'] ? 'selected' : '';
                        }else{
                            $selected = '';
                        }
                          
                        echo "<option value='" . esc_attr( $site ) . "' " . esc_attr( $selected ) . ">" . esc_html( $site ) . "</option>";
                    }

                ?>

            </select>
            <input type="submit" value="Filtrer" class="button action" id="site-search" name="site-search">
		</div>

        <div class="actions alignleft">
            <select name="published" id="filter-published">
                <?php
                    if( isset( $_REQUEST['published'] ) ){
                        $selected = $_REQUEST['published'] == 'true' ? 'selected' : '';
                        $selected_false = $_REQUEST['published'] == 'false' ? 'selected' : '';
                    }else{
                        $selected = '';
                        $selected_false = '';
                    }
                    
                ?>

                <option value="">Publié ?</option>
                <option value="true" <?php echo esc_attr( $selected ) ?>>Oui</option>
                <option value="false" <?php echo esc_attr( $selected_false ) ?>>Non</option>
                
            </select>
            <input type="submit" value="Filtrer" class="button action" id="published-search" name="published-search">
		</div>
       

		<?php endif;
	}

    function no_items() {
        $url = admin_url("admin.php?page=sn-purchase");
        _e( "Pas encore d'articles ? <a href='$url'>Venez en acheter</a>" );
    }

   

}



