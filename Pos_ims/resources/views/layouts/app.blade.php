<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>POS IMS - @yield('title', 'Point of Sale')</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="/css/app.css">
  <style>
    body {
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }
    main {
      flex: 1;
    }
    .mt-4 { margin-top: 1.5rem; }
  </style>
</head>
<body>
  <nav class="navbar is-dark">
    <div class="navbar-brand">
      <div class="navbar-item">
        <strong>POS IMS</strong>
      </div>
    </div>

    <div class="navbar-menu">
      <div class="navbar-end">
        @auth
          <div class="navbar-item">
            <span>Welcome, <strong>{{ Auth::user()->name }}</strong></span>
          </div>
          <div class="navbar-item">
            <div class="buttons">
              <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button class="button is-light" type="submit">
                  <span class="icon">
                    <i class="fas fa-sign-out-alt"></i>
                  </span>
                  <span>Logout</span>
                </button>
              </form>
            </div>
          </div>
        @else
          <div class="navbar-item">
            <div class="buttons">
              <a href="{{ route('login') }}" class="button is-primary">
                <span>Login</span>
              </a>
            </div>
          </div>
        @endauth
      </div>
    </div>
  </nav>

  <main>
    @if (session('status'))
      <div class="notification is-success">
        <button class="delete"></button>
        {{ session('status') }}
      </div>
    @endif

    @yield('content')
  </main>

  <footer class="footer has-background-grey-darker has-text-white-ter">
    <div class="content has-text-centered">
      <p>&copy; 2026 POS IMS. All rights reserved.</p>
    </div>
  </footer>

  <script src="/js/app.js"></script>
  <script>
    // Close notifications
    document.querySelectorAll('.notification .delete').forEach(button => {
      button.addEventListener('click', () => {
        button.parentElement.style.display = 'none';
      });
    });
  </script>
</body>
</html>
