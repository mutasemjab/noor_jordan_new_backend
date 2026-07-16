<nav id="navbar">
  <div class="nav-logo">
    <a href="<?php echo e(route('home')); ?>" style="display:flex;align-items:center;gap:10px;text-decoration:none;color:inherit;">
      <div class="nav-logo-icon">ن</div>
      <div class="nav-logo-text">
        <strong><?php echo e(app()->getLocale() === 'ar' ? 'مدارس نور الأردن الدولية' : 'Noor Jordan International Schools'); ?></strong>
        <span><?php echo e(app()->getLocale() === 'ar' ? 'تميّز وإبداع منذ 1999' : 'Excellence Since 1999'); ?></span>
      </div>
    </a>
  </div>

  <ul class="nav-links">
    <li><a href="<?php echo e(route('home')); ?>#features"><?php echo e(app()->getLocale() === 'ar' ? 'خدماتنا' : 'Services'); ?></a></li>
    <li><a href="<?php echo e(route('home')); ?>#teachers"><?php echo e(app()->getLocale() === 'ar' ? 'معلمونا' : 'Teachers'); ?></a></li>
    <li><a href="<?php echo e(route('home')); ?>#about"><?php echo e(app()->getLocale() === 'ar' ? 'من نحن' : 'About'); ?></a></li>
    <li><a href="<?php echo e(route('home')); ?>#contact" class="nav-cta"><?php echo e(app()->getLocale() === 'ar' ? 'تواصل معنا' : 'Contact Us'); ?></a></li>
  </ul>

  <div class="nav-actions">
    
    <?php $__currentLoopData = LaravelLocalization::getSupportedLocales(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $localeCode => $properties): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <?php if($localeCode !== app()->getLocale()): ?>
        <a href="<?php echo e(LaravelLocalization::getLocalizedURL($localeCode, null, [], true)); ?>"
           class="nav-lang-switch"
           title="<?php echo e($properties['native']); ?>">
          <?php echo e(strtoupper($localeCode)); ?>

        </a>
      <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>

  <div class="hamburger"><span></span><span></span><span></span></div>
</nav>
<?php /**PATH C:\xampp\htdocs\noor_jordan_new_backend\resources\views/front/includes/navbar.blade.php ENDPATH**/ ?>