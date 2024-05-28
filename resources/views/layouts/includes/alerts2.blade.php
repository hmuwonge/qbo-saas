@if (session('failed'))
  <div class="alert alert-danger alert-dismissible" role="alert">
    <p>
      <strong>Failed!</strong> {{ session('failed') }}
    </p>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
    </button>
  </div>
@endif

@if (session('errors'))
  <div class="alert alert-danger alert-dismissible" role="alert">
    <p>
      <strong>Error!</strong>
      @foreach (session('errors')->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </p>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
    </button>
  </div>
@endif

@if (session('successful') || session('success'))
  <div class="alert alert-success alert-dismissible" role="alert">
    <p>
      <strong>Successfully!</strong> {{ session('successful') ?? session('success') }}
    </p>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
    </button>
  </div>
@endif

@if (session('warning'))
  <div class="alert alert-warning alert-dismissible" role="alert">
    <p>
      <strong>Warning!</strong> {{ session('warning') }}
    </p>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
    </button>
  </div>
@endif

@if (session('status'))
  <div class="alert alert-info alert-dismissible" role="alert">
    <p>
      <strong>Great!</strong> {{ session('status') }}
    </p>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
    </button>
  </div>
@endif
