@extends('layouts.admin')

@section('title', __('Social Accounts'))

@section('content')
<style>
    body {
        font-family: {{ app()->getLocale() === 'ar' ? "'Cairo', sans-serif" : "'Inter', sans-serif" }};
    }

    .accounts-page {
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
    }

    .page-title {
        font-size: 2rem;
        font-weight: 800;
        color: white;
        margin: 0 0 0.5rem 0;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .page-subtitle {
        color: rgba(255, 255, 255, 0.9);
        font-size: 1rem;
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

    /* Accounts Grid */
    .accounts-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 1.5rem;
    }

    @media (max-width: 768px) {
        .accounts-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Account Card */
    .account-card {
        background: #1e293b;
        border-radius: 16px;
        padding: 1.75rem;
        border: 1px solid rgba(148, 163, 184, 0.1);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .account-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.2);
        border-color: rgba(99, 102, 241, 0.3);
    }

    .account-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .platform-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
        font-weight: 700;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
    }

    .platform-facebook { background: #1877f2; }
    .platform-twitter { background: #1da1f2; }
    .platform-instagram { background: linear-gradient(135deg, #833ab4, #fd1d1d, #fcb045); }
    .platform-linkedin { background: #0077b5; }
    .platform-tiktok { background: #000000; }

    .account-info {
        flex: 1;
    }

    .account-name {
        color: #e2e8f0;
        font-weight: 700;
        font-size: 1.1rem;
        margin-bottom: 0.25rem;
    }

    .platform-name {
        color: #94a3b8;
        font-size: 0.875rem;
    }

    .status-badge {
        padding: 0.375rem 0.875rem;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-active {
        background: rgba(16, 185, 129, 0.1);
        color: #10b981;
        border: 1px solid rgba(16, 185, 129, 0.2);
    }

    .status-inactive {
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
        border: 1px solid rgba(239, 68, 68, 0.2);
    }

    .account-details {
        background: #0f172a;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1.5rem;
        border: 1px solid rgba(148, 163, 184, 0.1);
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
    }

    .detail-row:not(:last-child) {
        border-bottom: 1px solid rgba(148, 163, 184, 0.1);
    }

    .detail-label {
        color: #94a3b8;
        font-size: 0.875rem;
    }

    .detail-value {
        color: #e2e8f0;
        font-weight: 600;
        font-size: 0.875rem;
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
        color: #94a3b8;
        font-size: 0.85rem;
    }

    .account-actions {
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

    @media (max-width: 768px) {
        .accounts-page {
            padding: 1rem;
        }

        .page-header {
            padding: 1.5rem;
        }

        .page-title {
            font-size: 1.5rem;
        }
    }
</style>

<div class="accounts-page">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">{{ __('Connected Social Accounts') }}</h1>
            <p class="page-subtitle">{{ __('Monitor all social media accounts connected by app users') }}</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar">
        <input
            type="text"
            class="filter-input"
            placeholder="{{ __('Search by account name or user...') }}"
            id="searchInput"
        >
        <select class="filter-input" style="flex: 0 0 auto; min-width: 150px;" id="platformFilter">
            <option value="">{{ __('All Platforms') }}</option>
            <option value="facebook">Facebook</option>
            <option value="twitter">Twitter</option>
            <option value="instagram">Instagram</option>
            <option value="linkedin">LinkedIn</option>
            <option value="tiktok">TikTok</option>
        </select>
        <select class="filter-input" style="flex: 0 0 auto; min-width: 150px;" id="statusFilter">
            <option value="">{{ __('All Status') }}</option>
            <option value="active">{{ __('Active') }}</option>
            <option value="inactive">{{ __('Inactive') }}</option>
        </select>
    </div>

    @if($accounts->count() > 0)
    <!-- Accounts Grid -->
    <div class="accounts-grid">
        @foreach($accounts as $account)
            <div class="account-card" data-platform="{{ $account->platform }}" data-status="{{ $account->is_active ? 'active' : 'inactive' }}" data-name="{{ strtolower($account->account_name) }}" data-user="{{ strtolower($account->user->name ?? '') }}">
                <!-- Header -->
                <div class="account-header">
                    <div class="platform-icon platform-{{ $account->platform }}">
                        @if($account->platform === 'facebook')
                            FB
                        @elseif($account->platform === 'twitter')
                            TW
                        @elseif($account->platform === 'instagram')
                            IG
                        @elseif($account->platform === 'linkedin')
                            IN
                        @elseif($account->platform === 'tiktok')
                            TT
                        @else
                            SM
                        @endif
                    </div>
                    <div class="account-info">
                        <div class="account-name">{{ $account->account_name }}</div>
                        <div class="platform-name">{{ ucfirst($account->platform) }}</div>
                    </div>
                    <div class="status-badge status-{{ $account->is_active ? 'active' : 'inactive' }}">
                        {{ $account->is_active ? __('Active') : __('Inactive') }}
                    </div>
                </div>

                <!-- Details -->
                <div class="account-details">
                    <div class="detail-row">
                        <span class="detail-label">{{ __('Account ID') }}</span>
                        <span class="detail-value">{{ Str::limit($account->account_id, 15) }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">{{ __('Connected') }}</span>
                        <span class="detail-value">{{ $account->created_at->format('M d, Y') }}</span>
                    </div>
                </div>

                <!-- User Info -->
                <div class="user-info">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span>{{ __('User') }}: {{ $account->user->name ?? __('N/A') }}</span>
                </div>

                <!-- Actions -->
                <div class="account-actions">
                    <a href="{{ route('admin.social-accounts.show', $account) }}" class="action-btn action-view">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        {{ __('View Details') }}
                    </a>
                    <form action="{{ route('admin.social-accounts.destroy', $account) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Are you sure you want to disconnect this account?') }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="action-btn action-delete">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            {{ __('Disconnect') }}
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
    @else
    <!-- Empty State -->
    <div class="empty-state">
        <svg class="empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
        </svg>
        <h2 class="empty-title">{{ __('No Social Accounts Connected') }}</h2>
        <p class="empty-subtitle">{{ __('Users haven\'t connected any social media accounts yet through the mobile app') }}</p>
    </div>
    @endif
</div>

<script>
    // Filter functionality
    const searchInput = document.getElementById('searchInput');
    const platformFilter = document.getElementById('platformFilter');
    const statusFilter = document.getElementById('statusFilter');
    const accountCards = document.querySelectorAll('.account-card');

    function filterAccounts() {
        const searchTerm = searchInput.value.toLowerCase();
        const platformValue = platformFilter.value.toLowerCase();
        const statusValue = statusFilter.value.toLowerCase();

        accountCards.forEach(card => {
            const name = card.dataset.name;
            const user = card.dataset.user;
            const platform = card.dataset.platform;
            const status = card.dataset.status;

            const matchesSearch = name.includes(searchTerm) || user.includes(searchTerm);
            const matchesPlatform = !platformValue || platform === platformValue;
            const matchesStatus = !statusValue || status === statusValue;

            if (matchesSearch && matchesPlatform && matchesStatus) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    searchInput.addEventListener('input', filterAccounts);
    platformFilter.addEventListener('change', filterAccounts);
    statusFilter.addEventListener('change', filterAccounts);
</script>
@endsection
