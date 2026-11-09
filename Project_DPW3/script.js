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

//js untuk bagian gallery
document.addEventListener("DOMContentLoaded", () => {
  const container = document.getElementById("gallery-container");
  const btnPrev = document.getElementById("btn-prev");
  const btnNext = document.getElementById("btn-next");

  const originalItems = Array.from(container.querySelectorAll(".gallery-item"));
  const N = originalItems.length; // 10 foto

  // 1. Duplikasi item di kiri dan kanan (Total 30 foto untuk efek tanpa henti)
  originalItems.forEach((item) => {
    const cloneBefore = item.cloneNode(true);
    container.prepend(cloneBefore);
    const cloneAfter = item.cloneNode(true);
    container.appendChild(cloneAfter);
  });

  const allItems = Array.from(container.querySelectorAll(".gallery-item"));

  // 2. Set padding otomatis agar foto selalu bisa pas di tengah layar HP / Desktop
  const updatePadding = () => {
    if (!allItems[0]) return;
    const pad = window.innerWidth / 2 - allItems[0].offsetWidth / 2;
    container.style.paddingLeft = `${Math.max(0, pad)}px`;
    container.style.paddingRight = `${Math.max(0, pad)}px`;
  };
  updatePadding();

  let currentIndex = N; // Mulai dari set tengah (foto indeks ke-10)

  // 3. Fungsi scroll ke indeks item target
  const scrollToIndex = (index, smooth = true) => {
    if (index < 0 || index >= allItems.length) return;
    allItems[index].scrollIntoView({
      behavior: smooth ? "smooth" : "auto",
      inline: "center",
      block: "nearest",
    });
  };

  // Set posisi awal ke tengah tanpa animasi
  setTimeout(() => {
    scrollToIndex(currentIndex, false);
    updateHighlights();
  }, 100);

  // 4. Efek Membesar untuk Foto di Tengah
  const updateHighlights = () => {
    const containerCenter =
      container.getBoundingClientRect().left + container.clientWidth / 2;
    let minDistance = Infinity;
    let activeIdx = currentIndex;

    allItems.forEach((item, idx) => {
      const rect = item.getBoundingClientRect();
      const itemCenter = rect.left + rect.width / 2;
      const distance = Math.abs(containerCenter - itemCenter);

      if (distance < minDistance) {
        minDistance = distance;
        activeIdx = idx;
      }
    });

    currentIndex = activeIdx;

    allItems.forEach((item, idx) => {
      if (idx === activeIdx) {
        item.classList.add(
          "scale-110",
          "z-10",
          "shadow-2xl",
          "ring-4",
          "ring-brown/40",
          "opacity-100",
        );
        item.classList.remove("scale-90", "opacity-50");
      } else {
        item.classList.remove(
          "scale-110",
          "z-10",
          "shadow-2xl",
          "ring-4",
          "ring-brown/40",
          "opacity-100",
        );
        item.classList.add("scale-90", "opacity-50");
      }
    });
  };

  // 5. Cek Infinite Loop secara tak terlihat saat scroll berhenti
  let scrollTimer;
  let isJumping = false;

  container.addEventListener("scroll", () => {
    updateHighlights();

    clearTimeout(scrollTimer);
    scrollTimer = setTimeout(() => {
      if (isJumping) return;

      // Jika digeser mentok kiri, lompat ke set tengah
      if (currentIndex < N) {
        isJumping = true;
        currentIndex += N;
        scrollToIndex(currentIndex, false);
        setTimeout(() => {
          isJumping = false;
          updateHighlights();
        }, 50);
      }
      // Jika digeser mentok kanan, lompat ke set tengah
      else if (currentIndex >= N * 2) {
        isJumping = true;
        currentIndex -= N;
        scrollToIndex(currentIndex, false);
        setTimeout(() => {
          isJumping = false;
          updateHighlights();
        }, 50);
      }
    }, 150);
  });

  // 6. Tombol Kanan & Kiri
  btnPrev.addEventListener("click", () => {
    currentIndex--;
    scrollToIndex(currentIndex, true);
  });

  btnNext.addEventListener("click", () => {
    currentIndex++;
    scrollToIndex(currentIndex, true);
  });

  window.addEventListener("resize", () => {
    updatePadding();
    updateHighlights();
  });
});
