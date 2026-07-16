<footer>
  <div class="footer-inner">
    <div class="footer-grid">

      
      <div class="footer-brand">
        <div class="footer-logo">
          <div class="footer-logo-icon">ن</div>
          <div class="footer-logo-text">
            <strong><?php echo e(app()->getLocale() === 'ar' ? 'مدارس نور الأردن الدولية' : 'Noor Jordan International Schools'); ?></strong>
            <span><?php echo e(app()->getLocale() === 'ar' ? 'تميّز وإبداع منذ 1999' : 'Excellence Since 1999'); ?></span>
          </div>
        </div>
        <p class="footer-desc">
          <?php echo e(app()->getLocale() === 'ar'
            ? 'نقدم تعليماً متميزاً يجمع بين الأصالة والحداثة، مدعوماً بمنظومة رقمية متكاملة تخدم الطالب والمعلم وولي الأمر.'
            : 'Delivering distinguished education blending heritage and modernity, supported by an integrated digital platform serving students, teachers, and parents.'); ?>

        </p>

        
        <?php
          $footerSocials = [
            'social_facebook'  => ['bi-facebook',  'Facebook'],
            'social_instagram' => ['bi-instagram', 'Instagram'],
            'social_youtube'   => ['bi-youtube',   'YouTube'],
            'social_twitter'   => ['bi-twitter-x', 'Twitter'],
            'social_tiktok'    => ['bi-tiktok',    'TikTok'],
            'social_snapchat'  => ['bi-snapchat',  'Snapchat'],
            'social_whatsapp'  => ['bi-whatsapp',  'WhatsApp'],
          ];
          $hasAnySocial = collect($footerSocials)->keys()->filter(fn($k) => sett_raw($k))->isNotEmpty();
        ?>
        <div class="footer-socials">
          <?php if($hasAnySocial): ?>
            <?php $__currentLoopData = $footerSocials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => [$icon, $label]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <?php if(sett_raw($key)): ?>
                <a href="<?php echo e(sett_raw($key)); ?>" class="footer-social" target="_blank" rel="noopener" title="<?php echo e($label); ?>">
                  <i class="bi <?php echo e($icon); ?>"></i>
                </a>
              <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          <?php else: ?>
            <a href="#" class="footer-social">𝕏</a>
            <a href="#" class="footer-social">f</a>
            <a href="#" class="footer-social">▶</a>
          <?php endif; ?>
        </div>

        
        <?php if(sett_raw('app_google_play') || sett_raw('app_store')): ?>
        <div class="d-flex gap-2 flex-wrap mt-3">
          <?php if(sett_raw('app_google_play')): ?>
          <a href="<?php echo e(sett_raw('app_google_play')); ?>" target="_blank" rel="noopener"
             style="display:inline-flex;align-items:center;gap:6px;padding:6px 12px;background:rgba(255,255,255,0.08);color:rgba(255,255,255,0.8);border-radius:8px;text-decoration:none;font-size:12px;border:1px solid rgba(255,255,255,0.15);">
            <i class="bi bi-google-play" style="font-size:16px"></i>
            <div style="line-height:1.1"><small style="opacity:.7;font-size:9px;display:block">GET IT ON</small>Google Play</div>
          </a>
          <?php endif; ?>
          <?php if(sett_raw('app_store')): ?>
          <a href="<?php echo e(sett_raw('app_store')); ?>" target="_blank" rel="noopener"
             style="display:inline-flex;align-items:center;gap:6px;padding:6px 12px;background:rgba(255,255,255,0.08);color:rgba(255,255,255,0.8);border-radius:8px;text-decoration:none;font-size:12px;border:1px solid rgba(255,255,255,0.15);">
            <i class="bi bi-apple" style="font-size:16px"></i>
            <div style="line-height:1.1"><small style="opacity:.7;font-size:9px;display:block">DOWNLOAD ON THE</small>App Store</div>
          </a>
          <?php endif; ?>
        </div>
        <?php endif; ?>
      </div>

      
      <div class="footer-col">
        <h4><?php echo e(app()->getLocale() === 'ar' ? 'خدماتنا' : 'Our Services'); ?></h4>
        <ul>
          <li><a href="<?php echo e(route('home')); ?>#features"><?php echo e(app()->getLocale() === 'ar' ? 'جدول الحصص الرقمي' : 'Digital Schedule'); ?></a></li>
          <li><a href="<?php echo e(route('home')); ?>#features"><?php echo e(app()->getLocale() === 'ar' ? 'متابعة العلامات' : 'Grade Tracking'); ?></a></li>
          <li><a href="<?php echo e(route('home')); ?>#features"><?php echo e(app()->getLocale() === 'ar' ? 'تسجيل الغيابات' : 'Attendance System'); ?></a></li>
          <li><a href="<?php echo e(route('home')); ?>#features"><?php echo e(app()->getLocale() === 'ar' ? 'فيديوهات تعليمية' : 'Educational Videos'); ?></a></li>
          <li><a href="<?php echo e(route('home')); ?>#features"><?php echo e(app()->getLocale() === 'ar' ? 'تطبيق الطالب' : 'Student App'); ?></a></li>
        </ul>
      </div>

      
      <div class="footer-col">
        <h4><?php echo e(app()->getLocale() === 'ar' ? 'روابط سريعة' : 'Quick Links'); ?></h4>
        <ul>
          <li><a href="<?php echo e(route('home')); ?>#about"><?php echo e(app()->getLocale() === 'ar' ? 'من نحن' : 'About Us'); ?></a></li>
          <li><a href="<?php echo e(route('home')); ?>#teachers"><?php echo e(app()->getLocale() === 'ar' ? 'معلمونا' : 'Our Teachers'); ?></a></li>
          <li><a href="<?php echo e(route('home')); ?>#contact"><?php echo e(app()->getLocale() === 'ar' ? 'تواصل معنا' : 'Contact Us'); ?></a></li>
          <li><a href="<?php echo e(route('student.login')); ?>"><?php echo e(app()->getLocale() === 'ar' ? 'دخول الطالب' : 'Student Login'); ?></a></li>
        </ul>
      </div>

      
      <div class="footer-col">
        <h4><?php echo e(app()->getLocale() === 'ar' ? 'معلومات التواصل' : 'Contact Info'); ?></h4>
        <ul>
          <?php if(sett('contact_address')): ?>
            <li><a href="#">📍 <?php echo e(sett('contact_address')); ?></a></li>
          <?php else: ?>
            <li><a href="#">📍 <?php echo e(app()->getLocale() === 'ar' ? 'عمّان، الأردن' : 'Amman, Jordan'); ?></a></li>
          <?php endif; ?>
          <?php if(sett_raw('contact_phone')): ?>
            <li><a href="tel:<?php echo e(preg_replace('/[^0-9+]/', '', sett_raw('contact_phone'))); ?>">📞 <?php echo e(sett_raw('contact_phone')); ?></a></li>
          <?php else: ?>
            <li><a href="#">📞 +962 6 XXX XXXX</a></li>
          <?php endif; ?>
          <?php if(sett_raw('contact_email')): ?>
            <li><a href="mailto:<?php echo e(sett_raw('contact_email')); ?>">📧 <?php echo e(sett_raw('contact_email')); ?></a></li>
          <?php else: ?>
            <li><a href="#">📧 info@noor-jordan.com</a></li>
          <?php endif; ?>
          <?php if(sett_raw('contact_whatsapp')): ?>
            <li><a href="https://wa.me/<?php echo e(preg_replace('/[^0-9]/', '', sett_raw('contact_whatsapp'))); ?>" target="_blank">💬 WhatsApp</a></li>
          <?php endif; ?>
        </ul>
      </div>

    </div>

    <div class="footer-bottom">
      <p class="footer-copy">
        &copy; <?php echo e(date('Y')); ?> <?php echo e(app()->getLocale() === 'ar' ? 'مدارس نور الأردن الدولية. جميع الحقوق محفوظة.' : 'Noor Jordan International Schools. All rights reserved.'); ?>

      </p>
      <div class="footer-bottom-links">
        <a href="#"><?php echo e(app()->getLocale() === 'ar' ? 'الشروط والأحكام' : 'Terms'); ?></a>
        <a href="#"><?php echo e(app()->getLocale() === 'ar' ? 'سياسة الخصوصية' : 'Privacy'); ?></a>
        <?php $__currentLoopData = LaravelLocalization::getSupportedLocales(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $localeCode => $properties): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php if($localeCode !== app()->getLocale()): ?>
            <a href="<?php echo e(LaravelLocalization::getLocalizedURL($localeCode, null, [], true)); ?>">
              <?php echo e($localeCode === 'ar' ? 'العربية' : 'English'); ?>

            </a>
          <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
    </div>
  </div>
</footer>
<?php /**PATH C:\xampp\htdocs\noor_jordan_new_backend\resources\views/front/includes/footer.blade.php ENDPATH**/ ?>