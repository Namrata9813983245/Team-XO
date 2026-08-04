</main>
<footer class="site-footer" id="contact-us">
  <div class="footer-inner">
    <div class="footer-col footer-brand">
      <div class="brand"><span class="brand-mark">🌾</span> <span class="brand-name"><?= e(get_setting('site_name','AgroSense')) ?></span></div>
      <p><?= e(get_setting('tagline','')) ?></p>
    </div>
    <div class="footer-col">
      <h4>Contact Us</h4>
      <ul class="footer-contact">
        <li>✉️ <?= e(get_setting('contact_email')) ?></li>
        <li>📞 <?= e(get_setting('contact_phone')) ?></li>
        <li>📍 <?= e(get_setting('contact_address')) ?></li>
      </ul>
    </div>
    <div class="footer-col">
      <h4>Send a Message</h4>
      <form class="footer-contact-form" method="POST" action="<?= base_url('contact.php') ?>">
        <input type="text" name="name" placeholder="Your name" required>
        <input type="email" name="email" placeholder="Your email" required>
        <textarea name="message" placeholder="Your message" rows="3" required></textarea>
        <button type="submit">Send Message</button>
      </form>
    </div>
    <div class="footer-col">
      <h4>Explore</h4>
      <ul class="footer-links">
        <li><a href="<?= base_url('index.php') ?>">Home</a></li>
        <li><a href="<?= base_url('about.php') ?>">About Us</a></li>
        <li><a href="<?= base_url('login.php') ?>">Log in</a></li>
        <li><a href="<?= base_url('register.php') ?>">Register</a></li>
      </ul>
    </div>
  </div>
  <div class="footer-bottom">© <?= date('Y') ?> <?= e(get_setting('site_name','AgroSense')) ?>. Built for smarter, data-driven farming.</div>
</footer>
</body>
</html>
