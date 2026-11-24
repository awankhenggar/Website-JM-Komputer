<?php $__env->startSection('title', 'Produk'); ?>
<?php $__env->startSection('content'); ?>
<h1 class="mb-6 text-3xl font-bold">Produk</h1>
<div class="flex flex-col gap-6 md:flex-row">
    <form method="GET" action="<?php echo e(route('products.index')); ?>" class="card flex flex-col gap-3 self-start md:w-60">
        <b>Filter</b>
        <label class="text-sm">Cari<input class="input mt-1" type="search" name="q" value="<?php echo e(request('q')); ?>"></label>
        <label class="text-sm">Kategori
            <select class="input mt-1" name="kategori">
                <option value="">Semua kategori</option>
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($c->slug); ?>" <?php if(request('kategori') === $c->slug): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </label>
        <label class="text-sm">Urutkan
            <select class="input mt-1" name="urut">
                <option value="">Terbaru</option>
                <option value="termurah" <?php if(request('urut') === 'termurah'): echo 'selected'; endif; ?>>Harga terendah</option>
                <option value="termahal" <?php if(request('urut') === 'termahal'): echo 'selected'; endif; ?>>Harga tertinggi</option>
            </select>
        </label>
        <button class="btn btn-primary" type="submit">Terapkan</button>
        <a class="btn" href="<?php echo e(route('products.index')); ?>">Reset</a>
    </form>
    <div class="flex-1">
        <p class="mb-3 text-sm text-stone-700"><?php echo e($products->total()); ?> produk</p>
        <?php if($products->isEmpty()): ?>
            <div class="card text-center">
                <b>Tidak ada hasil</b>
                <p class="mt-1 text-sm">Periksa ejaan, kurangi filter, atau tanyakan langsung ke kami.</p>
                <div class="mt-3 flex justify-center gap-3"><a class="btn" href="<?php echo e(route('products.index')); ?>">Hapus Filter</a><a class="btn btn-accent" target="_blank" rel="noopener" href="https://wa.me/<?php echo e(config('store.whatsapp')); ?>?text=<?php echo e(urlencode('Halo, saya mencari produk: '.request('q'))); ?>">Tanya via WhatsApp</a></div>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-2 gap-4 lg:grid-cols-3">
                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php echo $__env->make('partials.product-card', ['product' => $product], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div class="mt-6"><?php echo e($products->links()); ?></div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\jm-komputer\resources\views/products/index.blade.php ENDPATH**/ ?>