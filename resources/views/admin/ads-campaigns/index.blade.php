@extends('layouts.admin')

@section('title', __('Ads Campaigns'))

@section('content')
<style>
    body {
        font-family: {{ app()->getLocale() === 'ar' ? "'Cairo', sans-serif" : "'Inter', sans-serif" }};
    }

    .campaigns-page {
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

    /* Campaigns Grid */
    .campaigns-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
        gap: 1.5rem;
    }

    /* Campaign Card */
    .campaign-card {
        background: #1e293b;
        border-radius: 16px;
        padding: 1.75rem;
        border: 1px solid rgba(148, 163, 184, 0.1);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .campaign-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3);
        border-color: rgba(99, 102, 241, 0.3);
    }

    .campaign-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 1rem;
    }

    .campaign-name {
        color: #e2e8f0;
        font-weight: 700;
        font-size: 1.1rem;
        margin-bottom: 0.25rem;
    }

    .campaign-objective {
        color: #94a3b8;
        font-size: 0.875rem;
        text-transform: capitalize;
    }

    .status-badge {
        padding: 0.375rem 0.875rem;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-draft {
        background: rgba(148, 163, 184, 0.1);
        color: #94a3b8;
        border: 1px solid rgba(148, 163, 184, 0.2);
    }

    .status-active {
        background: rgba(16, 185, 129, 0.1);
        color: #10b981;
        border: 1px solid rgba(16, 185, 129, 0.2);
    }

    .status-paused {
        background: rgba(251, 191, 36, 0.1);
        color: #fbbf24;
        border: 1px solid rgba(251, 191, 36, 0.2);
    }

    .status-completed {
        background: rgba(99, 102, 241, 0.1);
        color: #818cf8;
        border: 1px solid rgba(99, 102, 241, 0.2);
    }

    .campaign-platforms {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1rem;
        flex-wrap: wrap;
    }

    .platform-badge {
        padding: 0.375rem 0.75rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        color: white;
    }

    .platform-facebook { background: #1877f2; }
    .platform-instagram { background: linear-gradient(135deg, #833ab4, #fd1d1d); }
    .platform-twitter { background: #1da1f2; }
    .platform-linkedin { background: #0077b5; }
    .platform-tiktok { background: #000000; }
    .platform-google { background: #4285f4; }

    .campaign-metrics {
        background: #0f172a;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1rem;
        border: 1px solid rgba(148, 163, 184, 0.1);
    }

    .metrics-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
    }

    .metric-item {
        text-align: center;
    }

    .metric-label {
        color: #94a3b8;
        font-size: 0.75rem;
        margin-bottom: 0.25rem;
    }

    .metric-value {
        color: #e2e8f0;
        font-weight: 700;
        font-size: 1.1rem;
    }

    .campaign-budget {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem;
        background: rgba(99, 102, 241, 0.05);
        border-radius: 8px;
        margin-bottom: 1rem;
    }

    .budget-label {
        color: #94a3b8;
        font-size: 0.875rem;
    }

    .budget-value {
        color: #818cf8;
        font-weight: 700;
        font-size: 1rem;
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1rem;
        color: #94a3b8;
        font-size: 0.85rem;
    }

    .campaign-actions {
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
        .campaigns-page {
            padding: 1rem;
        }

        .page-header {
            padding: 1.5rem;
        }

        .campaigns-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="campaigns-page">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">{{ __('Ads Campaigns') }}</h1>
            <p class="page-subtitle">{{ __('Monitor all advertising campaigns created by app users') }}</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar">
        <input
            type="text"
            class="filter-input"
            placeholder="{{ __('Search by campaign name or user...') }}"
            id="searchInput"
        >
        <select class="filter-input" style="flex: 0 0 auto; min-width: 150px;" id="statusFilter">
            <option value="">{{ __('All Status') }}</option>
            <option value="draft">{{ __('Draft') }}</option>
            <option value="active">{{ __('Active') }}</option>
            <option value="paused">{{ __('Paused') }}</option>
            <option value="completed">{{ __('Completed') }}</option>
        </select>
        <select class="filter-input" style="flex: 0 0 auto; min-width: 150px;" id="objectiveFilter">
            <option value="">{{ __('All Objectives') }}</option>
            <option value="awareness">{{ __('Awareness') }}</option>
            <option value="traffic">{{ __('Traffic') }}</option>
            <option value="engagement">{{ __('Engagement') }}</option>
            <option value="leads">{{ __('Leads') }}</option>
            <option value="conversions">{{ __('Conversions') }}</option>
            <option value="sales">{{ __('Sales') }}</option>
        </select>
    </div>

    @if($campaigns->count() > 0)
    <!-- Campaigns Grid -->
    <div class="campaigns-grid">
        @foreach($campaigns as $campaign)
            <div class="campaign-card" data-name="{{ strtolower($campaign->name) }}" data-user="{{ strtolower($campaign->user->name ?? '') }}" data-status="{{ $campaign->status }}" data-objective="{{ $campaign->objective }}">
                <!-- Header -->
                <div class="campaign-header">
                    <div>
                        <div class="campaign-name">{{ $campaign->name }}</div>
                        <div class="campaign-objective">{{ __(ucfirst($campaign->objective)) }}</div>
                    </div>
                    <div class="status-badge status-{{ $campaign->status }}">
                        {{ __(ucfirst($campaign->status)) }}
                    </div>
                </div>

                <!-- Platforms -->
                <div class="campaign-platforms">
                    @foreach($campaign->platforms ?? [] as $platform)
                        <span class="platform-badge platform-{{ $platform }}">
                            {{ ucfirst($platform) }}
                        </span>
                    @endforeach
                </div>

                <!-- Metrics -->
                <div class="campaign-metrics">
                    <div class="metrics-grid">
                        <div class="metric-item">
                            <div class="metric-label">{{ __('Impressions') }}</div>
                            <div class="metric-value">{{ number_format($campaign->analytics['impressions'] ?? 0) }}</div>
                        </div>
                        <div class="metric-item">
                            <div class="metric-label">{{ __('Clicks') }}</div>
                            <div class="metric-value">{{ number_format($campaign->analytics['clicks'] ?? 0) }}</div>
                        </div>
                        <div class="metric-item">
                            <div class="metric-label">{{ __('Conversions') }}</div>
                            <div class="metric-value">{{ number_format($campaign->analytics['conversions'] ?? 0) }}</div>
                        </div>
                        <div class="metric-item">
                            <div class="metric-label">{{ __('Spend') }}</div>
                            <div class="metric-value">${{ number_format($campaign->analytics['spend'] ?? 0, 2) }}</div>
                        </div>
                    </div>
                </div>

                <!-- Budget -->
                <div class="campaign-budget">
                    <span class="budget-label">{{ __(ucfirst($campaign->budget_type) . ' Budget') }}</span>
                    <span class="budget-value">${{ number_format($campaign->budget, 2) }}</span>
                </div>

                <!-- User Info -->
                <div class="user-info">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span>{{ __('User') }}: {{ $campaign->user->name ?? __('N/A') }}</span>
                </div>

                <!-- Actions -->
                <div class="campaign-actions">
                    <a href="{{ route('admin.ads-campaigns.show', $campaign) }}" class="action-btn action-view">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        {{ __('View Details') }}
                    </a>
                    <form action="{{ route('admin.ads-campaigns.destroy', $campaign) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this campaign?') }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="action-btn action-delete">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            {{ __('Delete') }}
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div style="margin-top: 2rem;">
        {{ $campaigns->links() }}
    </div>
    @else
    <!-- Empty State -->
    <div class="empty-state">
        <svg class="empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 00 2-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
        </svg>
        <h2 class="empty-title">{{ __('No Ads Campaigns') }}</h2>
        <p class="empty-subtitle">{{ __('Users haven\'t created any advertising campaigns yet through the mobile app') }}</p>
    </div>
    @endif
</div>

<script>
    // Filter functionality
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const objectiveFilter = document.getElementById('objectiveFilter');
    const campaignCards = document.querySelectorAll('.campaign-card');

    function filterCampaigns() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value.toLowerCase();
        const objectiveValue = objectiveFilter.value.toLowerCase();

        campaignCards.forEach(card => {
            const name = card.dataset.name;
            const user = card.dataset.user;
            const status = card.dataset.status;
            const objective = card.dataset.objective;

            const matchesSearch = name.includes(searchTerm) || user.includes(searchTerm);
            const matchesStatus = !statusValue || status === statusValue;
            const matchesObjective = !objectiveValue || objective === objectiveValue;

            if (matchesSearch && matchesStatus && matchesObjective) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    searchInput.addEventListener('input', filterCampaigns);
    statusFilter.addEventListener('change', filterCampaigns);
    objectiveFilter.addEventListener('change', filterCampaigns);
</script>
@endsection
