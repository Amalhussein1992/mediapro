@extends('layouts.admin')

@section('title', __('admin.posts_management'))

@section('content')
<style>
    body {
        font-family: {{ app()->getLocale() === 'ar' ? "'Cairo', sans-serif" : "'Inter', sans-serif" }};
    }

    .posts-page {
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

    /* Posts Grid */
    .posts-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
        gap: 1.5rem;
    }

    @media (max-width: 768px) {
        .posts-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Post Card */
    .post-card {
        background: #1e293b;
        border-radius: 16px;
        padding: 1.5rem;
        border: 1px solid rgba(148, 163, 184, 0.1);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .post-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.2);
        border-color: rgba(99, 102, 241, 0.3);
    }

    .post-card-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 1rem;
    }

    .post-id {
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: white;
        padding: 0.375rem 0.875rem;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .post-status {
        padding: 0.375rem 0.875rem;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-published {
        background: rgba(16, 185, 129, 0.1);
        color: #10b981;
        border: 1px solid rgba(16, 185, 129, 0.2);
    }

    .status-scheduled {
        background: rgba(59, 130, 246, 0.1);
        color: #3b82f6;
        border: 1px solid rgba(59, 130, 246, 0.2);
    }

    .status-draft {
        background: rgba(148, 163, 184, 0.1);
        color: #94a3b8;
        border: 1px solid rgba(148, 163, 184, 0.2);
    }

    .post-user {
        margin-bottom: 1rem;
    }

    .user-name {
        color: #e2e8f0;
        font-weight: 600;
        font-size: 0.95rem;
        display: block;
    }

    .user-email {
        color: #94a3b8;
        font-size: 0.8rem;
        display: block;
        margin-top: 0.25rem;
    }

    .post-content {
        color: #cbd5e1;
        line-height: 1.6;
        margin-bottom: 1rem;
        min-height: 60px;
    }

    .post-platforms {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    .platform-badge {
        background: rgba(99, 102, 241, 0.1);
        color: #818cf8;
        padding: 0.375rem 0.75rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 500;
        border: 1px solid rgba(99, 102, 241, 0.2);
    }

    .post-schedule {
        color: #94a3b8;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    .post-actions {
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
        .posts-page {
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

<div class="posts-page">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <div>
                <h1 class="page-title">{{ __('admin.posts_management') }}</h1>
                <p class="page-subtitle">{{ __('admin.view_manage_user_posts') }}</p>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar">
        <input
            type="text"
            class="filter-input"
            placeholder="{{ __('admin.search_posts_placeholder') }}"
            id="searchInput"
        >
        <select class="filter-input" style="flex: 0 0 auto; min-width: 150px;" id="statusFilter">
            <option value="">{{ __('admin.all_statuses') }}</option>
            <option value="published">{{ __('admin.published') }}</option>
            <option value="scheduled">{{ __('admin.scheduled') }}</option>
            <option value="draft">{{ __('admin.draft') }}</option>
        </select>
    </div>

    @if($posts->count() > 0)
    <!-- Posts Grid -->
    <div class="posts-grid">
        @foreach($posts as $post)
            <div class="post-card" data-status="{{ $post->status }}" data-content="{{ strtolower($post->content) }}" data-user="{{ strtolower($post->user->name ?? '') }}">
                <!-- Card Header -->
                <div class="post-card-header">
                    <div class="post-id">#{{ $post->id }}</div>
                    <div class="post-status status-{{ $post->status }}">
                        @if($post->status === 'published')
                            {{ __('admin.published') }}
                        @elseif($post->status === 'scheduled')
                            {{ __('admin.scheduled') }}
                        @else
                            {{ __('admin.draft') }}
                        @endif
                    </div>
                </div>

                <!-- User Info -->
                <div class="post-user">
                    <span class="user-name">{{ $post->user->name ?? __('admin.na') }}</span>
                    <span class="user-email">{{ $post->user->email ?? __('admin.na') }}</span>
                </div>

                <!-- Content -->
                <div class="post-content">
                    {{ Str::limit($post->content, 120) }}
                </div>

                <!-- Platforms -->
                <div class="post-platforms">
                    @if(is_array($post->platforms))
                        @foreach($post->platforms as $platform)
                            <span class="platform-badge">{{ ucfirst($platform) }}</span>
                        @endforeach
                    @endif
                </div>

                <!-- Schedule Info -->
                <div class="post-schedule">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>
                        @if($post->scheduled_at)
                            {{ __('admin.scheduled_for') }}: {{ $post->scheduled_at->format('M d, Y H:i') }}
                        @else
                            {{ __('admin.no_schedule_set') }}
                        @endif
                    </span>
                </div>

                <!-- Actions -->
                <div class="post-actions">
                    <a href="{{ route('admin.posts.show', $post) }}" class="action-btn action-view">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        {{ __('admin.view') }}
                    </a>
                    <a href="{{ route('admin.posts.edit', $post) }}" class="action-btn action-edit">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        {{ __('admin.edit') }}
                    </a>
                    <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('admin.confirm_delete_post') }}')">
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
    @if($posts->hasPages())
        <div class="pagination">
            {{ $posts->links() }}
        </div>
    @endif
    @else
    <!-- Empty State -->
    <div class="empty-state">
        <svg class="empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        <h2 class="empty-title">{{ __('admin.no_posts_found') }}</h2>
        <p class="empty-subtitle">{{ __('admin.users_create_posts_from_mobile') }}</p>
    </div>
    @endif
</div>

<script>
    // Search and Filter functionality
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const postCards = document.querySelectorAll('.post-card');

    function filterPosts() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value.toLowerCase();

        postCards.forEach(card => {
            const content = card.dataset.content;
            const user = card.dataset.user;
            const status = card.dataset.status;

            const matchesSearch = content.includes(searchTerm) || user.includes(searchTerm);
            const matchesStatus = !statusValue || status === statusValue;

            if (matchesSearch && matchesStatus) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    searchInput.addEventListener('input', filterPosts);
    statusFilter.addEventListener('change', filterPosts);
</script>
@endsection
