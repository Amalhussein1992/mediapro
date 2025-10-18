@extends('layouts.admin')

@section('title', __('Brand Kits'))

@section('content')
<style>
    body {
        font-family: {{ app()->getLocale() === 'ar' ? "'Cairo', sans-serif" : "'Inter', sans-serif" }};
    }

    .brand-kits-page {
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

    /* Brand Kits Grid */
    .brand-kits-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 1.5rem;
    }

    @media (max-width: 768px) {
        .brand-kits-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Brand Kit Card */
    .brand-kit-card {
        background: #1e293b;
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid rgba(148, 163, 184, 0.1);
        transition: all 0.3s ease;
    }

    .brand-kit-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3);
        border-color: rgba(99, 102, 241, 0.3);
    }

    .brand-kit-header {
        height: 120px;
        position: relative;
        overflow: hidden;
    }

    .brand-kit-body {
        padding: 1.75rem;
    }

    .brand-kit-name {
        color: #e2e8f0;
        font-weight: 700;
        font-size: 1.1rem;
        margin-bottom: 0.5rem;
    }

    .brand-kit-description {
        color: #94a3b8;
        font-size: 0.875rem;
        margin-bottom: 1.5rem;
        line-height: 1.5;
    }

    .brand-colors {
        margin-bottom: 1.5rem;
    }

    .colors-label {
        color: #94a3b8;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 0.75rem;
        display: block;
    }

    .colors-grid {
        display: flex;
        gap: 0.5rem;
    }

    .color-box {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        border: 2px solid rgba(148, 163, 184, 0.2);
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .color-box:hover {
        transform: scale(1.1);
        border-color: rgba(99, 102, 241, 0.5);
    }

    .brand-fonts {
        background: #0f172a;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1.5rem;
        border: 1px solid rgba(148, 163, 184, 0.1);
    }

    .fonts-label {
        color: #94a3b8;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 0.5rem;
        display: block;
    }

    .fonts-list {
        color: #e2e8f0;
        font-size: 0.875rem;
    }

    .brand-kit-actions {
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
        .brand-kits-page {
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

<div class="brand-kits-page">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">{{ app()->getLocale() === 'ar' ? 'مجموعات العلامة التجارية' : 'Brand Kits' }}</h1>
            <p class="page-subtitle">{{ app()->getLocale() === 'ar' ? 'عرض أصول العلامة التجارية والإرشادات التي أنشأها مستخدمو التطبيق' : 'View brand assets and guidelines created by app users' }}</p>
        </div>
    </div>

    @if(session('success'))
    <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 12px; padding: 1rem; margin-bottom: 1.5rem; color: #10b981;">
        <i class="fas fa-check-circle" style="margin-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}: 0.5rem;"></i>
        {{ session('success') }}
    </div>
    @endif

    <!-- Brand Kits Grid -->
    @if($brandKits->count() > 0)
    <div class="brand-kits-grid">
        @foreach($brandKits as $brandKit)
        <div class="brand-kit-card">
            @php
                $colors = is_array($brandKit->colors) ? $brandKit->colors : json_decode($brandKit->colors ?? '[]', true);
                $firstColor = $colors[0] ?? '#3b82f6';
                $secondColor = $colors[1] ?? '#8b5cf6';
            @endphp
            <div class="brand-kit-header" style="background: linear-gradient(135deg, {{ $firstColor }}, {{ $secondColor }});">
                @if($brandKit->is_default)
                <div style="position: absolute; top: 1rem; {{ app()->getLocale() === 'ar' ? 'left: 1rem;' : 'right: 1rem;' }} background: rgba(16, 185, 129, 0.9); color: white; padding: 0.25rem 0.75rem; border-radius: 0.5rem; font-size: 0.75rem; font-weight: 600;">
                    {{ app()->getLocale() === 'ar' ? 'افتراضي' : 'Default' }}
                </div>
                @endif
            </div>
            <div class="brand-kit-body">
                <h3 class="brand-kit-name">{{ $brandKit->name }}</h3>
                @if($brandKit->guidelines && is_array($brandKit->guidelines))
                    <p class="brand-kit-description">{{ Str::limit($brandKit->guidelines['text'] ?? '', 100) }}</p>
                @elseif($brandKit->guidelines && is_string($brandKit->guidelines))
                    @php $guidelinesData = json_decode($brandKit->guidelines, true); @endphp
                    <p class="brand-kit-description">{{ Str::limit($guidelinesData['text'] ?? '', 100) }}</p>
                @else
                    <p class="brand-kit-description">{{ app()->getLocale() === 'ar' ? 'مجموعة علامة تجارية شاملة للمحتوى' : 'Complete brand kit for content creation' }}</p>
                @endif

                <div class="brand-colors">
                    <span class="colors-label">{{ app()->getLocale() === 'ar' ? 'ألوان العلامة التجارية' : 'Brand Colors' }}</span>
                    <div class="colors-grid">
                        @foreach(array_slice($colors, 0, 5) as $color)
                        <div class="color-box" style="background: {{ $color }};" title="{{ $color }}"></div>
                        @endforeach
                    </div>
                </div>

                @php
                    $fonts = is_array($brandKit->fonts) ? $brandKit->fonts : json_decode($brandKit->fonts ?? '[]', true);
                @endphp
                @if(!empty($fonts))
                <div class="brand-fonts">
                    <span class="fonts-label">{{ app()->getLocale() === 'ar' ? 'الخطوط' : 'Typography' }}</span>
                    <div class="fonts-list">{{ implode(', ', $fonts) }}</div>
                </div>
                @endif

                @if($brandKit->hashtags)
                <div style="margin-bottom: 1rem;">
                    <span class="fonts-label">{{ app()->getLocale() === 'ar' ? 'الهاشتاجات' : 'Hashtags' }}</span>
                    <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: 0.5rem;">
                        @php
                            $hashtags = is_array($brandKit->hashtags) ? $brandKit->hashtags : json_decode($brandKit->hashtags ?? '[]', true);
                        @endphp
                        @foreach(array_slice($hashtags ?? [], 0, 3) as $hashtag)
                        <span style="background: rgba(99, 102, 241, 0.1); color: #818cf8; padding: 0.25rem 0.75rem; border-radius: 0.5rem; font-size: 0.75rem;">
                            #{{ $hashtag }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($brandKit->tone_of_voice)
                <div style="margin-bottom: 1rem;">
                    <span class="fonts-label">{{ app()->getLocale() === 'ar' ? 'نبرة الصوت' : 'Tone of Voice' }}</span>
                    @php
                        $toneData = is_array($brandKit->tone_of_voice) ? $brandKit->tone_of_voice : json_decode($brandKit->tone_of_voice ?? '[]', true);
                        $tone = is_array($toneData) && count($toneData) > 0 ? $toneData[0] : 'professional';
                        $toneTranslations = [
                            'professional' => app()->getLocale() === 'ar' ? 'احترافي' : 'Professional',
                            'casual' => app()->getLocale() === 'ar' ? 'غير رسمي' : 'Casual',
                            'friendly' => app()->getLocale() === 'ar' ? 'ودود' : 'Friendly',
                            'formal' => app()->getLocale() === 'ar' ? 'رسمي' : 'Formal',
                            'playful' => app()->getLocale() === 'ar' ? 'مرح' : 'Playful',
                            'inspirational' => app()->getLocale() === 'ar' ? 'ملهم' : 'Inspirational',
                        ];
                    @endphp
                    <div style="color: #e2e8f0; font-size: 0.875rem; margin-top: 0.25rem;">
                        {{ $toneTranslations[$tone] ?? ucfirst($tone) }}
                    </div>
                </div>
                @endif

                <div class="brand-kit-actions">
                    <a href="{{ route('admin.brand-kits.show', $brandKit->id) }}" class="action-btn action-view">
                        <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        {{ app()->getLocale() === 'ar' ? 'عرض التفاصيل' : 'View Details' }}
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    @if($brandKits->hasPages())
    <div style="margin-top: 2rem; display: flex; justify-content: center;">
        {{ $brandKits->links() }}
    </div>
    @endif

    @else
    <!-- If no brand kits exist -->
    <div class="empty-state">
        <svg class="empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
        </svg>
        <h2 class="empty-title">{{ app()->getLocale() === 'ar' ? 'لا توجد مجموعات علامة تجارية' : 'No Brand Kits Created' }}</h2>
        <p class="empty-subtitle">{{ app()->getLocale() === 'ar' ? 'لم يقم المستخدمون بإنشاء أي مجموعات علامة تجارية حتى الآن عبر تطبيق الموبايل' : 'Users haven\'t created any brand kits yet through the mobile app' }}</p>
    </div>
    @endif
</div>
@endsection
