<?php
/**
 * Underscores functions and definitions
 */

if ( ! defined( '_S_VERSION' ) ) {
  define( '_S_VERSION', '1.0.0' );
}

function underscores_setup() {
  load_theme_textdomain( 'underscores', get_template_directory() . '/languages' );
  add_theme_support( 'automatic-feed-links' );
  add_theme_support( 'title-tag' );
  add_theme_support( 'post-thumbnails' );
  register_nav_menus( array( 'menu' => esc_html__( 'Footer Menu', 'underscores' ) ) );
  add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
  add_theme_support( 'custom-background', apply_filters( 'underscores_custom_background_args', array( 'default-color' => 'ffffff', 'default-image' => '', ) ) );
  add_theme_support( 'customize-selective-refresh-widgets' );
  add_theme_support( 'custom-logo', array( 'height' => 250, 'width' => 250, 'flex-width' => true, 'flex-height' => true, ) );
}
add_action( 'after_setup_theme', 'underscores_setup' );

function underscores_content_width() {
  $GLOBALS['content_width'] = apply_filters( 'underscores_content_width', 640 );
}
add_action( 'after_setup_theme', 'underscores_content_width', 0 );

function underscores_widgets_init() {
  register_sidebar( array(
    'name'          => esc_html__( 'Sidebar', 'underscores' ),
    'id'            => 'sidebar-1',
    'description'   => esc_html__( 'Add widgets here.', 'underscores' ),
    'before_widget' => '<section id="%1$s" class="widget %2$s">',
    'after_widget'  => '</section>',
    'before_title'  => '<h2 class="widget-title">',
    'after_title'   => '</h2>',
  ) );
}
add_action( 'widgets_init', 'underscores_widgets_init' );

function underscores_scripts() {
  wp_enqueue_style( 'underscores-style', get_stylesheet_uri(), array(), filemtime( get_template_directory() . '/style.css' ) );
  wp_style_add_data( 'underscores-style', 'rtl', 'replace' );
  wp_enqueue_script( 'underscores-navigation', get_template_directory_uri() . '/js/navigation.js', array(), _S_VERSION, true );
  if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
    wp_enqueue_script( 'comment-reply' );
  }
}
add_action( 'wp_enqueue_scripts', 'underscores_scripts' );

require get_template_directory() . '/inc/custom-header.php';
require get_template_directory() . '/inc/template-tags.php';
require get_template_directory() . '/inc/template-functions.php';
require get_template_directory() . '/inc/customizer.php';

if ( defined( 'JETPACK__VERSION' ) ) {
  require get_template_directory() . '/inc/jetpack.php';
}

function clean_wordpress_head() {
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'feed_links_extra', 3);
    remove_action('wp_head', 'feed_links', 2);
    remove_action('wp_head', 'index_rel_link');
    remove_action('wp_head', 'parent_post_rel_link', 10, 0);
    remove_action('wp_head', 'start_post_rel_link', 10, 0);
    remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
    remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
}
add_action('after_setup_theme', 'clean_wordpress_head');

class My_Custom_Walker extends Walker_Nav_Menu {
  private $is_first_item = true;
  function start_lvl(&$output, $depth = 0, $args = null) {
    $class = ($depth === 0) ? 'dropdown' : 'sub-dropdown level-' . $depth;
    $output .= '<div class="' . esc_attr($class) . '">';
  }
  function end_lvl(&$output, $depth = 0, $args = null) {
    $output .= '</div>';
  }
  function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
    if ($depth === 0) {
      $classes = 'item-menu';
      if ($this->is_first_item) {
        $classes .= ' item-menu-one';
        $this->is_first_item = false;
      }
      $output .= '<div class="' . esc_attr($classes) . '">';
    } else {
      $output .= '<div class="sub-item-wrapper level-' . $depth . '">';
    }
    $attributes = !empty($item->url) ? ' href="' . esc_attr($item->url) . '"' : '';
    $item_output = '<a' . $attributes . '>' . apply_filters('the_title', $item->title, $item->ID) . '</a>';
    $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
  }
  function end_el(&$output, $item, $depth = 0, $args = null) {
    $output .= '</div>';
  }
}

add_action('init', function() {
    register_post_type('business', array(
        'labels' => array('name' => 'עסקים', 'singular_name' => 'עסק'),
        'public' => true,
        'has_archive' => true,
        'supports' => array('title', 'editor', 'thumbnail', 'comments'),
        'show_in_rest' => true,
    ));
});

add_action('init', function() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['business_title'])) return;
    if (!wp_verify_nonce($_POST['free_nonce'] ?? '', 'submit_free')) return;

    $post_id = wp_insert_post([
        'post_title'   => sanitize_text_field($_POST['business_title']),
        'post_content' => sanitize_textarea_field($_POST['business_desc']),
        'post_status'  => 'pending', 
        'post_type'    => 'business'
    ]);

    if ($post_id) {
        update_post_meta($post_id, 'city', sanitize_text_field($_POST['city_select'] == 'add_new' ? $_POST['business_city'] : $_POST['city_select']));
        update_post_meta($post_id, 'cat', sanitize_text_field($_POST['cat_select'] == 'add_new' ? $_POST['business_cat'] : $_POST['cat_select']));
        update_post_meta($post_id, 'subcat', sanitize_text_field($_POST['subcat_select'] == 'add_new' ? $_POST['business_subcat'] : $_POST['subcat_select']));
        update_post_meta($post_id, 'address', sanitize_text_field($_POST['business_address']));
        update_post_meta($post_id, 'email', sanitize_email($_POST['business_email']));
        update_post_meta($post_id, 'phone', sanitize_text_field($_POST['business_phone']));
        
        wp_redirect(home_url('/?status=success'));
        exit;
    }
});

add_action('transition_post_status', function($new_status, $old_status, $post) {
    if ($post->post_type !== 'business' || $new_status !== 'publish' || $old_status === 'publish') return;

    $city = get_post_meta($post->ID, 'city', true);
    $cat = get_post_meta($post->ID, 'cat', true);
    $subcat = get_post_meta($post->ID, 'subcat', true);

    if (!empty($city)) {
        $cities = get_option('site_cities', []);
        if (!in_array($city, $cities)) { $cities[] = $city; sort($cities); update_option('site_cities', $cities); }
    }
    if (!empty($cat)) {
        $cats_map = get_option('site_cats_map', []);
        if (!isset($cats_map[$cat])) $cats_map[$cat] = [];
        if (!empty($subcat) && !in_array($subcat, $cats_map[$cat])) {
            $cats_map[$cat][] = $subcat;
            update_option('site_cats_map', $cats_map);
        }
    }
}, 10, 3);

add_action('add_meta_boxes', function() {
    add_meta_box('business_details', 'פרטי העסק', function($post) {
        $meta = get_post_custom($post->ID);
        echo '<p>עיר: <input type="text" name="city" value="'.esc_attr($meta['city'][0] ?? '').'"></p>';
        echo '<p>קטגוריה: <input type="text" name="cat" value="'.esc_attr($meta['cat'][0] ?? '').'"></p>';
        echo '<p>תת-קטגוריה: <input type="text" name="subcat" value="'.esc_attr($meta['subcat'][0] ?? '').'"></p>';
        echo '<p>כתובת: <input type="text" name="address" value="'.esc_attr($meta['address'][0] ?? '').'"></p>';
        echo '<p>מייל: <input type="text" name="email" value="'.esc_attr($meta['email'][0] ?? '').'"></p>';
        echo '<p>טלפון: <input type="text" name="phone" value="'.esc_attr($meta['phone'][0] ?? '').'"></p>';
        wp_nonce_field('save_biz_meta', 'biz_meta_nonce');
    }, 'business', 'normal', 'high');
});

add_action('pre_get_posts', function($query) {
    if (!is_admin() && $query->is_main_query() && ($query->is_search() || $query->is_post_type_archive('business'))) {
        $query->set('post_type', array('post', 'business'));
    }
});

add_action('save_post_business', function($post_id) {
    if (!isset($_POST['biz_meta_nonce']) || !wp_verify_nonce($_POST['biz_meta_nonce'], 'save_biz_meta')) return;
    
    $fields = ['city', 'cat', 'subcat', 'address', 'email', 'phone'];
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
        }
    }
});

add_action('admin_menu', function() {
    add_menu_page('ניהול מאגרים', 'ניהול מאגרים', 'manage_options', 'manage-databases', 'render_db_manager_page');
});

function render_db_manager_page() {
    if (isset($_POST['save_db'])) {
        update_option('site_cities', explode("\n", str_replace("\r", "", $_POST['cities'])));
        update_option('site_cats_map', json_decode(stripslashes($_POST['cats_map']), true));
        echo '<div class="updated"><p>הנתונים עודכנו!</p></div>';
    }

    $cities = implode("\n", get_option('site_cities', []));
    $cats_map = json_encode(get_option('site_cats_map', []), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    ?>
    <div class="wrap">
        <h1>ניהול קבוע של מאגרים</h1>
        <form method="post">
            <h3>ערים (אחת בכל שורה):</h3>
            <textarea name="cities" rows="5" style="width:100%"><?php echo esc_textarea($cities); ?></textarea>
            
            <h3>קטגוריות ותתי קטגוריות (בפורמט JSON):</h3>
            <textarea name="cats_map" rows="10" style="width:100%"><?php echo esc_textarea($cats_map); ?></textarea>
            
            <input type="submit" name="save_db" class="button button-primary" value="שמור שינויים">
        </form>
    </div>
    <?php
}

function get_business_rating_data($post_id) {
    global $wpdb;
    
    $data = $wpdb->get_row($wpdb->prepare("
        SELECT AVG(meta_value) as average, COUNT(comment_ID) as count 
        FROM $wpdb->commentmeta 
        JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
        WHERE meta_key = 'rating' AND comment_post_ID = %d AND comment_approved = '1'", $post_id));
    
    if ($data) {
        return [
            'avg' => $data->average ? round($data->average, 1) : 0,
            'count' => (int)$data->count
        ];
    }
    
    return [
        'avg' => 0,
        'count' => 0
    ];
}

add_action('wp_ajax_load_business_reviews', 'ajax_load_business_reviews');
add_action('wp_ajax_nopriv_load_business_reviews', 'ajax_load_business_reviews');

function ajax_load_business_reviews() {
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    if (!$post_id) {
        wp_send_json_error();
    }

    $comments = get_comments(array(
        'post_id' => $post_id,
        'status'  => 'approve'
    ));

    ob_start();
    ?>
    <div class="existing-reviews" style="margin-bottom: 20px; border-bottom: 1px solid #ddd; padding-bottom: 15px;">
        <h4 style="margin-top: 0;">ביקורות שנכתבו:</h4>
        <?php if (empty($comments)) : ?>
            <p style="color: #666;">אין עדיין ביקורות לעסק זה. כתבו את הביקורת הראשונה!</p>
        <?php else : ?>
            <div style="max-height: 180px; overflow-y: auto;">
                <?php foreach ($comments as $comment) : 
                    $rating = get_comment_meta($comment->comment_ID, 'rating', true);
                ?>
                    <div style="padding: 8px; border-bottom: 1px dashed #eee; margin-bottom: 5px;">
                        <span style="color: #ff9800; font-weight: bold;">
                            <?php echo str_repeat('★', intval($rating)) . str_repeat('☆', 5 - intval($rating)); ?>
                        </span>
                        <p style="margin: 5px 0; font-size: 0.95em;"><?php echo esc_html($comment->comment_content); ?></p>
                        <small style="color: #999;"><?php echo get_comment_date('d/m/Y', $comment); ?></small>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <form id="anonymous-review-form" style="background: #f7f7f7; padding: 15px; border-radius: 5px;">
        <h4 style="margin: 0 0 10px 0;">הוספת ביקורת ודירוג מהירים:</h4>
        
        <div style="margin-bottom: 10px;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold;">דירוג: <span style="color:red;">*</span></label>
            <select name="review_rating" required style="width: 100%; padding: 6px;">
                <option value="">בחר דירוג...</option>
                <option value="5">5 כוכבים - מעולה</option>
                <option value="4">4 כוכבים - טוב מאוד</option>
                <option value="3">3 כוכבים - בסדר גמור</option>
                <option value="2">2 כוכבים - לא משהו</option>
                <option value="1">1 כוכב - גרוע</option>
            </select>
        </div>

        <div style="margin-bottom: 10px;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold;">הביקורת שלך: <span style="color:red;">*</span></label>
            <textarea name="review_text" required style="width: 100%; height: 70px; padding: 6px; resize: none;" placeholder="כתוב כאן מה דעתך על העסק..."></textarea>
        </div>

        <button type="submit" class="button" style="width: 100%; padding: 8px; background: #23282d; color: #fff; border: none; border-radius: 3px; cursor: pointer;">שלח ביקורת</button>
        <div id="review-submit-status" style="margin-top: 10px;"></div>
    </form>
    <?php
    $html = ob_get_clean();
    wp_send_json_success($html);
}

add_action('wp_ajax_submit_anonymous_review', 'ajax_submit_anonymous_review');
add_action('wp_ajax_nopriv_submit_anonymous_review', 'ajax_submit_anonymous_review');

function ajax_submit_anonymous_review() {
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    $rating  = isset($_POST['review_rating']) ? intval($_POST['review_rating']) : 0;
    $text    = isset($_POST['review_text']) ? sanitize_textarea_field($_POST['review_text']) : '';

    if (!$post_id || !$rating || empty($text)) {
        wp_send_json_error('נא למלא את כל שדות החובה (טקסט ודירוג).');
    }

    $comment_data = array(
        'comment_post_ID'      => $post_id,
        'comment_author'       => 'אורח',
        'comment_author_email' => 'guest@' . $_SERVER['HTTP_HOST'],
        'comment_content'      => $text,
        'comment_type'         => 'comment',
        'comment_approved'     => 1,
    );

    $comment_id = wp_insert_comment($comment_data);

    if ($comment_id) {
        update_comment_meta($comment_id, 'rating', $rating);
        
        $new_rating = get_business_rating_data($post_id);
        
        wp_send_json_success(array(
            'message' => 'הביקורת והדירוג נשמרו בהצלחה!',
            'avg'     => $new_rating['avg'],
            'count'   => $new_rating['count']
        ));
    } else {
        wp_send_json_error('משהו השתבש בשרת, לא הצלחנו לשמור את הביקורת.');
    }
}


add_action('add_meta_boxes', function() {
    add_meta_box('business_featured', 'הגדרות עסק', function($post) {
        $is_featured = get_post_meta($post->ID, 'is_featured', true);
        echo '<label><input type="checkbox" name="is_featured" value="1" '.checked($is_featured, '1', false).'> סמן כעסק מומלץ (יופיע בדף הבית)</label>';
        wp_nonce_field('save_biz_meta', 'biz_meta_nonce');
    }, 'business', 'side', 'high');
});

add_action('save_post_business', function($post_id) {
    if (isset($_POST['biz_meta_nonce']) && wp_verify_nonce($_POST['biz_meta_nonce'], 'save_biz_meta')) {
        update_post_meta($post_id, 'is_featured', isset($_POST['is_featured']) ? '1' : '0');
    }
});

function get_featured_businesses($limit = 3) {
    return new WP_Query([
        'post_type' => 'business',
        'meta_key' => 'is_featured',
        'meta_value' => '1',
        'posts_per_page' => $limit
    ]);
}

add_action('wp_ajax_send_site_message', 'send_site_message');
add_action('wp_ajax_nopriv_send_site_message', 'send_site_message');
function send_site_message() {
    $post_id = wp_insert_post([
        'post_title'   => 'הודעה חדשה: ' . date('d/m/Y H:i'),
        'post_content' => "איש קשר: " . sanitize_text_field($_POST['contact_info']) . "\n\n הודעה: " . sanitize_textarea_field($_POST['msg_text']),
        'post_type'    => 'page',
        'post_status'  => 'private'
    ]);
    if ($post_id) wp_send_json_success('הודעתך התקבלה!');
    wp_send_json_error('שגיאה בשליחה');
}
