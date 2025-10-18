@extends('layouts.admin')

@section('title', app()->getLocale() === 'ar' ? 'تفاصيل Brand Kit' : 'Brand Kit Details')

@section('content')
<style>
    body {
        font-family: {{ app()->getLocale() === 'ar' ? "'Cairo', sans-serif" : "'Inter', sans-serif" }};
        background: #0f172a;
        color: #e2e8f0;
    }

    .brand-kit-details {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem;
    }

    .back-button {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: #94a3b8;
        text-decoration: none;
        margin-bottom: 1.5rem;
        transition: color 0.3s ease;
    }

    .back-button:hover {
        color: #e2e8f0;
    }

    .details-header {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #ec4899 100%);
        border-radius: 24px;
        padding: 2.5rem;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }

    .details-header::before {
        content: '';
        position: absolute;
        top: -50%;
        {{ app()->getLocale() === 'ar' ? 'left: -20%;' : 'right: -20%;' }}
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
        border-radius: 50%;
    }

    .details-header-content {
        position: relative;
        z-index: 1;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .brand-name {
        font-size: 2rem;
        font-weight: 900;
        color: white;
        margin: 0;
    }

    .default-badge {
        background: rgba(16, 185, 129, 0.9);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.75rem;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .details-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .detail-card {
        background: #1e293b;
        border-radius: 16px;
        padding: 1.75rem;
        border: 1px solid rgba(148, 163, 184, 0.1);
    }

    .detail-title {
        color: #94a3b8;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .detail-content {
        color: #e2e8f0;
    }

    .color-palette {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
        gap: 1rem;
    }

    .color-item {
        text-align: center;
    }

    .color-circle {
        width: 80px;
        height: 80px;
        border-radius: 16px;
        margin: 0 auto 0.5rem;
        border: 2px solid rgba(148, 163, 184, 0.2);
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .color-circle:hover {
        transform: scale(1.05);
        border-color: rgba(99, 102, 241, 0.5);
    }

    .color-code {
        font-size: 0.75rem;
        color: #94a3b8;
        font-family: 'Courier New', monospace;
    }

    .font-list {
        list-style: none;
        padding: 0;
    }

    .font-item {
        padding: 1rem;
        background: rgba(99, 102, 241, 0.05);
        border-radius: 0.75rem;
        margin-bottom: 0.75rem;
        font-size: 1.25rem;
        border: 1px solid rgba(99, 102, 241, 0.1);
    }

    .hashtag-list {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .hashtag-item {
        background: rgba(99, 102, 241, 0.1);
        color: #818cf8;
        padding: 0.5rem 1rem;
        border-radius: 0.75rem;
        font-size: 0.875rem;
        font-weight: 500;
        border: 1px solid rgba(99, 102, 241, 0.2);
    }

    .logo-display {
        text-align: center;
        padding: 2rem;
        background: rgba(255, 255, 255, 0.03);
        border-radius: 0.75rem;
        border: 1px dashed rgba(148, 163, 184, 0.2);
    }

    .logo-display img {
        max-width: 200px;
        max-height: 100px;
        object-fit: contain;
    }

    .guidelines-box {
        background: rgba(99, 102, 241, 0.05);
        border: 1px solid rgba(99, 102, 241, 0.2);
        border-radius: 0.75rem;
        padding: 1.5rem;
        line-height: 1.7;
        white-space: pre-wrap;
    }

    .tone-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 0.75rem;
        font-size: 1rem;
        font-weight: 600;
    }

    @media (max-width: 768px) {
        .brand-kit-details {
            padding: 1rem;
        }

        .details-header {
            padding: 1.5rem;
        }

        .brand-name {
            font-size: 1.5rem;
        }

        .details-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="brand-kit-details">
    <a href="{{ route('admin.brand-kits.index') }}" class="back-button">
        <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ app()->getLocale() === 'ar' ? 'M9 5l7 7-7 7' : 'M15 19l-7-7 7-7' }}"></path>
        </svg>
        {{ app()->getLocale() === 'ar' ? 'العودة للقائمة' : 'Back to List' }}
    </a>

    <!-- Header -->
    <div class="details-header">
        <div class="details-header-content">
            <h1 class="brand-name">{{ $brandKit->name }}</h1>
            @if($brandKit->is_default)
            <span class="default-badge">
                <i class="fas fa-star" style="margin-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}: 0.5rem;"></i>
                {{ app()->getLocale() === 'ar' ? 'البراند كيت الافتراضي' : 'Default Brand Kit' }}
            </span>
            @endif
        </div>
    </div>

    <!-- Details Grid -->
    <div class="details-grid">
        <!-- Colors -->
        <div class="detail-card">
            <h3 class="detail-title">
                <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                </svg>
                {{ app()->getLocale() === 'ar' ? 'ألوان العلامة التجارية' : 'Brand Colors' }}
            </h3>
            <div class="detail-content">
                <div class="color-palette">
                    @php
                        $colors = is_array($brandKit->colors) ? $brandKit->colors : json_decode($brandKit->colors ?? '[]', true);
                    @endphp
                    @foreach($colors as $color)
                    <div class="color-item">
                        <div class="color-circle" style="background: {{ $color }};" onclick="navigator.clipboard.writeText('{{ $color }}')"></div>
                        <div class="color-code">{{ $color }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Fonts -->
        <div class="detail-card">
            <h3 class="detail-title">
                <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                </svg>
                {{ app()->getLocale() === 'ar' ? 'الخطوط' : 'Typography' }}
            </h3>
            <div class="detail-content">
                <ul class="font-list">
                    @php
                        $fonts = is_array($brandKit->fonts) ? $brandKit->fonts : json_decode($brandKit->fonts ?? '[]', true);
                    @endphp
                    @foreach($fonts as $font)
                    <li class="font-item" style="font-family: {{ $font }}, sans-serif;">{{ $font }}</li>
                    @endforeach
                </ul>
            </div>
        </div>

        <!-- Logo -->
        @if($brandKit->logo_url)
        <div class="detail-card">
            <h3 class="detail-title">
                <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                {{ app()->getLocale() === 'ar' ? 'الشعار' : 'Logo' }}
            </h3>
            <div class="detail-content">
                <div class="logo-display">
                    <img src="{{ $brandKit->logo_url }}" alt="{{ $brandKit->name }} Logo">
                </div>
            </div>
        </div>
        @endif

        <!-- Tone of Voice -->
        @if($brandKit->tone_of_voice)
        <div class="detail-card">
            <h3 class="detail-title">
                <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                </svg>
                {{ app()->getLocale() === 'ar' ? 'نبرة الصوت' : 'Tone of Voice' }}
            </h3>
            <div class="detail-content">
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
                <span class="tone-badge">
                    <i class="fas fa-comment-dots"></i>
                    {{ $toneTranslations[$tone] ?? ucfirst($tone) }}
                </span>
            </div>
        </div>
        @endif
    </div>

    <!-- Hashtags (Full Width) -->
    @if($brandKit->hashtags)
    <div class="detail-card" style="margin-bottom: 2rem;">
        <h3 class="detail-title">
            <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
            </svg>
            {{ app()->getLocale() === 'ar' ? 'الهاشتاجات الموصى بها' : 'Recommended Hashtags' }}
        </h3>
        <div class="detail-content">
            <div class="hashtag-list">
                @php
                    $hashtags = is_array($brandKit->hashtags) ? $brandKit->hashtags : json_decode($brandKit->hashtags ?? '[]', true);
                @endphp
                @foreach($hashtags ?? [] as $hashtag)
                <span class="hashtag-item">#{{ $hashtag }}</span>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Guidelines (Full Width) -->
    @if($brandKit->guidelines)
    <div class="detail-card">
        <h3 class="detail-title">
            <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            {{ app()->getLocale() === 'ar' ? 'إرشادات الاستخدام' : 'Usage Guidelines' }}
        </h3>
        <div class="detail-content">
            @if(is_array($brandKit->guidelines))
                <div class="guidelines-box">{{ $brandKit->guidelines['text'] ?? '' }}</div>
            @elseif(is_string($brandKit->guidelines))
                @php $guidelinesData = json_decode($brandKit->guidelines, true); @endphp
                <div class="guidelines-box">{{ $guidelinesData['text'] ?? $brandKit->guidelines }}</div>
            @endif
        </div>
    </div>
    @endif

    <!-- Created Info -->
    <div style="text-align: center; color: #64748b; font-size: 0.875rem; margin-top: 2rem; padding-top: 2rem; border-top: 1px solid rgba(148, 163, 184, 0.1);">
        {{ app()->getLocale() === 'ar' ? 'تم الإنشاء في' : 'Created on' }} {{ $brandKit->created_at->format('F j, Y') }}
    </div>
</div>
@endsection
