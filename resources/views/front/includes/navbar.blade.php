<nav id="navbar">
  <div class="nav-logo">
    <a href="{{ route('home') }}" style="display:flex;align-items:center;gap:10px;text-decoration:none;color:inherit;">
      <div class="nav-logo-icon">ن</div>
      <div class="nav-logo-text">
        <strong>{{ app()->getLocale() === 'ar' ? 'مدارس نور الأردن الدولية' : 'Noor Jordan International Schools' }}</strong>
        <span>{{ app()->getLocale() === 'ar' ? 'تميّز وإبداع منذ 1999' : 'Excellence Since 1999' }}</span>
      </div>
    </a>
  </div>

  <ul class="nav-links">
    <li><a href="{{ route('home') }}#features">{{ app()->getLocale() === 'ar' ? 'خدماتنا' : 'Services' }}</a></li>
    <li><a href="{{ route('home') }}#teachers">{{ app()->getLocale() === 'ar' ? 'معلمونا' : 'Teachers' }}</a></li>
    <li><a href="{{ route('home') }}#about">{{ app()->getLocale() === 'ar' ? 'من نحن' : 'About' }}</a></li>
    <li><a href="{{ route('home') }}#contact" class="nav-cta">{{ app()->getLocale() === 'ar' ? 'تواصل معنا' : 'Contact Us' }}</a></li>
  </ul>

  <div class="nav-actions">
    {{-- Locale switcher --}}
    @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
      @if($localeCode !== app()->getLocale())
        <a href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}"
           class="nav-lang-switch"
           title="{{ $properties['native'] }}">
          {{ strtoupper($localeCode) }}
        </a>
      @endif
    @endforeach

    {{-- Student login --}}
    @auth('student')
      <a href="{{ route('student.logout') }}"
         onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
         class="nav-auth-btn nav-auth-outline">
        {{ app()->getLocale() === 'ar' ? 'تسجيل الخروج' : 'Logout' }}
      </a>
      <form id="logout-form" action="{{ route('student.logout') }}" method="POST" style="display:none;">@csrf</form>
    @else
      <a href="{{ route('student.login') }}" class="nav-auth-btn nav-auth-primary">
        {{ app()->getLocale() === 'ar' ? 'دخول الطالب' : 'Student Login' }}
      </a>
    @endauth
  </div>

  <div class="hamburger"><span></span><span></span><span></span></div>
</nav>
