<nav class="navbar navbar-dark text-white bg-dark border-bottom shadow-sm d-md-none mobile-nav">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <span class="fw-bold">Triserv</span>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mobileSidebarMenu" aria-controls="mobileSidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
            <i class="bi bi-list"></i>
        </button>
    </div>

    <div class="collapse" id="mobileSidebarMenu">
        <div class="d-flex flex-column p-3 rounded-3" id="mobileSidebarMenuContent">

            <!-- Dropdown Item -->
            <a class="d-flex justify-content-between align-items-center text-decoration-none mb-2" data-bs-toggle="collapse" href="#mobileSettingsMenu" role="button" aria-expanded="false" aria-controls="mobileSettingsMenu">
                <span class="text-light"><i class="bi bi-gear me-2"></i>Setup</span>
                <i class="bi bi-chevron-down text-light"></i>
            </a>

            <div class="collapse ms-3 mb-2" id="mobileSettingsMenu">
                <a href="{{route('prospect')}}" class="d-block py-1 ps-4 text-light">Prospect</a>
            </div>

            <!-- Logout Button -->
            <form method="POST" action="{{ route('logout') }}" class="mt-3">
                @csrf
                <button type="submit" class="btn btn-logout w-100">
                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                </button>
            </form>

        </div>
    </div>
</nav>
