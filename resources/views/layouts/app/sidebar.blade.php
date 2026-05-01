<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('partials.head')
    <style>
        /* ─── ERP Design System ─── */
        :root {
            --erp-sidebar-w: 15rem;
            --erp-accent:      #3d7a69;
            --erp-accent-dim:  #2d5c4d;
            --erp-accent-glow: rgba(61, 122, 105, 0.12);
            --erp-border:      rgba(113, 113, 122, 0.2);
            --erp-radius:      0.375rem;
            --erp-transition:  150ms cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Light mode */
        html:not(.dark) {
            --erp-bg-canvas:    #edf2f0;
            --erp-bg-sidebar:   #a8c2b8;
            --erp-bg-surface:   #ffffff;
            --erp-bg-hover:     rgba(0, 0, 0, 0.07);
            --erp-text-primary: #18181b;
            --erp-text-muted:   #71717a;
            --erp-border:       rgba(26, 48, 40, 0.15);
            --erp-shadow:       0 1px 3px rgba(0,0,0,0.07), 0 1px 2px rgba(0,0,0,0.05);
        }

        /* Dark mode */
        html.dark {
            --erp-bg-canvas:    #0f1a17;
            --erp-bg-sidebar:   #1a2e28;
            --erp-bg-surface:   #1c2320;
            --erp-bg-hover:     rgba(168, 194, 184, 0.08);
            --erp-text-primary: #e8f0ed;
            --erp-text-muted:   #7fa99b;
            --erp-border:       rgba(168, 194, 184, 0.12);
            --erp-shadow:       0 1px 3px rgba(0,0,0,0.4), 0 1px 2px rgba(0,0,0,0.3);
        }

        /* ─── Light sidebar contrast (#a8c2b8 bg) ─── */
        html:not(.dark) .erp-sidebar .erp-nav-label         { color: #4a7a6a; }
        html:not(.dark) .erp-sidebar .erp-nav-item          { color: #2d5c4d; }
        html:not(.dark) .erp-sidebar .erp-nav-item:hover    { background: rgba(0,0,0,0.07); color: #1a3028; }
        html:not(.dark) .erp-sidebar .erp-nav-item.active   { color: #1a3028; background: rgba(0,0,0,0.1); }
        html:not(.dark) .erp-sidebar .erp-nav-item.active::before { background: #1a3028; }
        html:not(.dark) .erp-sidebar .erp-brand-name        { color: #1a3028; }
        html:not(.dark) .erp-sidebar .erp-brand-sub         { color: #3d7a69; }
        html:not(.dark) .erp-sidebar .erp-nav-badge         { background: #1a3028; color: #fff; }
        html:not(.dark) .erp-sidebar .erp-avatar            { background: #1a3028; color: #fff; }
        html:not(.dark) .erp-sidebar .erp-user-name         { color: #1a3028; }
        html:not(.dark) .erp-sidebar .erp-user-role         { color: #2d5c4d; }
        html:not(.dark) .erp-sidebar .erp-icon-btn          { color: #2d5c4d; }
        html:not(.dark) .erp-sidebar .erp-icon-btn:hover    { background: rgba(0,0,0,0.07); color: #1a3028; }
        html:not(.dark) .erp-sidebar .erp-user-card:hover   { background: rgba(0,0,0,0.07); }
        html:not(.dark) .erp-sidebar                        { border-right-color: rgba(26,48,40,0.15); }
        html:not(.dark) .erp-status-bar   { background: rgba(61,122,105,0.07); border-bottom-color: rgba(26,48,40,0.1); }
        html:not(.dark) .erp-status-text  { color: #4a7a6a; }
        html:not(.dark) .erp-topbar       { border-bottom-color: rgba(26,48,40,0.15); }
        html:not(.dark) .erp-mobile-topbar { border-bottom-color: rgba(26,48,40,0.15); }
        html:not(.dark) .erp-breadcrumb         { color: #4a7a6a; }
        html:not(.dark) .erp-breadcrumb-active  { color: #1a3028; font-weight: 600; }
        html:not(.dark) .erp-notif-btn          { color: #2d5c4d; }
        html:not(.dark) .erp-notif-btn:hover    { background: rgba(0,0,0,0.07); border-color: rgba(26,48,40,0.15); color: #1a3028; }
        html:not(.dark) .erp-theme-toggle       { border-color: rgba(26,48,40,0.18); background: rgba(0,0,0,0.04); color: #2d5c4d; }
        html:not(.dark) .erp-notif-count        { border-color: #a8c2b8; }
        html:not(.dark) .erp-mobile-menu-btn    { color: #2d5c4d; }

        /* ─── Dark sidebar contrast (#1a2e28 bg) ─── */
        html.dark .erp-sidebar .erp-nav-item.active         { color: #a8c2b8; background: rgba(168,194,184,0.1); }
        html.dark .erp-sidebar .erp-nav-item.active::before { background: #a8c2b8; }
        html.dark .erp-sidebar .erp-nav-badge               { background: rgba(168,194,184,0.15); color: #a8c2b8; }
        html.dark .erp-sidebar .erp-avatar                  { background: #2d5c4d; color: #fff; }
        html.dark .erp-sidebar .erp-brand-sub               { color: #7fa99b; }

        /* ─── Base ─── */
        body {
            font-family: 'Instrument Sans', sans-serif;
            background: var(--erp-bg-canvas);
            color: var(--erp-text-primary);
        }

        /* ─── Sidebar ─── */
        .erp-sidebar {
            position: fixed;
            top: 0; left: 0; bottom: 0;
            width: var(--erp-sidebar-w);
            background: var(--erp-bg-sidebar);
            border-right: 1px solid var(--erp-border);
            display: flex;
            flex-direction: column;
            z-index: 50;
            transition: transform var(--erp-transition);
            box-shadow: var(--erp-shadow);
        }

        /* ─── Brand Header ─── */
        .erp-brand {
            padding: 1.125rem 1.25rem;
            border-bottom: 1px solid var(--erp-border);
            display: flex;
            align-items: center;
            gap: 0.625rem;
        }
        .erp-brand-icon {
            width: 2rem; height: 2rem;
            background: #1a3028;
            border-radius: 0.375rem;
            display: grid; place-items: center;
            flex-shrink: 0;
        }
        .erp-brand-icon svg { width: 1.125rem; height: 1.125rem; color: #a8c2b8; }
        .erp-brand-name {
            font-size: 0.875rem;
            font-weight: 600;
            letter-spacing: -0.01em;
            color: var(--erp-text-primary);
            line-height: 1;
        }
        .erp-brand-sub {
            font-size: 0.625rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #7fa99b;
            font-weight: 500;
            line-height: 1;
            margin-top: 0.125rem;
        }

        /* ─── Status Bar ─── */
        .erp-status-bar {
            padding: 0.5rem 1.25rem;
            background: var(--erp-accent-glow);
            border-bottom: 1px solid var(--erp-border);
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }
        .erp-status-dot {
            width: 0.375rem; height: 0.375rem;
            background: #22c55e;
            border-radius: 50%;
            animation: pulse-dot 2s ease-in-out infinite;
        }
        @keyframes pulse-dot {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }
        .erp-status-text {
            font-size: 0.625rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--erp-text-muted);
            font-weight: 500;
        }

        /* ─── Nav ─── */
        .erp-nav { flex: 1; overflow-y: auto; padding: 0.75rem 0; }
        .erp-nav::-webkit-scrollbar { width: 0.25rem; }
        .erp-nav::-webkit-scrollbar-track { background: transparent; }
        .erp-nav::-webkit-scrollbar-thumb { background: var(--erp-border); border-radius: 9999px; }

        .erp-nav-section { margin-bottom: 0.25rem; }
        .erp-nav-label {
            padding: 0.375rem 1.25rem 0.25rem;
            font-size: 0.6rem;
            font-weight: 600;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--erp-text-muted);
        }

        .erp-nav-item {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            padding: 0.5rem 1.25rem;
            font-size: 0.8125rem;
            font-weight: 500;
            color: var(--erp-text-muted);
            text-decoration: none;
            border-radius: 0;
            transition: all var(--erp-transition);
            position: relative;
            cursor: pointer;
            border: none;
            background: transparent;
            width: 100%;
            text-align: left;
        }
        .erp-nav-item:hover {
            background: var(--erp-bg-hover);
            color: var(--erp-text-primary);
        }
        .erp-nav-item.active {
            color: var(--erp-accent);
            background: var(--erp-accent-glow);
        }
        .erp-nav-item.active::before {
            content: '';
            position: absolute;
            left: 0; top: 0.25rem; bottom: 0.25rem;
            width: 0.1875rem;
            background: var(--erp-accent);
            border-radius: 0 9999px 9999px 0;
        }
        .erp-nav-item svg {
            width: 1rem; height: 1rem;
            flex-shrink: 0;
            opacity: 0.7;
        }
        .erp-nav-item.active svg { opacity: 1; }
        .erp-nav-badge {
            margin-left: auto;
            font-size: 0.625rem;
            font-weight: 700;
            padding: 0.125rem 0.375rem;
            background: var(--erp-accent);
            color: #fff;
            border-radius: 9999px;
            line-height: 1.4;
        }

        /* ─── Sidebar Footer ─── */
        .erp-sidebar-footer {
            border-top: 1px solid var(--erp-border);
            padding: 0.875rem 1.25rem;
        }
        .erp-user-card {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            padding: 0.5rem;
            border-radius: var(--erp-radius);
            cursor: pointer;
            transition: background var(--erp-transition);
        }
        .erp-user-card:hover { background: var(--erp-bg-hover); }
        .erp-avatar {
            width: 1.875rem; height: 1.875rem;
            border-radius: var(--erp-radius);
            background: var(--erp-accent);
            color: #fff;
            font-size: 0.6875rem;
            font-weight: 700;
            display: grid; place-items: center;
            flex-shrink: 0;
            letter-spacing: 0.02em;
        }
        .erp-user-name {
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--erp-text-primary);
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .erp-user-role {
            font-size: 0.625rem;
            color: var(--erp-text-muted);
            letter-spacing: 0.04em;
        }
        .erp-user-actions {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }
        .erp-icon-btn {
            width: 1.625rem; height: 1.625rem;
            display: grid; place-items: center;
            border-radius: var(--erp-radius);
            color: var(--erp-text-muted);
            transition: all var(--erp-transition);
            cursor: pointer;
            background: transparent;
            border: none;
        }
        .erp-icon-btn:hover {
            background: var(--erp-bg-hover);
            color: var(--erp-text-primary);
        }
        .erp-icon-btn svg { width: 0.875rem; height: 0.875rem; }

        /* ─── Main Layout ─── */
        .erp-main {
            margin-left: var(--erp-sidebar-w);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* ─── Topbar ─── */
        .erp-topbar {
            position: sticky;
            top: 0;
            z-index: 40;
            height: 3.25rem;
            background: var(--erp-bg-sidebar);
            border-bottom: 1px solid var(--erp-border);
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            gap: 1rem;
            box-shadow: var(--erp-shadow);
        }
        .erp-topbar-title {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--erp-text-primary);
        }
        .erp-breadcrumb {
            display: flex;
            align-items: center;
            gap: 0.375rem;
            font-size: 0.75rem;
            color: var(--erp-text-muted);
        }
        .erp-breadcrumb-sep { opacity: 0.4; }
        .erp-breadcrumb-active { color: var(--erp-text-primary); font-weight: 500; }

        .erp-topbar-actions {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 0.375rem;
        }

        .erp-notif-btn {
            position: relative;
            width: 2rem; height: 2rem;
            display: grid; place-items: center;
            border-radius: var(--erp-radius);
            color: var(--erp-text-muted);
            transition: all var(--erp-transition);
            cursor: pointer;
            background: transparent;
            border: 1px solid transparent;
        }
        .erp-notif-btn:hover {
            background: var(--erp-bg-hover);
            border-color: var(--erp-border);
            color: var(--erp-text-primary);
        }
        .erp-notif-btn svg { width: 1rem; height: 1rem; }
        .erp-notif-count {
            position: absolute;
            top: 0.1875rem; right: 0.1875rem;
            width: 0.5rem; height: 0.5rem;
            background: var(--erp-accent);
            border-radius: 50%;
            border: 1.5px solid var(--erp-bg-sidebar);
        }

        .erp-theme-toggle {
            width: 2rem; height: 2rem;
            display: grid; place-items: center;
            border-radius: var(--erp-radius);
            color: var(--erp-text-muted);
            cursor: pointer;
            border: 1px solid var(--erp-border);
            background: var(--erp-bg-canvas);
            transition: all var(--erp-transition);
        }
        .erp-theme-toggle:hover {
            background: var(--erp-bg-hover);
            color: var(--erp-text-primary);
        }
        .erp-theme-toggle svg { width: 0.875rem; height: 0.875rem; }

        /* ─── Content Area ─── */
        .erp-content {
            flex: 1;
            padding: 1.5rem;
        }

        /* ─── Mobile Topbar ─── */
        .erp-mobile-topbar {
            display: none;
            position: sticky;
            top: 0; z-index: 50;
            height: 3rem;
            background: var(--erp-bg-sidebar);
            border-bottom: 1px solid var(--erp-border);
            padding: 0 1rem;
            align-items: center;
            gap: 0.75rem;
        }
        .erp-mobile-menu-btn {
            width: 2rem; height: 2rem;
            display: grid; place-items: center;
            cursor: pointer;
            color: var(--erp-text-muted);
            background: transparent;
            border: none;
        }
        .erp-mobile-menu-btn svg { width: 1.125rem; height: 1.125rem; }

        /* ─── Sidebar Overlay ─── */
        .erp-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 45;
            backdrop-filter: blur(2px);
        }

        /* ─── Responsive ─── */
        @media (max-width: 1024px) {
            .erp-sidebar {
                transform: translateX(-100%);
            }
            .erp-sidebar.open {
                transform: translateX(0);
                box-shadow: 0 0 0 9999px rgba(0,0,0,0.4);
            }
            .erp-main { margin-left: 0; }
            .erp-topbar { display: none; }
            .erp-mobile-topbar { display: flex; }
            .erp-overlay.open { display: block; }
        }
    </style>
</head>
<body class="min-h-screen antialiased">

    {{-- ─── Sidebar ─── --}}
    <aside class="erp-sidebar" id="erp-sidebar">

        {{-- Brand --}}
        <div class="erp-brand">
            <div class="erp-brand-icon">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <div>
                <div class="erp-brand-name">{{ config('app.name', 'ShopERP') }}</div>
                <div class="erp-brand-sub">Operations Suite</div>
            </div>

            {{-- Collapse button (desktop) --}}
            <button class="erp-icon-btn" style="margin-left:auto" title="Collapse">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                </svg>
            </button>
        </div>

        {{-- Status Bar --}}
        <div class="erp-status-bar">
            <div class="erp-status-dot"></div>
            <span class="erp-status-text">System Online</span>
        </div>

        {{-- Navigation --}}
        <nav class="erp-nav" id="erp-nav">

            <div class="erp-nav-section">
                <div class="erp-nav-label">Overview</div>
                <a href="{{ route('dashboard') }}" wire:navigate
                   class="erp-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard
                </a>
            </div>

            <div class="erp-nav-section">
                <div class="erp-nav-label">Rental Operations</div>
                <a href="{{ route('bookings.index') }}" wire:navigate class="erp-nav-item {{ request()->routeIs('bookings.*') ? 'active' : '' }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Bookings
                </a>
                <a href="{{ route('customers.index') }}" wire:navigate class="erp-nav-item {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Customers
                </a>
            </div>

            {{-- <div class="erp-nav-section">
                <div class="erp-nav-label">Shop Floor</div>
                <a href="#" wire:navigate class="erp-nav-item {{ request()->routeIs('orders.*') ? 'active' : '' }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Work Orders
                    <span class="erp-nav-badge">12</span>
                </a>
                <a href="#" wire:navigate class="erp-nav-item {{ request()->routeIs('jobs.*') ? 'active' : '' }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Production Jobs
                </a>
                <a href="#" wire:navigate class="erp-nav-item {{ request()->routeIs('machines.*') ? 'active' : '' }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/>
                    </svg>
                    Machines &amp; Assets
                </a>
                <a href="#" wire:navigate class="erp-nav-item {{ request()->routeIs('quality.*') ? 'active' : '' }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                    Quality Control
                </a>
            </div> --}}

            <div class="erp-nav-section">
                <div class="erp-nav-label">Inventory</div>
                <a href="{{  route('inventory.index') }}" wire:navigate class="erp-nav-item {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    Stock &amp; Materials
                    <span class="erp-nav-badge">3</span>
                </a>
                <a href="{{ route('suppliers.index') }}" wire:navigate class="erp-nav-item {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Suppliers
                </a>
                <a href="{{ route('purchase-orders.index') }}" wire:navigate class="erp-nav-item {{ request()->routeIs('purchase-orders.*') ? 'active' : '' }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Purchase Orders
                </a>
            </div>

            <div class="erp-nav-section">
                <div class="erp-nav-label">Finance</div>
                @can('expenses.view')
                    <a href="{{ route('expenses.index') }}" wire:navigate class="erp-nav-item {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Expenses
                    </a>
                @endcan
                @can('payments.view')
                    <a href="{{ route('invoices.index') }}" wire:navigate class="erp-nav-item {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z"/>
                        </svg>
                        Invoices
                    </a>
                @endcan
                @can('reports.view')
                    <a href="{{ route('reports.index') }}" wire:navigate class="erp-nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Reports
                    </a>
                    <a href="{{ route('analytics.dashboard') }}" wire:navigate class="erp-nav-item {{ request()->routeIs('analytics.*') ? 'active' : '' }}">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                        </svg>
                        Analytics
                    </a>
                @endcan
            </div>

            <div class="erp-nav-section">
                <div class="erp-nav-label">People</div>
                @can('employees.view')
                <a href="{{ route('employees.index') }}" wire:navigate class="erp-nav-item {{ request()->routeIs('employees.index') ? 'active' : '' }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Employees
                </a>
                @endcan
                @can('employees.view')
                <a href="{{ route('roles.index') }}" wire:navigate class="erp-nav-item {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    Roles &amp; Permissions
                </a>
                @endcan
                <a href="{{ route('attendance.index') }}" wire:navigate class="erp-nav-item {{ request()->routeIs('attendance.index') ? 'active' : '' }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Attendance
                    <span class="erp-nav-badge">SOON</span>
                </a>
            </div>

            <div class="erp-nav-section">
                <div class="erp-nav-label">System</div>
                <a href="{{ route('notifications.index') }}" wire:navigate
                   class="erp-nav-item {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
                    </svg>
                    Notifications
                </a>
                <a href="{{ route('profile.edit') }}" wire:navigate
                   class="erp-nav-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Settings
                </a>
            </div>

        </nav>

        {{-- User Footer --}}
        <div class="erp-sidebar-footer">
            <flux:dropdown position="top" align="start">
                <div class="erp-user-card">
                    <div class="erp-avatar">{{ auth()->user()->initials() }}</div>
                    <div style="min-width:0; flex:1">
                        <div class="erp-user-name">{{ auth()->user()->name }}</div>
                        <div class="erp-user-role">{{ auth()->user()->email }}</div>
                    </div>
                    <div class="erp-user-actions">
                        <div class="erp-icon-btn">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <flux:menu>
                    <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                        {{ __('Settings') }}
                    </flux:menu.item>
                    <flux:menu.separator />
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer" data-test="logout-button">
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </div>
    </aside>

    {{-- ─── Mobile Overlay ─── --}}
    <div class="erp-overlay" id="erp-overlay"></div>

    {{-- ─── Main Content ─── --}}
    <div class="erp-main">

        {{-- Mobile Topbar --}}
        <header class="erp-mobile-topbar">
            <button class="erp-mobile-menu-btn" id="erp-mobile-toggle">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <div style="display:flex;align-items:center;gap:0.5rem">
                <div class="erp-brand-icon" style="width:1.5rem;height:1.5rem">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" style="width:0.875rem;height:0.875rem;color:#a8c2b8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <span class="erp-brand-name">{{ config('app.name', 'ShopERP') }}</span>
            </div>
            <div style="margin-left:auto;display:flex;gap:0.25rem">
                @livewire('notifications.bell', key('mobile-bell'))
            </div>
        </header>

        {{-- Desktop Topbar --}}
        <header class="erp-topbar">
            {{-- Breadcrumb --}}
            <div class="erp-breadcrumb">
                <span>{{ config('app.name') }}</span>
                <span class="erp-breadcrumb-sep">/</span>
                @if(isset($title))
                    <span class="erp-breadcrumb-active">{{ $title }}</span>
                @else
                    <span class="erp-breadcrumb-active">Dashboard</span>
                @endif
            </div>

            {{-- Actions --}}
            <div class="erp-topbar-actions">
                {{-- Notifications bell --}}
                @livewire('notifications.bell')

                {{-- Quick Add --}}
                <flux:dropdown position="bottom" align="end">
                    <button style="
                        display:flex;align-items:center;gap:0.375rem;
                        padding:0 0.625rem;height:1.875rem;
                        background:var(--erp-accent);color:#fff;
                        border:none;border-radius:var(--erp-radius);
                        font-size:0.75rem;font-weight:600;cursor:pointer;
                        transition:background var(--erp-transition);
                    " onmouseover="this.style.background='var(--erp-accent-dim)'"
                       onmouseout="this.style.background='var(--erp-accent)'">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="width:0.75rem;height:0.75rem">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        New
                    </button>
                    <flux:menu>
                        <flux:menu.item icon="clipboard-document-list" href="{{ route('bookings.create') }}" wire:navigate>Booking</flux:menu.item>
                        <flux:menu.item icon="cube" href="{{ route('purchase-orders.create') }}" wire:navigate>Purchase Order</flux:menu.item>
                        <flux:menu.item icon="document-text" href="{{ route('expenses.index') }} wire:navigate">Bill</flux:menu.item>
                        <flux:menu.separator />
                        <flux:menu.item icon="user-plus" href="{{ route('employees.index') }}" wire:navigate>Employee</flux:menu.item>
                    </flux:menu>
                </flux:dropdown>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="erp-content">
            {{ $slot }}
        </main>

    </div>

    @persist('toast')
        <flux:toast.group>
            <flux:toast />
        </flux:toast.group>
    @endpersist

    @fluxScripts

    <script>
        // Mobile sidebar toggle
        const sidebar  = document.getElementById('erp-sidebar');
        const overlay  = document.getElementById('erp-overlay');
        const mobileToggle = document.getElementById('erp-mobile-toggle');

        function openSidebar() {
            sidebar.classList.add('open');
            overlay.classList.add('open');
        }
        function closeSidebar() {
            sidebar.classList.remove('open');
            overlay.classList.remove('open');
        }

        mobileToggle?.addEventListener('click', openSidebar);
        overlay?.addEventListener('click', closeSidebar);

        // Close sidebar on Livewire navigate
        document.addEventListener('livewire:navigate', closeSidebar);
    </script>
</body>
</html>
