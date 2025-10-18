@extends('layouts.admin')

@section('title', __('admin.analytics'))

@section('content')
<style>
    body {
        font-family: {{ app()->getLocale() === 'ar' ? "'Cairo', sans-serif" : "'Inter', sans-serif" }};
    }

    .analytics-page {
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

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    /* Stat Card */
    .stat-card {
        background: #1e293b;
        border-radius: 16px;
        padding: 1.75rem;
        border: 1px solid rgba(148, 163, 184, 0.1);
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3);
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        {{ app()->getLocale() === 'ar' ? 'right: 0;' : 'left: 0;' }}
        width: 4px;
        height: 100%;
        background: linear-gradient(135deg, #6366f1, #8b5cf6, #ec4899);
    }

    .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 1rem;
    }

    .stat-info {
        flex: 1;
    }

    .stat-label {
        color: #94a3b8;
        font-size: 0.875rem;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 0.5rem;
    }

    .stat-value {
        color: #e2e8f0;
        font-size: 2.5rem;
        font-weight: 800;
        line-height: 1;
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(99, 102, 241, 0.1);
        border: 1px solid rgba(99, 102, 241, 0.2);
    }

    .stat-icon svg {
        width: 28px;
        height: 28px;
        color: #818cf8;
    }

    .stat-footer {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid rgba(148, 163, 184, 0.1);
    }

    .stat-trend {
        font-size: 0.875rem;
        font-weight: 600;
    }

    .stat-trend.positive {
        color: #10b981;
    }

    .stat-trend.negative {
        color: #ef4444;
    }

    .stat-period {
        color: #64748b;
        font-size: 0.75rem;
    }

    /* Chart Section */
    .chart-section {
        background: #1e293b;
        border-radius: 16px;
        padding: 2rem;
        border: 1px solid rgba(148, 163, 184, 0.1);
        margin-bottom: 2rem;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .section-title {
        color: #e2e8f0;
        font-size: 1.25rem;
        font-weight: 700;
    }

    .chart-placeholder {
        background: #0f172a;
        border-radius: 12px;
        padding: 4rem 2rem;
        text-align: center;
        border: 2px dashed rgba(148, 163, 184, 0.2);
    }

    .chart-placeholder svg {
        width: 80px;
        height: 80px;
        color: #475569;
        margin: 0 auto 1rem;
    }

    .chart-placeholder-text {
        color: #94a3b8;
        font-size: 1rem;
        margin-bottom: 0.5rem;
    }

    .chart-placeholder-subtext {
        color: #64748b;
        font-size: 0.875rem;
    }

    /* Platform Performance */
    .platform-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
    }

    .platform-card {
        background: #1e293b;
        border-radius: 16px;
        padding: 1.75rem;
        border: 1px solid rgba(148, 163, 184, 0.1);
    }

    .platform-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .platform-name {
        color: #e2e8f0;
        font-weight: 700;
        font-size: 1.1rem;
    }

    .platform-percentage {
        color: #818cf8;
        font-weight: 700;
        font-size: 1.5rem;
    }

    .progress-bar {
        width: 100%;
        height: 8px;
        background: #0f172a;
        border-radius: 4px;
        overflow: hidden;
        margin-bottom: 1rem;
    }

    .progress-fill {
        height: 100%;
        border-radius: 4px;
        transition: width 1s ease;
    }

    .progress-facebook { background: #1877f2; }
    .progress-instagram { background: linear-gradient(90deg, #833ab4, #fd1d1d); }
    .progress-twitter { background: #1da1f2; }
    .progress-linkedin { background: #0077b5; }
    .progress-tiktok { background: #000000; }

    .platform-stats {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }

    .platform-stat-item {
        background: #0f172a;
        border-radius: 8px;
        padding: 0.75rem;
        text-align: center;
    }

    .platform-stat-value {
        color: #e2e8f0;
        font-size: 1.25rem;
        font-weight: 700;
        display: block;
    }

    .platform-stat-label {
        color: #94a3b8;
        font-size: 0.75rem;
        text-transform: uppercase;
        margin-top: 0.25rem;
    }

    @media (max-width: 768px) {
        .analytics-page {
            padding: 1rem;
        }

        .page-header {
            padding: 1.5rem;
        }

        .page-title {
            font-size: 1.5rem;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="analytics-page">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">{{ __('admin.analytics_dashboard') }}</h1>
            <p class="page-subtitle">{{ __('admin.monitor_app_usage') }}</p>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <!-- Total Users -->
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-info">
                    <div class="stat-label">{{ __('admin.total_users') }}</div>
                    <div class="stat-value">{{ number_format($totalUsers) }}</div>
                </div>
                <div class="stat-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="stat-footer">
                <span class="stat-trend {{ $usersGrowth >= 0 ? 'positive' : 'negative' }}">
                    {{ $usersGrowth >= 0 ? '↑' : '↓' }} {{ abs($usersGrowth) }}%
                </span>
                <span class="stat-period">{{ __('admin.vs_last_month') }}</span>
            </div>
        </div>

        <!-- Total Posts -->
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-info">
                    <div class="stat-label">{{ __('admin.total_posts') }}</div>
                    <div class="stat-value">{{ number_format($totalPosts) }}</div>
                </div>
                <div class="stat-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
            <div class="stat-footer">
                <span class="stat-trend {{ $postsGrowth >= 0 ? 'positive' : 'negative' }}">
                    {{ $postsGrowth >= 0 ? '↑' : '↓' }} {{ abs($postsGrowth) }}%
                </span>
                <span class="stat-period">{{ __('admin.vs_last_month') }}</span>
            </div>
        </div>

        <!-- Total Engagement -->
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-info">
                    <div class="stat-label">{{ __('admin.total_engagement') }}</div>
                    <div class="stat-value">{{ $totalEngagement >= 1000 ? number_format($totalEngagement / 1000, 1) . 'K' : number_format($totalEngagement) }}</div>
                </div>
                <div class="stat-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"></path>
                    </svg>
                </div>
            </div>
            <div class="stat-footer">
                <span class="stat-trend {{ $engagementGrowth >= 0 ? 'positive' : 'negative' }}">
                    {{ $engagementGrowth >= 0 ? '↑' : '↓' }} {{ abs($engagementGrowth) }}%
                </span>
                <span class="stat-period">{{ __('admin.vs_last_month') }}</span>
            </div>
        </div>

        <!-- AI Generations -->
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-info">
                    <div class="stat-label">{{ __('admin.ai_generations') }}</div>
                    <div class="stat-value">{{ number_format($aiGenerations) }}</div>
                </div>
                <div class="stat-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                </div>
            </div>
            <div class="stat-footer">
                <span class="stat-trend {{ $aiGrowth >= 0 ? 'positive' : 'negative' }}">
                    {{ $aiGrowth >= 0 ? '↑' : '↓' }} {{ abs($aiGrowth) }}%
                </span>
                <span class="stat-period">{{ __('admin.vs_last_month') }}</span>
            </div>
        </div>
    </div>

    <!-- Chart Section -->
    <div class="chart-section">
        <div class="section-header">
            <h2 class="section-title">{{ __('admin.engagement_over_time') }}</h2>
        </div>
        <div class="chart-placeholder">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            <p class="chart-placeholder-text">{{ __('admin.chart_visualization') }}</p>
            <p class="chart-placeholder-subtext">{{ __('admin.chart_integration_note') }}</p>
        </div>
    </div>

    <!-- Platform Performance -->
    <div class="chart-section">
        <div class="section-header">
            <h2 class="section-title">{{ __('admin.platform_performance') }}</h2>
        </div>
        <div class="platform-grid">
            @forelse($platforms as $platform)
            <div class="platform-card">
                <div class="platform-header">
                    <span class="platform-name">{{ $platform['name'] }}</span>
                    <span class="platform-percentage">{{ $platform['percentage'] }}%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill progress-{{ strtolower($platform['name']) }}" style="width: {{ $platform['percentage'] }}%"></div>
                </div>
                <div class="platform-stats">
                    <div class="platform-stat-item">
                        <span class="platform-stat-value">{{ $platform['posts'] >= 1000 ? number_format($platform['posts'] / 1000, 1) . 'K' : number_format($platform['posts']) }}</span>
                        <span class="platform-stat-label">{{ __('admin.posts') }}</span>
                    </div>
                    <div class="platform-stat-item">
                        <span class="platform-stat-value">{{ $platform['engagement'] >= 1000 ? number_format($platform['engagement'] / 1000, 1) . 'K' : number_format($platform['engagement']) }}</span>
                        <span class="platform-stat-label">{{ __('admin.engagement') }}</span>
                    </div>
                </div>
            </div>
            @empty
            <div class="empty-state" style="grid-column: 1 / -1;">
                <p style="color: #94a3b8;">{{ __('admin.no_platform_data') }}</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
