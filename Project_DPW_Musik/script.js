// Satu file untuk undangan: cover, musik, galeri, RSVP, ucapan, countdown

// ==========================================
// BUKA UNDANGAN, KUNCI SCROLL COVER & MUSIK
// ==========================================
const isPreview = new URLSearchParams(window.location.search).has("preview");
const coverSection = document.getElementById("cover");
const btnBuka = document.getElementById("btn-buka");
const bgMusic = document.getElementById("bg-music");
const musicBtn = document.getElementById("music-btn");
const musicIcon = document.getElementById("music-icon");
let isPlaying = false;

function setMusicUi(playing) {
  isPlaying = playing;
  if (musicBtn) {
    musicBtn.classList.toggle("bg-brown", playing);
    musicBtn.classList.toggle("bg-white/90", !playing);
  }
  if (musicIcon) musicIcon.classList.toggle("putar-lambat", playing);
}

function toggleMusic() {
  if (!bgMusic) return;
  if (isPlaying) {
    bgMusic.pause();
    setMusicUi(false);
  } else {
    bgMusic.play().then(() => setMusicUi(true)).catch(() => {});
  }
}

if (musicBtn) {
  musicBtn.addEventListener("click", (e) => {
    e.stopPropagation();
    toggleMusic();
  });
}

if (coverSection && btnBuka) {
  if (isPreview) {
    // Di preview dashboard cover tidak menutupi layar agar semua bagian terlihat
    coverSection.classList.remove("fixed", "inset-0", "z-50");
    coverSection.classList.add("relative", "min-h-screen");
  } else {
    if ("scrollRestoration" in history) history.scrollRestoration = "manual";
    window.scrollTo(0, 0);
    document.body.style.overflow = "hidden";

    btnBuka.addEventListener("click", () => {
      document.body.style.overflow = "auto";
      coverSection.style.opacity = "0";
      coverSection.style.visibility = "hidden";
      setTimeout(() => (coverSection.style.display = "none"), 700);

      if (!isPlaying) toggleMusic();

      const greeting = document.getElementById("greeting");
      if (greeting) greeting.scrollIntoView({ behavior: "smooth" });
    });
  }
}

// ==========================================
// NAVBAR DROPDOWN (MENU 3 TITIK)
// ==========================================
const menuBtn = document.getElementById("menu-btn");
const menuDropdown = document.getElementById("menu-dropdown");

if (menuBtn && menuDropdown) {
  menuBtn.addEventListener("click", (e) => {
    e.stopPropagation();
    menuDropdown.classList.toggle("hidden");
  });

  document.addEventListener("click", (e) => {
    if (!menuBtn.contains(e.target) && !menuDropdown.contains(e.target)) {
      menuDropdown.classList.add("hidden");
    }
  });

  menuDropdown.querySelectorAll("a").forEach((link) => {
    link.addEventListener("click", () => menuDropdown.classList.add("hidden"));
  });
}

document.addEventListener("DOMContentLoaded", () => {
  // ------------------------------------------
  // GALERI INFINITE CAROUSEL
  // ------------------------------------------
  const container = document.getElementById("gallery-container");
  const btnPrev = document.getElementById("btn-prev");
  const btnNext = document.getElementById("btn-next");

  if (container && btnPrev && btnNext) {
    const originalItems = Array.from(
      container.querySelectorAll(".gallery-item"),
    );
    const N = originalItems.length;

    originalItems.forEach((item) => {
      container.prepend(item.cloneNode(true));
      container.appendChild(item.cloneNode(true));
    });

    const allItems = Array.from(container.querySelectorAll(".gallery-item"));

    const updatePadding = () => {
      if (!allItems[0]) return;
      const pad = window.innerWidth / 2 - allItems[0].offsetWidth / 2;
      container.style.paddingLeft = `${Math.max(0, pad)}px`;
      container.style.paddingRight = `${Math.max(0, pad)}px`;
    };
    updatePadding();

    let currentIndex = N;

    // Hanya geser galeri secara horizontal (tidak menarik halaman ke tengah)
    const scrollToIndex = (index, smooth = true) => {
      if (index < 0 || index >= allItems.length) return;
      const item = allItems[index];
      const left =
        item.offsetLeft - container.clientWidth / 2 + item.offsetWidth / 2;
      container.scrollTo({ left, behavior: smooth ? "smooth" : "auto" });
    };

    const updateHighlights = () => {
      const containerCenter =
        container.getBoundingClientRect().left + container.clientWidth / 2;
      let minDistance = Infinity;
      let activeIdx = currentIndex;

      allItems.forEach((item, idx) => {
        const rect = item.getBoundingClientRect();
        const distance = Math.abs(containerCenter - (rect.left + rect.width / 2));
        if (distance < minDistance) {
          minDistance = distance;
          activeIdx = idx;
        }
      });

      currentIndex = activeIdx;

      const on = ["scale-110", "z-10", "shadow-2xl", "ring-4", "ring-brown/40", "opacity-100"];
      allItems.forEach((item, idx) => {
        if (idx === activeIdx) {
          item.classList.add(...on);
          item.classList.remove("scale-90", "opacity-50");
        } else {
          item.classList.remove(...on);
          item.classList.add("scale-90", "opacity-50");
        }
      });
    };

    setTimeout(() => {
      scrollToIndex(currentIndex, false);
      updateHighlights();
    }, 100);

    let scrollTimer;
    let isJumping = false;

    container.addEventListener("scroll", () => {
      updateHighlights();
      clearTimeout(scrollTimer);
      scrollTimer = setTimeout(() => {
        if (isJumping) return;
        if (currentIndex < N || currentIndex >= N * 2) {
          isJumping = true;
          currentIndex += currentIndex < N ? N : -N;
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

    window.addEventListener("resize", () => {
      updatePadding();
      scrollToIndex(currentIndex, false);
      updateHighlights();
    });
  }

  // ------------------------------------------
  // HITUNG MUNDUR (tanggal dari dashboard)
  // ------------------------------------------
  const cdDays = document.getElementById("cd-days");
  if (cdDays && window.EVENT_DATE) {
    const target = new Date(window.EVENT_DATE).getTime();
    const set = (id, v) => (document.getElementById(id).textContent = String(v));
    const tick = () => {
      const diff = Math.max(0, target - Date.now());
      set("cd-days", Math.floor(diff / 86400000));
      set("cd-hours", Math.floor((diff % 86400000) / 3600000));
      set("cd-mins", Math.floor((diff % 3600000) / 60000));
      set("cd-secs", Math.floor((diff % 60000) / 1000));
    };
    tick();
    setInterval(tick, 1000);
  }

  // ------------------------------------------
  // RSVP (disimpan ke database)
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
  // WEDDING WISHES (disimpan ke database)
  // ------------------------------------------
  const wishForm = document.getElementById("wishes-form");
  const wishNameInput = document.getElementById("wish-name");
  const wishTextInput = document.getElementById("wish-text");
  const charCountSpan = document.getElementById("char-count");
  const wishesList = document.getElementById("wishes-list");

  if (wishTextInput && charCountSpan) {
    wishTextInput.addEventListener("input", function () {
      charCountSpan.textContent = `Sisa huruf: ${300 - this.value.length}`;
    });
  }

  if (wishForm && wishesList) {
    wishForm.addEventListener("submit", async function (e) {
      e.preventDefault();

      const name = wishNameInput.value.trim();
      const text = wishTextInput.value.trim();
      if (!name || !text) return;

      const data = new FormData();
      data.append("name", name);
      data.append("message", text);
      data.append("code", window.GUEST_CODE || "");
      const res = await fetch("api/wish.php", { method: "POST", body: data });
      if (!res.ok) {
        alert("Ucapan gagal dikirim.");
        return;
      }

      const card = document.createElement("div");
      card.className =
        "bg-white/90 p-4 md:p-5 rounded-2xl shadow-sm flex items-start gap-4 transition-all duration-300";
      card.innerHTML = `
        <div class="w-10 h-10 md:w-12 md:h-12 bg-sage rounded-full flex items-center justify-center text-brown font-bold text-lg md:text-xl shrink-0">${escapeHtml(name.charAt(0).toUpperCase())}</div>
        <div class="flex-1 min-w-0">
          <p class="font-bold text-gray-800 text-sm md:text-base mb-1 truncate">${escapeHtml(name)}</p>
          <p class="text-gray-600 text-xs md:text-sm leading-relaxed">${escapeHtml(text)}</p>
        </div>`;
      wishesList.prepend(card);
      wishForm.reset();
      charCountSpan.textContent = "Sisa huruf: 300";
      wishesList.scrollTop = 0;
    });
  }

  // Autoscroll pelan + fade-out saat loop (pause saat disentuh / hover)
  if (wishesList) {
    let isHovered = false;
    let isResetting = false;
    let acc = 0;
    const speed = 0.25;

    wishesList.classList.add("transition-opacity", "duration-500");
    wishesList.addEventListener("mouseenter", () => (isHovered = true));
    wishesList.addEventListener("mouseleave", () => (isHovered = false));
    wishesList.addEventListener("touchstart", () => (isHovered = true), {
      passive: true,
    });
    wishesList.addEventListener("touchend", () => (isHovered = false));

    const autoScroll = () => {
      if (
        !isHovered &&
        !isResetting &&
        wishesList.scrollHeight > wishesList.clientHeight
      ) {
        acc += speed;
        if (acc >= 1) {
          const step = Math.floor(acc);
          acc -= step;
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
      requestAnimationFrame(autoScroll);
    };
    requestAnimationFrame(autoScroll);
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
