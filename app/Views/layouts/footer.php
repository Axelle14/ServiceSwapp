<?php $base = APP_BASE; ?>
<footer class="site-footer">
  <div class="footer-inner">
    <div class="footer-brand">
      <img src="<?= $base ?>/img/logo.png" alt="SkillSwap" class="footer-logo">
      <p>Exchange skills, help each other.</p>
    </div>
    <div class="footer-links">
      <a href="<?= $base ?>/services">Browse</a>
      <a href="<?= $base ?>/register">Join Free</a>
      <a href="<?= $base ?>/subscriptions">Plans</a>
    </div>
    <p class="footer-copy">&copy; <?= date('Y') ?> SkillSwap. All rights reserved.</p>
  </div>
</footer>
<div class="toast" id="globalToast" role="status" aria-live="polite"></div>
<script src="<?= $base ?>/js/app.js"></script>
<?php if (!empty($loadGoogleMaps)): ?>
  <script>
    window.googleMapsApiKey = <?= json_encode(trim((string)App\Core\Env::get('GOOGLE_MAPS_API_KEY', ''))) ?>;
    <?php if (!empty($serviceFormData)): ?>
      window.serviceFormData = <?= json_encode($serviceFormData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
    <?php endif; ?>
    <?php if (!empty($serviceMapData)): ?>
      window.serviceMapData = <?= json_encode($serviceMapData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
    <?php endif; ?>
  </script>
  <script src="<?= $base ?>/js/maps.js"></script>
<?php endif; ?>
</body>
</html>
