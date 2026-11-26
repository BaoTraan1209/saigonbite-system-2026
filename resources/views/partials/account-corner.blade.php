<div class="admin-profile account-corner">
  <button class="notification-button" type="button" aria-label="Notifications">
    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18 9a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/></svg><i></i>
  </button>
  <span class="avatar">{{ auth()->user()->role === 'admin' ? 'A' : 'C' }}</span>
  <span class="admin-name">{{ ucfirst(auth()->user()->role) }}</span>
  <form class="admin-logout" method="post" action="{{ route('logout') }}">
    @csrf
    <button class="admin-logout-button" type="submit">Log out</button>
  </form>
</div>
