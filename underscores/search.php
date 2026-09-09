<?php
get_header();

$args = array(
    'post_type'   => 'business',
    'post_status' => 'publish',
    's'           => get_search_query(), 
    'meta_query'  => array('relation' => 'AND')
);

if (!empty($_GET['city_filter'])) {
    $args['meta_query'][] = array('key' => 'city', 'value' => sanitize_text_field($_GET['city_filter']), 'compare' => '=');
}
if (!empty($_GET['cat_filter'])) {
    $args['meta_query'][] = array('key' => 'cat', 'value' => sanitize_text_field($_GET['cat_filter']), 'compare' => '=');
}
if (!empty($_GET['subcat_filter'])) {
    $args['meta_query'][] = array('key' => 'subcat', 'value' => sanitize_text_field($_GET['subcat_filter']), 'compare' => '=');
}

$search_query = new WP_Query($args);
?>

<style>
#reviews-dialog {
    width: 90%;
    max-width: 500px;
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    box-sizing: border-box;
}

@media (min-width: 768px) {
    #reviews-dialog {
        position: fixed;
        top: 50% !important;
        left: 50% !important;
        transform: translate(-50%, -50%) !important;
        margin: 0 !important;
    }
}

/* עיצוב בר הסינונים */
.filter-chip a:hover {
    color: #ff3b30 !important;
}
</style>

<main id="main" class="site-main">

    <?php
    $active_filters = [];
    if (!empty($_GET['city_filter'])) {
        $active_filters['city_filter'] = [
            'label' => 'עיר',
            'value' => sanitize_text_field($_GET['city_filter'])
        ];
    }
    if (!empty($_GET['cat_filter'])) {
        $active_filters['cat_filter'] = [
            'label' => 'קטגוריה',
            'value' => sanitize_text_field($_GET['cat_filter'])
        ];
    }
    if (!empty($_GET['subcat_filter'])) {
        $active_filters['subcat_filter'] = [
            'label' => 'תת-קטגוריה',
            'value' => sanitize_text_field($_GET['subcat_filter'])
        ];
    }
    ?>

    <?php if (!empty($active_filters)) : ?>
        <div class="active-filters-bar" style="background: #fdfdfd; padding: 15px 20px; margin-bottom: 25px; border: 1px solid #e0e0e0; border-radius: 8px; display: flex; align-items: center; gap: 12px; flex-wrap: wrap; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <span style="font-weight: bold; color: #444; font-size: 1em;">סינונים פעילים:</span>
            
            <?php foreach ($active_filters as $key => $filter) : 
                $clear_link = remove_query_arg($key);
            ?>
                <span class="filter-chip" style="background: #f1f1f1; border: 1px solid #ddd; padding: 6px 14px; border-radius: 25px; font-size: 0.9em; display: inline-flex; align-items: center; gap: 8px; color: #333;">
                    <strong><?php echo esc_html($filter['label']); ?>:</strong> <?php echo esc_html($filter['value']); ?>
                    <a href="<?php echo esc_url($clear_link); ?>" style="text-decoration: none; color: #999; font-weight: bold; font-size: 1.2em; display: inline-block; padding: 0 2px; transition: color 0.2s; line-height: 1;" title="בטל סינון">&times;</a>
                </span>
            <?php endforeach; ?>
            
            <a href="<?php echo esc_url(remove_query_arg(['city_filter', 'cat_filter', 'subcat_filter'])); ?>" style="font-size: 0.9em; color: #0073aa; margin-right: auto; text-decoration: underline; font-weight: 500;">נקה הכל</a>
        </div>
    <?php endif; ?>

    <?php if ($search_query->have_posts()) : ?>
        <div class="results-grid">
            <?php while ($search_query->have_posts()) : $search_query->the_post(); 
                $post_id = get_the_ID();
            ?>
                <div class="business-item" style="border: 1px solid #ccc; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
                    <div class="business-card" style="margin-bottom: 15px;">
                        <h2 style="margin-top: 0;"><?php the_title(); ?></h2>
                        <p><strong>כתובת:</strong> <?php echo esc_html(get_post_meta($post_id, 'address', true)); ?></p>
                        <p><strong>טלפון:</strong> <?php echo esc_html(get_post_meta($post_id, 'phone', true)); ?></p>
                        <p><strong>מייל:</strong> <?php echo esc_html(get_post_meta($post_id, 'email', true)); ?></p>
                        
                        <div class="business-description" style="margin-top: 10px; padding-top: 10px; border-top: 1px dashed #eee; color: #555;">
                            <strong>על העסק:</strong>
                            <?php the_excerpt(); ?>
                        </div>
                    </div>

                    <?php $rating_data = get_business_rating_data($post_id); ?>

                    <div id="rating-container-<?php echo $post_id; ?>" class="business-rating" style="background: #f9f9f9; padding: 10px; border-radius: 4px;">
                        <?php if ($rating_data['count'] > 0) : ?>
                            <p style="margin: 0 0 10px 0;">
                                <strong>דירוג ממוצע:</strong> 
                                <span style="color: #ff9800;">★</span> <?php echo number_format($rating_data['avg'], 1); ?>/5 
                                (<?php echo $rating_data['count']; ?> ביקורות)
                            </p>
                            <button class="open-reviews-btn button" 
                                    data-id="<?php echo $post_id; ?>" 
                                    data-title="<?php echo esc_attr(get_the_title()); ?>"
                                    style="cursor: pointer; padding: 5px 12px; background: #0073aa; color: #fff; border: none; border-radius: 3px;">
                                לקריאה וכתיבת ביקורת
                            </button>
                        <?php else : ?>
                            <p style="margin: 0 0 10px 0;">עדיין אין ביקורות לעסק זה.</p>
                            <button class="open-reviews-btn button" 
                                    data-id="<?php echo $post_id; ?>" 
                                    data-title="<?php echo esc_attr(get_the_title()); ?>"
                                    style="cursor: pointer; padding: 5px 12px; background: #0073aa; color: #fff; border: none; border-radius: 3px;">
                                כתוב ביקורת ראשונה
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else : ?>
        <p>לא נמצאו עסקים התואמים את הסינון שבחרת.</p>
    <?php endif; wp_reset_postdata(); ?>
</main>

<dialog id="reviews-dialog">
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 15px;">
        <h3 id="dialog-business-title" style="margin: 0; font-size: 1.3em;"></h3>
        <button id="close-dialog-btn" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #888;">&times;</button>
    </div>
    
    <div id="dialog-reviews-content" style="max-height: 400px; overflow-y: auto;">
        טוען...
    </div>
</dialog>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dialog = document.getElementById('reviews-dialog');
    const closeBtn = document.getElementById('close-dialog-btn');
    const contentDiv = document.getElementById('dialog-reviews-content');
    const titleH3 = document.getElementById('dialog-business-title');

    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.open-reviews-btn');
        if (!btn) return;

        const postId = btn.getAttribute('data-id');
        const postTitle = btn.getAttribute('data-title');
        
        titleH3.textContent = 'ביקורות עבור: ' + postTitle;
        contentDiv.innerHTML = '<p>טוען נתונים, אנא המתן...</p>';
        dialog.showModal(); 

        fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'load_business_reviews',
                post_id: postId
            })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                contentDiv.innerHTML = data.data;
                setupFormListener(postId, postTitle); 
            } else {
                contentDiv.innerHTML = '<p>שגיאה בטעינת הנתונים.</p>';
            }
        });
    });

    closeBtn.addEventListener('click', () => dialog.close());
    dialog.addEventListener('click', (e) => { if (e.target === dialog) dialog.close(); });

    function setupFormListener(postId, postTitle) {
        const form = document.getElementById('anonymous-review-form');
        if(!form) return;

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const submitBtn = form.querySelector('button[type="submit"]');
            const statusDiv = document.getElementById('review-submit-status');
            
            submitBtn.disabled = true;
            statusDiv.innerHTML = '<p style="color:blue;">שולח ביקורת...</p>';

            const formData = new FormData(form);
            formData.append('action', 'submit_anonymous_review');
            formData.append('post_id', postId);

            fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    statusDiv.innerHTML = '<p style="color:green;">' + data.data.message + '</p>';
                    
                    const ratingContainer = document.getElementById('rating-container-' + postId);
                    if (ratingContainer) {
                        const avg = parseFloat(data.data.avg).toFixed(1);
                        const count = parseInt(data.data.count);
                        
                        ratingContainer.innerHTML = `
                            <p style="margin: 0 0 10px 0;">
                                <strong>דירוג ממוצע:</strong> 
                                <span style="color: #ff9800;">★</span> ${avg}/5 
                                (${count} ביקורות)
                            </p>
                            <button class="open-reviews-btn button" 
                                    data-id="${postId}" 
                                    data-title="${postTitle.replace(/"/g, '&quot;')}"
                                    style="cursor: pointer; padding: 5px 12px; background: #0073aa; color: #fff; border: none; border-radius: 3px;">
                                לקריאה וכתיבת ביקורת
                            </button>
                        `;
                    }

                    setTimeout(() => {
                        dialog.close();
                    }, 1500);
                } else {
                    statusDiv.innerHTML = '<p style="color:red;">' + data.data + '</p>';
                    submitBtn.disabled = false;
                }
            });
        });
    }
});
</script>

<?php get_footer(); ?>