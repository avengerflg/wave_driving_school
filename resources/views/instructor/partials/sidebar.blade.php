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
    <li class="menu-item {{ request()->is('instructor/dashboard') ? 'active' : '' }}">
      <a href="{{ route('instructor.dashboard') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-home-circle"></i>
        <div>Dashboard</div>
      </a>
    </li>

    <!-- Calendar -->
    <li class="menu-item {{ request()->is('instructor/calendar*') ? 'active' : '' }}">
      <a href="{{ route('instructor.calendar') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-calendar"></i>
        <div>Calendar</div>
      </a>
    </li>

    <!-- Bookings -->
    <li class="menu-item {{ request()->is('instructor/bookings*') ? 'active' : '' }}">
      <a href="{{ route('instructor.bookings.index') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-book"></i>
      <div>Bookings</div>
      </a>
    </li>

    <!-- Availability -->
    <li class="menu-item {{ request()->is('instructor/availability*') ? 'active' : '' }}">
      <a href="{{ route('instructor.availability.index') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-time"></i>
      <div>Availability</div>
      </a>
    </li>

    <!-- Students -->
    <li class="menu-item {{ request()->is('instructor/clients*') ? 'active' : '' }}">
      <a href="{{ route('instructor.clients.index') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-user"></i>
      <div>Students</div>
      </a>
    </li>

    <!-- Packages -->
    <li class="menu-item {{ request()->routeIs('instructor.packages.*') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon bx bx-package"></i>
        <div>Packages</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item {{ request()->routeIs('instructor.packages.index') ? 'active' : '' }}">
          <a href="{{ route('instructor.packages.index') }}" class="menu-link">
            <div>Available Packages</div>
          </a>
        </li>
        <li class="menu-item {{ request()->routeIs('instructor.packages.orders') ? 'active' : '' }}">
          <a href="{{ route('instructor.packages.orders') }}" class="menu-link">
            <div>Student Orders</div>
          </a>
        </li>
        <li class="menu-item {{ request()->routeIs('instructor.packages.credits') ? 'active' : '' }}">
          <a href="{{ route('instructor.packages.credits') }}" class="menu-link">
            <div>Student Credits</div>
          </a>
        </li>
        <li class="menu-item {{ request()->routeIs('instructor.packages.lessons') ? 'active' : '' }}">
          <a href="{{ route('instructor.packages.lessons') }}" class="menu-link">
            <div>Package Lessons</div>
          </a>
        </li>
      </ul>
    </li>

    <!-- Services -->
    <li class="menu-item {{ request()->is('instructor/services*') ? 'active' : '' }}">
      <a href="{{ route('instructor.services.index') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-car"></i>
      <div>Services</div>
      </a>
    </li>

    <!-- Suburbs -->
    <li class="menu-item {{ request()->is('instructor/suburbs*') ? 'active' : '' }}">
      <a href="{{ route('instructor.suburbs.index') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-map"></i>
      <div>Suburbs</div>
      </a>
    </li>

    <!-- My Profile -->
    <!-- <li class="menu-item {{ request()->is('instructor/profile*') ? 'active' : '' }}">
      <a href="/" class="menu-link">
        <i class="menu-icon tf-icons bx bx-user-circle"></i>
        <div>My Profile</div>
      </a>
    </li> -->

    <!-- Settings -->
    <!-- <li class="menu-item {{ request()->is('instructor/settings*') ? 'active' : '' }}">
      <a href="/" class="menu-link">
        <i class="menu-icon tf-icons bx bx-cog"></i>
        <div>Settings</div>
      </a>
    </li> -->

    <!-- Logout -->
    <li class="menu-item">
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="menu-link" style="width: 100%; background: none; border: none;">
          <i class="menu-icon tf-icons bx bx-log-out"></i>
          <div>Logout</div>
        </button>
      </form>
    </li>
  </ul>
</aside>