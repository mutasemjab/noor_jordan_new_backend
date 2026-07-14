<aside class="sidebar" id="sidebar">

    {{-- Brand --}}
    <div class="sidebar-brand">
        <div class="brand-icon">
            <i class="bi bi-mortarboard-fill"></i>
        </div>
        <span class="brand-text">{{ __('messages.edu_platform') }}</span>
    </div>

    {{-- Navigation --}}
    <nav class="sidebar-nav">

        <div class="nav-label">{{ __('messages.main') }}</div>
        <ul>
            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}"
                    class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="nav-icon bi bi-speedometer2"></i>
                    <span>{{ __('messages.dashboard') }}</span>
                </a>
            </li>
        </ul>

        <div class="nav-label">{{ __('messages.users') }}</div>
        <ul>
            <li class="nav-item">
                <a href="{{ route('admin.students.index') }}"
                    class="nav-link {{ request()->routeIs('admin.students.*') ? 'active' : '' }}">
                    <i class="nav-icon bi bi-mortarboard"></i>
                    <span>{{ __('messages.students') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.teachers.index') }}"
                    class="nav-link {{ request()->routeIs('admin.teachers.*') ? 'active' : '' }}">
                    <i class="nav-icon bi bi-person-workspace"></i>
                    <span>{{ __('messages.teachers') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.enrollments.index') }}"
                    class="nav-link {{ request()->routeIs('admin.enrollments.*') ? 'active' : '' }}">
                    <i class="nav-icon bi bi-journal-check"></i>
                    <span>{{ __('messages.enrollments') }}</span>
                </a>
            </li>
        </ul>

        <div class="nav-label">{{ __('messages.academic') }}</div>
        <ul>

            {{-- Courses --}}
            <li class="nav-item">
                <a href="#courses-menu" class="nav-link" data-submenu="courses-menu"
                    aria-expanded="{{ request()->routeIs('admin.courses.*') ? 'true' : 'false' }}">
                    <i class="nav-icon bi bi-book"></i>
                    <span>{{ __('messages.courses') }}</span>
                    <i class="nav-arrow bi bi-chevron-right"></i>
                </a>
                <ul class="nav-submenu {{ request()->routeIs('admin.courses.*') ? 'show' : '' }}" id="courses-menu">
                    <li class="nav-item">
                        <a href="{{ route('admin.courses.index') }}" class="nav-link">
                            <span>{{ __('messages.all_courses') }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.courses.create') }}" class="nav-link">
                            <span>{{ __('messages.add_course') }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.categories.index') }}" class="nav-link">
                            <span>{{ __('messages.categories') }}</span>
                        </a>
                    </li>
                </ul>
            </li>

            {{-- Subjects --}}
            <li class="nav-item">
                <a href="{{ route('admin.subjects.index') }}"
                    class="nav-link {{ request()->routeIs('admin.subjects.*') ? 'active' : '' }}">
                    <i class="nav-icon bi bi-journals"></i>
                    <span>{{ __('messages.subjects') }}</span>
                </a>
            </li>

            {{-- Exams --}}
            <li class="nav-item">
                <a href="#exams-menu" class="nav-link" data-submenu="exams-menu"
                    aria-expanded="{{ request()->routeIs('admin.exams.*') ? 'true' : 'false' }}">
                    <i class="nav-icon bi bi-clipboard-check"></i>
                    <span>{{ __('messages.exams') }}</span>
                    <i class="nav-arrow bi bi-chevron-right"></i>
                </a>
                <ul class="nav-submenu {{ request()->routeIs('admin.exams.*') ? 'show' : '' }}" id="exams-menu">
                    <li class="nav-item">
                        <a href="{{ route('admin.exams.index') }}" class="nav-link">
                            <span>{{ __('messages.all_exams') }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.exams.create') }}" class="nav-link">
                            <span>{{ __('messages.add_exam') }}</span>
                        </a>
                    </li>
                </ul>
            </li>

            {{-- PDF Files --}}
            <li class="nav-item">
                <a href="#pdf-menu" class="nav-link" data-submenu="pdf-menu"
                    aria-expanded="{{ request()->routeIs('admin.question-banks.*') || request()->routeIs('admin.previous-year-exams.*') || request()->routeIs('admin.worksheets.*') ? 'true' : 'false' }}">
                    <i class="nav-icon bi bi-file-earmark-pdf"></i>
                    <span>{{ __('messages.pdf_files') }}</span>
                    <i class="nav-arrow bi bi-chevron-right"></i>
                </a>
                <ul class="nav-submenu {{ request()->routeIs('admin.question-banks.*') || request()->routeIs('admin.previous-year-exams.*') || request()->routeIs('admin.worksheets.*') ? 'show' : '' }}"
                    id="pdf-menu">
                    <li class="nav-item">
                        <a href="{{ route('admin.question-banks.index') }}"
                            class="nav-link {{ request()->routeIs('admin.question-banks.*') ? 'active' : '' }}">
                            <span>{{ __('messages.question_banks') }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.previous-year-exams.index') }}"
                            class="nav-link {{ request()->routeIs('admin.previous-year-exams.*') ? 'active' : '' }}">
                            <span>{{ __('messages.previous_year_exams') }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.worksheets.index') }}"
                            class="nav-link {{ request()->routeIs('admin.worksheets.*') ? 'active' : '' }}">
                            <span>{{ __('messages.worksheets') }}</span>
                        </a>
                    </li>

                </ul>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.announcements.index') }}"
                    class="nav-link {{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}">
                    <i class="nav-icon bi bi-megaphone"></i>
                    <span>{{ __('messages.announcements') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.banners.index') }}"
                    class="nav-link {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
                    <i class="nav-icon bi bi-images"></i>
                    <span>{{ __('messages.banners') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.notifications.send') }}"
                    class="nav-link {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}">
                    <i class="nav-icon bi bi-bell"></i>
                    <span>{{ __('messages.notifications') }}</span>
                </a>
            </li>

            {{-- Educational Notes --}}
            <li class="nav-item">
                <a href="{{ route('admin.educational-notes.index') }}"
                    class="nav-link {{ request()->routeIs('admin.educational-notes.*') ? 'active' : '' }}">
                    <i class="nav-icon bi bi-journal-text"></i>
                    <span>{{ __('messages.educational_notes') }}</span>
                </a>
            </li>

        </ul>

        <div class="nav-label">{{ __('messages.cards_management') }}</div>
        <ul>
            <li class="nav-item">
                <a href="{{ route('admin.cards.index') }}"
                    class="nav-link {{ request()->routeIs('admin.cards.*') ? 'active' : '' }}">
                    <i class="nav-icon bi bi-credit-card"></i>
                    <span>{{ __('messages.cards') }}</span>
                </a>
            </li>
        </ul>

        <div class="nav-label">{{ __('messages.system') }}</div>
        <ul>
            <li class="nav-item">
                <a href="{{ route('admin.role.index') }}"
                    class="nav-link {{ request()->routeIs('admin.role.*') ? 'active' : '' }}">
                    <i class="nav-icon bi bi-shield-check"></i>
                    <span>{{ __('messages.roles_permissions') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.contact_messages.index') }}"
                    class="nav-link {{ request()->routeIs('admin.contact_messages.*') ? 'active' : '' }}">
                    <i class="nav-icon bi bi-envelope"></i>
                    <span>{{ __('messages.contact_messages') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.site-settings.edit') }}"
                    class="nav-link {{ request()->routeIs('admin.site-settings.*') ? 'active' : '' }}">
                    <i class="nav-icon bi bi-gear"></i>
                    <span>{{ __('messages.site_settings') }}</span>
                </a>
            </li>
        </ul>

    </nav>

    {{-- Sidebar Footer --}}
    <div class="sidebar-footer">
        <ul>
            <li class="nav-item">
                <a href="{{ route('admin.login.edit', auth('admin')->id()) }}" class="nav-link">
                    <i class="nav-icon bi bi-gear"></i>
                    <span>{{ __('messages.settings') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link"
                    onclick="event.preventDefault(); document.getElementById('admin-logout-form').submit();">
                    <i class="nav-icon bi bi-box-arrow-right"></i>
                    <span>{{ __('messages.sign_out') }}</span>
                </a>
            </li>
        </ul>
        <button class="sidebar-collapse-btn" id="sidebarCollapseBtn" title="{{ __('messages.collapse_sidebar') }}">
            <i class="bi bi-arrow-bar-left"></i>
        </button>
    </div>

</aside>
