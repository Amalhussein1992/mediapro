@extends('layouts.admin')

@section('title', __('admin.users_management'))

@section('content')
<style>
    body {
        font-family: {{ app()->getLocale() === 'ar' ? "'Cairo', sans-serif" : "'Inter', sans-serif" }};
    }

    .users-page {
        padding: 2rem;
    }

    /* Page Header */
    .page-header {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #ec4899 100%);
        border-radius: 24px;
        padding: 2.5rem;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: -50%;
        {{ app()->getLocale() === 'ar' ? 'left: -20%;' : 'right: -20%;' }}
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
        border-radius: 50%;
        animation: float 6s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(20px); }
    }

    .page-header-content {
        position: relative;
        z-index: 1;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1.5rem;
    }

    .page-title {
        font-size: 2rem;
        font-weight: 800;
        color: white;
        margin: 0;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .page-subtitle {
        color: rgba(255, 255, 255, 0.9);
        margin-top: 0.5rem;
        font-size: 1rem;
    }

    .create-btn {
        background: white;
        color: #6366f1;
        padding: 0.875rem 1.75rem;
        border-radius: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        text-decoration: none;
    }

    .create-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 12px rgba(0, 0, 0, 0.15);
    }

    /* Filter Bar */
    .filter-bar {
        background: #1e293b;
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        align-items: center;
        border: 1px solid rgba(148, 163, 184, 0.1);
    }

    .filter-input {
        background: #0f172a;
        border: 1px solid rgba(148, 163, 184, 0.1);
        color: #e2e8f0;
        padding: 0.75rem 1rem;
        border-radius: 10px;
        outline: none;
        transition: all 0.3s ease;
        flex: 1;
        min-width: 200px;
    }

    .filter-input:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    .filter-input::placeholder {
        color: #64748b;
    }

    /* Users Grid */
    .users-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 1.5rem;
    }

    @media (max-width: 768px) {
        .users-grid {
            grid-template-columns: 1fr;
        }
    }

    /* User Card */
    .user-card {
        background: #1e293b;
        border-radius: 16px;
        padding: 1.75rem;
        border: 1px solid rgba(148, 163, 184, 0.1);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .user-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.2);
        border-color: rgba(99, 102, 241, 0.3);
    }

    .user-card-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .user-avatar {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        background: linear-gradient(135deg, #6366f1, #8b5cf6, #ec4899);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        font-weight: 700;
        box-shadow: 0 4px 6px rgba(99, 102, 241, 0.3);
    }

    .user-info {
        flex: 1;
    }

    .user-name {
        color: #e2e8f0;
        font-weight: 700;
        font-size: 1.1rem;
        margin-bottom: 0.25rem;
    }

    .user-email {
        color: #94a3b8;
        font-size: 0.875rem;
    }

    .user-id {
        background: rgba(99, 102, 241, 0.1);
        color: #818cf8;
        padding: 0.25rem 0.625rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        border: 1px solid rgba(99, 102, 241, 0.2);
    }

    .user-stats {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .stat-item {
        background: #0f172a;
        border-radius: 12px;
        padding: 1rem;
        border: 1px solid rgba(148, 163, 184, 0.1);
        text-align: center;
    }

    .stat-value {
        color: #e2e8f0;
        font-size: 1.5rem;
        font-weight: 700;
        display: block;
    }

    .stat-label {
        color: #94a3b8;
        font-size: 0.75rem;
        text-transform: uppercase;
        margin-top: 0.25rem;
        display: block;
    }

    .stat-posts .stat-value {
        color: #3b82f6;
    }

    .stat-accounts .stat-value {
        color: #10b981;
    }

    .user-meta {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #94a3b8;
        font-size: 0.85rem;
        margin-bottom: 1.5rem;
    }

    .user-actions {
        display: flex;
        gap: 0.75rem;
        padding-top: 1rem;
        border-top: 1px solid rgba(148, 163, 184, 0.1);
    }

    .action-btn {
        flex: 1;
        padding: 0.625rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        text-decoration: none;
        border: none;
        cursor: pointer;
    }

    .action-view {
        background: rgba(99, 102, 241, 0.1);
        color: #818cf8;
        border: 1px solid rgba(99, 102, 241, 0.2);
    }

    .action-view:hover {
        background: rgba(99, 102, 241, 0.2);
        transform: translateY(-2px);
    }

    .action-edit {
        background: rgba(139, 92, 246, 0.1);
        color: #a78bfa;
        border: 1px solid rgba(139, 92, 246, 0.2);
    }

    .action-edit:hover {
        background: rgba(139, 92, 246, 0.2);
        transform: translateY(-2px);
    }

    .action-delete {
        background: rgba(239, 68, 68, 0.1);
        color: #f87171;
        border: 1px solid rgba(239, 68, 68, 0.2);
    }

    .action-delete:hover {
        background: rgba(239, 68, 68, 0.2);
        transform: translateY(-2px);
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: #1e293b;
        border-radius: 16px;
        border: 1px solid rgba(148, 163, 184, 0.1);
    }

    .empty-icon {
        width: 120px;
        height: 120px;
        margin: 0 auto 1.5rem;
        color: #475569;
    }

    .empty-title {
        color: #e2e8f0;
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .empty-subtitle {
        color: #94a3b8;
        margin-bottom: 1.5rem;
    }

    /* Pagination */
    .pagination {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        margin-top: 2rem;
    }

    @media (max-width: 768px) {
        .users-page {
            padding: 1rem;
        }

        .page-header {
            padding: 1.5rem;
        }

        .page-title {
            font-size: 1.5rem;
        }

        .page-header-content {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

<div class="users-page">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <div>
                <h1 class="page-title">{{ __('admin.users_management') }}</h1>
                <p class="page-subtitle">{{ __('admin.manage_users_description') }}</p>
            </div>
            <a href="{{ route('admin.users.create') }}" class="create-btn">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>{{ __('admin.add_user') }}</span>
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar">
        <input
            type="text"
            class="filter-input"
            placeholder="{{ __('admin.search_users_placeholder') }}"
            id="searchInput"
        >
    </div>

    @if($users->count() > 0)
    <!-- Users Grid -->
    <div class="users-grid">
        @foreach($users as $user)
            <div class="user-card" data-name="{{ strtolower($user->name) }}" data-email="{{ strtolower($user->email) }}">
                <!-- Card Header -->
                <div class="user-card-header">
                    <div class="user-avatar">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <div class="user-info">
                        <div class="user-name">{{ $user->name }}</div>
                        <div class="user-email">{{ $user->email }}</div>
                    </div>
                    <div class="user-id">#{{ $user->id }}</div>
                </div>

                <!-- Stats -->
                <div class="user-stats">
                    <div class="stat-item stat-posts">
                        <span class="stat-value">{{ $user->posts_count ?? 0 }}</span>
                        <span class="stat-label">{{ __('admin.posts') }}</span>
                    </div>
                    <div class="stat-item stat-accounts">
                        <span class="stat-value">{{ $user->social_accounts_count ?? 0 }}</span>
                        <span class="stat-label">{{ __('admin.social_accounts') }}</span>
                    </div>
                </div>

                <!-- Meta -->
                <div class="user-meta">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span>{{ __('admin.joined') }}: {{ $user->created_at->format('M d, Y') }}</span>
                </div>

                <!-- Actions -->
                <div class="user-actions">
                    <a href="{{ route('admin.users.show', $user) }}" class="action-btn action-view">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        {{ __('admin.view') }}
                    </a>
                    <a href="{{ route('admin.users.edit', $user) }}" class="action-btn action-edit">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        {{ __('admin.edit') }}
                    </a>
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('admin.confirm_delete_user') }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="action-btn action-delete">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            {{ __('admin.delete') }}
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    @if($users->hasPages())
        <div class="pagination">
            {{ $users->links() }}
        </div>
    @endif
    @else
    <!-- Empty State -->
    <div class="empty-state">
        <svg class="empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
        </svg>
        <h2 class="empty-title">{{ __('admin.no_users_found') }}</h2>
        <p class="empty-subtitle">{{ __('admin.add_first_user') }}</p>
        <a href="{{ route('admin.users.create') }}" class="create-btn">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>{{ __('admin.add_user') }}</span>
        </a>
    </div>
    @endif
</div>

<script>
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    const userCards = document.querySelectorAll('.user-card');

    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();

        userCards.forEach(card => {
            const name = card.dataset.name;
            const email = card.dataset.email;

            if (name.includes(searchTerm) || email.includes(searchTerm)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
</script>
@endsection
