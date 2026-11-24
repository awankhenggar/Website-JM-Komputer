<?php if(session('success')): ?>
    <div role="status" class="mb-4 rounded-xl border border-green-200 bg-green-50 p-3 text-sm text-green-900"><?php echo e(session('success')); ?></div>
<?php endif; ?>
<?php if(session('error')): ?>
    <div role="alert" class="mb-4 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-900"><?php echo e(session('error')); ?></div>
<?php endif; ?>
<?php $__errorArgs = ['cart'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
    <div role="alert" class="mb-4 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-900"><?php echo e($message); ?></div>
<?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
<?php /**PATH C:\laragon\www\jm-komputer\resources\views/partials/flash.blade.php ENDPATH**/ ?>