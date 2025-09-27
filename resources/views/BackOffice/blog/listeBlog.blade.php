@extends('baseB')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center py-3 mb-4">
        <h4 class="fw-bold"><span class="text-muted fw-light">Tables /</span> Liste des Blogs</h4>
        <a href="{{ route('AjouterBlog') }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i>Ajouter Blog
        </a>
    </div>
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Basic Bootstrap Table -->
    <div class="card">
        <h5 class="card-header">Blogs</h5>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Contenu</th>
                        <th>Auteur</th>
                        <th>Categorie</th>
                        <th>Image</th>
                        <th>Créé le</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach($blogs as $blog)
                    <tr>
                        <!-- Titre du blog -->
                        <td><strong>{{ $blog->title }}</strong></td>

                        <!-- Contenu du blog -->
                        <td>
                            <strong>
                                @php
                                $limit = 20; // nombre de caractères à afficher
                                $content = $blog->content;
                                @endphp

                                @if(strlen($content) > $limit)
                                <span class="short-text">{{ substr($content, 0, $limit) }}</span>
                                <span class="full-text d-none">{{ $content }}</span>
                                <a href="javascript:void(0)" class="toggle-text"> voir plus</a>
                                @else
                                {{ $content }}
                                @endif
                            </strong>
                        </td>


                        <!-- Auteur -->
                        <td>{{ $blog->user->name ?? 'N/A' }}</td>
                        <!-- Categorie -->
                        <td>{{ $blog->category->name ?? 'N/A' }}</td>

                        <!-- Image -->
                        <td>
                            @if($blog->image)
                            <img src="{{ asset('uploads/' . $blog->image) }}" alt="Image Blog" class="rounded" width="50">
                            @else
                            N/A
                            @endif
                        </td>

                        <!-- Date de création -->
                        <td>{{ $blog->created_at->format('d/m/Y H:i') }}</td>

                        <!-- Actions -->
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ route('blogs.edit', $blog->id) }}">
                                        <i class="bx bx-edit-alt me-1"></i> Modifier
                                    </a>
                                    <form action="{{ route('blogs.delete', $blog->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="dropdown-item text-danger" onclick="return confirm('Voulez-vous vraiment supprimer ce blog ?')">
                                            <i class="bx bx-trash me-1"></i> Supprimer
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach

                    @if($blogs->isEmpty())
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            <div class="text-muted">
                                <i class="bx bx-category bx-lg mb-2"></i>
                                <p>Aucun blog trouvé</p>
                                <a href="{{ route('AjouterBlog') }}" class="btn btn-primary btn-sm">
                                    Ajouter le premier blog
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.toggle-text').forEach(function(link) {
            link.addEventListener('click', function() {
                const td = this.closest('td');
                const shortText = td.querySelector('.short-text');
                const fullText = td.querySelector('.full-text');

                if (shortText.classList.contains('d-none')) {
                    // Revenir au texte court
                    shortText.classList.remove('d-none');
                    fullText.classList.add('d-none');
                    this.textContent = ' voir plus';
                } else {
                    // Afficher texte complet
                    shortText.classList.add('d-none');
                    fullText.classList.remove('d-none');
                    this.textContent = ' voir moins';
                }
            });
        });
    });
</script>



@endsection