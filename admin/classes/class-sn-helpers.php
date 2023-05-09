<?php

class SnHelpers{

    protected static $instance;

    public static function get_instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function generate_unique_slug($slug, $post_type = 'post') {
        global $wpdb;

        $instance = self::get_instance();
    
        if ($instance->verify_unique_slug($slug, $post_type)) {
            return $slug;
        }
    
        $new_slug = '';
        $i = 1;
        do {
            $new_slug = $slug . '-' . $i;
            $i++;
        } while (!$instance->verify_unique_slug($new_slug, $post_type));
    
        return $new_slug;
    }
    
    public function verify_unique_slug($slug, $post_type = 'post') {
        global $wpdb;
    
        $sql = "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_name = %s AND post_type = %s";
    
        $stmt = $wpdb->prepare($sql, $slug, $post_type);
    
        $count = $wpdb->get_var($stmt);
    
        if ($count > 0) {
            return false; 
        } else {
            return true; 
        }
    }

    public function insert_post($article, $data = ''){
        $options = get_option('sn_options');

        empty($data) ? $status = $options['status'] : $status = $data;

        $doc = new DOMDocument();

        $doc->loadHTML( $article['content'] );

        $xml = simplexml_import_dom($doc);

        $images = $xml->xpath('//img');


        if(count($images) !== 0){
            for($i = 0; $i < count($images); $i++){

                $image = file_get_contents($images[$i]['src']);
                if(str_contains($images[$i]['src'], 'app.savage-note') ){

                    $filetype = wp_check_filetype(basename($images[$i]['src']), null);

                }else{
                    $filetype = [];
                    $f = getimagesize($images[$i]['src']);
                    $filetype['type'] = $f['mime'];
                }
                
                $dir = wp_upload_dir();
                $path = $dir['path'] . '/' . $article['slug'] . '-' . $i . '.' . basename($filetype['type']);
                wp_mkdir_p($dir['path']);
                file_put_contents($path, $image);


                $new_image = array(
                    'post_mime_type' => $filetype['type'],
                    'post_title' => $article['slug'] . '-' . $i,
                    'post_content' => '',
                    'post_status' => 'inherit',
                    'post_date' => date('Y-m-d H:i:s'),
                );
                
                $id_image = wp_insert_attachment($new_image, $path);

                $attach_data = wp_generate_attachment_metadata($id_image, $path);

                wp_update_attachment_metadata($id_image, $attach_data);
                
                $new_name = glob('../wp-content/uploads' . $dir['subdir'] . '/*' . $article['slug'] . '-' . $i . '*1024*');

                if(!empty($new_name[0])){
                    
                    $pos = strpos($new_name[0], $article['slug']);
                    $size = getimagesize($new_name[0]);
                    $new_name = substr_replace($new_name[0], '', 0, $pos);
                    $article['content'] = str_replace($images[$i]['src'], $dir['url'] . '/' . $new_name . '" ' . $size[3] . 'class="wp-image-' . $id_image .' size-large', $article['content']);
                }else{
                    
                    $article['content'] = str_replace($images[$i]['src'], $dir['url'] . '/' . $new_image['post_title'] . '.' . basename($filetype['type']) , $article['content'] );
                    
                }
                                
            }

        }

        $doc = new DOMDocument();

        $doc->loadHTML(mb_convert_encoding($article['content'], 'HTML-ENTITIES', 'UTF-8'));

        // Recherchez tous les éléments 'p' qui contiennent un élément 'iframe'
        $paragraphs = $doc->getElementsByTagName('p');
        foreach ($paragraphs as $paragraph) {
            $iframes = $paragraph->getElementsByTagName('iframe');
            if ($iframes->length > 0) {
                // Ajoutez style="text-align: center;" à l'élément 'p'
                $paragraph->setAttribute('style', 'text-align: center;');
            }
        }

        $article['content'] = $doc->saveHTML( $doc->getElementsByTagName('body')->item(0) );

        $article['content'] = str_replace('<body>', '', $article['content']);
        $article['content'] = str_replace('</body>', '', $article['content']);


        $new_post = [
            'post_title' => $article['title'],
            'post_name' => $this->generate_unique_slug($article['slug']),
            'post_content' => $article['content'],
            'post_status' => $status,
            'post_date' => date('Y-m-d H:i:s'),
            'post_author' => $options['author'],
            'post_type' => $options['post_type'],
        ];

        if(!empty($options['category']) && $options['category'] !== 0){

            $new_post['post_category'] = [$options['category']];

        }

        $id_post = wp_insert_post( $new_post );

        if(!empty($options['tag']) && $options['tag'] !== 0){

            $tag = get_term_by('id', $options['tag'], 'post_tag');
            wp_set_post_tags( $id_post, $tag->name, true );

        }
       
        if(!empty($article['thumbnail'])){
            $image = file_get_contents($article['thumbnail']);
            $filetype = wp_check_filetype(basename($article['thumbnail']), null);
            $dir = wp_upload_dir();
            $path = $dir['path'] . '/' . basename($article['thumbnail']);
            
            wp_mkdir_p($dir['path']);
    
            file_put_contents($path, $image);

            $new_image = array(
                'post_mime_type' => $filetype['type'],
                'post_title' => basename($article['thumbnail']),
                'post_content' => '',
                'post_status' => 'inherit',
                'post_date' => date('Y-m-d H:i:s'),
            );

            $id_image = wp_insert_attachment($new_image, $path);

            $attach_data = wp_generate_attachment_metadata($id_image, $path);
            wp_update_attachment_metadata($id_image, $attach_data);
            set_post_thumbnail($id_post, $id_image);

        }

        if(is_plugin_active('wordpress-seo/wp-seo.php')){ // Yoast
            add_post_meta( $id_post, '_yoast_wpseo_title', $article['meta_title'] );
            add_post_meta( $id_post, '_yoast_wpseo_metadesc', $article['meta_description'] );
        }else if(is_plugin_active('wp-seopress/seopress.php')){ // Seopress
            add_post_meta( $id_post, '_seopress_titles_title', $article['meta_title'] );
            add_post_meta( $id_post, '_seopress_titles_desc', $article['meta_description'] );
        }else if(is_plugin_active('seo-by-rank-math/rank-math.php')){ // Rankmath
            add_post_meta( $id_post, 'rank_math_title', $article['meta_title'] );
            add_post_meta( $id_post, 'rank_math_description', $article['meta_description'] );
        }
    }
}