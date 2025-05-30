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
    <li class="menu-item {{ request()->is('dashboard*') ? 'active' : '' }}">
      <a href="{{ route('admin.dashboard') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-home-circle"></i>
      <div>Dashboard</div>
      </a>
    </li>
    <!-- Bookings -->
    <li class="menu-item {{ request()->is('bookings*') ? 'active' : '' }}">
      <a href="{{ route('admin.bookings.index') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-calendar"></i>
      <div>Bookings</div>
      </a>
    </li>
    <!-- Calendar -->
    <li class="menu-item {{ request()->is('calendar*') ? 'active' : '' }}">
      <a href="{{ route('admin.calendar.index') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-calendar"></i>
      <div>Calendar</div>
      </a>
    </li>
    <!-- Orders -->
    <li class="menu-item {{ request()->is('orders*') ? 'active' : '' }}">
      <a href="{{ route('admin.orders.index') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-receipt"></i>
      <div>Orders</div>
      </a>
    </li>
    <!-- Instructors -->
    <li class="menu-item {{ request()->is('instructors*') ? 'active' : '' }}">
      <a href="{{ route('admin.instructors.index') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-user-voice"></i>
      <div>Instructors</div>
      </a>
    </li>
    <!-- Services -->
    <li class="menu-item {{ request()->is('services*') ? 'active' : '' }}">
      <a href="{{ route('admin.services.index') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-car"></i>
      <div>Services</div>
      </a>
    </li>
    <!-- Packages -->
    <li class="menu-item {{ request()->is('packages*') ? 'active' : '' }}">
      <a href="{{ route('admin.packages.index') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-package"></i>
      <div>Packages</div>
      </a>
    </li>
    <!-- Suburbs -->
    <li class="menu-item {{ request()->is('suburbs*') ? 'active' : '' }}">
      <a href="{{ route('admin.suburbs.index') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-map"></i>
      <div>Suburbs</div>
      </a>
    </li>
    <!-- Sales -->
    <!-- <li class="menu-item {{ Request::is('sales*') ? 'active' : '' }}">
      <a href="{{ url('/sales') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-bar-chart"></i>
      <div>Sales</div>
      </a>
    </li> -->
    <!-- Clients -->
    <li class="menu-item {{ request()->is('clients*') ? 'active' : '' }}">
      <a href="{{ route('admin.clients.index') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-user"></i>
      <div>Clients</div>
      </a>
    </li>
    <!-- Marketing -->
    <!-- <li class="menu-item {{ Request::is('marketing*') ? 'active' : '' }}">
      <a href="{{ url('/marketing') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-trending-up"></i>
      <div>Marketing</div>
      </a>
    </li> -->
    <!-- Reports -->
    <!-- <li class="menu-item {{ Request::is('reports*') ? 'active' : '' }}">
      <a href="{{ url('/reports') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-file"></i>
      <div>Reports</div>
      </a>
    </li> -->
    <!-- Setup -->
    <li class="menu-item {{ Request::is('setup*') ? 'active' : '' }}">
      <a href="{{ url('/setup') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-cog"></i>
      <div>Setup</div>
      </a>
    </li>
  </ul>
</aside>
