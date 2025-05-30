<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
  <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
    <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
      <i class="bx bx-menu bx-sm"></i>
    </a>
  </div>

  <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
    <!-- Search -->
    <div class="navbar-nav align-items-center">
      <div class="nav-item d-flex align-items-center">
        <i class="bx bx-search fs-4 lh-0"></i>
        <input
          type="text"
          class="form-control border-0 shadow-none"
          placeholder="Search..."
          aria-label="Search..."
        />
      </div>
    </div>
    <!-- /Search -->

    <ul class="navbar-nav flex-row align-items-center ms-auto">
      <!-- Notifications -->
      <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-1">
        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
          <i class="bx bx-bell bx-sm"></i>
          @if(auth()->user()->unreadNotifications->count() > 0)
          <span class="badge bg-danger rounded-pill badge-notifications">{{ auth()->user()->unreadNotifications->count() }}</span>
          @endif
        </a>
        <ul class="dropdown-menu dropdown-menu-end py-0">
          <li class="dropdown-menu-header border-bottom">
            <div class="dropdown-header d-flex align-items-center py-3">
              <h5 class="text-body mb-0 me-auto">Notifications</h5>
              @if(auth()->user()->unreadNotifications->count() > 0)
              <a href="{{ route('notifications.mark-all-read') }}" class="dropdown-notifications-all text-body" data-bs-toggle="tooltip" data-bs-placement="top" title="Mark all as read">
                <i class="bx bx-check-all fs-4"></i>
              </a>
              @endif
            </div>
          </li>
          <li class="dropdown-notifications-list scrollable-container">
            <ul class="list-group list-group-flush">
              @forelse(auth()->user()->notifications()->take(5)->get() as $notification)
              <li class="list-group-item list-group-item-action dropdown-notifications-item {{ $notification->read_at ? '' : 'marked-as-unread' }}">
                <a href="{{ route('notifications.redirect', $notification->id) }}">
                  <div class="d-flex">
                    <div class="flex-shrink-0 me-3">
                      <div class="avatar">
                        <span class="avatar-initial rounded-circle bg-label-{{ $notification->data['color'] ?? 'primary' }}">
                          <i class="bx {{ $notification->data['icon'] ?? 'bx-bell' }}"></i>
                        </span>
                      </div>
                    </div>
                    <div class="flex-grow-1">
                      <h6 class="mb-1">{{ $notification->data['title'] }}</h6>
                      <p class="mb-0">{{ $notification->data['message'] }}</p>
                      <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                    </div>
                  </div>
                </a>
              </li>
              @empty
              <li class="list-group-item list-group-item-action dropdown-notifications-item">
                <div class="d-flex justify-content-center align-items-center p-3">
                  <p class="mb-0 text-muted">No notifications</p>
                </div>
              </li>
              @endforelse
            </ul>
          </li>
          <li class="dropdown-menu-footer border-top">
            <a href="{{ route('notifications.index') }}" class="dropdown-item d-flex justify-content-center p-3">
              View all notifications
            </a>
          </li>
        </ul>
      </li>
      <!-- /Notifications -->

      <!-- User -->
      <li class="nav-item navbar-dropdown dropdown-user dropdown">
        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
          <div class="avatar avatar-online">
            <img src="{{ Auth::user()->avatar ?? asset('assets/img/avatars/1.png') }}" alt class="w-px-40 h-auto rounded-circle" />
          </div>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <li>
            <a class="dropdown-item" href="#">
              <div class="d-flex">
                <div class="flex-shrink-0 me-3">
                  <div class="avatar avatar-online">
                    <img src="{{ Auth::user()->avatar ?? asset('assets/img/avatars/1.png') }}" alt class="w-px-40 h-auto rounded-circle" />
                  </div>
                </div>
                <div class="flex-grow-1">
                  <span class="fw-semibold d-block">{{ Auth::user()->name ?? 'John Doe' }}</span>
                  <small class="text-muted">{{ Auth::user()->role ?? 'Admin' }}</small>
                </div>
              </div>
            </a>
          </li>
          <li>
            <div class="dropdown-divider"></div>
          </li>
          <li>
            <a class="dropdown-item" href="/">
              <i class="bx bx-user me-2"></i>
              <span class="align-middle">My Profile</span>
            </a>
          </li>
          <li>
            <a class="dropdown-item" href="{{ route('admin.settings') }}">
              <i class="bx bx-cog me-2"></i>
              <span class="align-middle">Settings</span>
            </a>
          </li>
          <li>
            <a class="dropdown-item" href="#">
              <span class="d-flex align-items-center align-middle">
                <i class="flex-shrink-0 bx bx-credit-card me-2"></i>
                <span class="flex-grow-1 align-middle">Billing</span>
                <span class="flex-shrink-0 badge badge-center rounded-pill bg-danger w-px-20 h-px-20">4</span>
              </span>
            </a>
          </li>
          <li>
            <div class="dropdown-divider"></div>
          </li>
          <li>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="dropdown-item">
                <i class="bx bx-power-off me-2"></i>
                <span class="align-middle">Log Out</span>
              </button>
            </form>
          </li>
        </ul>
      </li>
      <!--/ User -->
    </ul>
  </div>
</nav>