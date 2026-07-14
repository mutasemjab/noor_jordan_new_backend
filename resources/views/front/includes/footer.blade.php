<footer>
  <div class="footer-inner">
    <div class="footer-grid">
      <div class="footer-brand">
        <div class="footer-logo">
          <div class="footer-logo-icon">ب</div>
          <div class="footer-logo-text">
            <strong>{{ __('front.site_name') }}</strong>
            <span>{{ __('front.site_tagline') }}</span>
          </div>
        </div>
        <p class="footer-desc">{{ __('front.footer_tagline') }}</p>

        {{-- Dynamic social media icons --}}
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
            {{-- Fallback placeholders when no social links set --}}
            <a href="#" class="footer-social">𝕏</a>
            <a href="#" class="footer-social">f</a>
            <a href="#" class="footer-social">in</a>
            <a href="#" class="footer-social">▶</a>
          @endif
        </div>

        {{-- App store buttons --}}
        @if(sett_raw('app_google_play') || sett_raw('app_store'))
        <div class="d-flex gap-2 flex-wrap mt-3">
          @if(sett_raw('app_google_play'))
          <a href="{{ sett_raw('app_google_play') }}" target="_blank" rel="noopener"
             style="display:inline-flex;align-items:center;gap:6px;padding:6px 12px;background:rgba(255,255,255,0.1);color:inherit;border-radius:8px;text-decoration:none;font-size:12px;border:1px solid rgba(255,255,255,0.2);">
            <i class="bi bi-google-play" style="font-size:16px"></i>
            <div style="line-height:1.1"><small style="opacity:.7;font-size:9px;display:block">GET IT ON</small>Google Play</div>
          </a>
          @endif
          @if(sett_raw('app_store'))
          <a href="{{ sett_raw('app_store') }}" target="_blank" rel="noopener"
             style="display:inline-flex;align-items:center;gap:6px;padding:6px 12px;background:rgba(255,255,255,0.1);color:inherit;border-radius:8px;text-decoration:none;font-size:12px;border:1px solid rgba(255,255,255,0.2);">
            <i class="bi bi-apple" style="font-size:16px"></i>
            <div style="line-height:1.1"><small style="opacity:.7;font-size:9px;display:block">DOWNLOAD ON THE</small>App Store</div>
          </a>
          @endif
        </div>
        @endif
      </div>

      <div class="footer-col">
        <h4>{{ app()->getLocale() === 'ar' ? 'المراحل الدراسية' : 'Grade Levels' }}</h4>
        <ul>
          <li><a href="{{ route('home') }}#grades">{{ app()->getLocale() === 'ar' ? 'الصفوف الأساسية 1–6' : 'Grades 1–6' }}</a></li>
          <li><a href="{{ route('home') }}#grades">{{ app()->getLocale() === 'ar' ? 'الصفوف 7–10' : 'Grades 7–10' }}</a></li>
          <li><a href="{{ route('home') }}#grades">{{ app()->getLocale() === 'ar' ? 'الحادي عشر (توجيهي)' : 'Grade 11 (Tawjihi)' }}</a></li>
          <li><a href="{{ route('home') }}#grades">{{ app()->getLocale() === 'ar' ? 'الثاني عشر (توجيهي)' : 'Grade 12 (Tawjihi)' }}</a></li>
          <li><a href="{{ route('courses.index') }}">{{ app()->getLocale() === 'ar' ? 'دورات صيفية' : 'Summer Courses' }}</a></li>
        </ul>
      </div>

      <div class="footer-col">
        <h4>{{ __('front.footer_platform') }}</h4>
        <ul>
          <li><a href="{{ route('home') }}#about">{{ __('front.nav_about') }}</a></li>
          <li><a href="{{ route('home') }}#teachers">{{ __('front.nav_teachers') }}</a></li>
          <li><a href="{{ route('courses.index') }}">{{ __('front.nav_courses') }}</a></li>
          <li><a href="{{ route('exams.index') }}">{{ __('front.nav_exams') }}</a></li>
          <li><a href="{{ route('home') }}#contact">{{ __('front.nav_contact') }}</a></li>
        </ul>
      </div>

      <div class="footer-col">
        <h4>{{ __('front.footer_contact') }}</h4>
        <ul>
          @if(sett('contact_address'))
            <li><a href="#">📍 {{ sett('contact_address') }}</a></li>
          @else
            <li><a href="#">📍 {{ __('front.contact_location') }}</a></li>
          @endif
          @if(sett_raw('contact_phone'))
            <li><a href="tel:{{ preg_replace('/[^0-9+]/', '', sett_raw('contact_phone')) }}">📞 {{ sett_raw('contact_phone') }}</a></li>
          @else
            <li><a href="tel:+96264000000">📞 +962 6 XXX XXXX</a></li>
          @endif
          @if(sett_raw('contact_email'))
            <li><a href="mailto:{{ sett_raw('contact_email') }}">📧 {{ sett_raw('contact_email') }}</a></li>
          @else
            <li><a href="mailto:info@albahithacademy.edu.jo">📧 info@albahithacademy.edu.jo</a></li>
          @endif
          @if(sett_raw('contact_whatsapp'))
            <li><a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', sett_raw('contact_whatsapp')) }}" target="_blank">💬 WhatsApp</a></li>
          @else
            <li><a href="#">💬 WhatsApp</a></li>
          @endif
        </ul>
      </div>
    </div>

    <div class="footer-bottom">
      <p class="footer-copy">{{ str_replace(':year', date('Y'), __('front.footer_copyright')) }}</p>
      <div class="footer-bottom-links">
        <a href="#">{{ __('front.footer_terms') }}</a>
        <a href="#">{{ __('front.footer_privacy') }}</a>
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
