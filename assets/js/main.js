(() => {
    const $ = (s, c = document) => [...c.querySelectorAll(s)];
    const rm = matchMedia('(prefers-reduced-motion: reduce)').matches;
    const PH = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="600" height="400"><rect width="100%" height="100%" fill="%23dfe8da"/></svg>';
    // broken image fallback
    document.addEventListener('error', e => { const i = e.target; if (i.tagName === 'IMG' && !i.dataset.f) { i.dataset.f = 1; i.src = PH; } }, true);
    // reveal on scroll
    const io = new IntersectionObserver(es => es.forEach(e => { if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } }), { threshold: .12, rootMargin: '0px 0px -40px 0px' });
    $('.reveal').forEach(el => rm ? el.classList.add('in') : io.observe(el));
    // count-up
    const co = new IntersectionObserver(es => es.forEach(e => {
        if (!e.isIntersecting) return; co.unobserve(e.target);
        const el = e.target, to = +el.dataset.count, sfx = el.dataset.suffix || '', t0 = performance.now();
        const step = t => { const p = Math.min((t - t0) / 1600, 1); el.textContent = Math.round(to * (1 - Math.pow(1 - p, 3))) + (p === 1 ? sfx : ''); if (p < 1) requestAnimationFrame(step); };
        rm ? el.textContent = to + sfx : requestAnimationFrame(step);
    }), { threshold: .6 });
    $('[data-count]').forEach(el => co.observe(el));
    // scroll progress + back to top
    const bar = document.getElementById('aqProgress'), top = document.querySelector('.aq-top');
    const onScroll = () => { const h = document.documentElement; const y = h.scrollTop; if (bar) bar.style.width = (y / (h.scrollHeight - h.clientHeight) * 100) + '%'; if (top) top.classList.toggle('show', y > 600); };
    addEventListener('scroll', onScroll, { passive: true }); onScroll();
    top && top.addEventListener('click', () => scrollTo({ top: 0, behavior: 'smooth' }));
    // product category filter
    $('.aq-chip').forEach(ch => ch.addEventListener('click', () => {
        $('.aq-chip').forEach(c => c.classList.remove('on')); ch.classList.add('on');
        $('[data-cat]').forEach(p => p.classList.toggle('aq-hide', ch.dataset.f !== 'all' && p.dataset.cat !== ch.dataset.f));
    }));
    // gallery lightbox
    const m = document.getElementById('aqLb');
    if (m && window.bootstrap) {
        const lb = new bootstrap.Modal(m), im = m.querySelector('img');
        $('.aq-lb').forEach(a => a.addEventListener('click', e => { e.preventDefault(); im.src = a.href; lb.show(); }));
    }
})();