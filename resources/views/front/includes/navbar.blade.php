<nav id="navbar">
  <div class="nav-logo">
    <a href="{{ route('home') }}" style="display:flex;align-items:center;gap:10px;text-decoration:none;color:inherit;">
      <div class="nav-logo-icon">ب</div>
      <div class="nav-logo-text">
        <strong>{{ __('front.site_name') }}</strong>
        <span>{{ __('front.site_tagline') }}</span>
      </div>
    </a>
  </div>

  <ul class="nav-links">
    <li><a href="{{ route('home') }}#grades">{{ __('front.nav_grades') }}</a></li>
    <li><a href="{{ route('home') }}#teachers">{{ __('front.nav_teachers') }}</a></li>
    <li><a href="{{ route('courses.index') }}">{{ __('front.nav_courses') }}</a></li>
    <li><a href="{{ route('exams.index') }}">{{ __('front.nav_exams') }}</a></li>
    <li><a href="{{ route('home') }}#about">{{ __('front.nav_about') }}</a></li>
    <li><a href="{{ route('home') }}#contact" class="nav-cta">{{ __('front.nav_contact') }}</a></li>
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

    {{-- Auth links --}}
    @auth('student')
      <a href="{{ route('student.logout') }}"
         onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
         class="nav-auth-btn nav-auth-outline">
        {{ app()->getLocale() === 'ar' ? 'تسجيل الخروج' : 'Logout' }}
      </a>
      <form id="logout-form" action="{{ route('student.logout') }}" method="POST" style="display:none;">
        @csrf
      </form>
    @else
      <a href="{{ route('student.login') }}" class="nav-auth-btn nav-auth-outline">{{ __('front.nav_login') }}</a>
      <a href="{{ route('student.register') }}" class="nav-auth-btn nav-auth-primary">{{ __('front.nav_register') }}</a>
    @endauth
  </div>

  <div class="hamburger"><span></span><span></span><span></span></div>
</nav>
