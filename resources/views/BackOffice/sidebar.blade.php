<!-- Menu -->
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('dashboardAdmin') }}" class="app-brand-link">

              <span class="app-brand-logo demo">
                <img alt="icon"  src="../assets/img/libroLogo.png" style="width:40px; height:40px; margin-right:10px;" >
              </span>
              <span class="app-brand-text demo menu-text fw-bolder ms-2">LibroLink</span>

        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">

        <!-- Dashboard (visible pour admin OU auteur) -->
        @auth
        {{-- Si l'utilisateur est admin --}}
        @if(auth()->user()->role === 'admin')
        <li class="menu-item {{ request()->routeIs('dashboardAdmin') ? 'active' : '' }}">
            <a href="{{ route('dashboardAdmin') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard Admin</div>
            </a>
        </li>
        @endif

        {{-- Si l'utilisateur est auteur --}}
        @if(auth()->user()->role === 'auteur')
        <li class="menu-item {{ request()->routeIs('dashboardAuteur') ? 'active' : '' }}">
            <a href="{{ route('dashboardAuteur') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard Auteur</div>
            </a>
        </li>
        @endif
        @endauth

        <!-- ✅ Partie visible UNIQUEMENT pour ADMIN -->
        @if(auth()->user()->isAdmin())
        <!-- Gestion des utilisateurs -->
        <li class="menu-header small text-uppercase"><span class="menu-header-text">Gestion des utilisateurs</span></li>
        <li class="menu-item {{ request()->routeIs('AjouterUtilisateur', 'listeUtilisateur') ? 'open active' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-layout"></i>
                <div data-i18n="Layouts">Utilisateurs</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('AjouterUtilisateur') ? 'active' : '' }}">
                    <a href="{{ route('AjouterUtilisateur') }}" class="menu-link">
                        <div data-i18n="Without menu">Ajouter utilisateur</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('listeUtilisateur') ? 'active' : '' }}">
                    <a href="{{ route('listeUtilisateur') }}" class="menu-link">
                        <div data-i18n="Without navbar">Liste des utilisateurs</div>
                    </a>
                </li>
            </ul>
        </li>


        <!-- Gestion des magasins -->
        <li class="menu-header small text-uppercase"><span class="menu-header-text">Gestion des magasins</span></li>
        <li class="menu-item {{ request()->routeIs('AjouterMagasin', 'listeMagasin') ? 'open active' : '' }}">
            <a href="javascript:void(0)" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-box"></i>
                <div data-i18n="User interface">Magasins</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('AjouterMagasin') ? 'active' : '' }}">
                    <a href="{{ route('AjouterMagasin') }}" class="menu-link">
                        <div data-i18n="Accordion">Ajouter magasin</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('listeMagasin') ? 'active' : '' }}">
                    <a href="{{ route('listeMagasin') }}" class="menu-link">
                        <div data-i18n="Alerts">Liste des magasins</div>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Gestion des Blogs -->
        <li class="menu-header small text-uppercase"><span class="menu-header-text">Gestion Blogs</span></li>
        <li class="menu-item {{ request()->routeIs('AjouterBlog', 'listeBlog') ? 'open active' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-detail"></i>
                <div data-i18n="Form Elements">Blogs</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('AjouterBlog') ? 'active' : '' }}">
                    <a href="{{ route('AjouterBlog') }}" class="menu-link">
                        <div data-i18n="Basic Inputs">Ajouter Blog</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('listeBlog') ? 'active' : '' }}">
                    <a href="{{ route('listeBlog') }}" class="menu-link">
                        <div data-i18n="Input groups">Liste des blogs</div>
                    </a>
                </li>
            </ul>
        </li>
        @endif

        <!-- ✅ Partie visible pour ADMIN ET AUTEUR : Gestion des Livres -->
        @if(auth()->user()->isAdmin() || auth()->user()->isAuteur())
        <!-- Gestion des catégories -->
        <li class="menu-header small text-uppercase"><span class="menu-header-text">Gestion des Livres</span></li>
        <li class="menu-item {{ request()->routeIs('AjouterCategorie', 'listeCategorie') ? 'open active' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-cube-alt"></i>
                <div data-i18n="Misc">Categorie</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('AjouterCategorie') ? 'active' : '' }}">
                    <a href="{{ route('AjouterCategorie') }}" class="menu-link">
                        <div data-i18n="Error">Ajouter Categorie Livre</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('listeCategorie') ? 'active' : '' }}">
                    <a href="{{ route('listeCategorie') }}" class="menu-link">
                        <div data-i18n="Under Maintenance">Liste des categories de livre</div>
                    </a>
                </li>
            </ul>
        </li>
        <!-- Gestion des livres -->
        <li class="menu-item {{ request()->routeIs('AjouterLivre', 'listeLivre') ? 'open active' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-dock-top"></i>
                <div data-i18n="Account Settings">Livre</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('AjouterLivre') ? 'active' : '' }}">
                    <a href="{{ route('AjouterLivre') }}" class="menu-link">
                        <div data-i18n="Account">Ajouter Livre</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('listeLivre') ? 'active' : '' }}">
                    <a href="{{ route('listeLivre') }}" class="menu-link">
                        <div data-i18n="Notifications">Liste des livres</div>
                    </a>
                </li>
            </ul>
        </li>
        @endif
    </ul>
</aside>
<!-- / Menu -->