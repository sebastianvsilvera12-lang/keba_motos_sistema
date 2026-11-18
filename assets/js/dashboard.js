// ==============================================================
// DINÁMICAS DEL DASHBOARD - KEBA MOTOS
// ==============================================================

document.addEventListener('DOMContentLoaded', function () {
    // Animación de conteo para números de estadísticas
    const counters = document.querySelectorAll('.stat-number');
    counters.forEach(counter => {
        const target = +counter.getAttribute('data-target') || parseInt(counter.innerText) || 0;
        if (target > 0) {
            let count = 0;
            const speed = 25;
            const step = Math.ceil(target / speed);
            const updateCount = () => {
                count += step;
                if (count < target) {
                    counter.innerText = count;
                    setTimeout(updateCount, 20);
                } else {
                    counter.innerText = target;
                }
            };
            updateCount();
        }
    });
});

