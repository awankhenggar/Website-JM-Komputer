<?php $__env->startSection('title', 'Kontak & Lokasi'); ?>
<?php $__env->startSection('content'); ?>
<h1 class="mb-6 text-3xl font-bold">Kontak &amp; Lokasi</h1>
<div class="grid gap-6 md:grid-cols-2">
    <div>
        <?php if(config('store.maps_embed')): ?>
            <iframe src="<?php echo e(config('store.maps_embed')); ?>" class="h-80 w-full rounded-xl border border-stone-300" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Lokasi <?php echo e(config('store.name')); ?>"></iframe>
        <?php else: ?>
            <div class="flex h-80 items-center justify-center rounded-xl bg-stone-200 text-sm text-stone-600">Peta belum diatur (isi STORE_MAPS_EMBED di .env)</div>
        <?php endif; ?>
    </div>
    <div class="card flex flex-col gap-3 text-sm">
        <b class="text-lg"><?php echo e(config('store.name')); ?></b>
        <span><?php echo e(config('store.address')); ?></span>
        <span><?php echo e(config('store.hours')); ?></span>
        <span>Telepon: <?php echo e(config('store.phone')); ?></span>
        <a class="btn btn-accent" target="_blank" rel="noopener" href="https://wa.me/<?php echo e(config('store.whatsapp')); ?>?text=<?php echo e(urlencode('Halo '.config('store.name').', saya ingin konsultasi.')); ?>">Konsultasi via WhatsApp</a>
        <a class="btn" target="_blank" rel="noopener" href="<?php echo e(config('store.maps_link')); ?>">Buka di Maps</a>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\jm-komputer\resources\views/contact.blade.php ENDPATH**/ ?>