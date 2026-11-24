import './bootstrap';


// Signature product carousel: elastic drag + layered product reveal.
document.querySelectorAll('[data-hero-carousel]').forEach((carousel) => {
    const track = carousel.querySelector('[data-carousel-track]');
    const slides = Array.from(carousel.querySelectorAll('[data-carousel-slide]'));
    const dots = Array.from(carousel.querySelectorAll('[data-carousel-dot]'));
    const previous = carousel.querySelector('[data-carousel-prev]');
    const next = carousel.querySelector('[data-carousel-next]');
    const count = carousel.querySelector('[data-carousel-count]');
    const viewport = carousel.querySelector('[data-carousel-viewport]');

    if (!track || !viewport || slides.length < 2) return;

    let activeIndex = 0;
    let pointerStartX = 0;
    let pointerStartY = 0;
    let dragX = 0;
    let isDragging = false;
    let didDrag = false;
    const formatNumber = (number) => String(number).padStart(2, '0');

    const paint = (index, animate = true) => {
        activeIndex = (index + slides.length) % slides.length;
        track.classList.toggle('is-dragging', !animate);
        track.style.transform = `translate3d(-${activeIndex * 100}%, 0, 0)`;

        slides.forEach((slide, i) => {
            const active = i === activeIndex;
            slide.classList.toggle('is-active', active);
            slide.classList.remove('is-entering');
            if (active && animate) {
                // Restart the layered reveal on every change.
                void slide.offsetWidth;
                slide.classList.add('is-entering');
            }
            slide.setAttribute('aria-hidden', active ? 'false' : 'true');
        });

        dots.forEach((dot, i) => {
            const active = i === activeIndex;
            dot.classList.toggle('is-active', active);
            dot.setAttribute('aria-current', active ? 'true' : 'false');
        });
        if (count) count.textContent = `${formatNumber(activeIndex + 1)} / ${formatNumber(slides.length)}`;
    };

    const finishDrag = () => {
        if (!isDragging) return;
        isDragging = false;
        track.classList.remove('is-dragging');
        viewport.classList.remove('is-grabbing');
        const threshold = Math.max(46, viewport.clientWidth * 0.14);
        if (Math.abs(dragX) > threshold) {
            paint(activeIndex + (dragX < 0 ? 1 : -1));
        } else {
            paint(activeIndex);
        }
        if (didDrag) {
            window.setTimeout(() => { didDrag = false; }, 80);
        }
        dragX = 0;
    };

    previous?.addEventListener('click', () => paint(activeIndex - 1));
    next?.addEventListener('click', () => paint(activeIndex + 1));
    dots.forEach((dot) => dot.addEventListener('click', () => paint(Number(dot.dataset.carouselDot))));

    viewport.addEventListener('pointerdown', (event) => {
        if (event.pointerType === 'mouse' && event.button !== 0) return;
        pointerStartX = event.clientX;
        pointerStartY = event.clientY;
        dragX = 0;
        isDragging = true;
        didDrag = false;
        track.classList.add('is-dragging');
        viewport.classList.add('is-grabbing');
        viewport.setPointerCapture?.(event.pointerId);
    });

    viewport.addEventListener('pointermove', (event) => {
        if (!isDragging) return;
        const dx = event.clientX - pointerStartX;
        const dy = event.clientY - pointerStartY;
        if (Math.abs(dy) > Math.abs(dx) * 1.25 && Math.abs(dy) > 8) {
            isDragging = false;
            track.classList.remove('is-dragging');
            viewport.classList.remove('is-grabbing');
            return;
        }
        dragX = dx;
        if (Math.abs(dx) > 7) didDrag = true;
        const resistance = Math.abs(dx) > viewport.clientWidth * 0.8 ? 0.55 : 1;
        track.style.transform = `translate3d(calc(-${activeIndex * 100}% + ${dx * resistance}px), 0, 0)`;
        const activeSlide = slides[activeIndex];
        activeSlide?.style.setProperty('--hero-drag-progress', String(Math.min(1, Math.abs(dx) / viewport.clientWidth)));
    });

    viewport.addEventListener('pointerup', finishDrag);
    viewport.addEventListener('pointercancel', finishDrag);
    viewport.addEventListener('lostpointercapture', finishDrag);
    viewport.addEventListener('click', (event) => {
        if (didDrag) {
            event.preventDefault();
            event.stopPropagation();
            didDrag = false;
        }
    }, true);

    paint(0, false);
});

// Carousel ulasan pelanggan (4 desktop, 2 tablet, 1 mobile): geser mengikuti jari/mouse + animasi halus.
document.querySelectorAll('[data-review-carousel]').forEach((carousel) => {
    const viewport = carousel.querySelector('.review-carousel-viewport');
    const track = carousel.querySelector('[data-review-track]');
    const slides = Array.from(carousel.querySelectorAll('.review-carousel-slide'));
    const dotsWrap = carousel.querySelector('[data-review-dots]');
    const previous = carousel.querySelector('[data-review-prev]');
    const next = carousel.querySelector('[data-review-next]');
    if (!viewport || !track || slides.length === 0) return;

    let page = 0;
    let visible = 4;
    let x = 0;
    let rafId = 0;
    let startX = 0;
    let startY = 0;
    let startTime = 0;
    let dragOrigin = 0;
    let dx = 0;
    let pressed = false;
    let dragging = false;

    const visibleCount = () => window.matchMedia('(max-width: 640px)').matches ? 1
        : window.matchMedia('(max-width: 900px)').matches ? 2 : 4;
    const pageCount = () => Math.max(1, Math.ceil(slides.length / visible));
    const pageX = (i) => -i * viewport.clientWidth;

    const render = () => { track.style.transform = `translate3d(${x}px, 0, 0)`; };
    const stopAnim = () => { window.cancelAnimationFrame(rafId); rafId = 0; };
    const easeOut = (t) => 1 - Math.pow(1 - t, 4);
    const animateTo = (target) => {
        stopAnim();
        const from = x;
        const dist = target - from;
        if (Math.abs(dist) < 0.5) { x = target; render(); return; }
        const duration = Math.max(380, Math.min(760, 380 + (Math.abs(dist) / Math.max(viewport.clientWidth, 1)) * 360));
        const t0 = performance.now();
        const step = (now) => {
            const t = Math.min(1, (now - t0) / duration);
            x = from + dist * easeOut(t);
            render();
            if (t < 1) rafId = window.requestAnimationFrame(step); else rafId = 0;
        };
        rafId = window.requestAnimationFrame(step);
    };

    const markDots = () => {
        if (!dotsWrap) return;
        dotsWrap.querySelectorAll('.review-carousel-dot').forEach((dot, i) => {
            dot.classList.toggle('is-active', i === page);
            dot.setAttribute('aria-current', i === page ? 'true' : 'false');
        });
    };

    const renderDots = () => {
        if (!dotsWrap) return;
        dotsWrap.innerHTML = '';
        for (let i = 0; i < pageCount(); i += 1) {
            const dot = document.createElement('button');
            dot.type = 'button';
            dot.className = 'review-carousel-dot';
            dot.setAttribute('aria-label', `Tampilkan kelompok ulasan ${i + 1}`);
            dot.addEventListener('click', () => showPage(i));
            dotsWrap.appendChild(dot);
        }
        markDots();
    };

    const showPage = (index, animate = true) => {
        page = (index + pageCount()) % pageCount();
        markDots();
        if (animate) animateTo(pageX(page)); else { stopAnim(); x = pageX(page); render(); }
    };

    // Geser mengikuti jari; di ujung terasa menahan, tombol panah tetap berputar.
    const goBy = (step) => {
        const target = Math.max(0, Math.min(pageCount() - 1, page + step));
        showPage(target);
    };

    const release = () => {
        if (!pressed) return;
        pressed = false;
        viewport.classList.remove('is-grabbing');
        if (!dragging) return;
        dragging = false;
        const elapsed = Math.max(1, performance.now() - startTime);
        const velocity = dx / elapsed;
        const far = Math.abs(dx) > viewport.clientWidth * 0.14;
        const fast = Math.abs(velocity) > 0.4 && Math.abs(dx) > 20;
        if (far || fast) goBy(dx < 0 ? 1 : -1); else showPage(page);
        dx = 0;
    };

    viewport.addEventListener('pointerdown', (e) => {
        if (e.pointerType === 'mouse' && e.button !== 0) return;
        stopAnim();
        pressed = true;
        dragging = false;
        dx = 0;
        dragOrigin = x;
        startX = e.clientX;
        startY = e.clientY;
        startTime = performance.now();
    });

    viewport.addEventListener('pointermove', (e) => {
        if (!pressed) return;
        const mx = e.clientX - startX;
        const my = e.clientY - startY;
        if (!dragging) {
            if (Math.abs(mx) < 6 && Math.abs(my) < 6) return;
            if (Math.abs(my) > Math.abs(mx)) { pressed = false; showPage(page); return; }
            dragging = true;
            viewport.classList.add('is-grabbing');
            viewport.setPointerCapture?.(e.pointerId);
        }
        dx = mx;
        const atEdge = (page === 0 && dx > 0) || (page === pageCount() - 1 && dx < 0);
        x = dragOrigin + (atEdge ? dx * 0.35 : dx);
        render();
    });

    ['pointerup', 'pointercancel', 'lostpointercapture'].forEach((type) => viewport.addEventListener(type, release));

    previous?.addEventListener('click', () => showPage(page - 1));
    next?.addEventListener('click', () => showPage(page + 1));

    const refresh = () => {
        const nextVisible = visibleCount();
        if (nextVisible !== visible) {
            visible = nextVisible;
            page = Math.min(page, pageCount() - 1);
            renderDots();
        }
        // When fewer than four reviews are selected, let the cards use the full row.
        slides.forEach((slide) => { slide.style.flexBasis = `${100 / Math.min(visible, slides.length)}%`; });
        showPage(page, false);
    };

    window.addEventListener('resize', refresh);
    visible = visibleCount();
    renderDots();
    refresh();
});

// Add to cart without leaving the product page, with a product-to-cart animation.
(() => {
    const forms = document.querySelectorAll('form[data-cart-add]');
    if (!forms.length) return;

    const cartLink = document.querySelector('[data-cart-target]');
    const feedback = document.querySelector('[data-cart-feedback]');
    let feedbackTimer;

    const showFeedback = (message, isError = false) => {
        if (!feedback) return;
        feedback.textContent = message;
        feedback.classList.toggle('is-error', isError);
        feedback.classList.add('is-visible');
        window.clearTimeout(feedbackTimer);
        feedbackTimer = window.setTimeout(() => feedback.classList.remove('is-visible'), 2600);
    };

    const updateCartCount = (count) => {
        const badge = document.querySelector('[data-cart-count]');
        if (!badge || typeof count !== 'number') return;
        badge.textContent = count > 99 ? '99+' : String(count);
        badge.classList.toggle('is-empty', count < 1);
        if (cartLink) {
            cartLink.setAttribute('aria-label', `Keranjang belanja${count > 0 ? `, ${count} produk` : ''}`);
        }
    };

    const animateProductToCart = (form) => {
        if (!cartLink) return;
        const source = form.closest('.card')?.querySelector('[data-cart-product-image]')
            || document.querySelector('[data-cart-product-image]');
        if (!source) {
            cartLink.classList.remove('cart-bump');
            void cartLink.offsetWidth;
            cartLink.classList.add('cart-bump');
            return;
        }

        const start = source.getBoundingClientRect();
        const end = cartLink.getBoundingClientRect();
        if (!start.width || !start.height || !end.width || !end.height) return;

        const flyer = source.cloneNode(true);
        flyer.removeAttribute('data-cart-product-image');
        Object.assign(flyer.style, {
            position: 'fixed', left: `${start.left}px`, top: `${start.top}px`,
            width: `${Math.min(start.width, 150)}px`, height: `${Math.min(start.height, 120)}px`,
            objectFit: 'cover', borderRadius: '12px', zIndex: '9999',
            pointerEvents: 'none', boxShadow: '0 12px 30px rgba(8, 31, 54, .28)',
        });
        document.body.appendChild(flyer);

        const dx = end.left + end.width / 2 - (start.left + start.width / 2);
        const dy = end.top + end.height / 2 - (start.top + start.height / 2);
        const animation = flyer.animate([
            { transform: 'translate3d(0,0,0) scale(1)', opacity: 1 },
            { transform: `translate3d(${dx * .55}px,${dy * .55 - 35}px,0) scale(.62)`, opacity: .9, offset: .62 },
            { transform: `translate3d(${dx}px,${dy}px,0) scale(.12)`, opacity: .12 },
        ], { duration: 680, easing: 'cubic-bezier(.2,.72,.25,1)', fill: 'forwards' });

        animation.onfinish = () => flyer.remove();
        cartLink.classList.remove('cart-bump');
        void cartLink.offsetWidth;
        cartLink.classList.add('cart-bump');
        window.setTimeout(() => cartLink.classList.remove('cart-bump'), 520);
    };

    forms.forEach((form) => {
        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            const button = form.querySelector('button[type="submit"]');
            if (button?.disabled) return;
            const originalText = button?.textContent;
            if (button) {
                button.disabled = true;
                button.classList.add('is-adding-to-cart');
                button.textContent = 'Menambahkan…';
            }

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin',
                });
                const result = await response.json();
                if (!response.ok) throw new Error(result.message || 'Produk gagal ditambahkan.');

                updateCartCount(result.count);
                animateProductToCart(form);
                showFeedback(result.message || 'Produk berhasil ditambahkan ke keranjang.');
            } catch (error) {
                showFeedback(error.message || 'Terjadi kesalahan. Silakan coba lagi.', true);
            } finally {
                if (button) {
                    button.disabled = false;
                    button.classList.remove('is-adding-to-cart');
                    button.textContent = originalText || '+ Keranjang';
                }
            }
        });
    });
})();

// Banner beranda: swipe halus + looping tanpa batas.
// Animasi digerakkan JavaScript (requestAnimationFrame), jadi tidak ikut mati oleh pengaturan CSS/OS.
document.querySelectorAll('[data-home-banner]').forEach((banner) => {
    const viewport = banner.querySelector('[data-banner-viewport]');
    const track = banner.querySelector('[data-banner-track]');
    const slides = Array.from(banner.querySelectorAll('[data-banner-slide]'));
    const dots = Array.from(banner.querySelectorAll('[data-banner-dot]'));
    const prev = banner.querySelector('[data-banner-prev]');
    const next = banner.querySelector('[data-banner-next]');
    if (!viewport || !track || slides.length === 0) return;

    const total = slides.length;

    // Salinan gambar terakhir di awal dan gambar pertama di akhir, supaya geseran tetap searah.
    const makeClone = (slide) => {
        const clone = slide.cloneNode(true);
        clone.removeAttribute('data-banner-slide');
        clone.setAttribute('data-banner-clone', '');
        clone.setAttribute('aria-hidden', 'true');
        clone.setAttribute('tabindex', '-1');
        clone.setAttribute('inert', '');
        const img = clone.querySelector('img');
        img?.removeAttribute('fetchpriority');
        img?.setAttribute('loading', 'eager');
        return clone;
    };
    const headClone = total > 1 ? makeClone(slides[total - 1]) : null;
    const tailClone = total > 1 ? makeClone(slides[0]) : null;
    if (headClone && tailClone) {
        track.insertBefore(headClone, track.firstChild);
        track.appendChild(tailClone);
    }
    const offset = headClone ? 1 : 0;

    let pos = offset;          // posisi di track (0 dan total+1 = salinan)
    let width = viewport.clientWidth;
    let x = 0;                 // geser track dalam px
    let rafId = 0;
    let locked = false;
    let dragOrigin = 0;
    let startX = 0;
    let startY = 0;
    let startTime = 0;
    let dx = 0;
    let pressed = false;
    let dragging = false;
    let moved = false;

    const render = () => { track.style.transform = `translate3d(${x}px, 0, 0)`; };
    const stopAnim = () => { window.cancelAnimationFrame(rafId); rafId = 0; };
    const easeOut = (t) => 1 - Math.pow(1 - t, 4);

    const animateTo = (target, done) => {
        stopAnim();
        const from = x;
        const dist = target - from;
        if (Math.abs(dist) < 0.5) { x = target; render(); done?.(); return; }
        const duration = Math.max(420, Math.min(820, 420 + (Math.abs(dist) / Math.max(width, 1)) * 400));
        const t0 = performance.now();
        const step = (now) => {
            const t = Math.min(1, (now - t0) / duration);
            x = from + dist * easeOut(t);
            render();
            if (t < 1) { rafId = window.requestAnimationFrame(step); } else { rafId = 0; done?.(); }
        };
        rafId = window.requestAnimationFrame(step);
    };

    const logicalIndex = () => (((pos - offset) % total) + total) % total;

    const markActive = () => {
        const current = logicalIndex();
        slides.forEach((slide, n) => {
            const active = n === current;
            slide.classList.toggle('is-active', active);
            slide.setAttribute('aria-hidden', active ? 'false' : 'true');
            slide.tabIndex = active ? 0 : -1;
        });
        headClone?.classList.toggle('is-active', current === total - 1);
        tailClone?.classList.toggle('is-active', current === 0);
        dots.forEach((dot, n) => {
            dot.classList.toggle('is-active', n === current);
            dot.setAttribute('aria-current', n === current ? 'true' : 'false');
        });
    };

    // Mendarat di salinan -> pindah diam-diam ke gambar asli yang sama.
    const settle = () => {
        if (headClone && (pos === 0 || pos === total + 1)) {
            pos = pos === 0 ? total : 1;
            x = -pos * width;
            render();
        }
        locked = false;
    };

    const goTo = (newPos, animate = true) => {
        pos = Math.max(0, Math.min(total + offset, newPos));
        markActive();
        const target = -pos * width;
        if (!animate) { stopAnim(); x = target; render(); settle(); return; }
        if (headClone && (pos === 0 || pos === total + 1)) locked = true;
        animateTo(target, settle);
    };

    const release = () => {
        if (!pressed) return;
        pressed = false;
        viewport.classList.remove('is-grabbing');
        if (!dragging) return;
        dragging = false;
        const elapsed = Math.max(1, performance.now() - startTime);
        const velocity = dx / elapsed; // px per ms
        const far = Math.abs(dx) > width * 0.16;
        const fast = Math.abs(velocity) > 0.4 && Math.abs(dx) > 20;
        goTo(far || fast ? pos + (dx < 0 ? 1 : -1) : pos);
        dx = 0;
    };

    viewport.addEventListener('pointerdown', (e) => {
        if (locked) return;
        if (e.pointerType === 'mouse' && e.button !== 0) return;
        stopAnim();
        pressed = true;
        dragging = false;
        moved = false;
        dx = 0;
        dragOrigin = x;
        startX = e.clientX;
        startY = e.clientY;
        startTime = performance.now();
    });

    viewport.addEventListener('pointermove', (e) => {
        if (!pressed) return;
        const mx = e.clientX - startX;
        const my = e.clientY - startY;
        if (!dragging) {
            if (Math.abs(mx) < 6 && Math.abs(my) < 6) return;
            if (Math.abs(my) > Math.abs(mx)) { pressed = false; goTo(pos); return; } // scroll vertikal biasa
            dragging = true;
            moved = true;
            viewport.classList.add('is-grabbing');
            viewport.setPointerCapture?.(e.pointerId);
        }
        dx = mx;
        x = dragOrigin + dx;
        render();
    });

    ['pointerup', 'pointercancel', 'lostpointercapture'].forEach((type) => viewport.addEventListener(type, release));

    // Cegah klik (pindah halaman) setelah menggeser.
    viewport.addEventListener('click', (e) => {
        if (moved) { e.preventDefault(); e.stopPropagation(); moved = false; }
    }, true);

    prev?.addEventListener('click', () => { if (!locked) goTo(pos - 1); });
    next?.addEventListener('click', () => { if (!locked) goTo(pos + 1); });
    dots.forEach((dot) => dot.addEventListener('click', () => {
        if (!locked) goTo(Number(dot.dataset.bannerDot) + offset);
    }));
    banner.addEventListener('keydown', (e) => {
        if (locked) return;
        if (e.key === 'ArrowLeft') goTo(pos - 1);
        if (e.key === 'ArrowRight') goTo(pos + 1);
    });

    window.addEventListener('resize', () => {
        width = viewport.clientWidth;
        stopAnim();
        x = -pos * width;
        render();
    });

    goTo(offset, false);
});

// Navbar beranda: transparan di atas banner, kembali ke tampilan biasa setelah banner habis.
(() => {
    const header = document.querySelector('[data-overlay-header]');
    const banner = document.querySelector('[data-home-banner]');
    if (!header || !banner) return;

    let queued = false;
    const update = () => {
        queued = false;
        const bannerGone = banner.getBoundingClientRect().bottom <= header.offsetHeight;
        header.classList.toggle('is-transparent', !bannerGone);
    };
    const queue = () => {
        if (queued) return;
        queued = true;
        window.requestAnimationFrame(update);
    };

    // Tinggi navbar dipakai CSS mobile agar gambar banner mulai tepat di bawah navbar.
    const setHeaderHeight = () => {
        if (header.querySelector('details[open]')) return;
        banner.style.setProperty('--header-h', `${header.offsetHeight}px`);
    };

    window.addEventListener('scroll', queue, { passive: true });
    window.addEventListener('resize', () => { setHeaderHeight(); queue(); });
    window.addEventListener('pageshow', () => { setHeaderHeight(); queue(); });
    window.addEventListener('load', setHeaderHeight);
    setHeaderHeight();
    update();
})();


// Tombol mata: tampilkan / sembunyikan password.
document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-toggle-password]');
    if (!button) return;
    const input = button.parentElement?.querySelector('input');
    if (!input) return;
    const show = input.type === 'password';
    input.type = show ? 'text' : 'password';
    const label = show ? 'Sembunyikan password' : 'Tampilkan password';
    button.setAttribute('aria-pressed', show ? 'true' : 'false');
    button.setAttribute('aria-label', label);
    button.title = label;
});


// Pemberitahuan verifikasi email: tombol X menutupnya, dan tidak muncul lagi selama sesi browser.
document.querySelectorAll('[data-verify-toast]').forEach((toast) => {
    toast.querySelector('[data-verify-close]')?.addEventListener('click', () => {
        try { sessionStorage.setItem('jm-verify-dismissed', '1'); } catch (e) { /* abaikan */ }
        toast.classList.add('is-leaving');
        window.setTimeout(() => toast.remove(), 260);
    });
});
