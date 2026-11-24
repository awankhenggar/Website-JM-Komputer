<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $__env->yieldContent('title', config('store.name')); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script>try{if(sessionStorage.getItem('jm-verify-dismissed'))document.documentElement.classList.add('verify-dismissed')}catch(e){}</script>
    
    <style>
        @view-transition { navigation: auto; }
        .site-header { view-transition-name: site-header; }
        .verify-toast { view-transition-name: verify-toast; }
        ::view-transition-old(root) { animation: vt-out .38s cubic-bezier(.4, 0, .2, 1) both; }
        ::view-transition-new(root) { animation: vt-in .55s cubic-bezier(.22, .8, .2, 1) both; }
        html[data-vt="backward"]::view-transition-old(root) { animation-name: vt-out-back; }
        html[data-vt="backward"]::view-transition-new(root) { animation-name: vt-in-back; }
        @keyframes vt-out { to { opacity: 0; transform: translateX(-9%); } }
        @keyframes vt-in { from { opacity: 0; transform: translateX(9%); } }
        @keyframes vt-out-back { to { opacity: 0; transform: translateX(9%); } }
        @keyframes vt-in-back { from { opacity: 0; transform: translateX(-9%); } }
        @keyframes vt-fade-out { to { opacity: 0; } }
        @keyframes vt-fade-in { from { opacity: 0; } }
        @keyframes vt-rise { from { opacity: 0; transform: translateY(8px); } }
        @media (prefers-reduced-motion: reduce) {
            ::view-transition-old(root), html[data-vt="backward"]::view-transition-old(root) { animation: vt-fade-out .25s ease both; }
            ::view-transition-new(root), html[data-vt="backward"]::view-transition-new(root) { animation: vt-fade-in .3s ease both; }
        }
        /* Browser tanpa dukungan transisi halaman: isi halaman muncul dengan fade halus. */
        @supports not selector(::view-transition) {
            body > main { animation: vt-rise .45s cubic-bezier(.22, .8, .2, 1) both; }
        }
    </style>
    <script>
        (function () {
            try {
                window.addEventListener('pageswap', function (e) {
                    if (!e.viewTransition || !e.activation || !e.activation.entry) return;
                    var paths = Array.prototype.map.call(document.querySelectorAll('.site-nav a'), function (a) { return new URL(a.href).pathname; });
                    var idx = function (p) {
                        for (var i = paths.length - 1; i >= 0; i--) {
                            if (p === paths[i] || (paths[i] !== '/' && p.indexOf(paths[i]) === 0)) return i;
                        }
                        return -1;
                    };
                    var from = idx(location.pathname);
                    var to = idx(new URL(e.activation.entry.url).pathname);
                    var dir = (from > -1 && to > -1 && to < from) ? 'backward' : 'forward';
                    document.documentElement.setAttribute('data-vt', dir);
                    sessionStorage.setItem('jm-vt', dir);
                });
                window.addEventListener('pagereveal', function (e) {
                    if (e.viewTransition) document.documentElement.setAttribute('data-vt', sessionStorage.getItem('jm-vt') || 'forward');
                });
            } catch (err) { /* abaikan */ }
        })();
    </script>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="font-sans antialiased <?php echo e(request()->routeIs('home') ? 'home-page-body' : ''); ?>">
<div class="cart-feedback" data-cart-feedback role="status" aria-live="polite"></div>
<?php
    $nav = [
        [route('home'), 'Beranda', request()->routeIs('home')],
        [route('products.index'), 'Produk', request()->routeIs('products.*')],
        [route('services'), 'Layanan Servis', request()->routeIs('services')],
        [route('reviews.index'), 'Ulasan', request()->routeIs('reviews.*')],
        [route('about'), 'Tentang', request()->routeIs('about')],
        [route('contact'), 'Kontak', request()->routeIs('contact')],
    ];
    $isHome = request()->routeIs('home');
    $wa = 'https://wa.me/'.config('store.whatsapp').'?text='.urlencode('Halo '.config('store.name').', saya ingin konsultasi.');
?>

<header class="site-header z-30 <?php echo e($isHome ? 'is-overlay is-transparent' : 'sticky top-0'); ?>" <?php if($isHome): ?> data-overlay-header <?php endif; ?>>
    <div class="site-nav-shell mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-2.5 lg:px-6">
        <a href="<?php echo e(route('home')); ?>" class="site-brand flex shrink-0 items-center gap-2.5" aria-label="<?php echo e(config('store.name')); ?> - Beranda">
            <?php if(config('store.logo')): ?>
                <img src="<?php echo e(asset('storage/'.config('store.logo'))); ?>" alt="<?php echo e(config('store.name')); ?>" class="h-12 w-12 object-contain">
            <?php else: ?>
                <img src="<?php echo e(asset('images/jm-logo.png')); ?>" alt="" class="h-12 w-12 object-contain">
            <?php endif; ?>
            <span class="hidden text-[1.02rem] font-extrabold leading-tight tracking-tight text-[#0B1F36] sm:block"><?php echo e(config('store.name')); ?></span>
        </a>

        <nav class="site-nav hidden items-center gap-1 md:flex" aria-label="Menu utama">
            <?php $__currentLoopData = $nav; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$url, $label, $active]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e($url); ?>" class="site-nav-link <?php echo e($active ? 'is-active' : ''); ?>"><?php echo e($label); ?></a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </nav>

        <div class="site-nav-actions flex shrink-0 items-center gap-2">
            <form action="<?php echo e(route('products.index')); ?>" method="GET" class="hidden xl:block">
                <input class="site-search" type="search" name="q" placeholder="Cari produk..." aria-label="Cari produk" value="<?php echo e(request('q')); ?>">
            </form>
            <?php ($cartCount = \App\Support\Cart::count()); ?>
            <a class="site-cart" data-cart-target href="<?php echo e(route('cart.index')); ?>" aria-label="Keranjang belanja<?php echo e($cartCount > 0 ? ', '.$cartCount.' produk' : ''); ?>" title="Keranjang">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 4h2l2.1 10.1a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 1.9-1.4L21 8H6M10 20h.01M18 20h.01"/></svg>
                <span class="site-cart-count<?php echo e($cartCount < 1 ? ' is-empty' : ''); ?>" data-cart-count aria-label="<?php echo e($cartCount); ?> produk di keranjang"><?php echo e($cartCount > 99 ? '99+' : $cartCount); ?></span>
            </a>
            <?php if(auth()->guard()->check()): ?>
                <a class="site-account site-account-icon <?php echo e(auth()->user()->isStaff() ? '' : 'site-account-icon-only'); ?> hidden sm:inline-flex"
                   href="<?php echo e(auth()->user()->isStaff() ? (auth()->user()->isAdmin() ? route('admin.dashboard') : route('admin.servis.index')) : route('account.index')); ?>"
                   aria-label="<?php echo e(auth()->user()->isStaff() ? 'Panel Admin' : 'Akun Saya'); ?>"
                   title="<?php echo e(auth()->user()->isStaff() ? 'Panel Admin' : 'Akun Saya'); ?>">
                    <?php if(auth()->user()->isStaff()): ?>
                        <span>Panel Admin</span>
                    <?php else: ?>
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 21a8 8 0 0 0-16 0M12 13a5 5 0 1 0 0-10 5 5 0 0 0 0 10Z"/></svg>
                    <?php endif; ?>
                </a>
                <form method="POST" action="<?php echo e(route('logout')); ?>" class="hidden lg:block"><?php echo csrf_field(); ?><button class="site-logout" type="submit">Keluar</button></form>
            <?php else: ?>
                <a class="site-login hidden sm:inline-flex <?php echo e(request()->routeIs('login') ? 'is-active' : ''); ?>" href="<?php echo e(route('login')); ?>" <?php echo e(request()->routeIs('login') ? 'aria-current=page' : ''); ?>>Masuk</a>
                <a class="site-register hidden sm:inline-flex <?php echo e(request()->routeIs('register') ? 'is-active' : ''); ?>" href="<?php echo e(route('register')); ?>" <?php echo e(request()->routeIs('register') ? 'aria-current=page' : ''); ?>>Daftar</a>
            <?php endif; ?>
        </div>
    </div>

    <details class="site-mobile-menu border-t border-slate-200 md:hidden">
        <summary class="cursor-pointer list-none px-4 py-3 text-sm font-semibold text-[#102D4C]">Menu <span class="float-right">&#9662;</span></summary>
        <div class="grid gap-1 bg-white px-4 pb-4 text-sm">
            <form action="<?php echo e(route('products.index')); ?>" method="GET" class="mb-2"><input class="input" type="search" name="q" placeholder="Cari produk" aria-label="Cari produk"></form>
            <?php $__currentLoopData = $nav; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$url, $label, $active]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e($url); ?>" class="site-mobile-link <?php echo e($active ? 'is-active' : ''); ?>" <?php echo e($active ? 'aria-current=page' : ''); ?>><?php echo e($label); ?></a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php if(auth()->guard()->check()): ?>
                <a href="<?php echo e(auth()->user()->isStaff() ? (auth()->user()->isAdmin() ? route('admin.dashboard') : route('admin.servis.index')) : route('account.index')); ?>" class="flex items-center gap-2 rounded-xl px-3 py-2.5 text-[#29445F] hover:bg-blue-50">
                    <?php if(auth()->user()->isStaff()): ?>
                        Panel Admin
                    <?php else: ?>
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 21a8 8 0 0 0-16 0M12 13a5 5 0 1 0 0-10 5 5 0 0 0 0 10Z"/></svg>
                        Akun Saya
                    <?php endif; ?>
                </a>
                <form method="POST" action="<?php echo e(route('logout')); ?>"><?php echo csrf_field(); ?><button class="site-mobile-logout w-full rounded-xl px-3 py-2.5 text-left font-semibold" type="submit">Keluar</button></form>
            <?php else: ?>
                <a href="<?php echo e(route('login')); ?>" class="site-mobile-auth <?php echo e(request()->routeIs('login') ? 'is-active' : ''); ?>" <?php echo e(request()->routeIs('login') ? 'aria-current=page' : ''); ?>>Masuk</a>
                <a href="<?php echo e(route('register')); ?>" class="site-mobile-auth <?php echo e(request()->routeIs('register') ? 'is-active' : ''); ?>" <?php echo e(request()->routeIs('register') ? 'aria-current=page' : ''); ?>>Daftar</a>
            <?php endif; ?>
        </div>
    </details>
</header>


<main class="<?php echo e(request()->routeIs('home') ? 'home-main' : 'mx-auto max-w-6xl px-4 py-8'); ?>">
    <?php echo $__env->make('partials.flash', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->yieldContent('content'); ?>
</main>

<footer class="mt-12 bg-[#0B1F36] text-blue-100/70">
    <div class="mx-auto grid max-w-6xl gap-8 px-4 py-10 md:grid-cols-4">
        <div class="md:col-span-2">
            <b class="text-lg text-white"><?php echo e(config('store.name')); ?></b>
            <p class="mt-2 max-w-md text-sm"><?php echo e(\Illuminate\Support\Str::limit(config('store.about'), 160)); ?></p>
            <p class="mt-3 text-sm"><?php echo e(config('store.address')); ?><br><?php echo e(config('store.hours')); ?><br>Telepon: <?php echo e(config('store.phone')); ?></p>
        </div>
        <div class="text-sm">
            <b class="text-white">Menu</b>
            <ul class="mt-2 grid gap-1">
                <li><a class="hover:text-white" href="<?php echo e(route('products.index')); ?>">Produk</a></li>
                <li><a class="hover:text-white" href="<?php echo e(route('services')); ?>">Layanan Servis</a></li>
                <li><a class="hover:text-white" href="<?php echo e(route('booking.create')); ?>">Booking Servis</a></li>
                <li><a class="hover:text-white" href="<?php echo e(route('reviews.index')); ?>">Ulasan</a></li>
                <li><a class="hover:text-white" href="<?php echo e(route('contact')); ?>">Kontak &amp; Lokasi</a></li>
            </ul>
        </div>
        <div class="text-sm">
            <b class="text-white">Butuh bantuan?</b>
            <p class="mt-2">Tanyakan produk atau servis langsung ke kami.</p>
            <a class="btn btn-accent mt-3" href="<?php echo e($wa); ?>" target="_blank" rel="noopener">Konsultasi via WhatsApp</a>
        </div>
    </div>
    <div class="border-t border-white/10 py-4 text-center text-xs text-blue-100/45">&copy; <?php echo e(date('Y')); ?> <?php echo e(config('store.name')); ?>. Semua hak dilindungi.</div>
</footer>
<?php if(auth()->guard()->check()): ?>
    <?php if(auth()->user()->role === 'pelanggan' && ! auth()->user()->hasVerifiedEmail()): ?>
        <div class="verify-toast" data-verify-toast role="status">
            <p class="verify-toast-text">Email Anda belum diverifikasi. Pesanan diproses setelah email diverifikasi.</p>
            <form method="POST" action="<?php echo e(route('verification.send')); ?>"><?php echo csrf_field(); ?><button type="submit">Kirim ulang email</button></form>
            <button type="button" class="verify-toast-close" data-verify-close aria-label="Tutup pemberitahuan">&times;</button>
        </div>
    <?php endif; ?>
<?php endif; ?>
</body>
</html>
<?php /**PATH C:\laragon\www\jm-komputer\resources\views/layouts/app.blade.php ENDPATH**/ ?>