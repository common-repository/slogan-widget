<?php
/*
  Plugin Name: slogan-widget
  Plugin URI: http://gnetos.de
  Description: Plugin zum speichern von Zitaten mit der Hilfe von CustomPostTypes
  Version: 2.1.0
  Author: Tobias Gafner
  Author URI: http://gnetos.de
  License: GPL3
  UPDATE Server: ---
  Min Version: 4.0.0
 
  Copyright 2022  Tobias Gafner  (email : support@gnetos.de)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 3 of the License, or
  any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/* Translation start here*/
define("QS_SLOGAN", "Slogan Plugin");
define("QS_SCRIPTURE", "Stelle");
define("QS_TYPE_MONTH", "Monatsspruch");
define("QS_TYPE_YEAR", "Jahresspruch");
define("QS_TYPE", "Spruchtyp");
/* Translation end here*/

/**
 * @return void
 */
function create_qs_slogan_type()
{
    $labelsquote = array(
        'name' => _x('Slogan Plugin', 'taxonomy general name'),
    );
    register_post_type('qs_slogan_type',
        array(
            'labels' => $labelsquote,
            'public' => true,
            'capability_type' => 'post',
            'show_in_nav_menus' => false,
            'exclude_from_search' => false,
            'supports' => array(''),
            'rewrite' => false
        )
    );
}

/**
 * @param $postId
 * @param $taxonomy
 * @return string|void
 */
function qs_slogan_type_get_taxos($postId, $taxonomy)
{
    $terms = wp_get_object_terms($postId, $taxonomy);
    if (!empty($terms)) {
        if (!is_wp_error($terms)) {
            return $terms[0]->name;
        }
    }
}

/**
 * @return void
 */
function create_qs_slogan_type_taxonomy()
{
    function qs_slogan_type_change_columns($cols)
    {
        $cols = array(
            'cb' => '<input type="checkbox" />',
            'slogan' => __(QS_SLOGAN),
            'slogancategory' => __(QS_TYPE),
            'sloganscripture' => __(QS_SCRIPTURE),
            'slogantime' => __("Date")
        );
        return $cols;
    }

    register_taxonomy('slogan',
        array(
            0 => 'qs_slogan_type'
        ),
        array(
            'hierarchical' => false,
            'public' => false,
            'show_ui' => false,
            'label' => __(QS_SLOGAN),
            'query_var' => true,
            'rewrite' => false
        )
    );
    register_taxonomy('sloganscripture',
        array(
            0 => 'qs_slogan_type'
        ), array(
            'hierarchical' => false,
            'public' => false,
            'label' => __(QS_SCRIPTURE),
            'show_ui' => false,
            'query_var' => true,
            'rewrite' => false
        ));
    register_taxonomy('slogancategory',
        array(
            0 => 'qs_slogan_type'
        ),
        array(
            'hierarchical' => false,
            'label' => __(QS_TYPE),
            'show_ui' => false,
            'public' => false,
            'query_var' => true,
            'rewrite' => false,
        )
    );
    register_taxonomy('slogantime',
        array(
            0 => 'qs_slogan_type'),
        array(
            'hierarchical' => false,
            'label' => __("Date"),
            'show_ui' => false,
            'query_var' => true,
            'public' => false,
            'rewrite' => false,
        )
    );
    add_filter("manage_qs_slogan_type_posts_columns", "qs_slogan_type_change_columns");
}


/**
 * @param $column
 * @return void
 */
function qs_slogan_type_custom_columns2($column)
{
    global $post;
    //$wpg_row_actions  = '<div class="row-actions"><span class="edit"><a title="'.__('Edit this item', 'quotable').'" href="'.get_admin_url().'post.php?post='.$post->ID.'&amp;action=edit">Edit</a> | </span>';
    //$wpg_row_actions .= '<span class="inline hide-if-no-js"><a title="'.__('Edit this item inline', 'quotable').'" class="editinline" href="#">Quick&nbsp;Edit</a> | </span>';
    //$wpg_row_actions .= '<span class="trash"><a href="'.wp_nonce_url(get_admin_url().'post.php?post='.$post->ID.'&amp;action=trash', 'delete-post_'.$post->ID).'" title="'.__('Move this item to the Trash', 'quotable').'" class="submitdelete">Trash</a></span>';
    $wpg_row_actions = "";
    switch ($column) {
        case "slogancategory":
            echo qs_slogan_type_get_taxos($post->ID, 'slogancategory');
            break;
        case "slogan":
            $slogan = $post->post_excerpt;
            if (strlen($slogan) <= 0) {
                $slogan = qs_slogan_type_get_taxos($post->ID, 'slogan');
            }
            echo $slogan . $wpg_row_actions;
            break;
        case "sloganscripture":
            echo qs_slogan_type_get_taxos($post->ID, 'sloganscripture');
            break;
        case "slogantime":
            $slogantime = qs_slogan_type_get_taxos($post->ID, 'slogantime');
            $onlyYear = substr_count($slogantime, '00.');
            if ($onlyYear == 1) {
                echo substr($slogantime, 3);
            } else {
                echo $slogantime;
            }
            break;
    }
}


/**
 * @return void
 */
function add_qs_slogan_type_box_slogan()
{
    add_meta_box('slogan_box_ID', __(QS_SLOGAN), 'qs_slogan_type_styling_function', 'qs_slogan_type', 'advanced', 'core');
}

function add_qs_slogan_type_box_sloganscripture()
{
    add_meta_box('sloganscripture_box_ID', __(QS_SCRIPTURE), 'qs_sloganscripture_type_styling_function', 'qs_slogan_type', 'advanced', 'core');
}

function add_qs_slogan_type_box_slogantime()
{
    add_meta_box('slogantime_box_ID', __(QS_TYPE), 'qs_slogantime_type_styling_function', 'qs_slogan_type', 'advanced', 'core');
}

function add_qs_slogan_type_box_slogancategory()
{
    add_meta_box('slogancategory_box_ID', __("Date"), 'qs_slogancategory_type_styling_function', 'qs_slogan_type', 'advanced', 'core');
}

/**
 * @param $post
 * @return void
 */
function qs_slogan_type_styling_function($post)
{
    echo '<input type="hidden" name="taxonomy_y" id="taxonomy_noncename" value="' .
        wp_create_nonce('taxonomy_slogan') . '" />';
    // Get all theme taxonomy terms
    $slogan = $post->post_excerpt;
    if (strlen($slogan) <= 0) {
        $slogan = qs_slogan_type_get_taxos($post->ID, 'slogan');
    }
    ?>
    <p><textarea cols="80" rows="5" name="excerpt"><?php echo $slogan; ?></textarea>
    </p>
    <?php
}

/**
 * @param $post
 * @return void
 */
function qs_slogantime_type_styling_function($post)
{

    echo '<input type="hidden" name="taxonomy_x" id="taxonomy_noncename" value="' .
        wp_create_nonce('taxonomy_slogantime') . '" />';
    // Get all theme taxonomy terms
    $slogantime = qs_slogan_type_get_taxos($post->ID, 'slogantime');
    if (strlen($slogantime) == 0) {
        $slogantime = "00." . date("Y");
    }
    ?>
    <p><?php echo __("Month") . 'plugins' . __("Year"); ?> (mm.yyyy): <input type="text" value="<?php echo $slogantime; ?>"
                                                                             autocomplete="off" size="7" maxlength="7"
                                                                             class="form-input-tip" name="slogantime"
                                                                             id="slogantime"></p>
    <?php
}

/**
 *
 * Enter description here ...
 * @param unknown_type $post
 */
function qs_sloganscripture_type_styling_function($post)
{

    echo '<input type="hidden" name="taxonomy_z" id="taxonomy_noncename" value="' .
        wp_create_nonce('taxonomy_sloganscripture') . '" />';
    // Get all theme taxonomy terms
    $sloganscripture = qs_slogan_type_get_taxos($post->ID, 'sloganscripture');
    ?>
    <p><input type="text" value="<?php echo $sloganscripture; ?>" autocomplete="on" size="30" class="form-input-tip"
              name="sloganscripture" id="new-tag-sloganscripture">
    </p>
    <?php
}

/**
 *
 * Enter description here ...
 * @param unknown_type $post
 */
function qs_slogancategory_type_styling_function($post)
{

    echo '<input type="hidden" name="taxonomy_w" id="taxonomy_noncename" value="' .
        wp_create_nonce('taxonomy_slogancategory') . '" />';
    // Get all theme taxonomy terms
    $slogancategory = qs_slogan_type_get_taxos($post->ID, 'slogancategory');
    ?>
    <select name="slogancategory">
        <option value="monatsspruch" <?php if ($slogancategory == "monatsspruch") echo "selected"; ?>><?php echo __(QS_TYPE_MONTH); ?></option>
        <option value="jahresspruch" <?php if ($slogancategory == "jahresspruch") echo "selected"; ?>><?php echo __(QS_TYPE_YEAR); ?></option>
    </select>
    <?php
}

/**
 *
 * Enter description here ...
 * @param unknown_type $post_id
 */
function save_qs_slogan_type_taxonomy_slogancategory($post_id)
{
    return save_qs_slogan_type_taxonomy_data($post_id, 'slogancategory');
}

/**
 * @param $post_id
 * @return mixed
 */
function save_qs_slogan_type_taxonomy_sloganscripture($post_id)
{
    return save_qs_slogan_type_taxonomy_data($post_id, 'sloganscripture');
}

/**
 * @param $post_id
 * @return mixed|string
 */
function save_qs_slogan_type_taxonomy_slogantime($post_id)
{
    return save_qs_slogan_type_taxonomy_data_slogantime($post_id, 'slogantime');
}

/**
 * @param $post_id
 * @param $fieldname
 * @return mixed
 */
function save_qs_slogan_type_taxonomy_data($post_id, $fieldname)
{
    // verify this came from our screen and with proper authorization.

    if (!wp_verify_nonce($_POST['taxonomy_y'], 'taxonomy_' . $fieldname) && !wp_verify_nonce($_POST['taxonomy_x'], 'taxonomy_' . $fieldname) && !wp_verify_nonce($_POST['taxonomy_w'], 'taxonomy_' . $fieldname) && !wp_verify_nonce($_POST['taxonomy_z'], 'taxonomy_' . $fieldname)) {
        return $post_id;
    }
    // verify if this is an auto save routine. If it is our form has not been submitted, so we dont want to do anything
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return $post_id;

    // Check permissions
    if ('page' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id))
            return $post_id;
    } else {
        if (!current_user_can('edit_post', $post_id))
            return $post_id;
    }
    // OK, we're authenticated: we need to find and save the data
    $post = get_post($post_id);
    if (($post->post_type == 'post') || ($post->post_type == 'qs_slogan_type')) {
        // OR $post->post_type != 'revision'
        $theme = $_POST[$fieldname];
        wp_set_object_terms($post_id, $theme, $fieldname);
    }
    return $theme;
}

/**
 * @param $post_id
 * @param $fieldname
 * @return mixed|string
 */
function save_qs_slogan_type_taxonomy_data_slogantime($post_id, $fieldname)
{
    // verify this came from our screen and with proper authorization.

    if (!wp_verify_nonce($_POST['taxonomy_y'], 'taxonomy_' . $fieldname) && !wp_verify_nonce($_POST['taxonomy_x'], 'taxonomy_' . $fieldname) && !wp_verify_nonce($_POST['taxonomy_w'], 'taxonomy_' . $fieldname) && !wp_verify_nonce($_POST['taxonomy_z'], 'taxonomy_' . $fieldname)) {
        return $post_id;
    }
    // verify if this is an auto save routine. If it is our form has not been submitted, so we dont want to do anything
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return $post_id;

    // Check permissions
    if ('page' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id))
            return $post_id;
    } else {
        if (!current_user_can('edit_post', $post_id))
            return $post_id;
    }
    // OK, we're authenticated: we need to find and save the data
    $post = get_post($post_id);
    if (($post->post_type == 'post') || ($post->post_type == 'qs_slogan_type')) {
        // OR $post->post_type != 'revision'
        $slogantime = $_POST[$fieldname];
        $slogantimes = explode(".", $slogantime);
        $hits = count($slogantimes);
        if ($hits >= 2) {
            if (strlen($slogantimes[0]) == 1) {
                $slogantimes[0] = "0" . $slogantimes[0];
            }
            if (strlen($slogantimes[1]) == 2) {
                $slogantimes[1] = "20" . $slogantimes[1];
            }
        }
        if (qs_slogan_type_get_taxos($post_id, 'slogancategory') == "jahresspruch") {
            if ($hits >= 2) {
                $slogantime = '00.' . $slogantimes[1];
            } else {
                $slogantime = '00.' . $slogantimes[0];
            }
        } else {
            if ($hits == 1 && strlen($slogantimes) == 4) {
                $slogantime = '00.' . $slogantimes[0];
            } else {
                $slogantime = $slogantimes[0] . '.' . $slogantimes[1];
            }
        }
        wp_set_object_terms($post_id, $slogantime, $fieldname);
    }
    return $slogantime;
}

/**
 * Widget
 *
 */
class QSSloganSidebarWidget extends WP_Widget
{
    public function __construct()
    {
        $widget_ops = array('classname' => 'widget_qs_slogan_posts', 'description' => 'Use this widget to add slogans for a type to the sidebar.');

        parent::__construct('sidebar_slogan_wg', 'Monatsspruch / Jahresspruch Widget', $widget_ops);
    }

    /**
     * @param $args
     * @param $instance
     * @return void
     */
    function widget($args, $instance)
    {
        extract($args, EXTR_SKIP);
        echo $args['before_widget'];
        $title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
        $slogancategory = empty($instance['slogancategory']) ? '' : apply_filters('widget_title', $instance['slogancategory']);
        if (!empty($title)) {
            echo $before_title . $title . $after_title;
        }
        $dateString = "";
        $date = new DateTime();
        if ($slogancategory == "jahresspruch") {
            $dateString = "00." . $date->format('Y');
        } else {
            $dateString = $date->format('m.Y');
        }
        $queryGetPosts = array(
            'posts_per_page' => 2,
            'orderby' => 'none',
            'post_type' => 'qs_slogan_type',
            //'slogantime' => $dateString,
            'post_status' => 'publish',
            'numberposts' => 1,
            //'slogancategory' => $slogancategory,
            'tax_query' => array(
                array(
                    'taxonomy' => 'slogancategory',
                    'field' => 'slug',
                    'terms' => $slogancategory
                ),
                array(
                    'taxonomy' => 'slogantime',
                    'field' => 'slug',
                    'terms' => $dateString
                )
            )

        );
        $posts = get_posts($queryGetPosts);
        foreach ($posts as $post) {
            $slogan = $post->post_excerpt;
            if (strlen($slogan) <= 0) {
                $slogan = qs_slogan_type_get_taxos($post->ID, 'slogan');
            }
            echo '<p class="qsslogan-widget">' . $slogan . "</p>";
            echo '<p class="qsslogan-author-widget" style="float:right"><b>' . qs_slogan_type_get_taxos($post->ID, 'sloganscripture') . "</b></p>";
        }
        echo $args['after_widget'];
    }

    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['slogancategory'] = strip_tags($new_instance['slogancategory']);
        $instance['title'] = strip_tags($new_instance['title']);
        return $instance;
    }

    function form($instance)
    {
        $instance = wp_parse_args((array)$instance, array('title' => '', 'entry_title' => '', 'comments_title' => ''));
        $slogancategory = strip_tags($instance['slogancategory']);
        $title = strip_tags($instance['title']);
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">Title:</label> <input
                    class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
                    name="<?php echo $this->get_field_name('title'); ?>" type="text"
                    value="<?php echo attribute_escape($title); ?>"/>
            </label>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('slogancategory'); ?>">Type:</label>
            <select name="<?php echo $this->get_field_name('slogancategory'); ?>"'
            id="<?php echo $this->get_field_id('slogancategory'); ?>">
            <option value="monatsspruch" <?php if ($slogancategory == "monatsspruch") echo "selected"; ?>><?php echo __(QS_TYPE_MONTH); ?></option>
            <option value="jahresspruch" <?php if ($slogancategory == "jahresspruch") echo "selected"; ?>><?php echo __(QS_TYPE_YEAR); ?></option>
            </select>
        </p>
        <?php
    }
}

function qssloganplugin_register_widgets()
{
    register_widget('QSSloganSidebarWidget');
}

function replace_content_with_slogan($content)
{
    $queryStringYear = 'orderby=rand&numberposts=1&slogantime=00.' . date("Y") . '&post_type=qs_slogan_type&post_status=publish&slogancategory=jahresspruch';
    $queryStringMonth = 'orderby=rand&numberposts=1&slogantime=' . date("m") . '.' . date("Y") . '&post_type=qs_slogan_type&post_status=publish&slogancategory=monatsspruch';
    $posts = get_posts($queryStringYear);
    $sloganYear = "";
    foreach ($posts as $post) {
        $slogan = $post->post_excerpt;
        if (strlen($slogan) <= 0) {
            $slogan = qs_slogan_type_get_taxos($post->ID, 'slogan');
        }
        $sloganYear .= $slogan . '(' . qs_slogan_type_get_taxos($post->ID, 'sloganscripture') . ')';
    }
    $contentResult = str_replace('[_SLOGAN-YEAR_]', $sloganYear, $content);
    $posts = get_posts($queryStringMonth);
    $sloganMonth = "";
    foreach ($posts as $post) {
        $slogan = $post->post_excerpt;
        if (strlen($slogan) <= 0) {
            $slogan = qs_slogan_type_get_taxos($post->ID, 'slogan');
        }
        $sloganMonth .= $slogan . '(' . qs_slogan_type_get_taxos($post->ID, 'sloganscripture') . ')';
    }

    return str_replace('[_SLOGAN-MONTH_]', $sloganMonth, $contentResult);
}

function qs_slogan_flush()
{
    create_qs_slogan_type();
    create_qs_slogan_type_taxonomy();
}

add_action('manage_posts_custom_column', 'qs_slogan_type_custom_columns2');
add_action('admin_menu', 'add_qs_slogan_type_box_slogan');
/* Use the save_post action to save new post data */
add_action('admin_menu', 'add_qs_slogan_type_box_sloganscripture');
add_action('save_post', 'save_qs_slogan_type_taxonomy_slogancategory');
add_action('admin_menu', 'add_qs_slogan_type_box_slogancategory');
add_action('save_post', 'save_qs_slogan_type_taxonomy_sloganscripture');
add_action('admin_menu', 'add_qs_slogan_type_box_slogantime');
add_action('save_post', 'save_qs_slogan_type_taxonomy_slogantime');
add_action('init', 'create_qs_slogan_type');
add_action('init', 'create_qs_slogan_type_taxonomy');


register_activation_hook(__FILE__, 'qs_slogan_flush');

add_action('widgets_init', 'qssloganplugin_register_widgets');
add_filter('the_content', 'replace_content_with_slogan');
add_filter('get_user_option_screen_layout_qs_slogan_type', function () {
    return 1;
});
