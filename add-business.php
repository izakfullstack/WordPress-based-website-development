<?php 
require_once('wp-load.php'); 
get_header(); 
?>
<body>
    <main id="tofes-client" class="site-main">
        <form class="main-client" method="post" enctype="multipart/form-data">
            <legend>הוסף עסק :</legend>
            <?php wp_nonce_field('submit_free', 'free_nonce'); ?>
            
            <input class="client-content" type="text" pattern="[a-zA-Zא-ת ]{1,15}" name="business_title" placeholder="שם העסק" required>
            <input class="client-content" type="text" pattern="[a-zA-Zא-ת1-9 ]{1,20}" name="business_address" placeholder="כתובת" />
            
            <select class="client-content" pattern="[א-ת]{1,15}" name="city_select" onchange="toggleInput(this, 'city_text_input')" required>
                <option value="" class="client-content option">בחר עיר</option>
                <?php foreach(get_option('site_cities', []) as $c) echo "<option value='".esc_attr($c)."'>".esc_html($c)."</option>"; ?>
                <option value="add_new" class="client-content option">+ הוסף עיר</option>
            </select>
            <input class="client-content" type="text" id="city_text_input" name="business_city" style="display:none;" placeholder="הקלד עיר חדשה">

            <select class="client-content" pattern="[א-ת]{1,15}" name="cat_select" onchange="toggleInput(this, 'cat_text_input'); loadSubcats(this.value)" required>
                <option value="" class="client-content option">בחר קטגוריה</option>
                <?php foreach(array_keys(get_option('site_cats_map', [])) as $cat) echo "<option value='".esc_attr($cat)."'>".esc_html($cat)."</option>"; ?>
                <option value="add_new" class="client-content option" pattern="[א-ת]{1,15}">+ הוסף קטגוריה</option>
            </select>
            <input class="client-content" type="text" id="cat_text_input" name="business_cat" style="display:none;" placeholder="הקלד קטגוריה חדשה">

            <select class="client-content" pattern="[א-ת]{1,15}" name="subcat_select" id="subcat_select" onchange="toggleInput(this, 'subcat_text_input')" required>
                <option value="" class="client-content option">בחר תת קטגוריה</option>
                <option value="add_new" class="client-content option" pattern="[א-ת]{1,15}">+ הוסף תת קטגוריה</option>
            </select>
            <input class="client-content" type="text" id="subcat_text_input" name="business_subcat" style="display:none;" placeholder="הקלד תת קטגוריה חדשה">
            
            <input class="client-content" type="email" pattern="[a-zA-z0-9@.]{1,20}" name="business_email" placeholder="מייל של בית העסק" />
            <input dir="rtl" class="client-content" type="tel" name="business_phone" placeholder="טלפון של בית העסק" />
            <textarea class="client-content textarea" pattern="[א-ת ]{1,50}" name="business_desc" size="25" placeholder="תיאור קצר" required></textarea>
            
            <button id="submit-btn" class="link-black" type="submit">שלח</button>
        </form>
    </main>
<script>
    var catsMap = <?php echo json_encode(get_option('site_cats_map', [])); ?>;

    function toggleInput(s, id) { 
        var input = document.getElementById(id);
        if(s.value === 'add_new') {
            input.style.display = 'block';
            input.required = true;
        } else {
            input.style.display = 'none';
            input.required = false;
        }
    }

    function loadSubcats(cat) {
        var subSelect = document.getElementById('subcat_select');
        subSelect.innerHTML = '<option value="">בחר תת קטגוריה</option>';
        
        if(cat && catsMap[cat]) {
            catsMap[cat].forEach(function(v) {
                var opt = document.createElement('option');
                opt.value = v;
                opt.textContent = v;
                subSelect.appendChild(opt);
            });
        }
        
        var addOpt = document.createElement('option');
        addOpt.value = 'add_new';
        addOpt.textContent = '+ הוסף תת קטגוריה';
        subSelect.appendChild(addOpt);
    }
</script>
<?php get_footer(); ?>