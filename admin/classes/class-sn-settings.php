<?php

class SnSettings
{
    protected static $instance;

    public function __construct()
    {
        add_action('admin_init', [$this, 'register_settings']);
        add_filter("plugin_action_links_sn-plugin/sn-plugin.php", [$this, 'settings_link']);

    }

    public static function get_instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    function settings_link($links)
    {
        $url = admin_url("admin.php?page=savage-note");
        $settings_link = "<a href='$url'>Settings</a>";
        array_unshift($links, $settings_link);
        return $links;
    }

    function register_settings()
    {
        register_setting('sn_options', 'sn_options', [$this, 'validate']);

        add_settings_section('global_section', 'Paramètres généraux', [$this, 'section_globals'], 'savage-note');
        add_settings_section('api_section', 'Paramètres API', [$this, 'section_api'], 'savage-note');

        add_settings_field('setting_status', 'Statut de publication par défaut', [$this, 'status'], 'savage-note', 'global_section');
        add_settings_field('setting_post_type', 'Post type par défaut', [$this, 'post_type'], 'savage-note', 'global_section');
        add_settings_field('setting_author', 'Auteur par défaut', [$this, 'author'], 'savage-note', 'global_section');
        add_settings_field('setting_category', 'Catégorie par défaut', [$this, 'category'], 'savage-note', 'global_section');
        add_settings_field('setting_tag', 'Tag par défaut', [$this, 'tag'], 'savage-note', 'global_section');
        add_settings_field('setting_api_key', 'Clé API', [$this, 'api_key'], 'savage-note', 'api_section');
    }

    function validate($input)
    {
        $input['api_key'] = sanitize_text_field($input['api_key']);
        $input['status'] = sanitize_text_field($input['status']);

        return $input;
    }

    function section_globals(){
        echo 'Régler les paramètres globals du plugin :';
    }

    function section_api(){
        echo 'Vous pouvez trouver votre clé API sur <a href="https://www.savage-note.com/mon-compte/cle-api" target="_blank">notre site</a>';
    }

    function status()
    {
        $options = get_option('sn_options');

        $status = get_post_statuses();

        echo "<select name='sn_option_status_select' id='setting_status_select'>
                <option value='' disabled selected>Choisir le statut de publication</option>";
                foreach($status as $k => $s){
                    echo $options['status'] == $k ? 
                    "<option value='" . $k . "' selected>" . $s . "</option>" : "<option value='" . $k . "'>" . $s . "</option>"  ;
                }
        echo "</select>";
        echo "<input name='sn_options[status]' id='setting_status' type='hidden' value='draft' />";


    }

    function post_type(){
        $options = get_option('sn_options');
        $post_types = get_post_types(['public' => true], 'objects');

		$posts = array();
		foreach ($post_types as $post_type) {
			$posts[$post_type->name] = $post_type->labels->singular_name;
		}

        echo "<select name='sn_option_post_type_select' id='setting_post_type_select'>
                <option value='' disabled selected>Choisir le post type</option>";
                foreach($posts as $k => $s){
                    echo $options['post_type'] == $k ? 
                    "<option value='" . $k . "' selected>" . $s . "</option>" : "<option value='" . $k . "'>" . $s . "</option>"  ;
                }
        echo "</select>";
        echo "<input name='sn_options[post_type]' id='setting_post_type' type='hidden' value='post' />";

    }

    function author(){
        $options = get_option('sn_options');

        $author = 0;
        if(isset($options['author'])){
            $author = $options['author'];
        }

        $args = array(
            "name" => "sn_options[author]",
            "selected" => $author,
            'show_option_none' => __( 'Aucun utilisateur', 'sn-plugin' ),
            "option_none_value" => "0"
        );
        wp_dropdown_users($args);
    }

    function category(){
        $options = get_option('sn_options');
        $category = 0;
        if(isset($options['category'])){
            $category = $options['category'];
        }
        $args = array(
            "name" => "sn_options[category]",
            "selected" => $category,
            'hide_empty' => false,
            'show_option_none' => __( 'Aucune catégorie', 'sn-plugin' ),
            "option_none_value" => "0"
        );
        wp_dropdown_categories($args);
    }

    function tag(){
        $options = get_option('sn_options');
        $tag = 0;
        if(isset($options['tag'])){
            $tag = $options['tag'];
        }
        $args = array(
            "name" => "sn_options[tag]",
            "selected" => $tag,
            'hide_empty' => false,
            "taxonomy" => "post_tag",
            'show_option_none' => __( 'Aucun tag', 'sn-plugin' ),
            "option_none_value" => "0"
        );
        wp_dropdown_categories($args);
    }

    function api_key()
    {
        $options = get_option('sn_options');
        echo "<input name='sn_options[api_key]' id='setting_status' type='text' value='" . esc_attr($options['api_key']) . "' />";
    }


}
