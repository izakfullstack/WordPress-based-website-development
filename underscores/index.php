<?php
get_header();
?>

<main id="primary" class="site-main">
    <section class="title">
        <h2 class="find">מצאו עסקים ואנשי מקצוע באזורכם לפי קטגוריה!</h2>
        <p class="find">בחר עיר, וקטגוריה, או בחיפוש חופשי</p>
    </section>

    <form id="front" method="get" action="<?php echo esc_url(home_url('/')); ?>">
        <select class="design-find" " name="city_filter">
            <option value="">כל הערים</option>
            <?php 
            $cities = get_option('site_cities', []);
            foreach($cities as $c) echo "<option value='".esc_attr($c)."'>".esc_html($c)."</option>";
            ?>
        </select>

        <select class="design-find" name="cat_filter" onchange="updateSubcats(this.value)">
            <option value="">כל הקטגוריות</option>
            <?php 
            $cats_map = get_option('site_cats_map', []);
            foreach(array_keys($cats_map) as $cat) echo "<option value='".esc_attr($cat)."'>".esc_html($cat)."</option>";
            ?>
        </select>

        <select class="design-find" name="subcat_filter">
            <option value="">כל תתי הקטגוריות</option>
            </select>

        <input class="design-find" type="text" name="s" placeholder="חיפוש חופשי...">
        <button type="submit" class="link-brown">חפש</button>
    </form>

    <section class="pirsum">
        <a class="link-black" href="<?php echo esc_url(home_url('/add-business.php')); ?>">הוסף ללוח!</a>
        <p class="osafa">חיפשת עסק ולא מצאת?</p>
        <div class="line"></div>
        <p class="tocnit">בעל עסק? רוצה לבלוט?</p>
        <a href="add-owner-business.php#step-2" class="link-black">לתוכנית משתלמת!</a>
        
        <?php if (isset($_GET['status']) && $_GET['status'] == 'success') : ?>
            <div id="success-message">
                העסק שלך נשלח בהצלחה וממתין לאישור מנהל!
            </div>
            <script>
                setTimeout(function() {
                    var msg = document.getElementById('success-message');
                    if (msg) msg.style.display = 'none';
                }, 3000);
            </script>
        <?php endif; ?>
    </section>
</main>
<section class="main-businesses">
    <!-- אזור עסקים מומלצים -->
    <section class="featured-businesses">
        <h3>עסקים מומלצים :</h3>
        <div class="results-grid">
            <?php 
            $featured = get_featured_businesses();
            if ($featured->have_posts()) : while ($featured->have_posts()) : $featured->the_post(); 
                $post_id = get_the_ID();
                $rating_data = get_business_rating_data($post_id);
            ?>
                <div class="business-item">
                    <h4><?php the_title(); ?></h4>
                    <div id="rating-container-<?php echo $post_id; ?>" class="business-rating">
                        <?php
                        $rating_data = get_business_rating_data($post_id);
                        $count = $rating_data['count'];
                        $avg = $rating_data['avg'];

                        $full_stars = floor($avg);
                        $half_star = ($avg - $full_stars >= 0.5) ? 1 : 0;
                        $empty_stars = 5 - ($full_stars + $half_star);

                        if ($count == 0) {
                            $full_stars = 0;
                            $half_star = 0;
                            $empty_stars = 5;
                        }

                        $stars_html = str_repeat('★', $full_stars) . ($half_star ? '⯬' : '') . str_repeat('☆', $empty_stars);
                        ?>

<button class="button-small open-reviews-btn button flex" 
        data-id="<?php echo $post_id; ?>"
        data-title="<?php echo esc_attr(get_the_title()); ?>">
    <p><?php echo $stars_html; ?></p>
    <?php if ($count > 0) : ?>
        <p><strong>דירוג:</strong> <?php echo number_format($avg, 1); ?>/5 (<?php echo $count; ?> ביקורות)</p>
    <?php else : ?>
        <p>0 ביקורות.</p>
    <?php endif; ?>
</button>
                    </div>
                    <div class="flex">
                        <p><strong>כתובת:</strong> <?php echo esc_html(get_post_meta($post_id, 'address', true)); ?></p>
                        <p><strong>טלפון:</strong> <?php echo esc_html(get_post_meta($post_id, 'phone', true)); ?></p>
                    </div>
                </div>
            <?php endwhile; wp_reset_postdata(); endif; ?>
        </div>
    </section>
    <section class="kesher">
        <h3>צרו קשר :</h3>
        <section class="contact-form">
            <form id="site-msg-form">
                <h4>שלחו הודעה למערכת :</h4>
                <input class="client-content" type="text" name="contact_info" placeholder="שם/טלפון" required >
                <textarea class="client-content textarea" name="msg_text" placeholder="הודעה" required></textarea>
                <button type="submit" class="link-black">שלח הודעה</button>
            </form>
            <div class="nav-links">
                <a class="tel-mail" href="tel:0533157346">
                    <span class="border">
                        <svg width="30" height="30" viewBox="0 0 27 27" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M25.0384 17.6846L18.8422 14.9081L18.8251 14.9002C18.5035 14.7626 18.1526 14.7074 17.8042 14.7396C17.4558 14.7717 17.121 14.8902 16.8299 15.0843C16.7956 15.107 16.7627 15.1316 16.7313 15.158L13.53 17.8871C11.5018 16.902 9.40797 14.8239 8.42285 12.8221L11.1559 9.57212C11.1822 9.53924 11.2072 9.50636 11.2309 9.47085C11.4209 9.18058 11.5361 8.84784 11.5664 8.50226C11.5967 8.15668 11.5411 7.80897 11.4045 7.49008V7.4743L8.62014 1.26763C8.43961 0.851049 8.12919 0.50402 7.73522 0.278354C7.34126 0.0526883 6.88487 -0.039512 6.4342 0.0155168C4.652 0.250035 3.01611 1.12528 1.83206 2.47779C0.648017 3.8303 -0.00321043 5.56757 1.19012e-05 7.36513C1.19012e-05 17.8082 8.4965 26.3047 18.9396 26.3047C20.7371 26.308 22.4744 25.6567 23.8269 24.4727C25.1794 23.2886 26.0546 21.6527 26.2892 19.8705C26.3443 19.42 26.2523 18.9637 26.0269 18.5698C25.8014 18.1758 25.4547 17.8653 25.0384 17.6846ZM18.9396 24.2003C14.4761 24.1955 10.1968 22.4202 7.04069 19.264C3.88454 16.1079 2.10928 11.8286 2.10441 7.36513C2.09946 6.08078 2.56218 4.83853 3.40615 3.87039C4.25013 2.90225 5.41765 2.27441 6.69067 2.10413C6.69015 2.10938 6.69015 2.11467 6.69067 2.11992L9.45269 8.30159L6.73407 11.5555C6.70648 11.5873 6.68141 11.6211 6.6591 11.6568C6.46118 11.9605 6.34507 12.3102 6.32203 12.672C6.29898 13.0338 6.36979 13.3954 6.52758 13.7217C7.71919 16.1589 10.1748 18.5961 12.6382 19.7864C12.967 19.9427 13.3307 20.011 13.6938 19.9847C14.0568 19.9585 14.4069 19.8384 14.7097 19.6364C14.7435 19.6137 14.776 19.5891 14.8071 19.5628L18.0044 16.8349L24.1861 19.6035C24.1861 19.6035 24.1966 19.6035 24.2005 19.6035C24.0323 20.8784 23.4054 22.0482 22.4371 22.8943C21.4688 23.7404 20.2254 24.2047 18.9396 24.2003Z" fill="white"/>
                        </svg>
                    </span>
                    <span>053-315-7346</span>
                </a>
                <a class="tel-mail" href="mailto:ywtmspr@gmail.com">
                    <span class="border">
                        <svg width="30" height="30" viewBox="0 0 31 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12.0563 18.6325C12.0563 18.9232 11.9409 19.202 11.7353 19.4075C11.5298 19.6131 11.251 19.7286 10.9603 19.7286H5.48015C5.18947 19.7286 4.91069 19.6131 4.70514 19.4075C4.4996 19.202 4.38412 18.9232 4.38412 18.6325C4.38412 18.3418 4.4996 18.0631 4.70514 17.8575C4.91069 17.652 5.18947 17.5365 5.48015 17.5365H10.9603C11.251 17.5365 11.5298 17.652 11.7353 17.8575C11.9409 18.0631 12.0563 18.3418 12.0563 18.6325ZM30.6889 13.7004V21.9206C30.6889 22.502 30.4579 23.0595 30.0468 23.4706C29.6357 23.8817 29.0782 24.1127 28.4968 24.1127H16.4405V28.4968C16.4405 28.7875 16.325 29.0663 16.1194 29.2718C15.9139 29.4774 15.6351 29.5928 15.3444 29.5928C15.0537 29.5928 14.775 29.4774 14.5694 29.2718C14.3639 29.0663 14.2484 28.7875 14.2484 28.4968V24.1127H2.19206C1.61069 24.1127 1.05313 23.8817 0.64204 23.4706C0.230949 23.0595 0 22.502 0 21.9206V13.7004C0.00253815 11.521 0.869411 9.43165 2.41045 7.89061C3.9515 6.34957 6.04087 5.48269 8.22023 5.48015H18.6325V1.09603C18.6325 0.805345 18.748 0.526566 18.9535 0.32102C19.1591 0.115474 19.4379 0 19.7286 0H24.1127C24.4034 0 24.6821 0.115474 24.8877 0.32102C25.0932 0.526566 25.2087 0.805345 25.2087 1.09603C25.2087 1.38672 25.0932 1.6655 24.8877 1.87104C24.6821 2.07659 24.4034 2.19206 24.1127 2.19206H20.8246V5.48015H22.4686C24.648 5.48269 26.7374 6.34957 28.2784 7.89061C29.8195 9.43165 30.6863 11.521 30.6889 13.7004ZM14.2484 21.9206V13.7004C14.2484 12.1016 13.6133 10.5683 12.4828 9.43783C11.3523 8.30733 9.819 7.67222 8.22023 7.67222C6.62146 7.67222 5.08817 8.30733 3.95767 9.43783C2.82717 10.5683 2.19206 12.1016 2.19206 13.7004V21.9206H14.2484ZM28.4968 13.7004C28.495 12.1022 27.8593 10.5699 26.7292 9.43983C25.5991 8.30972 24.0668 7.67403 22.4686 7.67222H20.8246V17.5365C20.8246 17.8272 20.7091 18.106 20.5036 18.3115C20.298 18.5171 20.0192 18.6325 19.7286 18.6325C19.4379 18.6325 19.1591 18.5171 18.9535 18.3115C18.748 18.106 18.6325 17.8272 18.6325 17.5365V7.67222H13.8031C14.6351 8.44048 15.299 9.37272 15.7529 10.4102C16.2068 11.4477 16.4409 12.568 16.4405 13.7004V21.9206H28.4968V13.7004Z" fill="white"/>
                    </svg>
                    </span>
                    <span>ywtmspr@gmail.com</span>
                </a>
            </div>
        </section>
    </section>
</section>

<script>
    document.getElementById('site-msg-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        fd.append('action', 'send_site_message');
        fetch('<?php echo admin_url("admin-ajax.php"); ?>', {method:'POST', body:new URLSearchParams(fd)})
        .then(res => res.json()).then(data => { document.getElementById('msg-status').innerText = data.data; });
    });
</script>

<?php get_footer(); ?>

<script>
    var catsMap = <?php echo json_encode(get_option('site_cats_map', [])); ?>;

    function updateSubcats(cat) {
        var subSelect = document.querySelector('select[name="subcat_filter"]');
        subSelect.innerHTML = '<option value="">כל תתי הקטגוריות</option>';
        
        if(cat && catsMap[cat]) {
            catsMap[cat].forEach(function(sub) {
                var opt = document.createElement('option');
                opt.value = sub;
                opt.textContent = sub;
                subSelect.appendChild(opt);
            });
        }
    }
</script>
<dialog id="reviews-dialog">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
        <h3 id="dialog-business-title" style="margin: 0;"></h3>
        <button id="close-dialog-btn" style="cursor: pointer;">סגור</button>
    </div>
    <div id="dialog-reviews-content">טוען...</div>
</dialog>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dialog = document.getElementById('reviews-dialog');
    const contentDiv = document.getElementById('dialog-reviews-content');
    const titleH3 = document.getElementById('dialog-business-title');

    document.addEventListener('click', function(e) {
        if (!e.target.matches('.open-reviews-btn')) return;

        const btn = e.target;
        const postId = btn.getAttribute('data-id');
        const postTitle = btn.getAttribute('data-title');
        
        titleH3.textContent = 'ביקורות עבור: ' + postTitle;
        contentDiv.innerHTML = '<p>טוען נתונים...</p>';
        dialog.showModal();

        fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ action: 'load_business_reviews', post_id: postId })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                contentDiv.innerHTML = data.data;
                initReviewForm(postId, postTitle);
            }
        });
    });

    function initReviewForm(postId, postTitle) {
        const form = document.getElementById('anonymous-review-form');
        if(!form) return;

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(form);
            formData.append('action', 'submit_anonymous_review');
            formData.append('post_id', postId);

            fetch('<?php echo admin_url("admin-ajax.php"); ?>', { method: 'POST', body: new URLSearchParams(formData) })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    const container = document.getElementById('rating-container-' + postId);
                    if(container) {
                        container.innerHTML = `
                            <p><strong>דירוג ממוצע:</strong> ${parseFloat(data.data.avg).toFixed(1)}/5 (${data.data.count} ביקורות)</p>
                            <button class="open-reviews-btn button" data-id="${postId}" data-title="${postTitle}" 
                            style="cursor: pointer; padding: 5px 12px; background: #0073aa; color: #fff; border: none; border-radius: 3px;">
                            לקריאה וכתיבת ביקורת
                            </button>
                        `;
                    }
                    contentDiv.innerHTML = '<p style="color:green;">תודה! הביקורת נשמרה.</p>';
                    setTimeout(() => dialog.close(), 1500);
                } else {
                    alert('שגיאה: ' + data.data);
                }
            });
        });
    }

    document.getElementById('close-dialog-btn').addEventListener('click', () => dialog.close());
});
</script>

<?php
get_footer();
?>