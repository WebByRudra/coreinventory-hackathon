// script.js
document.querySelectorAll('.card').forEach(card => {
    card.addEventListener('click', () => {
        card.style.transform = 'scale(1.1)';
        setTimeout(() => {
            card.style.transform = 'scale(1)';
        }, 300);
    });
});
document.querySelectorAll('.card').forEach(card => {
  card.addEventListener('mouseenter', () => card.classList.add('float'));
  card.addEventListener('mouseleave', () => card.classList.remove('float'));
});