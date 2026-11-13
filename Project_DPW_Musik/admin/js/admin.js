// Tab Edit Landing Page + preview
const frame = document.getElementById("preview");
document.querySelectorAll(".tab").forEach((btn) => {
  btn.addEventListener("click", () => {
    document.querySelectorAll(".tab").forEach((b) => b.classList.toggle("on", b === btn));
    document.querySelectorAll(".panel").forEach((p) => (p.hidden = p.dataset.tab !== btn.dataset.tab));
    try {
      frame.contentWindow.location.hash = btn.dataset.anchor;
    } catch (e) {}
  });
});

// Simpan section
document.querySelectorAll(".save-form").forEach((form) => {
  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    const label = form.querySelector(".saved");
    const res = await fetch("save.php", { method: "POST", body: new FormData(form) });
    const j = await res.json().catch(() => ({}));
    label.textContent = res.ok ? "Tersimpan ✓" : j.error || "Gagal menyimpan";
    label.style.color = res.ok ? "#4f7a4f" : "#b3453f";
    if (res.ok) {
      const anchor = document.querySelector(".tab.on").dataset.anchor;
      frame.src = "../index.php?preview=1&t=" + Date.now() + "#" + anchor;
    }
    setTimeout(() => (label.textContent = ""), 3000);
  });
});

// Upload gambar
document.querySelectorAll(".upload").forEach((input) => {
  input.addEventListener("change", async () => {
    const row = input.closest(".img-row");
    const data = new FormData();
    data.append("file", input.files[0]);
    data.append("csrf", window.CSRF);
    const res = await fetch("upload.php", { method: "POST", body: data });
    const j = await res.json().catch(() => ({}));
    if (!res.ok) return alert(j.error || "Upload gagal");
    row.querySelector("input[type=hidden]").value = j.path;
    const t = row.querySelector(".thumb");
    if (t) {
      t.src = "../" + j.path;
      t.style.visibility = "visible";
    }
    const a = row.querySelector(".audio-preview");
    if (a) a.src = "../" + j.path;
  });
});

// Dialog umum
document.querySelectorAll("dialog [data-close]").forEach((b) =>
  b.addEventListener("click", (e) => {
    e.preventDefault();
    b.closest("dialog").close();
  }),
);

// Data tamu: tambah / edit
const gd = document.getElementById("guest-dialog");
if (gd) {
  const fill = (d) => {
    document.getElementById("gd-title").textContent = d.id ? "Edit Tamu" : "Tambah Tamu";
    document.getElementById("gd-id").value = d.id || "";
    document.getElementById("gd-name").value = d.name || "";
    document.getElementById("gd-phone").value = d.phone || "";
    document.getElementById("gd-pax").value = d.pax || 1;
    document.getElementById("gd-status").value = d.status || "menunggu";
    gd.showModal();
  };
  document.getElementById("add-guest").addEventListener("click", () => fill({}));
  document.querySelectorAll(".icon.edit").forEach((b) => b.addEventListener("click", () => fill(b.dataset)));
}

// Data tamu: share link
const sd = document.getElementById("share-dialog");
if (sd) {
  let waUrl = "";
  document.querySelectorAll(".icon.share").forEach((b) =>
    b.addEventListener("click", () => {
      const { name, link, phone } = b.dataset;
      const text = window.WA_TEMPLATE.replace("{nama}", name)
        .replace("{pasangan}", window.COUPLE)
        .replace("{link}", link);
      document.getElementById("sd-name").textContent = name;
      document.getElementById("sd-link").value = link;
      const base = phone ? "https://wa.me/" + phone.replace(/\D/g, "") : "https://wa.me/";
      document.getElementById("sd-wa").href = base + "?text=" + encodeURIComponent(text);
      sd.showModal();
    }),
  );
  document.getElementById("sd-copy").addEventListener("click", async (e) => {
    const input = document.getElementById("sd-link");
    input.select();
    try {
      await navigator.clipboard.writeText(input.value);
    } catch (err) {
      document.execCommand("copy");
    }
    e.target.textContent = "Tersalin ✓";
    setTimeout(() => (e.target.textContent = "Salin Link"), 1500);
  });
}
