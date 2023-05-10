<?php
require_once(SAVAGE_NOTE_PATH . 'admin/classes/class-sn-api.php');

class Savage_Note_ListPurchaseTable extends WP_List_Table
{
    private $table_data;

    function get_columns()
    {
        $columns = array(
                'cb'            => '<input type="checkbox" />',
                'title'          => __('Lot', 'sn-plugin'),
                'category'         => __('Catégorie', 'sn-plugin'),
                'tag'   => __('Thématique', 'sn-plugin'),
                'language'        => __('Langue', 'sn-plugin'),
                'nextlevel'        => __('NextLevel', 'sn-plugin'),
                'price'        => __('Prix', 'sn-plugin'),
                'action'        => __('Acheter', 'sn-plugin'),
        );
        return $columns;
    }

    function prepare_items()
    {
        $data = []; 

        isset($_REQUEST['thematique']) && !empty($_REQUEST['thematique']) ? $data['tag'] = absint( $_REQUEST['thematique'] ) : $data['tag'] = '';
        isset($_REQUEST['s']) && !empty($_REQUEST['s']) ? $data['search'] = sanitize_text_field( $_REQUEST['s'] ) : $data['search'] = '';
        isset($_REQUEST['category']) && !empty($_REQUEST['category']) ? $data['category'] = absint( $_REQUEST['category'] ) : $data['category'] = '';

        $api = new Savage_Note_Api();
        $this->process_bulk_action();

       

        $this->table_data = $api->get('/lots/purchase', $data);


        $columns = $this->get_columns();
        $hidden = ( is_array(get_user_meta( get_current_user_id(), 'managesavage-note_page_sn-purchasecolumnshidden', true)) ) ? get_user_meta( get_current_user_id(), 'managesavage-note_page_sn-purchasecolumnshidden', true) : array();
        $sortable = $this->get_sortable_columns();
        $primary  = 'title';
        $this->_column_headers = array($columns, $hidden, $sortable, $primary);

        if(!empty($this->table_data)){
            usort($this->table_data, array($this, 'usort_reorder'));

            /* pagination */
            $per_page = $this->get_items_per_page('purchase_per_page', 10);
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

    function column_title($item)
    {
        return $item['title'];
    }

    function column_category($item)
    {
        return 
            ($item['category']);
    }

    function column_tag($item)
    {
        return 
            ($item['tag']);
    }

    function column_language($item)
    {
        $src_fr = SAVAGE_NOTE_URL . 'admin/assets/img/france.png';
        $src_en = SAVAGE_NOTE_URL . 'admin/assets/img/anglais.png';

        if($item['language'] == 'true'){
            return sprintf('<img src="%1$s"/>', $src_en);
        }else{
            return sprintf('<img src="%1$s"/>', $src_fr);
        }
    }

    function column_nextlevel($item)
    {
        $src = SAVAGE_NOTE_URL . 'admin/assets/img/nextlevel.png';

        if($item['nextlevel'] == 'true'){
            return sprintf('<img src="%1$s" style="width:31px !important;"/>', $src);
        }else{
            return '';
        }
    }

    function column_price($item)
    {
        if($item['price']){
            return sprintf('<span class="sn_credits">%1$s crédits</span>', $item['price']);
        }else{
            return '';
        }
    }

    function column_action($item){
        return sprintf('<span id="purchase_button" class="purchase_button" data-id="%1$s" data-credits="%2$s">%3$s</span>', $item['id_lot'], $item['price'] , __( "Acheter", "sn-plugin" ));
    }


    function column_cb($item)
    {
        return sprintf(
                '<input type="checkbox" name="element[]" value="%s" />',
                $item['id_lot']
        );
    }
    
    function get_sortable_columns()
    {
        $sortable_columns = [
            'title' => ['title', true],
            'category' => ['category', true],
            'tag' => ['tag', true],
            'language' => ['language', true],
            'nextlevel' => ['nextlevel', true],
            'price' => ['price', true],
        ];
        return $sortable_columns;
    }

    function usort_reorder($a, $b)
    {
        $orderby = 'id_lot';
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
            );
            return $actions;
    }

    function process_bulk_action()
    {
        $api = new Savage_Note_Api;
        $action = $this->current_action();

        switch ( $action ) {


            default:
                // do nothing or something else
                return;
        }

    }


    function extra_tablenav( $which ) {

        $api = new Savage_Note_Api();

        $tags = $api->get('/tags');
        $categories = $api->get('/categories');
        $credits = $api->get('/credits');

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

		<?php endif;
	}


    function no_items() {
        _e( "Nous sommes victimes de notre succès" );
    }

}





