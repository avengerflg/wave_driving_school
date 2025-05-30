<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand demo">
    <a href="{{ url('/') }}" class="app-brand-link">
     <span class="app-brand-logo demo">
        <img src="{{ asset('assets/img/logo.webp') }}" alt="Wave Driving School Logo" style="max-width: 40%; height: auto; object-fit: contain;">
      </span>
    </a>
    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
      <i class="bx bx-chevron-left bx-sm align-middle"></i>
    </a>
  </div>

  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1">
    <!-- Dashboard -->
    <li class="menu-item {{ request()->is('student/dashboard') ? 'active' : '' }}">
      <a href="{{ route('student.dashboard') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-home-circle"></i>
      <div>Dashboard</div>
      </a>
    </li>

    <!-- My Bookings -->
    <li class="menu-item {{ request()->is('student/bookings*') ? 'active' : '' }}">
      <a href="{{ route('student.bookings.index') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-calendar-check"></i>
      <div>My Lessons</div>
      </a>
    </li>

    <!-- My Packages -->
    <li class="menu-item {{ request()->routeIs('student.packages.*') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon bx bx-package"></i>
        <div>My Packages</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item {{ request()->routeIs('student.packages.index') ? 'active' : '' }}">
          <a href="{{ route('student.packages.index') }}" class="menu-link">
            <div>Browse Packages</div>
          </a>
        </li>
        <li class="menu-item {{ request()->routeIs('student.packages.credits') ? 'active' : '' }}">
          <a href="{{ route('student.packages.credits') }}" class="menu-link">
            <div>My Credits</div>
          </a>
        </li>
        <li class="menu-item {{ request()->routeIs('student.packages.orders') || request()->routeIs('student.packages.orders.show') ? 'active' : '' }}">
          <a href="{{ route('student.packages.orders') }}" class="menu-link">
            <div>My Orders</div>
          </a>
        </li>
      </ul>
    </li>

    <!-- Book a Lesson -->
    <li class="menu-item {{ request()->is('booking*') ? 'active' : '' }}">
      <a href="{{ route('booking.index') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-plus-circle"></i>
        <div>Book a Lesson</div>
      </a>
    </li>

    <!-- Profile -->
    <li class="menu-item {{ request()->is('profile*') ? 'active' : '' }}">
      <a href="{{ route('profile.show') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-user"></i>
        <div>My Profile</div>
      </a>
    </li>

    <!-- Notifications -->
    <li class="menu-item {{ request()->is('notifications*') ? 'active' : '' }}">
      <a href="{{ route('notifications.index') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-bell"></i>
        <div>Notifications</div>
        @if(auth()->user()->unreadNotifications->count() > 0)
          <span class="badge badge-center rounded-pill bg-danger ms-auto">
            {{ auth()->user()->unreadNotifications->count() }}
          </span>
        @endif
      </a>
    </li>

    <!-- Help & Support -->
    <li class="menu-item {{ request()->is('contact') ? 'active' : '' }}">
      <a href="{{ route('contact') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-help-circle"></i>
        <div>Help & Support</div>
      </a>
    </li>

    <!-- Logout -->
    <li class="menu-item">
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="menu-link" style="width: 100%; background: none; border: none; text-align: left; padding: 0.75rem 1.5rem;">
          <i class="menu-icon tf-icons bx bx-log-out"></i>
          <div>Logout</div>
        </button>
      </form>
    </li>
  </ul>
</aside>
