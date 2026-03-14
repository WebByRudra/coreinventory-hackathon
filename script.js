document.querySelectorAll('input').forEach(input => {
  input.addEventListener('focus', () => {
    input.style.transform = "translateY(-2px)";
  });
  input.addEventListener('blur', () => {
    input.style.transform = "translateY(0)";
  });
});