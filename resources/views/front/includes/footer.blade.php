<footer>
  <div class="footer-inner">
    <div class="footer-grid">

      {{-- Brand --}}
      <div class="footer-brand">
        <div class="footer-logo">
          <div class="footer-logo-icon">ن</div>
          <div class="footer-logo-text">
            <strong>{{ app()->getLocale() === 'ar' ? 'مدارس نور الأردن الدولية' : 'Noor Jordan International Schools' }}</strong>
            <span>{{ app()->getLocale() === 'ar' ? 'تميّز وإبداع منذ 1999' : 'Excellence Since 1999' }}</span>
          </div>
        </div>
        <p class="footer-desc">
          {{ app()->getLocale() === 'ar'
            ? 'نقدم تعليماً متميزاً يجمع بين الأصالة والحداثة، مدعوماً بمنظومة رقمية متكاملة تخدم الطالب والمعلم وولي الأمر.'
            : 'Delivering distinguished education blending heritage and modernity, supported by an integrated digital platform serving students, teachers, and parents.' }}
        </p>

        {{-- Social icons --}}
        @php
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
        @endphp
        <div class="footer-socials">
          @if($hasAnySocial)
            @foreach($footerSocials as $key => [$icon, $label])
              @if(sett_raw($key))
                <a href="{{ sett_raw($key) }}" class="footer-social" target="_blank" rel="noopener" title="{{ $label }}">
                  <i class="bi {{ $icon }}"></i>
                </a>
              @endif
            @endforeach
          @else
            <a href="#" class="footer-social">𝕏</a>
            <a href="#" class="footer-social">f</a>
            <a href="#" class="footer-social">▶</a>
          @endif
        </div>

        {{-- App store buttons --}}
        @if(sett_raw('app_google_play') || sett_raw('app_store'))
        <div class="d-flex gap-2 flex-wrap mt-3">
          @if(sett_raw('app_google_play'))
          <a href="{{ sett_raw('app_google_play') }}" target="_blank" rel="noopener"
             style="display:inline-flex;align-items:center;gap:6px;padding:6px 12px;background:rgba(255,255,255,0.08);color:rgba(255,255,255,0.8);border-radius:8px;text-decoration:none;font-size:12px;border:1px solid rgba(255,255,255,0.15);">
            <i class="bi bi-google-play" style="font-size:16px"></i>
            <div style="line-height:1.1"><small style="opacity:.7;font-size:9px;display:block">GET IT ON</small>Google Play</div>
          </a>
          @endif
          @if(sett_raw('app_store'))
          <a href="{{ sett_raw('app_store') }}" target="_blank" rel="noopener"
             style="display:inline-flex;align-items:center;gap:6px;padding:6px 12px;background:rgba(255,255,255,0.08);color:rgba(255,255,255,0.8);border-radius:8px;text-decoration:none;font-size:12px;border:1px solid rgba(255,255,255,0.15);">
            <i class="bi bi-apple" style="font-size:16px"></i>
            <div style="line-height:1.1"><small style="opacity:.7;font-size:9px;display:block">DOWNLOAD ON THE</small>App Store</div>
          </a>
          @endif
        </div>
        @endif
      </div>

      {{-- Services --}}
      <div class="footer-col">
        <h4>{{ app()->getLocale() === 'ar' ? 'خدماتنا' : 'Our Services' }}</h4>
        <ul>
          <li><a href="{{ route('home') }}#features">{{ app()->getLocale() === 'ar' ? 'جدول الحصص الرقمي' : 'Digital Schedule' }}</a></li>
          <li><a href="{{ route('home') }}#features">{{ app()->getLocale() === 'ar' ? 'متابعة العلامات' : 'Grade Tracking' }}</a></li>
          <li><a href="{{ route('home') }}#features">{{ app()->getLocale() === 'ar' ? 'تسجيل الغيابات' : 'Attendance System' }}</a></li>
          <li><a href="{{ route('home') }}#features">{{ app()->getLocale() === 'ar' ? 'فيديوهات تعليمية' : 'Educational Videos' }}</a></li>
          <li><a href="{{ route('home') }}#features">{{ app()->getLocale() === 'ar' ? 'تطبيق الطالب' : 'Student App' }}</a></li>
        </ul>
      </div>

      {{-- Quick links --}}
      <div class="footer-col">
        <h4>{{ app()->getLocale() === 'ar' ? 'روابط سريعة' : 'Quick Links' }}</h4>
        <ul>
          <li><a href="{{ route('home') }}#about">{{ app()->getLocale() === 'ar' ? 'من نحن' : 'About Us' }}</a></li>
          <li><a href="{{ route('home') }}#teachers">{{ app()->getLocale() === 'ar' ? 'معلمونا' : 'Our Teachers' }}</a></li>
          <li><a href="{{ route('home') }}#contact">{{ app()->getLocale() === 'ar' ? 'تواصل معنا' : 'Contact Us' }}</a></li>
          <li><a href="{{ route('student.login') }}">{{ app()->getLocale() === 'ar' ? 'دخول الطالب' : 'Student Login' }}</a></li>
        </ul>
      </div>

      {{-- Contact --}}
      <div class="footer-col">
        <h4>{{ app()->getLocale() === 'ar' ? 'معلومات التواصل' : 'Contact Info' }}</h4>
        <ul>
          @if(sett('contact_address'))
            <li><a href="#">📍 {{ sett('contact_address') }}</a></li>
          @else
            <li><a href="#">📍 {{ app()->getLocale() === 'ar' ? 'عمّان، الأردن' : 'Amman, Jordan' }}</a></li>
          @endif
          @if(sett_raw('contact_phone'))
            <li><a href="tel:{{ preg_replace('/[^0-9+]/', '', sett_raw('contact_phone')) }}">📞 {{ sett_raw('contact_phone') }}</a></li>
          @else
            <li><a href="#">📞 +962 6 XXX XXXX</a></li>
          @endif
          @if(sett_raw('contact_email'))
            <li><a href="mailto:{{ sett_raw('contact_email') }}">📧 {{ sett_raw('contact_email') }}</a></li>
          @else
            <li><a href="#">📧 info@noor-jordan.com</a></li>
          @endif
          @if(sett_raw('contact_whatsapp'))
            <li><a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', sett_raw('contact_whatsapp')) }}" target="_blank">💬 WhatsApp</a></li>
          @endif
        </ul>
      </div>

    </div>{{-- /footer-grid --}}

    <div class="footer-bottom">
      <p class="footer-copy">
        &copy; {{ date('Y') }} {{ app()->getLocale() === 'ar' ? 'مدارس نور الأردن الدولية. جميع الحقوق محفوظة.' : 'Noor Jordan International Schools. All rights reserved.' }}
      </p>
      <div class="footer-bottom-links">
        <a href="#">{{ app()->getLocale() === 'ar' ? 'الشروط والأحكام' : 'Terms' }}</a>
        <a href="#">{{ app()->getLocale() === 'ar' ? 'سياسة الخصوصية' : 'Privacy' }}</a>
        @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
          @if($localeCode !== app()->getLocale())
            <a href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">
              {{ $localeCode === 'ar' ? 'العربية' : 'English' }}
            </a>
          @endif
        @endforeach
      </div>
    </div>
  </div>
</footer>
