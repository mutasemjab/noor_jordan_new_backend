<?php $__env->startSection('title', 'مدارس نور الأردن الدولية'); ?>

<?php $__env->startSection('content'); ?>


<section id="hero">
  <div class="hero-bg"></div>
  <div class="hero-img-overlay"></div>
  <canvas id="particles-canvas"></canvas>
  <div class="hero-slash"></div>

  <div class="hero-inner">
    <div class="hero-content">
      <div class="hero-badge"><?php echo e(app()->getLocale() === 'ar' ? 'منذ عام 1999 · تميّز وإبداع' : 'Since 1999 · Excellence & Innovation'); ?></div>
      <h1 class="hero-title">
        <?php echo e(app()->getLocale() === 'ar' ? 'مدارس' : 'Noor Jordan'); ?><br>
        <span class="accent-gold"><?php echo e(app()->getLocale() === 'ar' ? 'نور الأردن' : 'International'); ?></span><br>
        <span style="font-size:0.65em;font-weight:700;color:rgba(255,255,255,0.7)"><?php echo e(app()->getLocale() === 'ar' ? 'الدولية' : 'Schools'); ?></span>
      </h1>
      <p class="hero-sub">
        <?php echo e(app()->getLocale() === 'ar'
          ? 'نقدم تعليماً متميزاً يجمع بين الأصالة والحداثة، مدعوماً بمنظومة رقمية متكاملة تربط الطالب والمعلم وأولياء الأمور.'
          : 'Delivering distinguished education that blends heritage and modernity, supported by an integrated digital platform connecting students, teachers, and parents.'); ?>

      </p>
      <div class="hero-actions">
        <a href="#contact" class="btn-primary">
          <span><?php echo e(app()->getLocale() === 'ar' ? 'تواصل معنا' : 'Contact Us'); ?></span>
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>
        <a href="#about" class="btn-secondary">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polygon points="10 8 16 12 10 16 10 8" fill="currentColor" stroke="none"/></svg>
          <span><?php echo e(app()->getLocale() === 'ar' ? 'تعرّف علينا' : 'About Us'); ?></span>
        </a>
      </div>
      <div class="hero-stats">
        <div class="stat-item">
          <div class="stat-number" data-count="<?php echo e($stats['students']); ?>"><span>+</span>0</div>
          <div class="stat-label"><?php echo e(app()->getLocale() === 'ar' ? 'طالب مسجّل' : 'Enrolled Students'); ?></div>
        </div>
        <div class="stat-item">
          <div class="stat-number" data-count="<?php echo e($stats['teachers']); ?>">0</div>
          <div class="stat-label"><?php echo e(app()->getLocale() === 'ar' ? 'معلم متخصص' : 'Specialist Teachers'); ?></div>
        </div>
        <div class="stat-item">
          <div class="stat-number" data-count="<?php echo e(sett_raw('about_years') ?: 25); ?>"><span>+</span>0</div>
          <div class="stat-label"><?php echo e(app()->getLocale() === 'ar' ? 'عاماً من العطاء' : 'Years of Excellence'); ?></div>
        </div>
      </div>
    </div>

    <div class="hero-visual">
      <div class="hero-card-main reveal-right">
        <?php $heroImg = sett_raw('hero_image'); ?>
        <img src="<?php echo e($heroImg ? asset('assets/uploads/site/'.$heroImg) : 'https://images.unsplash.com/photo-1427504494785-3a9ca7044f45?w=600&q=80&auto=format'); ?>"
             alt="<?php echo e(app()->getLocale() === 'ar' ? 'بيئة تعليمية متميزة' : 'Distinguished Learning Environment'); ?>">
        <div class="hero-card-main-body">
          <div class="hero-card-badge"><?php echo e(app()->getLocale() === 'ar' ? 'العام الدراسي 2025–2026' : 'Academic Year 2025–2026'); ?></div>
          <h4><?php echo e(app()->getLocale() === 'ar' ? 'مسيرة نحو التفوق' : 'A Journey Toward Excellence'); ?></h4>
          <p><?php echo e(app()->getLocale() === 'ar' ? 'بيئة تعليمية آمنة ومحفّزة تُنمّي مواهب طلابنا وتُعدّهم لمستقبل واعد.' : 'A safe and stimulating educational environment that nurtures talent and prepares students for a bright future.'); ?></p>
        </div>
      </div>
      <div class="card-float-1">
        <div class="f-num">⭐ 4.9</div>
        <div class="f-label"><?php echo e(app()->getLocale() === 'ar' ? 'تقييم أولياء الأمور' : 'Parent Rating'); ?></div>
      </div>
      <div class="card-float-2">
        <div class="f-num">🏆 #1</div>
        <div class="f-label"><?php echo e(app()->getLocale() === 'ar' ? 'مدرسة رائدة في المنطقة' : 'Leading School in Region'); ?></div>
      </div>
    </div>
  </div>

  <div class="scroll-indicator">
    <div class="scroll-line"></div>
    <span>SCROLL</span>
  </div>
</section>


<div class="stats-band">
  <div class="stats-band-inner">
    <div class="stat-band-item reveal">
      <div class="stat-band-num" data-count="<?php echo e(sett_raw('about_years') ?: 25); ?>"><span>+</span>0</div>
      <div class="stat-band-label"><?php echo e(app()->getLocale() === 'ar' ? 'عاماً من التميّز' : 'Years of Excellence'); ?></div>
    </div>
    <div class="stat-band-item reveal">
      <div class="stat-band-num" data-count="<?php echo e($stats['students']); ?>"><span>+</span>0</div>
      <div class="stat-band-label"><?php echo e(app()->getLocale() === 'ar' ? 'طالب يثق بنا' : 'Students Trust Us'); ?></div>
    </div>
    <div class="stat-band-item reveal">
      <div class="stat-band-num" data-count="<?php echo e($stats['teachers']); ?>">0</div>
      <div class="stat-band-label"><?php echo e(app()->getLocale() === 'ar' ? 'كادر تعليمي متميز' : 'Specialist Teaching Staff'); ?></div>
    </div>
    <div class="stat-band-item reveal">
      <div class="stat-band-num" data-count="<?php echo e($stats['satisfaction']); ?>"><span>%</span>0</div>
      <div class="stat-band-label"><?php echo e(app()->getLocale() === 'ar' ? 'نسبة النجاح في التوجيهي' : 'Tawjihi Pass Rate'); ?></div>
    </div>
  </div>
</div>


<section id="features">
  <div class="features-inner">
    <div class="section-header reveal" style="text-align:center;">
      <div class="section-eyebrow" style="justify-content:center;">
        <?php echo e(app()->getLocale() === 'ar' ? 'خدماتنا' : 'Our Services'); ?>

      </div>
      <h2 class="section-title">
        <?php echo e(app()->getLocale() === 'ar' ? 'منظومة تعليمية متكاملة' : 'A Complete Educational System'); ?><br>
        <span class="text-gradient-accent"><?php echo e(app()->getLocale() === 'ar' ? 'في متناول يدك' : 'At Your Fingertips'); ?></span>
      </h2>
      <p class="section-sub" style="margin:0 auto;">
        <?php echo e(app()->getLocale() === 'ar'
          ? 'نوفر أدوات رقمية حديثة تجعل التجربة التعليمية أكثر وضوحاً وتفاعلاً لجميع أفراد الأسرة التعليمية.'
          : 'We provide modern digital tools that make the educational experience clearer and more interactive for all members of the school community.'); ?>

      </p>
    </div>

    <div class="features-grid">
      <div class="feature-card reveal">
        <div class="feature-icon">📅</div>
        <h3 class="feature-title"><?php echo e(app()->getLocale() === 'ar' ? 'جدول الحصص الرقمي' : 'Digital Class Schedule'); ?></h3>
        <p class="feature-desc"><?php echo e(app()->getLocale() === 'ar' ? 'يطّلع الطالب على جدول حصصه اليومي والأسبوعي بشكل فوري من خلال التطبيق، مع عرض المادة واسم المعلم والغرفة الدراسية.' : 'Students can instantly view their daily and weekly class schedule through the app, with subject name, teacher, and classroom.'); ?></p>
      </div>

      <div class="feature-card reveal">
        <div class="feature-icon">📊</div>
        <h3 class="feature-title"><?php echo e(app()->getLocale() === 'ar' ? 'متابعة العلامات والتقييم' : 'Grades & Assessment'); ?></h3>
        <p class="feature-desc"><?php echo e(app()->getLocale() === 'ar' ? 'تقارير علامات مفصّلة لكل مادة مع معدلات الأداء، تُتاح للطلاب وأولياء الأمور فور رصدها.' : 'Detailed grade reports for each subject with performance averages, made available to students and parents as soon as they are recorded.'); ?></p>
      </div>

      <div class="feature-card reveal">
        <div class="feature-icon">🎯</div>
        <h3 class="feature-title"><?php echo e(app()->getLocale() === 'ar' ? 'تسجيل الغيابات' : 'Attendance Tracking'); ?></h3>
        <p class="feature-desc"><?php echo e(app()->getLocale() === 'ar' ? 'نظام متابعة دقيق للحضور والغياب يومياً وبحسب الحصة، مع إشعارات فورية لأولياء الأمور عند الغياب.' : 'A precise daily and period-by-period attendance system with instant parent notifications upon absence.'); ?></p>
      </div>

      <div class="feature-card feature-card-accent reveal">
        <div class="feature-icon">📱</div>
        <h3 class="feature-title"><?php echo e(app()->getLocale() === 'ar' ? 'تطبيق الطالب والمعلم' : 'Student & Teacher App'); ?></h3>
        <p class="feature-desc"><?php echo e(app()->getLocale() === 'ar' ? 'تطبيق موحّد يتيح للطالب متابعة شؤونه الدراسية كاملة، وللمعلم تسجيل الغيابات والعلامات من أي مكان.' : 'A unified app that lets students track all academic affairs, and allows teachers to record attendance and grades from anywhere.'); ?></p>
      </div>

      <div class="feature-card reveal">
        <div class="feature-icon">🎬</div>
        <h3 class="feature-title"><?php echo e(app()->getLocale() === 'ar' ? 'فيديوهات تعليمية لكل مادة' : 'Educational Videos per Subject'); ?></h3>
        <p class="feature-desc"><?php echo e(app()->getLocale() === 'ar' ? 'مكتبة فيديوهات يوتيوب منظّمة لكل صف ومادة، يصل إليها الطالب من التطبيق لمراجعة الدروس في أي وقت.' : 'A library of YouTube videos organized by class and subject, accessible through the app for lesson review at any time.'); ?></p>
      </div>

      <div class="feature-card reveal">
        <div class="feature-icon">📢</div>
        <h3 class="feature-title"><?php echo e(app()->getLocale() === 'ar' ? 'الإعلانات والإشعارات' : 'Announcements & Notifications'); ?></h3>
        <p class="feature-desc"><?php echo e(app()->getLocale() === 'ar' ? 'قناة تواصل مباشرة بين الإدارة وأولياء الأمور والطلاب، تُرسل عبرها الإعلانات المدرسية والإشعارات الفورية.' : 'A direct communication channel between administration, parents, and students for school announcements and instant notifications.'); ?></p>
      </div>
    </div>
  </div>
</section>


<?php if(sett_raw('app_google_play') || sett_raw('app_store')): ?>
<div class="app-band">
  <div class="app-band-inner">
    <div class="app-band-content reveal-left">
      <div class="app-band-eyebrow"><?php echo e(app()->getLocale() === 'ar' ? 'التطبيق المدرسي' : 'School App'); ?></div>
      <h2 class="app-band-title">
        <?php echo e(app()->getLocale() === 'ar' ? 'كل شيء تحتاجه' : 'Everything You Need'); ?><br>
        <?php echo e(app()->getLocale() === 'ar' ? 'في تطبيق واحد' : 'In One App'); ?>

      </h2>
      <p class="app-band-desc">
        <?php echo e(app()->getLocale() === 'ar'
          ? 'حمّل تطبيق مدارس نور الأردن الدولية وتابع جدول الحصص والعلامات والغيابات والإعلانات من هاتفك مباشرة.'
          : 'Download the Noor Jordan International Schools app and track schedules, grades, attendance, and announcements directly from your phone.'); ?>

      </p>
      <div class="app-badges">
        <?php if(sett_raw('app_google_play')): ?>
        <a href="<?php echo e(sett_raw('app_google_play')); ?>" target="_blank" rel="noopener" class="app-badge-btn">
          <i class="bi bi-google-play"></i>
          <div>
            <small>GET IT ON</small>
            <span>Google Play</span>
          </div>
        </a>
        <?php endif; ?>
        <?php if(sett_raw('app_store')): ?>
        <a href="<?php echo e(sett_raw('app_store')); ?>" target="_blank" rel="noopener" class="app-badge-btn">
          <i class="bi bi-apple"></i>
          <div>
            <small>DOWNLOAD ON THE</small>
            <span>App Store</span>
          </div>
        </a>
        <?php endif; ?>
      </div>
    </div>
    <div class="app-mockups reveal-right">
      <div class="app-mock-card">
        <div class="app-mock-icon">📅</div>
        <div class="app-mock-title"><?php echo e(app()->getLocale() === 'ar' ? 'جدول الحصص' : 'Class Schedule'); ?></div>
        <div class="app-mock-sub"><?php echo e(app()->getLocale() === 'ar' ? 'يومي وأسبوعي' : 'Daily & weekly'); ?></div>
      </div>
      <div class="app-mock-card">
        <div class="app-mock-icon">📊</div>
        <div class="app-mock-title"><?php echo e(app()->getLocale() === 'ar' ? 'العلامات' : 'Grades'); ?></div>
        <div class="app-mock-sub"><?php echo e(app()->getLocale() === 'ar' ? 'بحسب المادة' : 'Per subject'); ?></div>
      </div>
      <div class="app-mock-card">
        <div class="app-mock-icon">✅</div>
        <div class="app-mock-title"><?php echo e(app()->getLocale() === 'ar' ? 'الحضور' : 'Attendance'); ?></div>
        <div class="app-mock-sub"><?php echo e(app()->getLocale() === 'ar' ? 'يومي ومفصّل' : 'Daily & detailed'); ?></div>
      </div>
      <div class="app-mock-card">
        <div class="app-mock-icon">🔔</div>
        <div class="app-mock-title"><?php echo e(app()->getLocale() === 'ar' ? 'الإشعارات' : 'Notifications'); ?></div>
        <div class="app-mock-sub"><?php echo e(app()->getLocale() === 'ar' ? 'فورية ومباشرة' : 'Instant & direct'); ?></div>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>


<section id="teachers">
  <div class="section">
    <div class="section-header reveal">
      <div class="section-eyebrow"><?php echo e(app()->getLocale() === 'ar' ? 'كادرنا التعليمي' : 'Our Teaching Staff'); ?></div>
      <h2 class="section-title">
        <?php echo e(app()->getLocale() === 'ar' ? 'معلمون يُلهمون' : 'Teachers Who Inspire'); ?><br>
        <span class="text-gradient-primary"><?php echo e(app()->getLocale() === 'ar' ? 'ويُغيّرون حياة طلابهم' : 'And Transform Students\' Lives'); ?></span>
      </h2>
      <p class="section-sub"><?php echo e(app()->getLocale() === 'ar' ? 'نخبة من المعلمين المؤهلين تأهيلاً عالياً والمتخصصين في مجالاتهم، يُقدّمون التعليم بأسلوب مبتكر وشغف حقيقي.' : 'A selection of highly qualified and specialized teachers who deliver education with an innovative approach and genuine passion.'); ?></p>
    </div>
    <div class="teachers-grid">
      <?php $__empty_1 = true; $__currentLoopData = $teachers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $teacher): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="teacher-card reveal">
          <div class="teacher-img-wrap">
            <?php if($teacher->avatar): ?>
              <img src="<?php echo e(asset('assets/uploads/'.$teacher->avatar)); ?>" alt="<?php echo e($teacher->name); ?>">
            <?php else: ?>
              <img src="https://images.unsplash.com/photo-1568602471122-7832951cc4c5?w=400&q=80&auto=format" alt="<?php echo e($teacher->name); ?>">
            <?php endif; ?>
            <div class="teacher-subject-badge"><?php echo e(strtoupper(substr($teacher->name, 0, 4))); ?></div>
          </div>
          <div class="teacher-card-body">
            <div class="teacher-name"><?php echo e($teacher->name); ?></div>
            <div class="teacher-role"><?php echo e(app()->getLocale() === 'ar' ? 'معلم متخصص' : 'Specialist Teacher'); ?></div>
            <div class="teacher-stats-row">
              <div class="t-stat">
                <div class="t-stat-num"><?php echo e($teacher->total_students ?? 0); ?><span>+</span></div>
                <div class="t-stat-label"><?php echo e(app()->getLocale() === 'ar' ? 'طالب' : 'Students'); ?></div>
              </div>
              <div class="t-stat">
                <div class="t-stat-num">97<span>%</span></div>
                <div class="t-stat-label"><?php echo e(app()->getLocale() === 'ar' ? 'نسبة النجاح' : 'Pass Rate'); ?></div>
              </div>
            </div>
          </div>
        </div>
   
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <p style="color:var(--text-muted)"><?php echo e(app()->getLocale() === 'ar' ? 'لا يوجد معلمون حالياً.' : 'No teachers available.'); ?></p>
      <?php endif; ?>
    </div>
  </div>
</section>


<section id="about">
  <div class="about-inner">
    <div class="about-grid">
      <div class="about-images reveal-left">
        <div class="about-img-main">
          <?php $imgMain = sett_raw('about_image_main'); ?>
          <img src="<?php echo e($imgMain ? asset('assets/uploads/site/'.$imgMain) : 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=600&q=80&auto=format'); ?>"
               alt="<?php echo e(app()->getLocale() === 'ar' ? 'مبنى المدرسة' : 'School Building'); ?>">
        </div>
        <div class="about-img-secondary">
          <?php $imgSec = sett_raw('about_image_secondary'); ?>
          <img src="<?php echo e($imgSec ? asset('assets/uploads/site/'.$imgSec) : 'https://images.unsplash.com/photo-1509062522246-3755977927d7?w=400&q=80&auto=format'); ?>"
               alt="<?php echo e(app()->getLocale() === 'ar' ? 'بيئة الفصل الدراسي' : 'Classroom Environment'); ?>">
        </div>
        <div class="about-badge">
          <div class="about-badge-num"><?php echo e(sett_raw('about_years') ?: '25'); ?>+</div>
          <div class="about-badge-text"><?php echo e(app()->getLocale() === 'ar' ? 'عاماً من العطاء' : 'Years of Service'); ?></div>
        </div>
      </div>

      <div class="reveal-right">
        <div class="about-section-eyebrow"><?php echo e(app()->getLocale() === 'ar' ? 'من نحن' : 'About Us'); ?></div>
        <h2 class="about-title"><?php echo e(sett('about_title') ?: (app()->getLocale() === 'ar' ? 'مدارس نور الأردن الدولية' : 'Noor Jordan International Schools')); ?></h2>
        <p class="about-desc"><?php echo e(sett('about_description') ?: (app()->getLocale() === 'ar' ? 'منذ تأسيسنا، ونحن نسعى لتقديم تعليم عالي الجودة يجمع بين الأصالة والحداثة. نؤمن بأن كل طالب يملك موهبة فريدة تستحق أن تُنمَّى في بيئة آمنة ومحفّزة تحت إشراف معلمين متميزين.' : 'Since our founding, we have been committed to delivering high-quality education that blends heritage and modernity. We believe every student has a unique talent that deserves to flourish in a safe and stimulating environment under the supervision of distinguished teachers.')); ?></p>
        <div class="about-values">
          <?php
            $valueIcons  = ['🎯','🔬','🤝','🌟'];
            $valueColors = ['vi-primary','vi-accent','vi-primary','vi-accent'];
            $valueTitles = app()->getLocale() === 'ar'
              ? ['رؤية تعليمية واضحة','منهج علمي رصين','شراكة مع الأهل','بيئة آمنة ومحفّزة']
              : ['Clear Educational Vision','Rigorous Academic Curriculum','Partnership With Parents','Safe & Stimulating Environment'];
            $valueDescs = app()->getLocale() === 'ar'
              ? ['نستشرف المستقبل ونُعدّ طلابنا لمتطلبات سوق العمل الحديث.','مناهج معتمدة ومحدّثة تواكب المعايير الدولية.','نؤمن بأهمية التواصل المستمر مع أولياء الأمور.','نوفر مساحة تعليمية تُشجّع على الإبداع والتفكير النقدي.']
              : ['We envision the future and prepare students for modern workforce demands.','Accredited and updated curricula aligned with international standards.','We believe in continuous communication with parents.','We provide a learning space that encourages creativity and critical thinking.'];
          ?>
          <?php $__currentLoopData = [0,1,2,3]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <div class="value-item">
            <div class="value-icon <?php echo e($valueColors[$i]); ?>"><?php echo e($valueIcons[$i]); ?></div>
            <div class="value-item-text">
              <h5><?php echo e(sett('about_value'.($i+1).'_title') ?: $valueTitles[$i]); ?></h5>
              <p><?php echo e(sett('about_value'.($i+1).'_desc') ?: $valueDescs[$i]); ?></p>
            </div>
          </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <a href="#contact" class="btn-primary">
          <span><?php echo e(app()->getLocale() === 'ar' ? 'تواصل معنا الآن' : 'Contact Us Now'); ?></span>
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>
      </div>
    </div>
  </div>
</section>


<section id="contact">
  <div class="contact-inner">
    <div class="section-header reveal" style="text-align:center; max-width:600px; margin: 0 auto 64px;">
      <div class="section-eyebrow" style="justify-content:center">
        <?php echo e(app()->getLocale() === 'ar' ? 'تواصل معنا' : 'Get In Touch'); ?>

      </div>
      <h2 class="section-title">
        <?php echo e(app()->getLocale() === 'ar' ? 'هل لديك استفسار؟' : 'Have a Question?'); ?><br>
        <span class="text-gradient-accent"><?php echo e(app()->getLocale() === 'ar' ? 'نحن هنا للمساعدة' : 'We Are Here To Help'); ?></span>
      </h2>
      <p class="section-sub" style="margin: 0 auto;">
        <?php echo e(app()->getLocale() === 'ar' ? 'سواء كنت تريد الاستفسار عن التسجيل، المناهج، أو الخدمات التي نقدمها — تواصل معنا وسيرد عليك فريقنا في أقرب وقت.' : 'Whether you are inquiring about enrollment, curriculum, or our services — reach out and our team will respond as soon as possible.'); ?>

      </p>
    </div>
    <div class="contact-grid">
      <div>
        <div class="section-eyebrow"><?php echo e(app()->getLocale() === 'ar' ? 'معلومات التواصل' : 'Contact Information'); ?></div>
        <h3 style="font-size:26px; font-weight:800; color:var(--primary); margin-bottom:8px; line-height:1.2;">
          <?php echo e(app()->getLocale() === 'ar' ? 'تواصل معنا بسهولة' : 'Reach Us Easily'); ?><br>
          <span style="font-size:0.8em;font-weight:400;color:var(--text-muted)"><?php echo e(app()->getLocale() === 'ar' ? 'عبر أي قناة تفضّلها' : 'Through Any Channel You Prefer'); ?></span>
        </h3>
        <div class="contact-info-card">
          <div class="contact-info-item">
            <div class="ci-icon">📍</div>
            <div class="ci-text">
              <h5><?php echo e(app()->getLocale() === 'ar' ? 'العنوان' : 'Address'); ?></h5>
              <p><?php echo e(sett('contact_address') ?: (app()->getLocale() === 'ar' ? 'عمّان، المملكة الأردنية الهاشمية' : 'Amman, Hashemite Kingdom of Jordan')); ?></p>
            </div>
          </div>
          <div class="contact-info-item">
            <div class="ci-icon">📞</div>
            <div class="ci-text">
              <h5><?php echo e(app()->getLocale() === 'ar' ? 'الهاتف' : 'Phone'); ?></h5>
              <p><?php echo e(sett_raw('contact_phone') ?: '+962 6 XXX XXXX'); ?></p>
            </div>
          </div>
          <div class="contact-info-item">
            <div class="ci-icon">📧</div>
            <div class="ci-text">
              <h5><?php echo e(app()->getLocale() === 'ar' ? 'البريد الإلكتروني' : 'Email'); ?></h5>
              <p><?php echo e(sett_raw('contact_email') ?: 'info@noor-jordan.com'); ?></p>
            </div>
          </div>
          <div class="contact-info-item">
            <div class="ci-icon">🕐</div>
            <div class="ci-text">
              <h5><?php echo e(app()->getLocale() === 'ar' ? 'ساعات العمل' : 'Working Hours'); ?></h5>
              <p><?php echo e(sett('contact_hours') ?: (app()->getLocale() === 'ar' ? 'الأحد – الخميس: 7:30 ص – 3:00 م' : 'Sun – Thu: 7:30 AM – 3:00 PM')); ?></p>
            </div>
          </div>
          <?php if(sett_raw('contact_whatsapp')): ?>
          <div class="contact-info-item">
            <div class="ci-icon">💬</div>
            <div class="ci-text">
              <h5>WhatsApp</h5>
              <p>
                <a href="https://wa.me/<?php echo e(preg_replace('/[^0-9]/', '', sett_raw('contact_whatsapp'))); ?>"
                   target="_blank" style="color:white;text-decoration:none;">
                  <?php echo e(sett_raw('contact_whatsapp')); ?>

                </a>
              </p>
            </div>
          </div>
          <?php endif; ?>
        </div>

        
        <?php
          $socials = [
            'social_facebook'  => ['bi-facebook',  'Facebook'],
            'social_instagram' => ['bi-instagram', 'Instagram'],
            'social_youtube'   => ['bi-youtube',   'YouTube'],
            'social_twitter'   => ['bi-twitter-x', 'Twitter'],
            'social_tiktok'    => ['bi-tiktok',    'TikTok'],
            'social_snapchat'  => ['bi-snapchat',  'Snapchat'],
            'social_whatsapp'  => ['bi-whatsapp',  'WhatsApp'],
          ];
          $hasSocial = collect($socials)->keys()->filter(fn($k) => sett_raw($k))->isNotEmpty();
        ?>
        <?php if($hasSocial): ?>
        <div class="d-flex gap-2 flex-wrap mt-4">
          <?php $__currentLoopData = $socials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => [$icon, $label]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(sett_raw($key)): ?>
              <a href="<?php echo e(sett_raw($key)); ?>" target="_blank" rel="noopener" title="<?php echo e($label); ?>"
                 style="display:inline-flex;align-items:center;justify-content:center;width:40px;height:40px;border-radius:50%;background:var(--primary);color:#fff;font-size:18px;text-decoration:none;border:1px solid rgba(244,174,45,0.3);transition:all 0.2s"
                 onmouseover="this.style.background='var(--accent)';this.style.color='var(--primary)'"
                 onmouseout="this.style.background='var(--primary)';this.style.color='#fff'">
                <i class="bi <?php echo e($icon); ?>"></i>
              </a>
            <?php endif; ?>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endif; ?>
      </div>

      <div class="contact-form reveal-right">
        <h4 style="font-size:22px; font-weight:800; color:var(--primary); margin-bottom:28px;">
          <?php echo e(app()->getLocale() === 'ar' ? 'أرسل لنا رسالة' : 'Send Us a Message'); ?>

        </h4>

        <?php if(session('contact_success')): ?>
          <div style="background:#d1fae5;color:#065f46;padding:14px 18px;border-radius:8px;margin-bottom:20px;font-weight:600;">
            <?php echo e(app()->getLocale() === 'ar' ? 'شكراً! تم إرسال رسالتك بنجاح. سنرد عليك في أقرب وقت.' : 'Thank you! Your message has been sent. We will reply soon.'); ?>

          </div>
        <?php endif; ?>

        <form action="<?php echo e(route('contact.store')); ?>" method="POST">
          <?php echo csrf_field(); ?>
          <div class="form-row">
            <div class="form-group">
              <label><?php echo e(app()->getLocale() === 'ar' ? 'الاسم الكامل' : 'Full Name'); ?></label>
              <input type="text" name="name" value="<?php echo e(old('name')); ?>"
                     placeholder="<?php echo e(app()->getLocale() === 'ar' ? 'أدخل اسمك الكامل' : 'Enter your full name'); ?>" required>
              <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span style="color:red;font-size:12px"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="form-group">
              <label><?php echo e(app()->getLocale() === 'ar' ? 'رقم الهاتف' : 'Phone Number'); ?></label>
              <input type="tel" name="phone" value="<?php echo e(old('phone')); ?>"
                     placeholder="<?php echo e(app()->getLocale() === 'ar' ? '+962 7X XXX XXXX' : '+962 7X XXX XXXX'); ?>">
            </div>
          </div>
          <div class="form-group" style="margin-bottom:18px;">
            <label><?php echo e(app()->getLocale() === 'ar' ? 'البريد الإلكتروني' : 'Email Address'); ?></label>
            <input type="email" name="email" value="<?php echo e(old('email')); ?>"
                   placeholder="email@example.com" required>
            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span style="color:red;font-size:12px"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          </div>
          <div class="form-group" style="margin-bottom:18px;">
            <label><?php echo e(app()->getLocale() === 'ar' ? 'موضوع الرسالة' : 'Subject'); ?></label>
            <select name="subject">
              <option value=""><?php echo e(app()->getLocale() === 'ar' ? 'اختر الموضوع' : 'Select a subject'); ?></option>
              <option value="<?php echo e(app()->getLocale() === 'ar' ? 'استفسار عن التسجيل' : 'Enrollment Inquiry'); ?>"><?php echo e(app()->getLocale() === 'ar' ? 'استفسار عن التسجيل' : 'Enrollment Inquiry'); ?></option>
              <option value="<?php echo e(app()->getLocale() === 'ar' ? 'المناهج والخدمات' : 'Curriculum & Services'); ?>"><?php echo e(app()->getLocale() === 'ar' ? 'المناهج والخدمات' : 'Curriculum & Services'); ?></option>
              <option value="<?php echo e(app()->getLocale() === 'ar' ? 'الرسوم الدراسية' : 'Tuition Fees'); ?>"><?php echo e(app()->getLocale() === 'ar' ? 'الرسوم الدراسية' : 'Tuition Fees'); ?></option>
              <option value="<?php echo e(app()->getLocale() === 'ar' ? 'شكوى أو اقتراح' : 'Complaint or Suggestion'); ?>"><?php echo e(app()->getLocale() === 'ar' ? 'شكوى أو اقتراح' : 'Complaint or Suggestion'); ?></option>
              <option value="<?php echo e(app()->getLocale() === 'ar' ? 'أخرى' : 'Other'); ?>"><?php echo e(app()->getLocale() === 'ar' ? 'أخرى' : 'Other'); ?></option>
            </select>
          </div>
          <div class="form-group" style="margin-bottom:24px;">
            <label><?php echo e(app()->getLocale() === 'ar' ? 'نص الرسالة' : 'Message'); ?></label>
            <textarea name="message" placeholder="<?php echo e(app()->getLocale() === 'ar' ? 'اكتب رسالتك هنا...' : 'Write your message here...'); ?>" required><?php echo e(old('message')); ?></textarea>
            <?php $__errorArgs = ['message'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span style="color:red;font-size:12px"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          </div>
          <div class="form-submit">
            <button type="submit" class="btn-primary" style="flex:1; justify-content:center;">
              <span><?php echo e(app()->getLocale() === 'ar' ? 'إرسال الرسالة' : 'Send Message'); ?></span>
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
            </button>
          </div>
          <p class="form-note" style="margin-top:14px;">
            <?php echo e(app()->getLocale() === 'ar' ? 'سيتم الرد على رسالتك خلال 24 ساعة عمل.' : 'We will respond to your message within 24 business hours.'); ?>

          </p>
        </form>
      </div>
    </div>
  </div>
</section>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.front', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\noor_jordan_new_backend\resources\views/front/home.blade.php ENDPATH**/ ?>