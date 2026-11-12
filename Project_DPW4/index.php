<?php
require __DIR__ . '/config.php';
$guest = null;
if (!empty($_GET['id'])) {
    $s = db()->prepare('SELECT * FROM guests WHERE code = ?');
    $s->execute([$_GET['id']]);
    $guest = $s->fetch() ?: null;
}
$guestName = $guest['name'] ?? c('cover.guest_default');
$wishes = db()->query('SELECT name, message FROM wishes ORDER BY id DESC LIMIT 100')->fetchAll();
$photos = [];
for ($i = 1; $i <= 10; $i++) if (img("gallery.g$i")) $photos[] = img("gallery.g$i");
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Undangan Pernikahan - <?= e(c('cover.couple')) ?></title>

    <!-- Import Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Great Vibes untuk tulisan sambung/nama, Cormorant Garamond untuk teks elegan, Montserrat untuk teks ui -->
    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400&family=Great+Vibes&family=Montserrat:wght@300;400;600&display=swap"
        rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        // Warna berdasarkan palet mockup
                        'sage': '#CCDEC1',    // Hijau pastel untuk background luar
                        'cream': '#F9F8F3',   // Putih tulang/cream untuk background arch & section
                        'brown': '#7C6757',   // Coklat untuk tombol dan teks penekanan
                        'dark': '#4A4A4A',     // Abu gelap untuk teks biasa
                        'black': '#302D29'
                    },
                    fontFamily: {
                        'script': ['"Great Vibes"', 'cursive'],
                        'serif': ['"Cormorant Garamond"', 'serif'],
                        'sans': ['"Montserrat"', 'sans-serif']
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-cream text-dark font-serif antialiased overflow-x-hidden relative">

    <!-- Navbar 3 Titik (Kanan Atas) -->
    <nav class="fixed top-4 right-4 z-50">
        <button id="menu-btn"
            class="p-2 bg-white/90 backdrop-blur-sm rounded-full shadow-md text-brown hover:bg-brown hover:text-sage transition-colors focus:outline-none">
            <!-- Icon 3 titik vertikal (kebab menu) -->
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 6.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 12.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 18.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5Z" />
            </svg>
        </button>

        <!-- Menu Dropdown -->
        <div id="menu-dropdown"
            class="hidden absolute right-0 mt-2 w-48 bg-white/95 backdrop-blur-md rounded-xl shadow-xl py-2 border border-sage/50 font-sans text-sm transform origin-top-right transition-all">
            <a href="#cover" class="block px-4 py-2 text-dark hover:bg-sage/40 hover:text-brown transition">Home</a>
            <a href="#greeting"
                class="block px-4 py-2 text-dark hover:bg-sage/40 hover:text-brown transition">Greeting</a>
            <a href="#mempelai" class="block px-4 py-2 text-dark hover:bg-sage/40 hover:text-brown transition"><?= e(c('mempelai.title')) ?></a>
            <a href="#countdown"
                class="block px-4 py-2 text-dark hover:bg-sage/40 hover:text-brown transition">Countdown</a>
            <a href="#story" class="block px-4 py-2 text-dark hover:bg-sage/40 hover:text-brown transition">Love
                Story</a>
            <a href="#gallery" class="block px-4 py-2 text-dark hover:bg-sage/40 hover:text-brown transition">
                Gallery</a>
            <a href="#location" class="block px-4 py-2 text-dark hover:bg-sage/40 hover:text-brown transition">Lokasi &
                Jadwal</a>
            <a href="#rsvp" class="block px-4 py-2 text-dark hover:bg-sage/40 hover:text-brown transition">RSVP &
                Wishes</a>
        </div>
    </nav>

    <!-- 1. SECTION COVER -->
    <section id="cover"
        class="min-h-screen flex flex-col justify-center items-center relative bg-cream p-4 md:p-8 bg-cover bg-center">
        <img src="assets/atas1.png" alt="top-arc" class="absolute top-0 left-0 w-full h-auto z-10 pointer-events-none">
        <img src="assets/halaman1-kanan.png" alt="kanan bawah"
            class="absolute bottom-0 right-0 h-[400px] w-auto z-10 pointer-events-none">
        <img src="assets/halaman1-kiri.png" alt="kiri bawah"
            class="absolute bottom-0 left-0 h-[400px] w-auto z-10 pointer-events-none">

        <!-- Bentuk Arch (Lengkungan) -->
        <div
            class="bg-sage p-10 mt-[120px] md:p-16 rounded-full text-center w-full max-w-md shadow-[inset_0_4px_12px_rgba(0,0,0,0.2)] relative mt-10">
            <p class="text-3xl mb-4 tracking-wide text-black"><?= nl2br(e(c('cover.intro'))) ?></p>
            <h1 class="pt-10 font-script text-6xl md:text-6xl text-brown mb-10 leading-tight"><?= e(c('cover.couple')) ?></h1>

            <div
                class="border border-dark/30 rounded-2xl p-5 mb-10 inline-block min-w-[200px] bg-white/50 backdrop-blur-sm">
                <p class="font-sans text-xs uppercase tracking-widest text-gray-500 mb-2"><?= e(c('cover.guest_prefix')) ?></p>
                <p class="font-bold text-xl border-b border-dark/20 pb-2 mb-2"><?= e($guestName) ?></p>
                <p class="font-sans text-xs"><?= e(c('cover.guest_suffix')) ?></p>
            </div>

            <div>
                <!-- Tombol ini diarahkan ke ID greeting -->
                <a href="#greeting"
                    class="inline-flex items-center justify-center gap-2 bg-brown text-white font-sans text-sm font-semibold tracking-wider px-8 py-3 rounded-full hover:bg-brown/80 transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                    <!-- Icon Amplop -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                        <path
                            d="M1.5 8.67v8.58a3 3 0 0 0 3 3h15a3 3 0 0 0 3-3V8.67l-8.928 5.493a3 3 0 0 1-3.144 0L1.5 8.67Z" />
                        <path
                            d="M22.5 6.908V6.75a3 3 0 0 0-3-3h-15a3 3 0 0 0-3 3v.158l9.714 5.978a1.5 1.5 0 0 0 1.572 0L22.5 6.908Z" />
                    </svg>
                    <?= e(c('cover.button')) ?>
                </a>
            </div>
        </div>
    </section>

    <!-- 2. SECTION GREETING -->
    <section id="greeting"
        class="relative min-h-screen flex flex-col justify-center items-center text-center p-8 bg-cream z-20">
        <img src="assets/atas2.png" alt="top-arc" class="absolute top-0 left-0 w-full h-auto z-20 pointer-events-none">
        <img src="assets/gold-kiri.png" alt="gold kiri"
            class="absolute bottom-0 -left-[5%] h-[60%] w-auto z-10 mix-blend-multiply pointer-events-none">
        <img src="assets/gold-kanan.png" alt="gold kanan"
            class="absolute bottom-0 -right-[5%] h-[60%] w-auto z-10 mix-blend-multiply pointer-events-none">
        <img src="assets/halaman2-bawahkiri.png" alt="kiri bawah"
            class="absolute bottom-0 left-0 h-[400px] w-auto z-10 pointer-events-none">
        <img src="assets/halaman2-bawahkanan.png" alt="kanan bawah"
            class="absolute bottom-0 right-0 h-[400px] w-auto z-10 pointer-events-none">
        <img src="assets/halaman2-ataskiri.png" alt="kiri atas"
            class="absolute top-0 left-0 h-[200px] w-auto z-30 pointer-events-none">
        <img src="assets/halaman2-ataskanan.png" alt="kanan atas"
            class="absolute top-0 right-0 h-[200px] w-auto z-30 pointer-events-none">


        <div class="absolute bottom-0 left-1/2 -translate-x-1/2 z-0 w-2/5 h-[90vh] rounded-t-full overflow-hidden">
            <img src="assets/pilar2.png" alt="pilar" class="w-full h-full object-cover block">
            <div class="absolute inset-0 rounded-t-full pointer-events-none shadow-[inset_0_8px_20px_rgba(0,0,0,0.15)]">
            </div>
        </div>
        <div class="relative z-10 translate-y-0">
            <h2 class="text-3xl md:text-4xl mb-4 italic"><?= e(c('greeting.title')) ?></h2>
            <h3 class="font-script text-6xl md:text-7xl text-brown mb-6"><?= e(c('cover.couple')) ?></h3>
            <p class="text-xl md:text-2xl mb-10 tracking-[0.3em] uppercase font-bold border-b border-brown pb-4"><?= e(c('greeting.subtitle')) ?></p>
            <p class="max-w-md mx-auto leading-relaxed font-bold text-lg text-gray-500"><?= e(c('greeting.text')) ?></p>
        </div>

    </section>

    <!-- 3. SECTION MEMPELAI -->
    <section id="mempelai">
        <div class="relative py-24 bg-sage/20 z-10">
            <img src="assets/atas2.png" alt="top-arc"
                class="absolute -top-[5%] left-0 w-full h-auto z-10 pointer-events-none">
            <img src="assets/halaman2-bawahkiri.png" alt="kiri bawah"
                class="absolute bottom-0 left-0 h-[400px] w-auto z-10 pointer-events-none">
            <img src="assets/halaman2-bawahkanan.png" alt="kanan bawah"
                class="absolute bottom-0 right-0 h-[400px] w-auto z-10 pointer-events-none">

            <div class="max-w-5xl mx-auto px-6 ">
                <h2 class="mt-[10%] font-script text-6xl text-center text-brown mb-5">Bride & Groom</h2>

                <div class="flex flex-col md:flex-row justify-center items-center gap-16 md:gap-8">
                    <!-- Mempelai Pria -->
                    <div class="text-center w-full md:w-5/12">
                        <div
                            class="relative w-[300px] h-[350px] mx-auto mb-8 rounded-t-full rounded-b-xl shadow-xl bg-sage overflow-hidden flex items-center justify-center p-4">
                            <div class="w-[82%] h-[90%] rounded-t-full rounded-b-[40%] overflow-hidden relative z-0">
                                <img src="<?= e(c('mempelai.groom_photo')) ?>" alt="Mufid Waladi"
                                    class="w-full h-full object-cover scale-[1.3] translate-y-4">
                            </div>
                            <img src="assets/bride-groom-border.png" alt="border"
                                class="absolute inset-0 w-full h-full object-cover pointer-events-none z-10">
                        </div>

                        <h3 class="text-4xl font-bold mb-3"><?= e(c('mempelai.groom_name')) ?></h3>
                        <p class="font-sans text-xs font-semibold uppercase tracking-widest text-gray-500 mb-1"><?= e(c('mempelai.groom_label')) ?></p>
                        <p class="text-xl italic font-semibold text-gray-500"><?= e(c('mempelai.groom_parents')) ?></p>
                    </div>

                    <!-- Pemisah Dan -->
                    <div class="font-script text-7xl text-brown md:w-2/12 text-center">&</div>

                    <!-- Mempelai Wanita -->
                    <div class="text-center w-full md:w-5/12">
                        <div
                            class="relative w-[300px] h-[350px] mx-auto mb-8 rounded-t-full rounded-b-xl shadow-xl bg-sage overflow-hidden flex items-center justify-center p-4">
                            <div class="w-[82%] h-[90%] rounded-t-full rounded-b-[40%] overflow-hidden relative z-0">
                                <img src="<?= e(c('mempelai.bride_photo')) ?>" alt="Velicia Regina Ananta"
                                    class="w-full h-full object-cover scale-[1.3]">
                            </div>
                            <img src="assets/bride-groom-border.png" alt="border"
                                class="absolute inset-0 w-full h-full object-cover pointer-events-none z-10">
                        </div>

                        <h3 class="text-4xl font-bold mb-3"><?= e(c('mempelai.bride_name')) ?></h3>
                        <p class="font-sans text-xs font-semibold uppercase tracking-widest text-gray-500 mb-1"><?= e(c('mempelai.bride_label')) ?></p>
                        <p class="text-xl italic font-semibold text-gray-500"><?= e(c('mempelai.bride_parents')) ?></p>
                    </div>
                </div>
            </div>
        </div>

    </section>

    <!-- 4. SECTION COUNTDOWN  -->
    <section id="countdown"
        class="relative min-h-screen flex flex-col justify-center items-center py-20 bg-cream overflow-hidden z-20">

        <!-- Ornamen Bunga Sisi Samping -->
        <img src="assets/halaman2-ataskiri.png" alt="kiri atas"
            class="absolute top-0 left-0 h-[220px] md:h-[300px] w-auto z-10 pointer-events-none opacity-80">
        <img src="assets/halaman2-ataskanan.png" alt="kanan atas"
            class="absolute top-0 right-0 h-[220px] md:h-[300px] w-auto z-10 pointer-events-none opacity-80">
        <img src="assets/halaman2-bawahkiri.png" alt="kiri bawah"
            class="absolute bottom-0 left-0 h-[300px] md:h-[400px] w-auto z-10 pointer-events-none">
        <img src="assets/halaman2-bawahkanan.png" alt="kanan bawah"
            class="absolute bottom-0 right-0 h-[300px] md:h-[400px] w-auto z-10 pointer-events-none">

        <div class="relative z-20 max-w-4xl mx-auto px-4 flex flex-col items-center">

            <!-- FRAME FOTO ARCH DENGAN KUPU-KUPU -->
            <div
                class="relative w-[280px] sm:w-[340px] md:w-[380px] h-[360px] sm:h-[430px] md:h-[470px] rounded-t-[180px] rounded-b-[40px] shadow-xl border-4 border-white/80 bg-sage/20 mb-10">

                <!-- Foto Sunset (Wajib overflow-hidden agar foto terpotong melengkung) -->
                <div class="w-full h-full rounded-t-[176px] rounded-b-[36px] overflow-hidden">
                    <?php if (img('countdown.photo')): ?><img src="<?= e(img('countdown.photo')) ?>" alt="Foto" class="w-full h-full object-cover"><?php endif; ?>
                </div>

                <!-- Gambar Kupu-kupu -->
                <img src="assets/kupu-kiri.png" alt="kupu-kiri"
                    class="absolute top-[60px] -left-[10px] md:top-[70px] md:-left-[20px] w-16 md:w-19 h-auto z-30 pointer-events-none drop-shadow-md -rotate-12">
                <img src="assets/kupu-kiri.png" alt="kupu-kanan"
                    class="absolute top-[160px] -right-[40px] md:top-[180px] md:-right-[35px] w-16 md:w-19 h-auto z-30 pointer-events-none rotate-20 scale-x-[-1] drop-shadow-md">
            </div>

            <!-- Tampilan Hitung Mundur -->
            <div class="grid grid-cols-4 gap-6 sm:gap-10 md:gap-16 text-center text-dark">
                <div class="flex flex-col items-center p-5 bg-white/80 rounded-2xl shadow-md shadow-dark/10">
                    <span class="text-3xl sm:text-4xl md:text-5xl font-serif font-normal mb-1 " id="cd-days">0</span>
                    <span class="font-sans text-xs sm:text-sm text-gray-500">Days</span>
                </div>
                <div class="flex flex-col items-center  p-5 bg-white/80 rounded-2xl shadow-md shadow-dark/10">
                    <span class="text-3xl sm:text-4xl md:text-5xl font-serif font-normal mb-1" id="cd-hours">0</span>
                    <span class="font-sans text-xs sm:text-sm text-gray-500">Hours</span>
                </div>
                <div class="flex flex-col items-center p-5 bg-white/80 rounded-2xl shadow-md shadow-dark/10">
                    <span class="text-3xl sm:text-4xl md:text-5xl font-serif font-normal mb-1" id="cd-mins">0</span>
                    <span class="font-sans text-xs sm:text-sm text-gray-500">Mins</span>
                </div>
                <div class="flex flex-col items-center p-5 bg-white/80 rounded-2xl shadow-md shadow-dark/10">
                    <span class="text-3xl sm:text-4xl md:text-5xl font-serif font-normal mb-1" id="cd-secs">0</span>
                    <span class="font-sans text-xs sm:text-sm text-gray-500">Secs</span>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. SECTION LOVE STORY -->
    <section id="story"
        class=" relative min-h-screen flex flex-col justify-center items-center py-20 bg-cream overflow-hidden">
        <img src="assets/ataskubah.png" alt="top-arc"
            class="absolute top-0 left-0 w-full h-auto z-10 pointer-events-none">
        <img src="assets/atas1.png" alt="top-arc" class="absolute top-0 left-0 w-full h-auto z-10 pointer-events-none">
        <img src="assets/halaman1-kanan.png" alt="kanan bawah"
            class="absolute bottom-0 right-0 h-[400px] w-auto z-10 pointer-events-none">
        <img src="assets/halaman1-kiri.png" alt="kiri bawah"
            class="absolute bottom-0 left-0 h-[400px] w-auto z-10 pointer-events-none">

        <div class="max-w-6xl mx-auto px-6">
            <h2 class="font-script text-6xl text-center text-brown mb-16 mt-28"><?= e(c('story.title')) ?></h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-7">
                <!-- Card 1 -->
                <div
                    class="bg-white p-8 rounded-2xl shadow-sm border-2 border-sage/80 text-center hover:shadow-md transition-shadow z-20 ">
                    <h3 class="text-3xl font-bold mb-4 text-brown border-b border-sage pb-4 inline-block"><?= e(c('story.t1')) ?></h3>
                    <p class="text-gray-600 text-lg font-bold leading-relaxed mt-4"><?= e(c('story.p1')) ?></p>
                </div>

                <!-- Card 2 -->
                <div
                    class="bg-white p-8 rounded-2xl shadow-sm border-2 border-sage/80 text-center hover:shadow-md transition-shadow z-20">
                    <h3 class="text-3xl font-bold mb-4 text-brown border-b border-sage pb-4 inline-block"><?= e(c('story.t2')) ?></h3>
                    <p class="text-gray-600 text-lg font-bold leading-relaxed mt-4"><?= e(c('story.p2')) ?></p>
                </div>

                <!-- Card 3 -->
                <div
                    class="bg-white p-8 rounded-2xl shadow-sm border-2 border-sage/80 text-center hover:shadow-md transition-shadow z-20">
                    <h3 class="text-3xl font-bold mb-4 text-brown border-b border-sage pb-4 inline-block"><?= e(c('story.t3')) ?></h3>
                    <p class="text-gray-600 text-lg font-bold leading-relaxed mt-4"><?= e(c('story.p3')) ?></p>
                </div>
            </div>
        </div>
    </section>

    <!-- 6. SECTION GALLERY  -->
    <style>
        /* Sembunyikan scrollbar bawaan */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>

    <section id="gallery"
        class="relative min-h-screen flex flex-col justify-center items-center py-20 bg-cream overflow-hidden">
        <img src="assets/halaman2-bawahkiri.png" alt="kiri bawah"
            class="absolute bottom-0 left-0 h-[200px] w-auto z-10 pointer-events-none">
        <img src="assets/halaman2-bawahkanan.png" alt="kanan bawah"
            class="absolute bottom-0 right-0 h-[200px] w-auto z-10 pointer-events-none">
        <!-- Dekorasi Background -->
        <img src="assets/atas2.png" alt="top border"
            class="absolute top-0 left-0 w-full h-auto z-10 pointer-events-none">

        <div class="relative z-20 w-full">
            <!-- Judul Gallery -->
            <div class="text-center mb-6 mt-24 px-6">
                <h2 class="font-script text-5xl md:text-6xl text-brown mb-2"><?= e(c('gallery.title')) ?></h2>
                <p class="font-sans text-xs uppercase tracking-[0.2em] md:tracking-[0.3em] text-gray-500"><?= e(c('gallery.subtitle')) ?></p>
            </div>

            <!-- Slider Wrapper -->
            <div class="relative w-full flex items-center justify-center py-6">

                <!-- Tombol Kiri -->
                <button id="btn-prev" type="button" aria-label="Previous"
                    class="absolute left-3 md:left-12 z-30 w-12 h-12 flex items-center justify-center bg-white/90 backdrop-blur-md rounded-2xl text-brown border border-sage/60 hover:bg-brown hover:text-white transition-all shadow-md active:scale-95 focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                        stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                    </svg>
                </button>

                <!-- Scroll Container (1 Baris) -->
                <div id="gallery-container"
                    class="flex gap-4 md:gap-6 overflow-x-auto snap-x snap-mandatory no-scrollbar w-full py-8 items-center ">

                    <?php foreach ($photos as $p): ?>
                    <div class="gallery-item snap-center shrink-0 w-[180px] md:w-[220px] h-[260px] md:h-[300px] rounded-[2rem] overflow-hidden bg-sage/30 shadow-md transition-all duration-300 ease-out"><img src="<?= e($p) ?>" alt="Gallery" class="w-full h-full object-cover pointer-events-none"></div>
                    <?php endforeach; ?>

                </div>

                <!-- Tombol Kanan -->
                <button id="btn-next" type="button" aria-label="Next"
                    class="absolute right-3 md:right-12 z-30 w-12 h-12 flex items-center justify-center bg-white/90 backdrop-blur-md rounded-2xl text-brown border border-sage/60 hover:bg-brown hover:text-white transition-all shadow-md active:scale-95 focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                        stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                </button>

            </div>
        </div>
    </section>

    <!-- SECTION LOKASI -->
    <section id="location" class="relative min-h-screen flex items-center justify-center py-16 bg-cream px-4 md:px-12">
        <img src="assets/halaman2-bawahkanan.png" alt="kiri bawah"
            class="absolute top-0 left-0 h-[200px] md:h-[200px] w-auto z-10 pointer-events-none opacity-80 rotate-180">
        <img src="assets/halaman2-bawahkiri.png" alt="kanan bawah"
            class="absolute top-0 right-0 h-[200px] md:h-[200px] w-auto z-10 pointer-events-none opacity-80 rotate-180">
        <img src="assets/gold-kiri.png" alt="gold kiri"
            class="absolute top-30 -left-[11%] h-[60%] w-auto z-10 mix-blend-multiply pointer-events-none">
        <img src="assets/gold-kanan.png" alt="gold kanan"
            class="absolute top-30 -right-[11%] h-[60%] w-auto z-10 mix-blend-multiply pointer-events-none">
        <!-- <img src="assets/halaman2-bawahkiri.png" alt="kiri bawah"
            class="absolute bottom-0 left-0 h-[400px] w-auto z-10 pointer-events-none">
        <img src="assets/halaman2-bawahkanan.png" alt="kanan bawah"
            class="absolute bottom-0 right-0 h-[400px] w-auto z-10 pointer-events-none"> -->

        <!-- Frame Utama sage -->
        <div class="relative w-full max-w-[1500px] bg-sage/40 rounded-3xl p-8 md:p-12 shadow-sm overflow-hidden">
            <img src="assets/halaman2-ataskiri.png" alt="dekorasi-kiri"
                class="absolute top-0 left-0 w-32 md:w-48 pointer-events-none z-0">
            <img src="assets/halaman2-ataskanan.png" alt="dekorasi-kanan"
                class="absolute top-0 right-0 w-32 md:w-48 pointer-events-none z-0">
            <img src="assets/halaman2-bawahkiri.png" alt="dekorasi-bawah-kiri"
                class="absolute bottom-0 left-0 w-32 md:w-48 pointer-events-none z-0">
            <img src="assets/halaman2-bawahkanan.png" alt="dekorasi-bawah-kanan"
                class="absolute bottom-0 right-0 w-32 md:w-48 pointer-events-none z-0">

            <!-- Konten Teks & Card -->
            <div class="relative z-10 flex flex-col items-center text-center">
                <h2 class="font-script text-5xl md:text-6xl text-brown mb-4"><?= e(c('location.title')) ?></h2>
                <p class="max-w-xl font-bold md:text-base text-gray-700 mb-6"><?= e(c('location.intro')) ?></p>

                <div class="bg-white/80backdrop-blur-sm px-6 py-2 rounded-full font-bold text-gray-800 shadow-sm mb-8"><?= e(c('location.date_text')) ?></div>

                <p class="max-w-lg font-bold text-gray-600 mb-8"><?= e(c('location.note')) ?></p>

                <!-- Grid 2 Kolom untuk Kartu Acara & Detail -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full h-[320px] max-w-2xl rounded-t-2xl">

                    <!-- Card 1: Akad Nikah -->
                    <div
                        class="relative bg-cream pt-24 pb-8 px-6 rounded-t-[300px] rounded-b-2xl shadow-md overflow-hidden flex flex-col items-center text-center">
                        <img src="assets/location-atas.png" alt="dekorasi atas"
                            class="absolute top-0 left-0 w-full h-auto pointer-events-none z-10">
                        <img src="assets/location-bunga.png" alt="bunga"
                            class="absolute bottom-0 left-0 w-full h-auto pointer-events-none z-10 ">

                        <!-- Gradasi HijauBagian Bawah -->
                        <div
                            class="absolute bottom-0 left-0 w-full h-10 bg-gradient-to-t from-[#B5C99A]/50 via-[#B5C99A]/15 to-transparent pointer-events-none z-10">
                        </div>

                        <div
                            class="absolute inset-0 rounded-t-[200px] rounded-b-2xl pointer-events-none shadow-[inset_0_10px_25px_rgba(138,154,134,0.3)] z-20">
                        </div>

                        <div class="relative z-20 pt-6">
                            <h3 class=" font-script text-3xl text-brown mb-2"><?= e(c('location.akad_title')) ?></h3>
                            <p class="mb-2 font-bold text-gray-600"><?= e(c('location.akad_day')) ?></p>
                            <p class="text-3xl font-script "><?= e(c('location.akad_date')) ?></p>
                            <p class=" font-bold text-gray-600"><?= e(c('location.akad_time')) ?></p>
                        </div>
                    </div>

                    <!-- Card 2: Resepsi  -->
                    <div
                        class="relative bg-cream pt-24 pb-8 px-6 rounded-t-[200px] rounded-b-2xl shadow-md overflow-hidden flex flex-col items-center text-center">
                        <img src="assets/location-atas.png" alt="dekorasi atas"
                            class="absolute top-0 left-0 w-full h-auto pointer-events-none z-10">
                        <img src="assets/location-bunga.png" alt="bunga"
                            class="absolute bottom-0 left-0 w-full h-auto pointer-events-none z-10 ">

                        <!-- Gradasi HijauBagian Bawah -->
                        <div
                            class="absolute bottom-0 left-0 w-full h-10 bg-gradient-to-t from-[#B5C99A]/50 via-[#B5C99A]/15 to-transparent pointer-events-none z-10">
                        </div>

                        <div
                            class="absolute inset-0 rounded-t-[200px] rounded-b-2xl pointer-events-none shadow-[inset_0_10px_25px_rgba(138,154,134,0.3)] z-20">
                        </div>

                        <div class="relative z-20 pt-6">
                            <h3 class=" font-script text-3xl text-brown mb-2"><?= e(c('location.resepsi_title')) ?></h3>
                            <p class="mb-2 font-bold text-gray-600"><?= e(c('location.resepsi_day')) ?></p>
                            <p class="text-3xl font-script "><?= e(c('location.resepsi_date')) ?></p>
                            <p class=" font-bold text-gray-600"><?= e(c('location.resepsi_time')) ?></p>
                        </div>
                    </div>

                </div>

                <!-- Detail Hotel Pangeran Pekanbaru & Dress Code -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full max-w-2xl mt-6">

                    <!-- Dress Code -->
                    <div class="bg-cream p-6 rounded-2xl shadow-md flex flex-col items-center">
                        <h4 class=" uppercase font-bold tracking-widest text-gray-700 border-b pb-1 mb-3">Dress
                            Code</h4>
                        <p class="font-bold text-gray-700"><?= e(c('location.dress_text')) ?></p>
                        <div class="flex gap-2 mt-4">
                            <span class="w-4 h-4 rounded-full bg-[#e0d0b4]"></span>
                            <span class="w-4 h-4 rounded-full bg-[#D4C3B3]"></span>
                            <span class="w-4 h-4 rounded-full bg-[#B5C99A]"></span>
                        </div>
                        <div class="flex gap-2 mt-2">
                            <span class="w-4 h-4 rounded-full bg-[#E8C8B8]"></span>
                            <span class="w-4 h-4 rounded-full bg-[#D8A47F]"></span>
                            <span class="w-4 h-4 rounded-full bg-[#94A388]"></span>
                        </div>
                        <div class="flex gap-2 mt-2">
                            <span class="w-4 h-4 rounded-full bg-[#D5B9B2]"></span>
                            <span class="w-4 h-4 rounded-full bg-[#C4A482]"></span>
                            <span class="w-4 h-4 rounded-full bg-[#6B705C]"></span>
                        </div>
                    </div>

                    <!-- Hotel Pangeran Pekanbaru -->
                    <div class="bg-cream p-6 rounded-2xl shadow-md flex flex-col items-center text-center">
                        <img src="assets/icon-location.png" alt="icon" class="w-[26px] h-auto mb-2">
                        <h4 class="font-bold text-gray-700 mb-1"><?= e(c('location.venue_name')) ?></h4>
                        <p class="font-bold text-gray-700 mb-4"><?= e(c('location.venue_address')) ?></p>
                        <a href="<?= e(c('location.map_url')) ?>"
                            target="_blank"
                            class="bg-[#8A9A86] text-white font-bold px-6 py-2 rounded-lg shadow hover:bg-[#788874] transition"><?= e(c('location.map_button')) ?></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 7. SECTION RSVP -->
    <section id="rsvp"
        class="relative min-h-screen flex flex-col justify-center items-center py-24 bg-cream bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI4MCIgaGVpZ2h0PSI4MCI+PHBhdGggZD0iTTAgMGg4MHY4MEgwem00MCA0MGMxMS4wNDYgMCAyMC04Ljk1NCAyMC0yMFM1MS4wNDYgMCA0MCAwIDIwIDguOTU0IDIwIDIwczguOTU0IDIwIDIwIDIwem0wIDEwYzE2LjU2OCAwIDMwLTEzLjQzMiAzMC0zMFM1Ni41NjggMCA0MCAwIDEwIDEzLjQzMiAxMCAzMHMxMy40MzIgMzAgMzAgMzB6bTAgMTBjMjIuMDkxIDAgNDAtMTcuOTA5IDQwLTQwUzYyLjA5MSAwIDQwIDAgMCAxNy45MDkgMCA0MHMxNy45MDkgNDAgNDAgNDAtIiBmaWxsPSIjZDFkOWNhIiBmaWxsLW9wYWNpdHk9Ii4wNSIgZmlsbC1ydWxlPSJldmVub2RkIi8+PC9zdmc=')]">
        <img src="assets/atas2.png" alt="top-arc" class="absolute top-0 left-0 w-full h-auto z-20 pointer-events-none">
        <img src="assets/halaman1-kanan.png" alt="kanan bawah"
            class="absolute bottom-0 right-0 h-[450px] w-auto z-10 pointer-events-none">
        <img src="assets/halaman1-kiri.png" alt="kiri bawah"
            class="absolute bottom-0 left-0 h-[450px] w-auto z-10 pointer-events-none">

        <div class="max-w-xl mx-auto mt-[5%] px-6 w-full">
            <div
                class="bg-sage p-10 md:p-14 rounded-full text-center shadow-[inset_0_4px_12px_rgba(0,0,0,0.15)] relative">
                <h2 class="font-script text-5xl md:text-6xl text-brown mb-8"><?= e(c('rsvp.title')) ?></h2>
                <form id="rsvp-form" class="flex flex-col gap-4 font-sans text-left">
                    <input type="text" name="name" required maxlength="100" value="<?= e($guest['name'] ?? '') ?>" placeholder="Nama"
                        class="p-4 border border-sage/50 rounded-xl bg-white/70 focus:outline-none focus:border-brown focus:ring-1 focus:ring-brown transition shadow-sm">
                    <input type="email" name="email" maxlength="100" value="<?= e($guest['email'] ?? '') ?>" placeholder="Email"
                        class="p-4 border border-sage/50 rounded-xl bg-white/70 focus:outline-none focus:border-brown focus:ring-1 focus:ring-brown transition shadow-sm">
                    <input type="tel" name="phone" maxlength="30" value="<?= e($guest['phone'] ?? '') ?>" placeholder="Nomor Telfon"
                        class="p-4 border border-sage/50 rounded-xl bg-white/70 focus:outline-none focus:border-brown focus:ring-1 focus:ring-brown transition shadow-sm">

                    <div class="relative">
                        <select name="status" required class="w-full p-4 border border-sage/50 rounded-xl bg-white/70 focus:outline-none focus:border-brown focus:ring-1 focus:ring-brown appearance-none text-gray-600 transition shadow-sm">
                            <option value="" disabled selected>Konfirmasi Kehadiran</option>
                            <option value="hadir">Hadir</option>
                            <option value="tidak_hadir">Tidak Hadir</option>
                        </select>
                        <div
                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>

                    <input type="number" name="pax" min="1" max="20" value="<?= (int)($guest['pax'] ?? 1) ?>" placeholder="Jumlah Tamu"
                        class="p-4 border border-sage/50 rounded-xl bg-white/70 focus:outline-none focus:border-brown focus:ring-1 focus:ring-brown transition shadow-sm">

                    <div class="text-center mt-4">
                        <button type="submit"
                            class="bg-brown text-white px-10 py-3.5 rounded-xl hover:bg-brown/80 transition-colors font-semibold tracking-wider uppercase shadow-md">
                            Kirim
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- 8. SECTION WEDDING WISHES -->
    <style>
        /* Sembunyikan scrollbar bawaan */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>

    <!-- 8. SECTION WEDDING WISHES -->
    <!-- 8. SECTION WEDDING WISHES -->
    <section id="wishes"
        class="relative min-h-screen flex flex-col justify-center items-center py-24 bg-cream bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI4MCIgaGVpZ2h0PSI4MCI+PHBhdGggZD0iTTAgMGg4MHY4MEgwem00MCA0MGMxMS4wNDYgMCAyMC04Ljk1NCAyMC0yMFM1MS4wNDYgMCA4MCAwIDIwIDguOTU0IDIwIDIwczguOTU0IDIwIDIwIDIwem0wIDEwYzE2LjU2OCAwIDMwLTEzLjQzMiAzMC0zMFM1Ni41NjggMCA0MCAwIDEwIDEzLjQzMiAxMCAzMHMxMy40MzIgMzAgMzAgMzB6bTAgMTBjMjIuMDkxIDAgNDAtMTcuOTA5IDQwLTQwUzYyLjA5MSAwIDQwIDAgMCAxNy45MDkgMCA0MHMxNy45MDkgNDAgNDAgNDAtIiBmaWxsPSIjZDFkOWNhIiBmaWxsLW9wYWNpdHk9Ii4wNSIgZmlsbC1ydWxlPSJldmVub2RkIi8+PC9zdmc+')]">
        <img src="assets/halaman2-ataskiri.png" alt="kiri atas"
            class="absolute top-0 left-0 h-[200px] w-auto z-30 pointer-events-none">
        <img src="assets/halaman2-ataskanan.png" alt="kanan atas"
            class="absolute top-0 right-0 h-[200px] w-auto z-30 pointer-events-none">
        <img src="assets/halaman2-bawahkanan.png" alt="kanan bawah"
            class="absolute top-0 left-0 -rotate-180 h-[300px] w-auto z-10 pointer-events-none">
        <img src="assets/halaman2-bawahkiri.png" alt="kiri bawah"
            class="absolute top-0 right-0 -rotate-180 h-[300px] w-auto z-10 pointer-events-none">

        <div class="max-w-5xl mx-auto px-4 sm:px-8 w-full relative z-20">
            <div class="bg-sage/20 p-6 md:p-10 rounded-3xl shadow-sm border border-sage/40 flex flex-col">
                <h2 class="font-script text-5xl md:text-6xl text-center mb-8 text-brown"><?= e(c('rsvp.wishes_title')) ?></h2>

                <!-- Form Input Ucapan -->
                <form id="wishes-form" class="flex flex-col gap-4 font-sans mb-8 max-w-2xl mx-auto w-full">
                    <input type="text" id="wish-name" placeholder="Nama" required
                        class="p-4 border border-white rounded-xl bg-white focus:outline-none focus:border-brown focus:ring-1 focus:ring-brown transition shadow-sm">
                    <textarea id="wish-text" rows="3" placeholder="Tuliskan ucapan anda" maxlength="300" required
                        class="p-4 border border-white rounded-xl bg-white focus:outline-none focus:border-brown focus:ring-1 focus:ring-brown transition shadow-sm resize-none"></textarea>

                    <div class="flex justify-between items-center text-sm text-gray-500 mt-2">
                        <span id="char-count">Sisa huruf: 300</span>
                        <button type="submit"
                            class="bg-brown text-white px-8 py-3 rounded-full hover:bg-brown/80 transition shadow-md font-semibold tracking-wide cursor-pointer">
                            Kirim
                        </button>
                    </div>
                </form>

                <!-- Grid Ucapan 2 Kolom (Card Lebih Besar, Batas Tampil ~3 Baris) -->
                <div id="wishes-list"
                    class="grid grid-cols-1 md:grid-cols-2 gap-4 overflow-y-auto max-h-[350px] p-2 font-sans no-scrollbar">
<?php foreach ($wishes as $w): ?>
<div class="bg-white p-5 rounded-2xl shadow-sm flex items-start gap-4"><div class="w-12 h-12 bg-sage rounded-full flex items-center justify-center text-brown font-bold text-xl shrink-0"><?= e(mb_strtoupper(mb_substr($w['name'], 0, 1))) ?></div><div class="flex-1 min-w-0"><p class="font-bold text-gray-800 text-base mb-1 truncate"><?= e($w['name']) ?></p><p class="text-gray-600 text-sm leading-relaxed"><?= e($w['message']) ?></p></div></div>
<?php endforeach; ?>

                </div>
            </div>
        </div>
    </section>

    <!-- Footer Simple -->
    <footer class="bg-brown text-white text-center py-6 font-sans text-sm">
        <p>&copy; 2026 Wedding Invitation of <?= e(c('cover.couple')) ?>. All rights reserved.</p>
    </footer>

    <!-- Script Navbar 3 Titik -->
    <script>window.GUEST_CODE = <?= json_encode($guest['code'] ?? '') ?>; window.EVENT_DATE = <?= json_encode(c('countdown.date')) ?>;</script>
    <script src="script.js"></script>
</body>

</html>