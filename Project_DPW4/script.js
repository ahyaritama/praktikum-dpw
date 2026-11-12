// ==========================================
// 0. KUNCI HALAMAN SAMPAI KLIK "BUKA UNDANGAN"
// ==========================================
const isPreview = new URLSearchParams(window.location.search).has("preview");
const openBtn = document.querySelector('#cover a[href="#greeting"]');
const topNav = document.querySelector("nav");

if (!isPreview && openBtn) {
  if ("scrollRestoration" in history) history.scrollRestoration = "manual";
  window.scrollTo(0, 0);
  document.documentElement.classList.add("overflow-hidden");
  document.body.classList.add("overflow-hidden", "h-screen");
  if (topNav) topNav.classList.add("hidden");

  openBtn.addEventListener("click", (e) => {
    e.preventDefault();
    document.documentElement.classList.remove("overflow-hidden");
    document.body.classList.remove("overflow-hidden", "h-screen");
    if (topNav) topNav.classList.remove("hidden");
    const greeting = document.getElementById("greeting");
    if (greeting) greeting.scrollIntoView({ behavior: "smooth" });
  });
}

// ==========================================
// 1. NAVBAR DROPDOWN (MENU 3 TITIK)
// ==========================================
const menuBtn = document.getElementById("menu-btn");
const menuDropdown = document.getElementById("menu-dropdown");

if (menuBtn && menuDropdown) {
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

  // Tutup menu saat link diklik
  const menuLinks = menuDropdown.querySelectorAll("a");
  menuLinks.forEach((link) => {
    link.addEventListener("click", () => {
      menuDropdown.classList.add("hidden");
    });
  });
}

// ==========================================
// DOM CONTENT LOADED (GALERI, RSVP & WISHES)
// ==========================================
document.addEventListener("DOMContentLoaded", () => {
  // ------------------------------------------
  // 2. GALERI INFINITE CAROUSEL (KODE ASLI KAMU)
  // ------------------------------------------
  const container = document.getElementById("gallery-container");
  const btnPrev = document.getElementById("btn-prev");
  const btnNext = document.getElementById("btn-next");

  if (container && btnPrev && btnNext) {
    const originalItems = Array.from(
      container.querySelectorAll(".gallery-item"),
    );
    const N = originalItems.length; // 10 foto

    // Duplikasi item di kiri dan kanan (Total 30 foto untuk efek tanpa henti)
    originalItems.forEach((item) => {
      const cloneBefore = item.cloneNode(true);
      container.prepend(cloneBefore);
      const cloneAfter = item.cloneNode(true);
      container.appendChild(cloneAfter);
    });

    const allItems = Array.from(container.querySelectorAll(".gallery-item"));

    // Set padding otomatis agar foto selalu pas di tengah layar HP / Desktop
    const updatePadding = () => {
      if (!allItems[0]) return;
      const pad = window.innerWidth / 2 - allItems[0].offsetWidth / 2;
      container.style.paddingLeft = `${Math.max(0, pad)}px`;
      container.style.paddingRight = `${Math.max(0, pad)}px`;
    };
    updatePadding();

    let currentIndex = N; // Mulai dari set tengah (foto indeks ke-10)

    // Fungsi scroll ke indeks item target (hanya horizontal di dalam container)
    const scrollToIndex = (index, smooth = true) => {
      if (index < 0 || index >= allItems.length) return;
      const item = allItems[index];
      const left =
        item.offsetLeft - container.clientWidth / 2 + item.offsetWidth / 2;
      container.scrollTo({
        left,
        behavior: smooth ? "smooth" : "auto",
      });
    };

    // Set posisi awal ke tengah tanpa animasi
    setTimeout(() => {
      scrollToIndex(currentIndex, false);
      updateHighlights();
    }, 100);

    // Efek Membesar untuk Foto di Tengah
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

    // Cek Infinite Loop secara tak terlihat saat scroll berhenti
    let scrollTimer;
    let isJumping = false;

    container.addEventListener("scroll", () => {
      updateHighlights();

      clearTimeout(scrollTimer);
      scrollTimer = setTimeout(() => {
        if (isJumping) return;

        if (currentIndex < N) {
          isJumping = true;
          currentIndex += N;
          scrollToIndex(currentIndex, false);
          setTimeout(() => {
            isJumping = false;
            updateHighlights();
          }, 50);
        } else if (currentIndex >= N * 2) {
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

    // Tombol Kanan & Kiri
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
  }

  // ------------------------------------------
  // 3. RSVP FORM SUBMIT (TANPA RELOAD)
  // ------------------------------------------
  const rsvpForm = document.querySelector("#rsvp form");
  if (rsvpForm) {
    rsvpForm.addEventListener("submit", async (e) => {
      e.preventDefault();
      const data = new FormData(rsvpForm);
      data.append("code", window.GUEST_CODE || "");
      const res = await fetch("api/rsvp.php", { method: "POST", body: data });
      alert(
        res.ok
          ? "Terima kasih! Konfirmasi kehadiran RSVP Anda telah berhasil dikirim."
          : "Maaf, data belum lengkap atau terjadi kesalahan.",
      );
    });
  }

  // ------------------------------------------
  // HITUNG MUNDUR (tanggal dari dashboard)
  // ------------------------------------------
  const cdDays = document.getElementById("cd-days");
  if (cdDays && window.EVENT_DATE) {
    const target = new Date(window.EVENT_DATE).getTime();
    const tick = () => {
      const diff = Math.max(0, target - Date.now());
      const set = (id, v) =>
        (document.getElementById(id).textContent = String(v));
      set("cd-days", Math.floor(diff / 86400000));
      set("cd-hours", Math.floor((diff % 86400000) / 3600000));
      set("cd-mins", Math.floor((diff % 3600000) / 60000));
      set("cd-secs", Math.floor((diff % 60000) / 1000));
    };
    tick();
    setInterval(tick, 1000);
  }

  // ------------------------------------------
  // 4. WEDDING WISHES (CARD BESAR & AUTOSCROLL FADE-OUT)
  // ------------------------------------------
  const wishForm = document.getElementById("wishes-form");
  const wishNameInput = document.getElementById("wish-name");
  const wishTextInput = document.getElementById("wish-text");
  const charCountSpan = document.getElementById("char-count");
  const wishesList = document.getElementById("wishes-list");

  // a. Counter Sisa Huruf Real-time
  if (wishTextInput && charCountSpan) {
    wishTextInput.addEventListener("input", function () {
      const remaining = 300 - this.value.length;
      charCountSpan.textContent = `Sisa huruf: ${remaining}`;
    });
  }

  // b. Submit Ucapan Tanpa Reload Halaman (Format Card Besar p-5)
  if (wishForm && wishesList) {
    wishForm.addEventListener("submit", async function (e) {
      e.preventDefault();

      const name = wishNameInput.value.trim();
      const text = wishTextInput.value.trim();

      if (name && text) {
        const data = new FormData();
        data.append("name", name);
        data.append("message", text);
        data.append("code", window.GUEST_CODE || "");
        const res = await fetch("api/wish.php", { method: "POST", body: data });
        if (!res.ok) {
          alert("Ucapan gagal dikirim.");
          return;
        }

        const initial = name.charAt(0).toUpperCase();

        const newWishCard = document.createElement("div");
        newWishCard.className =
          "bg-white p-5 rounded-2xl shadow-sm flex items-start gap-4 transition-all duration-300";
        newWishCard.innerHTML = `
            <div class="w-12 h-12 bg-sage rounded-full flex items-center justify-center text-brown font-bold text-xl shrink-0">${initial}</div>
            <div class="flex-1 min-w-0">
                <p class="font-bold text-gray-800 text-base mb-1 truncate">${escapeHtml(name)}</p>
                <p class="text-gray-600 text-sm leading-relaxed">${escapeHtml(text)}</p>
            </div>
        `;

        // Masukkan ucapan baru ke paling atas
        wishesList.prepend(newWishCard);

        // Reset input form
        wishForm.reset();
        charCountSpan.textContent = "Sisa huruf: 300";

        // Scroll langsung ke paling atas agar ucapan baru langsung terlihat
        wishesList.scrollTop = 0;
      }
    });
  }

  // c. Autoscroll Pelan + Fade-Out Transisi saat Reset Loop
  if (wishesList) {
    let isHovered = false;
    let isResetting = false;
    let scrollAccumulator = 0;
    const scrollSpeed = 0.25; // Kecepatan halus

    wishesList.classList.add("transition-opacity", "duration-500");

    wishesList.addEventListener("mouseenter", () => (isHovered = true));
    wishesList.addEventListener("mouseleave", () => (isHovered = false));
    wishesList.addEventListener("touchstart", () => (isHovered = true));
    wishesList.addEventListener("touchend", () => (isHovered = false));

    function autoScrollWishes() {
      if (
        !isHovered &&
        !isResetting &&
        wishesList.scrollHeight > wishesList.clientHeight
      ) {
        scrollAccumulator += scrollSpeed;

        if (scrollAccumulator >= 1) {
          const step = Math.floor(scrollAccumulator);
          scrollAccumulator -= step;

          // Saat scroll menyentuh batas paling bawah
          if (
            Math.ceil(wishesList.scrollTop + wishesList.clientHeight) >=
            wishesList.scrollHeight - 2
          ) {
            isResetting = true;

            // Step 1: Fade out
            wishesList.style.opacity = "0";

            // Step 2: Kembalikan scroll ke paling atas
            setTimeout(() => {
              wishesList.scrollTop = 0;

              // Step 3: Fade in kembali
              setTimeout(() => {
                wishesList.style.opacity = "1";
                isResetting = false;
              }, 50);
            }, 500);
          } else {
            wishesList.scrollTop += step;
          }
        }
      }
      requestAnimationFrame(autoScrollWishes);
    }

    requestAnimationFrame(autoScrollWishes);
  }

  // Helper Sanitasi HTML (Mencegah XSS)
  function escapeHtml(str) {
    return str
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }
});
