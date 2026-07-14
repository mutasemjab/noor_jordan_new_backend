<aside class="sidebar" id="sidebar">

    <div class="sidebar-brand">
        <div class="brand-icon"><i class="bi bi-mortarboard-fill"></i></div>
        <span class="brand-text">Al<span>Bahith</span></span>
    </div>

    <nav class="sidebar-nav">

        <div class="nav-label">{{ __('messages.t_main') }}</div>
        <ul>
            <li class="nav-item">
                <a href="{{ route('teacher.dashboard') }}"
                   class="nav-link {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}">
                    <i class="nav-icon bi bi-speedometer2"></i>
                    <span>{{ __('messages.t_dashboard') }}</span>
                </a>
            </li>
        </ul>

        <div class="nav-label">{{ __('messages.t_teaching') }}</div>
        <ul>
            <li class="nav-item">
                <a href="#my-courses" class="nav-link {{ request()->routeIs('teacher.courses.*') ? 'active' : '' }}"
                   data-submenu="my-courses"
                   aria-expanded="{{ request()->routeIs('teacher.courses.*') ? 'true' : 'false' }}">
                    <i class="nav-icon bi bi-book"></i>
                    <span>{{ __('messages.t_my_courses') }}</span>
                    <i class="nav-arrow bi bi-chevron-right"></i>
                </a>
                <ul class="nav-submenu {{ request()->routeIs('teacher.courses.*') ? 'show' : '' }}" id="my-courses">
                    <li class="nav-item">
                        <a href="{{ route('teacher.courses.index') }}" class="nav-link {{ request()->routeIs('teacher.courses.index') ? 'active' : '' }}">
                            <span>{{ __('messages.t_all_courses') }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('teacher.courses.create') }}" class="nav-link {{ request()->routeIs('teacher.courses.create') ? 'active' : '' }}">
                            <span>{{ __('messages.t_create_course') }}</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item">
                <a href="{{ route('teacher.exams.index') }}"
                   class="nav-link {{ request()->routeIs('teacher.exams.*') ? 'active' : '' }}">
                    <i class="nav-icon bi bi-journal-check"></i>
                    <span>{{ __('messages.t_my_exams') }}</span>
                </a>
            </li>
        </ul>

        <div class="nav-label">{{ __('messages.pdf_files') }}</div>
        <ul>
            <li class="nav-item">
                <a href="{{ route('teacher.previous-year-exams.index') }}"
                   class="nav-link {{ request()->routeIs('teacher.previous-year-exams.*') ? 'active' : '' }}">
                    <i class="nav-icon bi bi-clock-history"></i>
                    <span>{{ __('messages.t_previous_year_exams') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('teacher.question-banks.index') }}"
                   class="nav-link {{ request()->routeIs('teacher.question-banks.*') ? 'active' : '' }}">
                    <i class="nav-icon bi bi-bank"></i>
                    <span>{{ __('messages.t_question_banks') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('teacher.worksheets.index') }}"
                   class="nav-link {{ request()->routeIs('teacher.worksheets.*') ? 'active' : '' }}">
                    <i class="nav-icon bi bi-file-earmark-text"></i>
                    <span>{{ __('messages.worksheets') }}</span>
                </a>
            </li>
        </ul>

        <div class="nav-label">{{ __('messages.educational_notes') }}</div>
        <ul>
            <li class="nav-item">
                <a href="{{ route('teacher.educational-notes.index') }}"
                   class="nav-link {{ request()->routeIs('teacher.educational-notes.*') ? 'active' : '' }}">
                    <i class="nav-icon bi bi-journal-text"></i>
                    <span>{{ __('messages.educational_notes') }}</span>
                </a>
            </li>
        </ul>

    </nav>

    <div class="sidebar-footer">
        <ul>
            <li class="nav-item">
                <a href="{{ route('teacher.profile') }}"
                   class="nav-link {{ request()->routeIs('teacher.profile') ? 'active' : '' }}">
                    <i class="nav-icon bi bi-person-circle"></i>
                    <span>{{ __('messages.t_profile') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link"
                   onclick="event.preventDefault(); document.getElementById('teacher-logout-form').submit();">
                    <i class="nav-icon bi bi-box-arrow-right"></i>
                    <span>{{ __('messages.t_sign_out') }}</span>
                </a>
            </li>
        </ul>
        <button class="sidebar-collapse-btn" id="sidebarCollapseBtn" title="{{ __('messages.t_collapse_sidebar') }}">
            <i class="bi bi-arrow-bar-left"></i>
        </button>
    </div>

</aside>
