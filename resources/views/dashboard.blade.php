@extends('layouts.admin')

@section('title', __('Dashboard'))

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: {{ app()->getLocale() === 'ar' ? "'Cairo', sans-serif" : "'Inter', sans-serif" }};
        background: #0f172a;
        color: #e2e8f0;
        overflow-x: hidden;
    }

    /* Container */
    .dashboard-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 1rem;
    }

    @media (min-width: 768px) {
        .dashboard-container {
            padding: 2rem;
        }
    }

    /* Hero Banner */
    .hero-banner {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        border-radius: 1.5rem;
        padding: 2rem;
        margin-bottom: 2rem;
        border: 1px solid rgba(148, 163, 184, 0.1);
        position: relative;
        overflow: hidden;
    }

    .hero-banner::before {
        content: '';
        position: absolute;
        top: -50%;
        {{ app()->getLocale() === 'ar' ? 'left: -20%;' : 'right: -20%;' }}
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.15) 0%, transparent 70%);
        border-radius: 50%;
        animation: float 6s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-20px); }
    }

    .hero-content {
        position: relative;
        z-index: 1;
    }

    .hero-title {
        font-size: 1.875rem;
        font-weight: 900;
        color: #f8fafc;
        margin-bottom: 0.5rem;
        line-height: 1.2;
    }

    @media (min-width: 768px) {
        .hero-title {
            font-size: 2.5rem;
        }
    }

    .hero-gradient-text {
        background: linear-gradient(135deg, #6366f1 0%, #a855f7 50%, #ec4899 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-weight: 900;
    }

    .hero-subtitle {
        color: #94a3b8;
        font-size: 1rem;
        margin-bottom: 1.5rem;
    }

    @media (min-width: 768px) {
        .hero-subtitle {
            font-size: 1.125rem;
        }
    }

    .hero-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 0.75rem;
        font-weight: 600;
        font-size: 0.875rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        color: white;
        box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.5);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 35px -5px rgba(99, 102, 241, 0.6);
    }

    .btn-secondary {
        background: rgba(148, 163, 184, 0.1);
        color: #e2e8f0;
        border: 1px solid rgba(148, 163, 184, 0.2);
    }

    .btn-secondary:hover {
        background: rgba(148, 163, 184, 0.15);
        border-color: rgba(148, 163, 184, 0.3);
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
        margin-bottom: 2rem;
    }

    @media (min-width: 640px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (min-width: 1024px) {
        .stats-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    .stat-card {
        background: #1e293b;
        border: 1px solid rgba(148, 163, 184, 0.1);
        border-radius: 1.25rem;
        padding: 1.5rem;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.5);
        border-color: rgba(99, 102, 241, 0.3);
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #6366f1, #8b5cf6);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .stat-card:hover::before {
        opacity: 1;
    }

    .stat-icon {
        width: 3rem;
        height: 3rem;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
    }

    .stat-icon svg {
        width: 1.5rem;
        height: 1.5rem;
        color: white;
    }

    .stat-label {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #94a3b8;
        margin-bottom: 0.5rem;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 900;
        color: #f8fafc;
        line-height: 1;
        margin-bottom: 0.5rem;
    }

    @media (min-width: 768px) {
        .stat-value {
            font-size: 2.5rem;
        }
    }

    .stat-change {
        font-size: 0.75rem;
        color: #10b981;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    /* Main Grid */
    .main-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    @media (min-width: 1024px) {
        .main-grid {
            grid-template-columns: 2fr 1fr;
        }
    }

    /* Chart Card */
    .chart-card {
        background: #1e293b;
        border: 1px solid rgba(148, 163, 184, 0.1);
        border-radius: 1.25rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    @media (min-width: 768px) {
        .chart-card {
            padding: 2rem;
        }
    }

    .chart-header {
        margin-bottom: 1.5rem;
    }

    .chart-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #f8fafc;
        margin-bottom: 0.25rem;
    }

    @media (min-width: 768px) {
        .chart-title {
            font-size: 1.5rem;
        }
    }

    .chart-subtitle {
        font-size: 0.875rem;
        color: #64748b;
    }

    .chart-wrapper {
        position: relative;
        height: 250px;
    }

    @media (min-width: 768px) {
        .chart-wrapper {
            height: 300px;
        }
    }

    /* Quick Actions */
    .quick-actions {
        background: #1e293b;
        border: 1px solid rgba(148, 163, 184, 0.1);
        border-radius: 1.25rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .section-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: #f8fafc;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .section-icon {
        width: 2rem;
        height: 2rem;
        border-radius: 0.5rem;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .section-icon svg {
        width: 1rem;
        height: 1rem;
        color: white;
    }

    .action-list {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .action-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: rgba(148, 163, 184, 0.05);
        border: 1px solid rgba(148, 163, 184, 0.1);
        border-radius: 0.75rem;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .action-item:hover {
        background: rgba(99, 102, 241, 0.1);
        border-color: rgba(99, 102, 241, 0.3);
        transform: translateX({{ app()->getLocale() === 'ar' ? '-5px' : '5px' }});
    }

    .action-icon {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .action-icon svg {
        width: 1.25rem;
        height: 1.25rem;
        color: white;
    }

    .action-content {
        flex: 1;
    }

    .action-title {
        font-size: 0.875rem;
        font-weight: 600;
        color: #f8fafc;
        margin-bottom: 0.125rem;
    }

    .action-subtitle {
        font-size: 0.75rem;
        color: #64748b;
    }

    /* Activity Feed */
    .activity-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

    @media (min-width: 1024px) {
        .activity-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    .activity-card {
        background: #1e293b;
        border: 1px solid rgba(148, 163, 184, 0.1);
        border-radius: 1.25rem;
        overflow: hidden;
    }

    .activity-header {
        padding: 1.5rem;
        border-bottom: 1px solid rgba(148, 163, 184, 0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .activity-title-group {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .activity-icon {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .activity-icon svg {
        width: 1.25rem;
        height: 1.25rem;
        color: white;
    }

    .activity-title {
        font-size: 1rem;
        font-weight: 700;
        color: #f8fafc;
    }

    .activity-link {
        font-size: 0.875rem;
        font-weight: 600;
        color: #6366f1;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .activity-link:hover {
        color: #8b5cf6;
    }

    .activity-list {
        max-height: 400px;
        overflow-y: auto;
    }

    .activity-list::-webkit-scrollbar {
        width: 6px;
    }

    .activity-list::-webkit-scrollbar-track {
        background: transparent;
    }

    .activity-list::-webkit-scrollbar-thumb {
        background: rgba(148, 163, 184, 0.2);
        border-radius: 3px;
    }

    .activity-item {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid rgba(148, 163, 184, 0.05);
        transition: background 0.3s ease;
    }

    .activity-item:hover {
        background: rgba(148, 163, 184, 0.05);
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-item-content {
        display: flex;
        gap: 1rem;
        align-items: start;
    }

    .activity-avatar {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 0.75rem;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.875rem;
        color: white;
        flex-shrink: 0;
    }

    .activity-details {
        flex: 1;
        min-width: 0;
    }

    .activity-user {
        font-size: 0.875rem;
        font-weight: 600;
        color: #f8fafc;
        margin-bottom: 0.25rem;
    }

    .activity-text {
        font-size: 0.875rem;
        color: #94a3b8;
        margin-bottom: 0.25rem;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .activity-time {
        font-size: 0.75rem;
        color: #64748b;
    }

    .badge {
        padding: 0.25rem 0.75rem;
        border-radius: 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-block;
    }

    .badge-success {
        background: rgba(16, 185, 129, 0.15);
        color: #10b981;
    }

    .badge-info {
        background: rgba(99, 102, 241, 0.15);
        color: #6366f1;
    }

    .badge-warning {
        background: rgba(251, 191, 36, 0.15);
        color: #fbbf24;
    }

    /* Empty State */
    .empty-state {
        padding: 3rem 1.5rem;
        text-align: center;
    }

    .empty-icon {
        width: 4rem;
        height: 4rem;
        margin: 0 auto 1rem;
        background: rgba(148, 163, 184, 0.1);
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .empty-icon svg {
        width: 2rem;
        height: 2rem;
        color: #64748b;
    }

    .empty-title {
        font-size: 1rem;
        font-weight: 600;
        color: #94a3b8;
        margin-bottom: 0.25rem;
    }

    .empty-subtitle {
        font-size: 0.875rem;
        color: #64748b;
    }

    /* Responsive utilities */
    @media (max-width: 639px) {
        .hero-banner {
            padding: 1.5rem;
        }

        .stat-card {
            padding: 1.25rem;
        }

        .chart-card {
            padding: 1.25rem;
        }
    }

    /* Animation */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-in {
        animation: fadeInUp 0.6s ease-out;
    }

    .animate-in-1 { animation-delay: 0.1s; animation-fill-mode: both; }
    .animate-in-2 { animation-delay: 0.2s; animation-fill-mode: both; }
    .animate-in-3 { animation-delay: 0.3s; animation-fill-mode: both; }
    .animate-in-4 { animation-delay: 0.4s; animation-fill-mode: both; }
    .animate-in-5 { animation-delay: 0.5s; animation-fill-mode: both; }
    .animate-in-6 { animation-delay: 0.6s; animation-fill-mode: both; }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    <!-- Hero Banner -->
    <div class="hero-banner animate-in animate-in-1">
        <div class="hero-content">
            <h1 class="hero-title">{{ __('Welcome Back') }}, <span class="hero-gradient-text">{{ __('Admin') }}</span></h1>
            <p class="hero-subtitle">{{ __('Monitor your social media performance and manage your content effortlessly') }}</p>
            <div class="hero-buttons">
                <a href="{{ route('admin.posts.create') }}" class="btn btn-primary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 1rem; height: 1rem;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    {{ __('Create New Post') }}
                </a>
                <a href="{{ route('admin.analytics.index') }}" class="btn btn-secondary">
                    {{ __('View Analytics') }}
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card animate-in animate-in-2">
            <div class="stat-icon" style="background: linear-gradient(135deg, #6366f1, #8b5cf6);">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div class="stat-label">{{ __('Total Users') }}</div>
            <div class="stat-value">{{ number_format($stats['total_users']) }}</div>
            <div class="stat-change">
                <svg fill="currentColor" viewBox="0 0 20 20" style="width: 0.875rem; height: 0.875rem;">
                    <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"/>
                </svg>
                {{ __('+12.5% this month') }}
            </div>
        </div>

        <div class="stat-card animate-in animate-in-3">
            <div class="stat-icon" style="background: linear-gradient(135deg, #8b5cf6, #a855f7);">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                </svg>
            </div>
            <div class="stat-label">{{ __('Total Posts') }}</div>
            <div class="stat-value">{{ number_format($stats['total_posts']) }}</div>
            <div class="stat-change">
                <svg fill="currentColor" viewBox="0 0 20 20" style="width: 0.875rem; height: 0.875rem;">
                    <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"/>
                </svg>
                {{ __('+8.3% this month') }}
            </div>
        </div>

        <div class="stat-card animate-in animate-in-4">
            <div class="stat-icon" style="background: linear-gradient(135deg, #ec4899, #f472b6);">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                </svg>
            </div>
            <div class="stat-label">{{ __('Connected Accounts') }}</div>
            <div class="stat-value">{{ number_format($stats['total_social_accounts']) }}</div>
            <div class="stat-change">
                <svg fill="currentColor" viewBox="0 0 20 20" style="width: 0.875rem; height: 0.875rem;">
                    <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"/>
                </svg>
                {{ __('Active') }}
            </div>
        </div>

        <div class="stat-card animate-in animate-in-5">
            <div class="stat-icon" style="background: linear-gradient(135deg, #10b981, #34d399);">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="stat-label">{{ __('Published Posts') }}</div>
            <div class="stat-value">{{ number_format($stats['published_posts']) }}</div>
            <div class="stat-change">
                <svg fill="currentColor" viewBox="0 0 20 20" style="width: 0.875rem; height: 0.875rem;">
                    <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"/>
                </svg>
                {{ __('+15.8% this month') }}
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="main-grid">
        <!-- Charts Section -->
        <div>
            <!-- Post Status Chart -->
            <div class="chart-card animate-in animate-in-2">
                <div class="chart-header">
                    <h3 class="chart-title">{{ __('Content Distribution') }}</h3>
                    <p class="chart-subtitle">{{ __('Overview of your post statuses') }}</p>
                </div>
                <div class="chart-wrapper">
                    <canvas id="postStatusChart"></canvas>
                </div>
            </div>

            <!-- Platform Chart -->
            <div class="chart-card animate-in animate-in-3">
                <div class="chart-header">
                    <h3 class="chart-title">{{ __('Platform Performance') }}</h3>
                    <p class="chart-subtitle">{{ __('Posts across different platforms') }}</p>
                </div>
                <div class="chart-wrapper">
                    <canvas id="platformChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Quick Actions -->
            <div class="quick-actions animate-in animate-in-4">
                <div class="section-title">
                    <div class="section-icon">
                        <svg fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    {{ __('Quick Actions') }}
                </div>
                <div class="action-list">
                    <a href="{{ route('admin.posts.create') }}" class="action-item">
                        <div class="action-icon" style="background: linear-gradient(135deg, #6366f1, #8b5cf6);">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                        <div class="action-content">
                            <div class="action-title">{{ __('Create Post') }}</div>
                            <div class="action-subtitle">{{ __('New content') }}</div>
                        </div>
                    </a>

                    <a href="{{ route('admin.users.create') }}" class="action-item">
                        <div class="action-icon" style="background: linear-gradient(135deg, #8b5cf6, #a855f7);">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                        </div>
                        <div class="action-content">
                            <div class="action-title">{{ __('Add User') }}</div>
                            <div class="action-subtitle">{{ __('New member') }}</div>
                        </div>
                    </a>

                    <a href="{{ route('admin.social-accounts.create') }}" class="action-item">
                        <div class="action-icon" style="background: linear-gradient(135deg, #ec4899, #f472b6);">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                            </svg>
                        </div>
                        <div class="action-content">
                            <div class="action-title">{{ __('Connect Account') }}</div>
                            <div class="action-subtitle">{{ __('Link platform') }}</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Feed -->
    <div class="activity-grid">
        <!-- Recent Posts -->
        <div class="activity-card animate-in animate-in-5">
            <div class="activity-header">
                <div class="activity-title-group">
                    <div class="activity-icon" style="background: linear-gradient(135deg, #6366f1, #8b5cf6);">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="activity-title">{{ __('Recent Posts') }}</div>
                </div>
                <a href="{{ route('admin.posts.index') }}" class="activity-link">{{ __('View All') }} →</a>
            </div>
            <div class="activity-list">
                @forelse($recent_posts->take(5) as $post)
                    <div class="activity-item">
                        <div class="activity-item-content">
                            <div class="activity-avatar">{{ strtoupper(substr($post->user->name ?? 'U', 0, 2)) }}</div>
                            <div class="activity-details">
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                                    <div class="activity-user">{{ $post->user->name ?? __('Unknown') }}</div>
                                    @if($post->status === 'published')
                                        <span class="badge badge-success">{{ __('Published') }}</span>
                                    @elseif($post->status === 'scheduled')
                                        <span class="badge badge-info">{{ __('Scheduled') }}</span>
                                    @else
                                        <span class="badge badge-warning">{{ __('Draft') }}</span>
                                    @endif
                                </div>
                                <div class="activity-text">{{ $post->content }}</div>
                                <div class="activity-time">{{ $post->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <div class="empty-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div class="empty-title">{{ __('No recent posts') }}</div>
                        <div class="empty-subtitle">{{ __('Create your first post to get started') }}</div>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Users -->
        <div class="activity-card animate-in animate-in-6">
            <div class="activity-header">
                <div class="activity-title-group">
                    <div class="activity-icon" style="background: linear-gradient(135deg, #8b5cf6, #a855f7);">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <div class="activity-title">{{ __('Recent Users') }}</div>
                </div>
                <a href="{{ route('admin.users.index') }}" class="activity-link">{{ __('View All') }} →</a>
            </div>
            <div class="activity-list">
                @forelse($recent_users->take(5) as $user)
                    <div class="activity-item">
                        <div class="activity-item-content">
                            <div class="activity-avatar" style="background: linear-gradient(135deg, #ec4899, #f472b6);">{{ strtoupper(substr($user->name, 0, 2)) }}</div>
                            <div class="activity-details">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <div>
                                        <div class="activity-user">{{ $user->name }}</div>
                                        <div class="activity-text">{{ $user->email }}</div>
                                    </div>
                                    <span class="badge badge-success">{{ __('Active') }}</span>
                                </div>
                                <div class="activity-time">{{ $user->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <div class="empty-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <div class="empty-title">{{ __('No recent users') }}</div>
                        <div class="empty-subtitle">{{ __('Waiting for new registrations') }}</div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Chart.js Configuration
Chart.defaults.color = '#94a3b8';
Chart.defaults.borderColor = 'rgba(148, 163, 184, 0.1)';
Chart.defaults.font.family = "'{{ app()->getLocale() === 'ar' ? 'Cairo' : 'Inter' }}', sans-serif";

// Doughnut Chart
const postStatusCtx = document.getElementById('postStatusChart').getContext('2d');
new Chart(postStatusCtx, {
    type: 'doughnut',
    data: {
        labels: ['{{ __("Scheduled") }}', '{{ __("Draft") }}', '{{ __("Published") }}'],
        datasets: [{
            data: [{{ $stats['scheduled_posts'] }}, {{ $stats['draft_posts'] }}, {{ $stats['published_posts'] }}],
            backgroundColor: ['#6366f1', '#fbbf24', '#10b981'],
            borderWidth: 0,
            hoverOffset: 10
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 15,
                    font: { size: 12, weight: '600' },
                    usePointStyle: true,
                    color: '#e2e8f0'
                }
            },
            tooltip: {
                backgroundColor: '#1e293b',
                padding: 12,
                titleFont: { size: 14, weight: '700' },
                bodyFont: { size: 13 },
                cornerRadius: 8,
                borderColor: 'rgba(148, 163, 184, 0.2)',
                borderWidth: 1
            }
        },
        cutout: '65%'
    }
});

// Bar Chart
const platformCtx = document.getElementById('platformChart').getContext('2d');
new Chart(platformCtx, {
    type: 'bar',
    data: {
        labels: ['Instagram', 'Facebook', 'Twitter', 'LinkedIn', 'TikTok'],
        datasets: [{
            label: '{{ __("Posts") }}',
            data: [45, 38, 25, 20, 15],
            backgroundColor: ['#8b5cf6', '#6366f1', '#3b82f6', '#06b6d4', '#ec4899'],
            borderWidth: 0,
            borderRadius: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#1e293b',
                padding: 12,
                titleFont: { size: 14, weight: '700' },
                bodyFont: { size: 13 },
                cornerRadius: 8,
                borderColor: 'rgba(148, 163, 184, 0.2)',
                borderWidth: 1
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    font: { size: 11, weight: '600' },
                    color: '#94a3b8'
                },
                grid: {
                    color: 'rgba(148, 163, 184, 0.08)',
                    drawBorder: false
                }
            },
            x: {
                ticks: {
                    font: { size: 11, weight: '600' },
                    color: '#94a3b8'
                },
                grid: { display: false }
            }
        }
    }
});
</script>
@endpush
