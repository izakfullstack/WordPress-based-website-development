<?php
/**
 * The template for displaying the footer
 */
?>

  <footer>
    <section class="footer-top">
      <pre><p class="footer-top-p">מחפשים איש מקצוע? עסק? לא יודעים מהיכן להתחיל?
אנחנו כאן כדי לעשות לכם סדר, ב"העסקן החרדי" ריכזנו עבורכם
לוח עסקים ואנשי מקצוע נגיש ונוח, באמצעות סינון חכם או
חיפוש חופשי, תוכלו למצוא בקלות עסק או בעל המקצוע באזורכם, 
ולקרוא ביקורות מלקוחות קודמים,
הכול כדי שתוכלו לקבל החלטה מושכלת בראש שקט!
שלכם העסקן החרדי.</p></pre>
      <hr class="footer-divider" />
    </section>
    <section class="footer-links"></section>
      <?php
      wp_nav_menu( array(
          'theme_location' => 'menu',
          'container'      => false,
          'menu_class'     => 'footer-links-container',
          'fallback_cb'    => false 
      ) );
      ?>
    </section>
    <section class="footer-bottom">
      <p dir="rtl" class="footer-bottom-p">עיצוב ופיתוח WEB-ir<br /><a class="footer-bottom-link" href="mailto:ywtmspr@gmail.com">ywtmspr@gmail.com</a><br /><?php echo date('Y'); ?> &#169;</p>
    </section>
  </footer>
  <script>
    document.addEventListener('DOMContentLoaded', function () {

    if (window.innerWidth > 700) return;

    const items = document.querySelectorAll('.item-menu, .sub-item-wrapper');

    items.forEach(item => {

        const dropdown = item.querySelector('.dropdown, .sub-dropdown');

        if (!dropdown) return;

        const btn = document.createElement('button');
        btn.className = 'submenu-toggle';
        btn.type = 'button';
        btn.innerHTML = '';

        item.insertBefore(btn, dropdown);

        btn.addEventListener('click', function (e) {
            e.preventDefault();
            item.classList.toggle('open');
        });
    });

});
  </script>
  </body>
</html>