<div class="card card-hover group flex flex-col overflow-hidden p-0">
    <a href="<?php echo e(route('products.show', $product->slug)); ?>" class="relative block overflow-hidden bg-stone-100">
        <?php if($product->image_url): ?>
            <img src="<?php echo e($product->image_url); ?>" alt="<?php echo e($product->name); ?>" data-cart-product-image class="h-44 w-full object-cover transition duration-300 group-hover:scale-105">
        <?php else: ?>
            <div class="flex h-44 items-center justify-center text-sm text-stone-500">Foto produk</div>
        <?php endif; ?>
        <div class="absolute left-2 top-2 flex gap-1">
            <?php if($product->is_featured): ?><span class="badge">Unggulan</span><?php endif; ?>
            <?php if($product->stock > 0 && $product->stock <= 3): ?><span class="badge-accent">Sisa <?php echo e($product->stock); ?></span><?php endif; ?>
        </div>
    </a>
    <div class="flex flex-1 flex-col gap-1 p-4">
        <span class="text-xs text-stone-500"><?php echo e($product->category->name ?? ''); ?></span>
        <a href="<?php echo e(route('products.show', $product->slug)); ?>" class="font-semibold leading-snug hover:underline"><?php echo e($product->name); ?></a>
        <b class="mt-1 text-lg text-accent"><?php echo 'Rp '.number_format((float) ($product->price), 0, ',', '.'); ?></b>
        <?php if($product->stock > 0): ?>
            <form method="POST" action="<?php echo e(route('cart.add', $product)); ?>" data-cart-add class="mt-auto pt-3">
                <?php echo csrf_field(); ?>
                <button class="btn btn-primary w-full" type="submit">+ Keranjang</button>
            </form>
        <?php else: ?>
            <span class="chip chip-red mt-1 w-fit">Stok habis</span>
            <button class="btn mt-auto w-full" type="button" disabled>Stok Habis</button>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH C:\laragon\www\jm-komputer\resources\views/partials/product-card.blade.php ENDPATH**/ ?>