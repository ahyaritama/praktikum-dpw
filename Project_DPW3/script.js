const menuBtn = document.getElementById("menu-btn");
const menuDropdown = document.getElementById("menu-dropdown");

// Toggle dropdown saat tombol diklik
menuBtn.addEventListener("click", (e) => {
  e.stopPropagation();
  menuDropdown.classList.toggle("hidden");
});

// Tutup dropdown jika klik di luar area menu
document.addEventListener("click", (e) => {
  if (!menuBtn.contains(e.target) && !menuDropdown.contains(e.target)) {
    menuDropdown.classList.add("hidden");
  }
});

// Tutup menu saat link diklik (opsional, karena ini one-page scroll)
const menuLinks = menuDropdown.querySelectorAll("a");
menuLinks.forEach((link) => {
  link.addEventListener("click", () => {
    menuDropdown.classList.add("hidden");
  });
});
