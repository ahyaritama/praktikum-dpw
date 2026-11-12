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

  // Tutup menu saat link diklik (terutama berguna di versi mobile)
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
  // 2. GALERI INFINITE CAROUSEL
  // ------------------------------------------
  const container = document.getElementById("gallery-container");
  const btnPrev = document.getElementById("btn-prev");
  const btnNext = document.getElementById("btn-next");

  if (container && btnPrev && btnNext) {
    const originalItems = Array.from(
      container.querySelectorAll(".gallery-item"),
    );
    const N = originalItems.length;

    // Duplikasi item di kiri dan kanan
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

    // Inisialisasi padding
    updatePadding();

    let currentIndex = N;

    const scrollToIndex = (index, smooth = true) => {
      if (index < 0 || index >= allItems.length) return;
      allItems[index].scrollIntoView({
        behavior: smooth ? "smooth" : "auto",
        inline: "center",
        block: "nearest",
      });
    };

    setTimeout(() => {
      scrollToIndex(currentIndex, false);
      updateHighlights();
    }, 100);

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

    btnPrev.addEventListener("click", () => {
      currentIndex--;
      scrollToIndex(currentIndex, true);
    });

    btnNext.addEventListener("click", () => {
      currentIndex++;
      scrollToIndex(currentIndex, true);
    });

    // Menangani perubahan ukuran layar (rotasi layar atau resize windows)
    window.addEventListener("resize", () => {
      updatePadding();
      scrollToIndex(currentIndex, false);
      updateHighlights();
    });
  }

  // ------------------------------------------
  // 3. RSVP FORM SUBMIT
  // ------------------------------------------
  const rsvpForm = document.querySelector("#rsvp form");
  if (rsvpForm) {
    rsvpForm.addEventListener("submit", (e) => {
      e.preventDefault();
      alert(
        "Terima kasih! Konfirmasi kehadiran RSVP Anda telah berhasil dikirim.",
      );
      rsvpForm.reset();
    });
  }

  // ------------------------------------------
  // 4. WEDDING WISHES
  // ------------------------------------------
  const wishForm = document.getElementById("wishes-form");
  const wishNameInput = document.getElementById("wish-name");
  const wishTextInput = document.getElementById("wish-text");
  const charCountSpan = document.getElementById("char-count");
  const wishesList = document.getElementById("wishes-list");

  if (wishTextInput && charCountSpan) {
    wishTextInput.addEventListener("input", function () {
      const remaining = 300 - this.value.length;
      charCountSpan.textContent = `Sisa huruf: ${remaining}`;
    });
  }

  if (wishForm && wishesList) {
    wishForm.addEventListener("submit", function (e) {
      e.preventDefault();

      const name = wishNameInput.value.trim();
      const text = wishTextInput.value.trim();

      if (name && text) {
        const initial = name.charAt(0).toUpperCase();

        const newWishCard = document.createElement("div");
        newWishCard.className =
          "bg-white/90 p-4 md:p-5 rounded-2xl shadow-sm flex items-start gap-4 transition-all duration-300";
        newWishCard.innerHTML = `
            <div class="w-10 h-10 md:w-12 md:h-12 bg-sage rounded-full flex items-center justify-center text-brown font-bold text-lg md:text-xl shrink-0">${initial}</div>
            <div class="flex-1 min-w-0">
                <p class="font-bold text-gray-800 text-sm md:text-base mb-1 truncate">${escapeHtml(name)}</p>
                <p class="text-gray-600 text-xs md:text-sm leading-relaxed">${escapeHtml(text)}</p>
            </div>
        `;

        wishesList.prepend(newWishCard);
        wishForm.reset();
        charCountSpan.textContent = "Sisa huruf: 300";
        wishesList.scrollTop = 0;
      }
    });
  }

  // Autoscroll pada daftar ucapan
  if (wishesList) {
    let isHovered = false;
    let isResetting = false;
    let scrollAccumulator = 0;
    const scrollSpeed = 0.25;

    wishesList.classList.add("transition-opacity", "duration-500");

    // Pause scroll saat disentuh / hover (Penting untuk versi mobile agar mudah dibaca)
    wishesList.addEventListener("mouseenter", () => (isHovered = true));
    wishesList.addEventListener("mouseleave", () => (isHovered = false));
    wishesList.addEventListener("touchstart", () => (isHovered = true), {
      passive: true,
    });
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

          if (
            Math.ceil(wishesList.scrollTop + wishesList.clientHeight) >=
            wishesList.scrollHeight - 2
          ) {
            isResetting = true;
            wishesList.style.opacity = "0";

            setTimeout(() => {
              wishesList.scrollTop = 0;
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

  function escapeHtml(str) {
    return str
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }
});

// ==========================================
// BACKGROUND MUSIC CONTROL (DENGAN IMG & PUTAR LAMBAT)
// ==========================================
const musicBtn = document.getElementById("music-btn");
const bgMusic = document.getElementById("bg-music");
const musicIcon = document.getElementById("music-icon");

if (musicBtn && bgMusic) {
  let isPlaying = false;

  function toggleMusic() {
    if (isPlaying) {
      bgMusic.pause();
      musicBtn.classList.remove("bg-brown");
      musicBtn.classList.add("bg-white/90");
      if (musicIcon) musicIcon.classList.remove("putar-lambat"); // Hapus kelas putar lambat
      isPlaying = false;
    } else {
      bgMusic
        .play()
        .then(() => {
          musicBtn.classList.add("bg-brown");
          musicBtn.classList.remove("bg-white/90");
          if (musicIcon) musicIcon.classList.add("putar-lambat"); // Tambahkan kelas putar lambat
          isPlaying = true;
        })
        .catch((error) => {
          console.log("Autoplay dicegah oleh browser:", error);
        });
    }
  }

  musicBtn.addEventListener("click", (e) => {
    e.stopPropagation();
    toggleMusic();
  });

  const openInvitationBtn = document.querySelector('a[href="#greeting"]');
  if (openInvitationBtn) {
    openInvitationBtn.addEventListener("click", () => {
      if (!isPlaying) {
        toggleMusic();
      }
    });
  }
}

// ==========================================
// BUKA UNDANGAN & KUNCI SCROLL COVER
// ==========================================
document.addEventListener("DOMContentLoaded", () => {
  const btnBuka = document.getElementById("btn-buka");
  const coverSection = document.getElementById("cover");

  // 1. Kunci scroll body di awal
  document.body.style.overflow = "hidden";

  if (btnBuka && coverSection) {
    btnBuka.addEventListener("click", () => {
      // 2. Aktifkan kembali scroll pada body
      document.body.style.overflow = "auto";

      // 3. Hilangkan cover dengan efek transisi halus
      coverSection.style.opacity = "0";
      coverSection.style.visibility = "hidden";

      // Hapus elemen dari layout setelah animasi selesai
      setTimeout(() => {
        coverSection.style.display = "none";
      }, 700);

      // 4. (Opsional) Nyalakan musik otomatis jika fungsi musik Anda aktif
      if (typeof toggleMusic === "function" && !isPlaying) {
        toggleMusic();
      }
    });
  }
});

// ==========================================
// BUKA UNDANGAN, SCROLL KE GREETING, & AUDIO
// ==========================================
document.addEventListener("DOMContentLoaded", () => {
  // 1. Paksa browser untuk kembali ke paling atas (0, 0) saat di-refresh
  if ("scrollRestoration" in history) {
    history.scrollRestoration = "manual";
  }
  window.scrollTo(0, 0);

  const btnBuka = document.getElementById("btn-buka");
  const bgMusic = document.getElementById("bg-music");
  const musicBtn = document.getElementById("music-btn");
  const musicIcon = document.getElementById("music-icon");

  // 2. Kunci scroll body di awal agar tidak bisa digulir manual
  document.body.style.overflow = "hidden";

  if (btnBuka) {
    btnBuka.addEventListener("click", () => {
      // 3. Buka kunci scroll body
      document.body.style.overflow = "auto";

      // 4. Putar musik secara otomatis saat tombol diklik
      if (bgMusic) {
        bgMusic
          .play()
          .then(() => {
            if (musicBtn) {
              musicBtn.classList.add("bg-brown");
              musicBtn.classList.remove("bg-white/90");
            }
            if (musicIcon) {
              musicIcon.classList.add("putar-lambat");
            }
            if (typeof isPlaying !== "undefined") isPlaying = true;
          })
          .catch((error) => {
            console.log("Autoplay dicegah:", error);
          });
      }

      // 5. Scroll mulus tepat ke section #greeting tanpa nyasar
      const greetingSection = document.querySelector("#greeting");
      if (greetingSection) {
        greetingSection.scrollIntoView({ behavior: "smooth" });
      }
    });
  }
});
