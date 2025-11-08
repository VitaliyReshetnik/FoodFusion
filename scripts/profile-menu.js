document.addEventListener("DOMContentLoaded", () => {
  const btn = document.querySelector(".profile-btn");
  const dropdown = document.querySelector(".dropdown");

  if (!btn || !dropdown) {
    console.error("❌ profile-menu.js: кнопка або меню не знайдені");
    return;
  }

  btn.addEventListener("click", (e) => {
    e.stopPropagation();
    dropdown.classList.toggle("active");
  });

  document.addEventListener("click", () => {
    dropdown.classList.remove("active");
  });
});
