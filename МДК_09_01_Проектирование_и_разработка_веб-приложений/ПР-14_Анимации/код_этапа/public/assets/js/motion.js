const calm = matchMedia('(prefers-reduced-motion: reduce)').matches;

const items = document.querySelectorAll('.ws, .card');
if (!calm && 'IntersectionObserver' in window) {
    const io = new IntersectionObserver(entries => {
        entries.forEach(en => {
            if (en.isIntersecting) {
                en.target.classList.add('is-visible');
                io.unobserve(en.target);
            }
        });
    }, { threshold: 0.12 });
    items.forEach((el, i) => {
        el.classList.add('reveal');
        el.style.transitionDelay = `${(i % 3) * 70}ms`;
        io.observe(el);
    });
}

const notes = document.querySelectorAll('.page > .note');
if (notes.length) {
    const box = document.createElement('div');
    box.className = 'toasts';
    box.setAttribute('role', 'status');
    document.body.append(box);
    notes.forEach(n => {
        box.append(n);
        const close = () => {
            n.classList.add('is-gone');
            n.addEventListener('animationend', () => n.remove(), { once: true });
        };
        n.addEventListener('click', close);
        setTimeout(close, calm ? 8000 : 5000);
    });
}

function wobble() {
    document.querySelectorAll('.field--bad').forEach(f => {
        f.classList.remove('wobble');
        void f.offsetWidth;
        f.classList.add('wobble');
    });
}
wobble();
document.querySelectorAll('form[data-check]').forEach(f => f.addEventListener('submit', () => setTimeout(wobble)));
