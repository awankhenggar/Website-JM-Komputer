
<?php
    $n = \Illuminate\Support\Str::lower($name ?? '');
    $type = match (true) {
        str_contains($n, 'gaming') => 'gamepad',
        str_contains($n, 'laptop') => 'laptop',
        str_contains($n, 'rakitan'), str_contains($n, 'desktop'), str_contains($n, 'pc') => 'tower',
        str_contains($n, 'aksesori'), str_contains($n, 'aksesoris'), str_contains($n, 'upgrade') => 'keyboard',
        default => 'box',
    };
?>
<svg class="category-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    <?php switch($type):
        case ('gamepad'): ?>
            <rect x="2" y="6" width="20" height="12" rx="4"/>
            <path d="M6 12h4M8 10v4"/>
            <path d="M15 11.5h.01M18 13.5h.01"/>
            <?php break; ?>
        <?php case ('laptop'): ?>
            <path d="M5 17V7a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v10"/>
            <path d="M2.5 19.5h19"/>
            <path d="M3 17h18l.6 2.5H2.4L3 17Z"/>
            <?php break; ?>
        <?php case ('tower'): ?>
            <rect x="5" y="2.5" width="14" height="19" rx="2.5"/>
            <circle cx="12" cy="14" r="3.2"/>
            <path d="M8.5 6h7"/>
            <?php break; ?>
        <?php case ('keyboard'): ?>
            <rect x="2" y="5" width="20" height="14" rx="2.5"/>
            <path d="M6 9h.01M10 9h.01M14 9h.01M18 9h.01M6 12.5h.01M10 12.5h.01M14 12.5h.01M18 12.5h.01M8 16h8"/>
            <?php break; ?>
        <?php default: ?>
            <path d="M21 8 12 3 3 8v8l9 5 9-5V8Z"/>
            <path d="m3 8 9 5 9-5M12 13v8"/>
    <?php endswitch; ?>
</svg>
<?php /**PATH C:\laragon\www\jm-komputer\resources\views/partials/category-icon.blade.php ENDPATH**/ ?>