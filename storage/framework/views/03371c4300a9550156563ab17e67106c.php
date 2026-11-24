<?php $__env->startSection('title', config('store.name').' - Laptop, Komputer & Servis'); ?>
<?php $__env->startSection('content'); ?>
<?php $wa = 'https://wa.me/'.config('store.whatsapp').'?text='.urlencode('Halo '.config('store.name').', saya ingin konsultasi.'); ?>


<section class="home-banner" data-home-banner aria-roledescription="carousel" aria-label="Kategori produk JM Komputer">
    <div class="home-banner-viewport" data-banner-viewport>
        <div class="home-banner-track" data-banner-track>
            <?php $__currentLoopData = $heroSlides; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slide): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e($slide['url']); ?>" class="home-banner-slide" data-banner-slide draggable="false" aria-roledescription="slide" aria-label="<?php echo e($loop->iteration); ?> dari <?php echo e(count($heroSlides)); ?>: <?php echo e($slide['title']); ?>">
                    <img src="<?php echo e($slide['image']); ?>" alt="" class="home-banner-image" draggable="false" style="object-position: <?php echo e($slide['position']); ?>" <?php if($loop->first): ?> fetchpriority="high" <?php else: ?> loading="lazy" <?php endif; ?>>
                    <div class="home-banner-copy">
                        <p class="home-banner-eyebrow"><?php echo e($slide['eyebrow']); ?></p>
                        <h2><?php echo e($slide['heading']); ?></h2>
                        <p class="home-banner-desc"><?php echo e($slide['text']); ?></p>
                        <p class="home-banner-trust"><?php $__currentLoopData = $slide['points']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $point): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><span><i aria-hidden="true">&#10003;</i> <?php echo e($point); ?></span><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></p>
                        <span class="home-banner-cta">Lihat <?php echo e($slide['title']); ?> <span aria-hidden="true">&#8594;</span></span>
                    </div>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <button type="button" class="home-banner-arrow is-prev" data-banner-prev aria-label="Gambar sebelumnya">&#8592;</button>
    <button type="button" class="home-banner-arrow is-next" data-banner-next aria-label="Gambar berikutnya">&#8594;</button>
    <div class="home-banner-dots" role="group" aria-label="Pilih gambar">
        <?php $__currentLoopData = $heroSlides; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slide): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <button type="button" class="home-banner-dot <?php echo e($loop->first ? 'is-active' : ''); ?>" data-banner-dot="<?php echo e($loop->index); ?>" aria-label="Tampilkan <?php echo e($slide['title']); ?>" aria-current="<?php echo e($loop->first ? 'true' : 'false'); ?>"></button>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</section>

<div class="home-content-shell">


<section class="home-category-panel mb-14">
    <div class="mb-5 flex items-end justify-between gap-3"><div><p class="mb-1 text-xs font-bold uppercase tracking-[0.18em] text-accent">Temukan kebutuhanmu</p><h2 class="section-title">Cari Berdasarkan Kategori</h2></div></div>
    <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
        <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <a class="card card-hover flex items-center gap-3" href="<?php echo e(route('products.index', ['kategori' => $c->slug])); ?>">
                <span class="icon-circle"><?php echo $__env->make('partials.category-icon', ['name' => $c->name], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></span>
                <span><b class="block text-sm leading-tight"><?php echo e($c->name); ?></b><span class="text-xs text-slate-500"><?php echo e($c->products_count); ?> produk</span></span>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-sm text-slate-500">Belum ada kategori.</p>
        <?php endif; ?>
    </div>
</section>


<section class="home-featured-panel mb-14">
    <div class="mb-5 flex items-end justify-between gap-3"><div><p class="mb-1 text-xs font-bold uppercase tracking-[0.18em] text-accent">Pilihan untuk Anda</p><h2 class="section-title">Produk Pilihan</h2></div><a class="btn" href="<?php echo e(route('products.index')); ?>">Lihat Semua <span aria-hidden="true">&#8594;</span></a></div>
    <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
        <?php $__empty_1 = true; $__currentLoopData = $featured; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php echo $__env->make('partials.product-card', ['product' => $product], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-sm text-slate-500">Belum ada produk pilihan.</p>
        <?php endif; ?>
    </div>
</section>


<section class="hero-bg mb-14 rounded-3xl p-7 text-white md:p-10">
    <p class="mb-2 text-xs font-bold uppercase tracking-[0.18em] text-blue-200">Dukungan teknis</p>
    <h2 class="text-2xl font-bold md:text-3xl">Servis Laptop &amp; Komputer</h2>
    <p class="mt-2 max-w-xl text-sm leading-6 text-blue-100/75">Proses servis yang jelas, mulai dari booking hingga perangkat selesai ditangani.</p>
    <ol class="mt-6 grid gap-3 md:grid-cols-4">
        <?php $__currentLoopData = ['Booking' => 'Isi form keluhan perangkat.', 'Diagnosa' => 'Teknisi memeriksa dan memberi estimasi.', 'Konfirmasi biaya' => 'Anda setuju atau menolak.', 'Selesai & bayar' => 'Bayar setelah servis selesai, lalu ambil.']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $title => $text): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li class="rounded-2xl border border-white/10 bg-white/[0.07] p-4">
                <span class="mb-3 flex h-8 w-8 items-center justify-center rounded-full bg-blue-200 text-sm font-bold text-brand-dark"><?php echo e($loop->iteration); ?></span>
                <b class="block"><?php echo e($title); ?></b><span class="mt-1 block text-sm text-blue-100/70"><?php echo e($text); ?></span>
            </li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ol>
    <div class="mt-6 flex flex-wrap gap-3"><a class="btn btn-accent" href="<?php echo e(route('booking.create')); ?>">Booking Servis</a><a class="btn btn-ghost" href="<?php echo e(route('services')); ?>">Lihat Layanan Servis</a></div>
</section>


<?php if($featuredReviews->isNotEmpty()): ?>
    <section class="home-reviews-panel mb-10" aria-label="Ulasan pilihan admin">
        <div class="home-reviews-heading">
            <div>
                <p class="home-reviews-eyebrow">Dipilih oleh admin</p>
                <h2 class="home-reviews-title">Cerita Pelanggan</h2>
            </div>
            <a class="home-reviews-all" href="<?php echo e(route('reviews.index')); ?>">Semua Ulasan <span aria-hidden="true">&#8594;</span></a>
        </div>
        <div class="review-carousel" data-review-carousel aria-roledescription="carousel">
            <div class="review-carousel-viewport">
                <div class="review-carousel-track" data-review-track>
                    <?php $__currentLoopData = $featuredReviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <article class="review-carousel-slide">
                            <div class="review-feature-card">
                                <div class="review-card-top">
                                    <div class="review-person">
                                        <span class="review-avatar"><?php echo e(strtoupper(substr($r->user->name ?? 'P', 0, 1))); ?></span>
                                        <div class="review-person-meta">
                                            <b><?php echo e($r->user->name ?? 'Pelanggan'); ?></b>
                                            <span><?php echo e($r->created_at->diffForHumans()); ?></span>
                                        </div>
                                    </div>
                                    <span class="review-source-mark" aria-label="Ulasan pelanggan">JM</span>
                                </div>
                                <div class="review-stars" aria-label="<?php echo e($r->rating); ?> dari 5 bintang"><?php echo e($r->stars()); ?></div>
                                <p class="review-comment"><?php echo e($r->comment ? \Illuminate\Support\Str::limit($r->comment, 125) : 'Pelanggan memberikan penilaian untuk layanan JM Komputer.'); ?></p>
                                <?php if($r->product): ?>
                                    <div class="review-product-label"><?php echo e($r->product->name); ?></div>
                                <?php endif; ?>
                                <?php if($r->admin_reply): ?>
                                    <div class="review-admin-reply"><b>Balasan JM Komputer</b><span><?php echo e(\Illuminate\Support\Str::limit($r->admin_reply, 75)); ?></span></div>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <?php if($featuredReviews->count() > 1): ?>
                <div class="review-carousel-controls">
                    <button type="button" class="review-carousel-arrow" data-review-prev aria-label="Ulasan sebelumnya">&#8592;</button>
                    <div class="review-carousel-dots" data-review-dots aria-label="Navigasi ulasan"></div>
                    <button type="button" class="review-carousel-arrow" data-review-next aria-label="Ulasan berikutnya">&#8594;</button>
                </div>
            <?php endif; ?>
        </div>
    </section>
<?php endif; ?>


<section class="home-contact-panel mb-8 grid gap-4 md:grid-cols-2">
    <div class="card">
        <p class="mb-1 text-xs font-bold uppercase tracking-[0.18em] text-accent">Datang langsung</p>
        <h2 class="text-xl font-bold text-slate-900">Kunjungi Toko Kami</h2>
        <p class="mt-3 text-sm"><?php echo e(config('store.address')); ?></p><p class="text-sm text-slate-500"><?php echo e(config('store.hours')); ?></p>
        <div class="mt-4 flex flex-wrap gap-3"><a class="btn btn-primary" href="<?php echo e(route('contact')); ?>">Lokasi &amp; Kontak</a><a class="btn" target="_blank" rel="noopener" href="<?php echo e(config('store.maps_link')); ?>">Buka di Maps</a></div>
    </div>
    <div class="relative overflow-hidden rounded-2xl border border-blue-900/20 bg-gradient-to-br from-[#163B60] to-[#0B1F36] p-6 text-white shadow-sm">
        <div class="pointer-events-none absolute -right-12 -top-16 h-52 w-52 rounded-full border border-white/10"></div>
        <div class="relative"><p class="mb-1 text-xs font-bold uppercase tracking-[0.18em] text-blue-200">Butuh bantuan?</p><h2 class="text-xl font-bold">Konsultasi sebelum membeli</h2>
        <p class="mt-2 max-w-md text-sm leading-6 text-blue-100/75">Kami siap membantu memilih perangkat sesuai kebutuhan dan anggaran Anda.</p>
        <a class="btn btn-light mt-4 w-fit" target="_blank" rel="noopener" href="<?php echo e($wa); ?>">Konsultasi via WhatsApp</a></div>
    </div>
</section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\jm-komputer\resources\views/home.blade.php ENDPATH**/ ?>