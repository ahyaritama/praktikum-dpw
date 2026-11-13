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
                        'sage': '#CCDEC1',
                        'cream': '#F9F8F3',
                        'brown': '#7C6757',
                        'dark': '#4A4A4A',
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
    <style>
        /* Sembunyikan scrollbar bawaan */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Custom Animasi Putar Lambat */
        @keyframes putarLambat {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .putar-lambat {
            animation: putarLambat 15s linear infinite;
        }
    </style>
</head>

<body class="bg-cream text-dark font-serif antialiased overflow-x-hidden relative">
    <!-- Tombol Musik Melayang (Pojok Kiri Bawah) -->
    <div id="music-control" class="fixed bottom-5 right-5 z-50">
        <button id="music-btn" type="button" aria-label="Toggle Music"
            class="w-12 h-12 bg-white/90 backdrop-blur-md rounded-full shadow-lg border border-sage/60 flex items-center justify-center hover:bg-brown transition-all focus:outline-none overflow-hidden p-2.5">

            <img id="music-icon" src="assets/icon-music.png" alt="Music Icon"
                class="w-3/4 h-full object-contain transition-transform duration-300">
        </button>
        <!-- File Audio -->
        <audio id="bg-music" loop preload="none">
            <source src="<?= e(c('music.file')) ?>" type="audio/mpeg">
            Browsermu tidak mendukung elemen audio.
        </audio>
    </div>

    <!-- Navbar 3 Titik (Kanan Atas) -->
    <nav class="fixed top-4 right-4 z-50">
        <button id="menu-btn"
            class="p-2 bg-white/90 backdrop-blur-sm rounded-full shadow-md text-brown hover:bg-brown hover:text-sage transition-colors focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 6.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 12.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 18.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5Z" />
            </svg>
        </button>

        <!-- Menu Dropdown -->
        <div id="menu-dropdown"
            class="hidden absolute right-0 mt-2 w-48 bg-white/95 backdrop-blur-md rounded-xl shadow-xl py-2 border border-sage/50 font-sans text-sm transform origin-top-right transition-all">
            <!-- <a href="#cover" class="block px-4 py-2 text-dark hover:bg-sage/40 hover:text-brown transition">Home</a> -->
            <a href="#greeting"
                class="block px-4 py-2 text-dark hover:bg-sage/40 hover:text-brown transition">Greeting</a>
            <a href="#mempelai" class="block px-4 py-2 text-dark hover:bg-sage/40 hover:text-brown transition"><?= e(c('mempelai.title')) ?></a>
            <a href="#countdown"
                class="block px-4 py-2 text-dark hover:bg-sage/40 hover:text-brown transition">Countdown</a>
            <a href="#story" class="block px-4 py-2 text-dark hover:bg-sage/40 hover:text-brown transition">Love
                Story</a>
            <a href="#gallery"
                class="block px-4 py-2 text-dark hover:bg-sage/40 hover:text-brown transition">Gallery</a>
            <a href="#location" class="block px-4 py-2 text-dark hover:bg-sage/40 hover:text-brown transition">Lokasi &
                Jadwal</a>
            <a href="#rsvp" class="block px-4 py-2 text-dark hover:bg-sage/40 hover:text-brown transition"><?= e(c('rsvp.title')) ?></a>
            <a href="#wishes" class="block px-4 py-2 text-dark hover:bg-sage/40 hover:text-brown transition">
                Wishes</a>
        </div>
    </nav>

    <!-- 1. SECTION COVER -->
    <section id="cover"
        class="fixed inset-0 z-50 flex flex-col justify-center items-center bg-cream p-4 md:p-8 bg-cover bg-center overflow-hidden transition-opacity duration-700">
        <img src="assets/atas1.png" alt="top-arc" class="absolute top-0 left-0 w-full h-auto z-10 pointer-events-none">
        <img src="assets/halaman1-kanan.png" alt="kanan bawah"
            class="absolute bottom-0 right-0 h-[250px] md:h-[400px] w-auto z-10 pointer-events-none">
        <img src="assets/halaman1-kiri.png" alt="kiri bawah"
            class="absolute bottom-0 left-0 h-[250px] md:h-[400px] w-auto z-10 pointer-events-none">

        <!-- Bentuk Arch (Lengkungan) -->
        <div
            class="bg-sage p-8 md:p-16 rounded-full text-center w-full max-w-md shadow-[inset_0_4px_12px_rgba(0,0,0,0.2)] relative mt-20 md:mt-[120px] z-20">
            <p class="text-xl md:text-3xl mb-4 tracking-wide text-black"><?= nl2br(e(c('cover.intro'))) ?></p>
            <h1 class="pt-6 md:pt-10 font-script text-5xl md:text-6xl text-brown mb-8 md:mb-10 leading-tight"><?= e(c('cover.couple')) ?></h1>

            <div
                class="border border-dark/30 rounded-2xl p-4 md:p-5 mb-8 md:mb-10 inline-block min-w-[200px] bg-white/50 backdrop-blur-sm">
                <p class="font-sans text-[10px] md:text-xs uppercase tracking-widest text-gray-500 mb-2"><?= e(c('cover.guest_prefix')) ?></p>
                <p class="font-bold text-lg md:text-xl border-b border-dark/20 pb-2 mb-2"><?= e($guestName) ?></p>
                <p class="font-sans text-[10px] md:text-xs"><?= e(c('cover.guest_suffix')) ?></p>
            </div>

            <!-- Tombol Buka Undangan di dalam section #cover -->
            <div>
                <button id="btn-buka" type="button"
                    class="inline-flex items-center justify-center gap-2 bg-brown text-white font-sans text-xs md:text-sm font-semibold tracking-wider px-6 md:px-8 py-3 rounded-full hover:bg-brown/80 transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5 cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                        class="w-4 h-4 md:w-5 md:h-5">
                        <path
                            d="M1.5 8.67v8.58a3 3 0 0 0 3 3h15a3 3 0 0 0 3-3V8.67l-8.928 5.493a3 3 0 0 1-3.144 0L1.5 8.67Z" />
                        <path
                            d="M22.5 6.908V6.75a3 3 0 0 0-3-3h-15a3 3 0 0 0-3 3v.158l9.714 5.978a1.5 1.5 0 0 0 1.572 0L22.5 6.908Z" />
                    </svg>
                    <?= e(c('cover.button')) ?>
                </button>
            </div>
        </div>
    </section>

    <!-- 2. SECTION GREETING -->
    <section id="greeting"
        class="relative min-h-screen flex flex-col justify-center items-center text-center p-6 md:p-8 bg-cream z-20 overflow-hidden">
        <img src="assets/atas2.png" alt="top-arc" class="absolute top-0 left-0 w-full h-auto z-20 pointer-events-none">
        <img src="assets/gold-kiri.png" alt="gold kiri"
            class="absolute bottom-0 -left-[20%] h-[40%] md:h-[60%] md:-left[5%] w-auto z-10 mix-blend-multiply pointer-events-none">
        <img src="assets/gold-kanan.png" alt="gold kanan"
            class="absolute bottom-0 -right-[20%] h-[40%] md:h-[60%] md:-right[5%] w-auto z-10 mix-blend-multiply pointer-events-none">
        <img src="assets/halaman2-bawahkiri.png" alt="kiri bawah"
            class="absolute bottom-0 left-0 h-[200px] md:h-[400px] w-auto z-30 pointer-events-none">
        <img src="assets/halaman2-bawahkanan.png" alt="kanan bawah"
            class="absolute bottom-0 right-0 h-[200px] md:h-[400px] w-auto z-30 pointer-events-none">
        <img src="assets/halaman2-ataskiri.png" alt="kiri atas"
            class="absolute top-0 left-0 h-[120px] md:h-[200px] w-auto z-30 pointer-events-none">
        <img src="assets/halaman2-ataskanan.png" alt="kanan atas"
            class="absolute top-0 right-0 h-[120px] md:h-[200px] w-auto z-30 pointer-events-none">

        <!-- Pilar Background -->
        <div
            class="absolute bottom-0 left-1/2 -translate-x-1/2 z-20 w-4/5 sm:w-3/5 md:w-2/5 h-[80vh] md:h-[90vh] rounded-t-full overflow-hidden">
            <img src="assets/pilar2.png" alt="pilar" class="w-full h-full object-cover block">
            <div class="absolute inset-0 rounded-t-full pointer-events-none shadow-[inset_0_8px_20px_rgba(0,0,0,0.15)]">
            </div>
        </div>

        <div class="relative z-20 translate-y-0 px-4">
            <h2 class="text-2xl md:text-4xl mb-3 md:mb-4 italic"><?= e(c('greeting.title')) ?></h2>
            <h3 class="font-script text-5xl md:text-7xl text-brown mb-4 md:mb-6"><?= e(c('cover.couple')) ?></h3>
            <p
                class="text-lg mb-8 md:text-2xl md:mb-10 tracking-[0.2em] md:tracking-[0.3em] uppercase font-bold border-b border-brown pb-4"><?= e(c('greeting.subtitle')) ?></p>
            <p
                class="max-w-xs md:max-w-md mx-auto leading-relaxed font-bold text-sm md:text-lg text-gray-500 md:bg-transparent p-4 md:p-0 rounded-xl md:backdrop-blur-none md:shadow-none"><?= e(c('greeting.text')) ?></p>
        </div>
    </section>

    <!-- 3. SECTION MEMPELAI -->
    <section id="mempelai">
        <div class="relative py-20 md:py-24 bg-sage/20 z-10 overflow-hidden">
            <img src="assets/atas2.png" alt="top-arc"
                class="absolute -top-[2%] left-0 w-full h-auto z-10 pointer-events-none">
            <img src="assets/halaman2-bawahkiri.png" alt="kiri bawah"
                class="absolute bottom-0 left-0 h-[200px] md:h-[200px] w-auto z-10 pointer-events-none opacity-60">
            <img src="assets/halaman2-bawahkanan.png" alt="kanan bawah"
                class="absolute bottom-0 right-0 h-[200px] md:h-[200px] w-auto z-10 pointer-events-none opacity-60">
            <img src="assets/gold-kiri.png" alt="gold kiri"
                class="absolute bottom-[25%] -left-[37%] h-[40%] md:h-[0px] md:-left[100%] w-auto z-10 mix-blend-multiply pointer-events-none">
            <img src="assets/gold-kanan.png" alt="gold kanan"
                class="absolute bottom-[25%] -right-[37%] h-[40%] md:h-[0px] md:-right[100%] w-auto z-10 mix-blend-multiply pointer-events-none">

            <div class="max-w-5xl mx-auto px-6 relative z-20">
                <h2 class="mt-[15%] md:mt-[10%] font-script text-5xl md:text-6xl text-center text-brown mb-8 md:mb-12"><?= e(c('mempelai.title')) ?></h2>

                <div class="flex flex-col md:flex-row justify-center items-center gap-12 md:gap-8">
                    <!-- Mempelai Pria -->
                    <div class="text-center w-full md:w-5/12">
                        <div
                            class="relative w-[240px] h-[290px] md:w-[300px] md:h-[350px] mx-auto mb-6 md:mb-8 rounded-t-full rounded-b-xl shadow-xl bg-sage overflow-hidden flex items-center justify-center p-3 md:p-4">
                            <div class="w-[82%] h-[90%] rounded-t-full rounded-b-[40%] overflow-hidden relative z-0">
                                <img src="<?= e(c('mempelai.groom_photo')) ?>" alt="Mufid Waladi"
                                    class="w-full h-full object-cover scale-[1.3] translate-y-4">
                            </div>
                            <img src="assets/bride-groom-border.png" alt="border"
                                class="absolute inset-0 w-full h-full object-cover pointer-events-none z-10">
                        </div>
                        <h3 class="text-3xl md:text-4xl font-bold mb-2 md:mb-3"><?= e(c('mempelai.groom_name')) ?></h3>
                        <p
                            class="font-sans text-[10px] md:text-xs font-semibold uppercase tracking-widest text-gray-500 mb-1"><?= e(c('mempelai.groom_label')) ?></p>
                        <p class="text-lg md:text-xl italic font-semibold text-gray-500"><?= e(c('mempelai.groom_parents')) ?></p>
                    </div>

                    <!-- Pemisah Dan -->
                    <div class="font-script text-6xl md:text-7xl text-brown md:w-2/12 text-center">&</div>

                    <!-- Mempelai Wanita -->
                    <div class="text-center w-full md:w-5/12">
                        <div
                            class="relative w-[240px] h-[290px] md:w-[300px] md:h-[350px] mx-auto mb-6 md:mb-8 rounded-t-full rounded-b-xl shadow-xl bg-sage overflow-hidden flex items-center justify-center p-3 md:p-4">
                            <div class="w-[82%] h-[90%] rounded-t-full rounded-b-[40%] overflow-hidden relative z-0">
                                <img src="<?= e(c('mempelai.bride_photo')) ?>" alt="Velicia Regina Ananta"
                                    class="w-full h-full object-cover scale-[1.3]">
                            </div>
                            <img src="assets/bride-groom-border.png" alt="border"
                                class="absolute inset-0 w-full h-full object-cover pointer-events-none z-10">
                        </div>
                        <h3 class="text-3xl md:text-4xl font-bold mb-2 md:mb-3"><?= e(c('mempelai.bride_name')) ?></h3>
                        <p
                            class="font-sans text-[10px] md:text-xs font-semibold uppercase tracking-widest text-gray-500 mb-1"><?= e(c('mempelai.bride_label')) ?></p>
                        <p class="text-lg md:text-xl italic font-semibold text-gray-500"><?= e(c('mempelai.bride_parents')) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. SECTION COUNTDOWN  -->
    <section id="countdown"
        class="relative min-h-screen flex flex-col justify-center items-center py-16 md:py-20 bg-cream overflow-hidden z-20">
        <!-- Ornamen Bunga Sisi Samping -->
        <img src="assets/halaman2-ataskiri.png" alt="kiri atas"
            class="absolute top-0 left-0 h-[150px] md:h-[300px] w-auto z-10 pointer-events-none opacity-80">
        <img src="assets/halaman2-ataskanan.png" alt="kanan atas"
            class="absolute top-0 right-0 h-[150px] md:h-[300px] w-auto z-10 pointer-events-none opacity-80">
        <img src="assets/halaman2-bawahkiri.png" alt="kiri bawah"
            class="absolute bottom-0 left-0 h-[200px] md:h-[400px] w-auto z-10 pointer-events-none">
        <img src="assets/halaman2-bawahkanan.png" alt="kanan bawah"
            class="absolute bottom-0 right-0 h-[200px] md:h-[400px] w-auto z-10 pointer-events-none">

        <div class="relative z-20 max-w-4xl mx-auto px-4 flex flex-col items-center">
            <!-- FRAME FOTO ARCH -->
            <div
                class="relative w-[260px] sm:w-[340px] md:w-[380px] h-[340px] sm:h-[430px] md:h-[470px] rounded-t-[180px] rounded-b-[40px] shadow-xl border-4 border-white/80 bg-sage/20 mb-8 md:mb-10">
                <div class="w-full h-full rounded-t-[176px] rounded-b-[36px] overflow-hidden">
                    <?php if (img('countdown.photo')): ?><img src="<?= e(img('countdown.photo')) ?>" alt="Foto" class="w-full h-full object-cover"><?php endif; ?>
                </div>
                <img src="assets/kupu-kiri.png" alt="kupu-kiri"
                    class="absolute top-[50px] -left-[15px] md:top-[70px] md:-left-[20px] w-14 md:w-19 h-auto z-30 pointer-events-none drop-shadow-md -rotate-12">
                <img src="assets/kupu-kiri.png" alt="kupu-kanan"
                    class="absolute top-[140px] -right-[30px] md:top-[180px] md:-right-[35px] w-14 md:w-19 h-auto z-30 pointer-events-none rotate-20 scale-x-[-1] drop-shadow-md">
            </div>

            <!-- Tampilan Hitung Mundur -->
            <div class="grid grid-cols-4 gap-3 sm:gap-6 md:gap-10 text-center text-dark w-full px-2">
                <div
                    class="flex flex-col items-center p-3 md:p-5 bg-white/80 rounded-xl md:rounded-2xl shadow-md shadow-dark/10">
                    <span class="text-2xl sm:text-4xl md:text-5xl font-serif font-normal mb-1" id="cd-days">0</span>
                    <span class="font-sans text-[10px] sm:text-sm text-gray-500">Days</span>
                </div>
                <div
                    class="flex flex-col items-center p-3 md:p-5 bg-white/80 rounded-xl md:rounded-2xl shadow-md shadow-dark/10">
                    <span class="text-2xl sm:text-4xl md:text-5xl font-serif font-normal mb-1" id="cd-hours">0</span>
                    <span class="font-sans text-[10px] sm:text-sm text-gray-500">Hours</span>
                </div>
                <div
                    class="flex flex-col items-center p-3 md:p-5 bg-white/80 rounded-xl md:rounded-2xl shadow-md shadow-dark/10">
                    <span class="text-2xl sm:text-4xl md:text-5xl font-serif font-normal mb-1" id="cd-mins">0</span>
                    <span class="font-sans text-[10px] sm:text-sm text-gray-500">Mins</span>
                </div>
                <div
                    class="flex flex-col items-center p-3 md:p-5 bg-white/80 rounded-xl md:rounded-2xl shadow-md shadow-dark/10">
                    <span class="text-2xl sm:text-4xl md:text-5xl font-serif font-normal mb-1" id="cd-secs">0</span>
                    <span class="font-sans text-[10px] sm:text-sm text-gray-500">Secs</span>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. SECTION LOVE STORY -->
    <section id="story"
        class="relative min-h-screen flex flex-col justify-center items-center py-16 md:py-20 bg-cream overflow-hidden">
        <img src="assets/ataskubah.png" alt="top-arc"
            class="absolute top-0 left-0 w-full h-auto z-10 pointer-events-none">
        <img src="assets/atas1.png" alt="top-arc" class="absolute top-0 left-0 w-full h-auto z-10 pointer-events-none">
        <img src="assets/halaman1-kanan.png" alt="kanan bawah"
            class="absolute bottom-0 right-0 h-[200px] md:h-[400px] w-auto z-10 pointer-events-none">
        <img src="assets/halaman1-kiri.png" alt="kiri bawah"
            class="absolute bottom-0 left-0 h-[200px] md:h-[400px] w-auto z-10 pointer-events-none">

        <div class="max-w-6xl mx-auto px-6 relative z-20">
            <h2 class="font-script text-5xl md:text-6xl text-center text-brown mb-10 md:mb-16 mt-16 md:mt-28"><?= e(c('story.title')) ?></h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-7">
                <!-- Card 1 -->
                <div
                    class="bg-white p-6 md:p-8 rounded-2xl shadow-sm border-2 border-sage/80 text-center hover:shadow-md transition-shadow z-20">
                    <h3
                        class="text-2xl md:text-3xl font-bold mb-3 md:mb-4 text-brown border-b border-sage pb-2 md:pb-4 inline-block"><?= e(c('story.t1')) ?></h3>
                    <p class="text-gray-600 text-sm md:text-lg font-bold leading-relaxed mt-3 md:mt-4"><?= e(c('story.p1')) ?></p>
                </div>
                <!-- Card 2 -->
                <div
                    class="bg-white p-6 md:p-8 rounded-2xl shadow-sm border-2 border-sage/80 text-center hover:shadow-md transition-shadow z-20">
                    <h3
                        class="text-2xl md:text-3xl font-bold mb-3 md:mb-4 text-brown border-b border-sage pb-2 md:pb-4 inline-block"><?= e(c('story.t2')) ?></h3>
                    <p class="text-gray-600 text-sm md:text-lg font-bold leading-relaxed mt-3 md:mt-4"><?= e(c('story.p2')) ?></p>
                </div>
                <!-- Card 3 -->
                <div
                    class="bg-white p-6 md:p-8 rounded-2xl shadow-sm border-2 border-sage/80 text-center hover:shadow-md transition-shadow z-20">
                    <h3
                        class="text-2xl md:text-3xl font-bold mb-3 md:mb-4 text-brown border-b border-sage pb-2 md:pb-4 inline-block"><?= e(c('story.t3')) ?></h3>
                    <p class="text-gray-600 text-sm md:text-lg font-bold leading-relaxed mt-3 md:mt-4"><?= e(c('story.p3')) ?></p>
                </div>
            </div>
        </div>
    </section>

    <!-- 6. SECTION GALLERY  -->
    <section id="gallery"
        class="relative min-h-screen flex flex-col justify-center items-center py-16 md:py-20 bg-cream overflow-hidden">
        <img src="assets/halaman2-bawahkiri.png" alt="kiri bawah"
            class="absolute bottom-0 left-0 h-[150px] md:h-[200px] w-auto z-10 pointer-events-none">
        <img src="assets/halaman2-bawahkanan.png" alt="kanan bawah"
            class="absolute bottom-0 right-0 h-[150px] md:h-[200px] w-auto z-10 pointer-events-none">
        <img src="assets/atas2.png" alt="top border"
            class="absolute top-0 left-0 w-full h-auto z-10 pointer-events-none">

        <div class="relative z-20 w-full">
            <div class="text-center mb-6 mt-16 md:mt-24 px-6">
                <h2 class="font-script text-5xl md:text-6xl text-brown mb-2"><?= e(c('gallery.title')) ?></h2>
                <p
                    class="font-sans text-[10px] md:text-xs uppercase tracking-[0.2em] md:tracking-[0.3em] text-gray-500"><?= e(c('gallery.subtitle')) ?></p>
            </div>

            <div class="relative w-full flex items-center justify-center py-4 md:py-6">
                <button id="btn-prev" type="button" aria-label="Previous"
                    class="absolute left-2 md:left-12 z-30 w-10 h-10 md:w-12 md:h-12 flex items-center justify-center bg-white/90 backdrop-blur-md rounded-2xl text-brown border border-sage/60 hover:bg-brown hover:text-white transition-all shadow-md active:scale-95 focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                        stroke="currentColor" class="w-5 h-5 md:w-6 md:h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                    </svg>
                </button>

                <div id="gallery-container"
                    class="flex gap-3 md:gap-6 overflow-x-auto snap-x snap-mandatory no-scrollbar w-full py-6 md:py-8 items-center"
                    style="touch-action: pan-y;">
                    <?php foreach ($photos as $p): ?>
                    <div class="gallery-item snap-center shrink-0 w-[160px] md:w-[220px] h-[220px] md:h-[300px] rounded-[1.5rem] md:rounded-[2rem] overflow-hidden bg-sage/30 shadow-md transition-all duration-300 ease-out"><img src="<?= e($p) ?>" alt="Gallery" class="w-full h-full object-cover pointer-events-none"></div>
                    <?php endforeach; ?>
                </div>

                <button id="btn-next" type="button" aria-label="Next"
                    class="absolute right-2 md:right-12 z-30 w-10 h-10 md:w-12 md:h-12 flex items-center justify-center bg-white/90 backdrop-blur-md rounded-2xl text-brown border border-sage/60 hover:bg-brown hover:text-white transition-all shadow-md active:scale-95 focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                        stroke="currentColor" class="w-5 h-5 md:w-6 md:h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                </button>
            </div>
        </div>
    </section>

    <!-- 7. SECTION LOKASI -->
    <section id="location"
        class="relative min-h-screen flex items-center justify-center py-12 md:py-16 bg-cream px-4 md:px-12 overflow-hidden">
        <img src="assets/halaman2-bawahkanan.png" alt="kiri bawah"
            class="absolute top-0 left-0 h-[150px] md:h-[200px] w-auto z-10 pointer-events-none opacity-80 rotate-180">
        <img src="assets/halaman2-bawahkiri.png" alt="kanan bawah"
            class="absolute top-0 right-0 h-[150px] md:h-[200px] w-auto z-10 pointer-events-none opacity-80 rotate-180">
        <img src="assets/gold-kiri.png" alt="gold kiri"
            class="absolute top-30 h-[30%] -left-[37%] md:bottom-[50px] md:-left-[25%] md:h-[60%] lg:-left-[11%] lg:h-[60%] w-auto z-10 mix-blend-multiply pointer-events-none">
        <img src="assets/gold-kanan.png" alt="gold kanan"
            class="absolute top-30 h-[30%] -right-[37%] md:bottom-[50px] md:-right-[25%] md:h-[60%] lg:-right-[11%] lg:h-[60%] w-auto z-10 mix-blend-multiply pointer-events-none">

        <div
            class="relative w-full max-w-[1200px] bg-sage/40 rounded-3xl p-6 md:p-12 shadow-sm overflow-hidden mt-10 md:mt-0 z-20">
            <img src="assets/halaman2-ataskiri.png" alt="dekorasi-kiri"
                class="absolute top-0 left-0 w-24 md:w-48 pointer-events-none z-0">
            <img src="assets/halaman2-ataskanan.png" alt="dekorasi-kanan"
                class="absolute top-0 right-0 w-24 md:w-48 pointer-events-none z-0">
            <img src="assets/halaman2-bawahkiri.png" alt="dekorasi-bawah-kiri"
                class="absolute bottom-0 left-0 w-24 md:w-48 pointer-events-none z-0">
            <img src="assets/halaman2-bawahkanan.png" alt="dekorasi-bawah-kanan"
                class="absolute bottom-0 right-0 w-24 md:w-48 pointer-events-none z-0">

            <div class="relative z-10 flex flex-col items-center text-center">
                <h2 class="font-script text-5xl md:text-6xl text-brown mb-3 md:mb-4"><?= e(c('location.title')) ?></h2>
                <p class="max-w-xl font-bold text-sm md:text-base text-gray-700 mb-5 md:mb-6"><?= e(c('location.intro')) ?></p>

                <div
                    class="bg-white/80 backdrop-blur-sm px-6 py-2 rounded-full font-bold text-sm md:text-base text-gray-800 shadow-sm mb-6 md:mb-8"><?= e(c('location.date_text')) ?></div>

                <p class="max-w-lg font-bold text-sm md:text-base text-gray-600 mb-8"><?= e(c('location.note')) ?></p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full max-w-2xl rounded-t-2xl">
                    <!-- Card 1: Akad Nikah -->
                    <div
                        class="relative bg-cream pt-20 pb-8 px-6 rounded-t-full rounded-b-2xl shadow-md overflow-hidden flex flex-col items-center text-center">
                        <img src="assets/location-atas.png" alt="dekorasi atas"
                            class="absolute top-0 left-0 w-full h-auto pointer-events-none z-10">
                        <img src="assets/location-bunga.png" alt="bunga"
                            class="absolute bottom-0 left-0 w-full h-auto pointer-events-none z-10">
                        <div
                            class="absolute bottom-0 left-0 w-full h-10 bg-gradient-to-t from-[#B5C99A]/50 via-[#B5C99A]/15 to-transparent pointer-events-none z-10">
                        </div>
                        <div
                            class="absolute inset-0 rounded-t-full rounded-b-2xl pointer-events-none shadow-[inset_0_10px_25px_rgba(138,154,134,0.3)] z-20">
                        </div>

                        <div class="relative z-20 pt-6">
                            <h3 class="font-script text-3xl text-brown mb-2"><?= e(c('location.akad_title')) ?></h3>
                            <p class="mb-1 font-bold text-sm text-gray-600"><?= e(c('location.akad_day')) ?></p>
                            <p class="text-3xl font-script text-brown mb-1"><?= e(c('location.akad_date')) ?></p>
                            <p class="font-bold text-sm text-gray-600"><?= e(c('location.akad_time')) ?></p>
                        </div>
                    </div>

                    <!-- Card 2: Resepsi -->
                    <div
                        class="relative bg-cream pt-20 pb-8 px-6 rounded-t-full rounded-b-2xl shadow-md overflow-hidden flex flex-col items-center text-center">
                        <img src="assets/location-atas.png" alt="dekorasi atas"
                            class="absolute top-0 left-0 w-full h-auto pointer-events-none z-10">
                        <img src="assets/location-bunga.png" alt="bunga"
                            class="absolute bottom-0 left-0 w-full h-auto pointer-events-none z-10">
                        <div
                            class="absolute bottom-0 left-0 w-full h-10 bg-gradient-to-t from-[#B5C99A]/50 via-[#B5C99A]/15 to-transparent pointer-events-none z-10">
                        </div>
                        <div
                            class="absolute inset-0 rounded-t-full rounded-b-2xl pointer-events-none shadow-[inset_0_10px_25px_rgba(138,154,134,0.3)] z-20">
                        </div>

                        <div class="relative z-20 pt-6">
                            <h3 class="font-script text-3xl text-brown mb-2"><?= e(c('location.resepsi_title')) ?></h3>
                            <p class="mb-1 font-bold text-sm text-gray-600"><?= e(c('location.resepsi_day')) ?></p>
                            <p class="text-3xl font-script text-brown mb-1"><?= e(c('location.resepsi_date')) ?></p>
                            <p class="font-bold text-sm text-gray-600"><?= e(c('location.resepsi_time')) ?></p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full max-w-2xl mt-6">
                    <!-- Dress Code -->
                    <div class="bg-cream p-5 md:p-6 rounded-2xl shadow-md flex flex-col items-center">
                        <h4
                            class="uppercase text-sm md:text-base font-bold tracking-widest text-gray-700 border-b pb-1 mb-3">
                            Dress Code</h4>
                        <p class="font-bold text-xs md:text-sm text-gray-700 mb-4 text-center"><?= e(c('location.dress_text')) ?></p>
                        <div class="flex flex-col gap-2">
                            <div class="flex gap-2 justify-center">
                                <span class="w-5 h-5 md:w-6 md:h-6 rounded-full bg-[#e0d0b4] shadow-sm"></span>
                                <span class="w-5 h-5 md:w-6 md:h-6 rounded-full bg-[#D4C3B3] shadow-sm"></span>
                                <span class="w-5 h-5 md:w-6 md:h-6 rounded-full bg-[#B5C99A] shadow-sm"></span>
                            </div>
                            <div class="flex gap-2 justify-center">
                                <span class="w-5 h-5 md:w-6 md:h-6 rounded-full bg-[#E8C8B8] shadow-sm"></span>
                                <span class="w-5 h-5 md:w-6 md:h-6 rounded-full bg-[#D8A47F] shadow-sm"></span>
                                <span class="w-5 h-5 md:w-6 md:h-6 rounded-full bg-[#94A388] shadow-sm"></span>
                            </div>
                            <div class="flex gap-2 justify-center">
                                <span class="w-5 h-5 md:w-6 md:h-6 rounded-full bg-[#D5B9B2] shadow-sm"></span>
                                <span class="w-5 h-5 md:w-6 md:h-6 rounded-full bg-[#C4A482] shadow-sm"></span>
                                <span class="w-5 h-5 md:w-6 md:h-6 rounded-full bg-[#6B705C] shadow-sm"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Hotel Pangeran Pekanbaru -->
                    <div
                        class="bg-cream p-5 md:p-6 rounded-2xl shadow-md flex flex-col items-center text-center justify-center">
                        <img src="assets/icon-location.png" alt="icon" class="w-6 h-auto mb-3">
                        <h4 class="font-bold text-base md:text-lg text-gray-800 mb-1"><?= e(c('location.venue_name')) ?></h4>
                        <p class="font-bold text-xs md:text-sm text-gray-600 mb-5 leading-relaxed"><?= e(c('location.venue_address')) ?></p>
                        <a href="<?= e(c('location.map_url')) ?>" target="_blank"
                            class="bg-[#8A9A86] text-white text-sm md:text-base font-bold px-6 py-2.5 rounded-full shadow-md hover:bg-[#788874] transition w-full md:w-auto"><?= e(c('location.map_button')) ?></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 8. SECTION RSVP -->
    <section id="rsvp"
        class="relative min-h-screen flex flex-col justify-center items-center py-24 bg-cream overflow-hidden bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI4MCIgaGVpZ2h0PSI4MCI+PHBhdGggZD0iTTAgMGg4MHY4MEgwem00MCA0MGMxMS4wNDYgMCAyMC04Ljk1NCAyMC0yMFM1MS4wNDYgMCA0MCAwIDIwIDguOTU0IDIwIDIwczguOTU0IDIwIDIwIDIwem0wIDEwYzE2LjU2OCAwIDMwLTEzLjQzMiAzMC0zMFM1Ni41NjggMCA0MCAwIDEwIDEzLjQzMiAxMCAzMHMxMy40MzIgMzAgMzAgMzB6bTAgMTBjMjIuMDkxIDAgNDAtMTcuOTA5IDQwLTQwUzYyLjA5MSAwIDQwIDAgMCAxNy45MDkgMCA0MHMxNy45MDkgNDAgNDAgNDAtIiBmaWxsPSIjZDFkOWNhIiBmaWxsLW9wYWNpdHk9Ii4wNSIgZmlsbC1ydWxlPSJldmVub2RkIi8+PC9zdmc=')]">
        <img src="assets/atas2.png" alt="top-arc" class="absolute top-0 left-0 w-full h-auto z-10 pointer-events-none">

        <img src="assets/halaman1-kanan.png" alt="kanan bawah"
            class="absolute bottom-0 -right-10 md:right-0 h-[180px] md:h-[450px] w-auto z-10 pointer-events-none">

        <img src="assets/halaman1-kiri.png" alt="kiri bawah"
            class="absolute bottom-0 -left-10 md:left-0 h-[180px] md:h-[450px] w-auto z-10 pointer-events-none">

        <div class="max-w-lg md:max-w-xl mx-auto mt-[7%] px-6 w-full z-20">
            <!-- rounded-3xl untuk mobile (kotak), md:rounded-full untuk desktop (oval/lingkaran) -->
            <div
                class="bg-sage p-8 md:p-14 rounded-3xl md:rounded-full text-center shadow-[inset_0_4px_12px_rgba(0,0,0,0.15)] relative">
                <h2 class="font-script text-4xl md:text-5xl text-brown mb-12"><?= e(c('rsvp.title')) ?></h2>
                <form id="rsvp-form" class="flex flex-col gap-4 font-sans text-left">
                    <input type="text" name="name" required maxlength="100" value="<?= e($guest['name'] ?? '') ?>" placeholder="Nama"
                        class="p-3 border border-sage/50 rounded-xl bg-white/70 focus:outline-none focus:border-brown focus:ring-1 focus:ring-brown transition shadow-sm">
                    <input type="email" name="email" maxlength="100" value="<?= e($guest['email'] ?? '') ?>" placeholder="Email"
                        class="p-3 border border-sage/50 rounded-xl bg-white/70 focus:outline-none focus:border-brown focus:ring-1 focus:ring-brown transition shadow-sm">
                    <input type="tel" name="phone" maxlength="30" value="<?= e($guest['phone'] ?? '') ?>" placeholder="Nomor Telfon"
                        class="p-3 border border-sage/50 rounded-xl bg-white/70 focus:outline-none focus:border-brown focus:ring-1 focus:ring-brown transition shadow-sm">

                    <div class="relative">
                        <select name="status" required class="w-full p-3 border border-sage/50 rounded-xl bg-white/70 focus:outline-none focus:border-brown focus:ring-1 focus:ring-brown appearance-none text-gray-600 transition shadow-sm">
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
                        class="p-3 border border-sage/50 rounded-xl bg-white/70 focus:outline-none focus:border-brown focus:ring-1 focus:ring-brown transition shadow-sm">

                    <div class="text-center mt-4">
                        <button type="submit"
                            class="bg-brown text-white px-6 py-2.5 rounded-xl hover:bg-brown/80 transition-colors mt-8 font-semibold tracking-wider uppercase shadow-md">
                            Kirim
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- 9. SECTION WEDDING WISHES -->
    <section id="wishes"
        class="relative min-h-screen flex flex-col justify-center items-center py-20 md:py-24 bg-cream overflow-hidden">
        <div
            class="absolute inset-0 opacity-5 pointer-events-none bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI4MCIgaGVpZ2h0PSI4MCI+PHBhdGggZD0iTTAgMGg4MHY4MEgwem00MCA0MGMxMS4wNDYgMCAyMC04Ljk1NCAyMC0yMFM1MS4wNDYgMCA4MCAwIDIwIDguOTU0IDIwIDIwczguOTU0IDIwIDIwIDIwem0wIDEwYzE2LjU2OCAwIDMwLTEzLjQzMiAzMC0zMFM1Ni41NjggMCA0MCAwIDEwIDEzLjQzMiAxMCAzMHMxMy40MzIgMzAgMzAgMzB6bTAgMTBjMjIuMDkxIDAgNDAtMTcuOTA5IDQwLTQwUzYyLjA5MSAwIDQwIDAgMCAxNy45MDkgMCA0MHMxNy45MDkgNDAgNDAgNDAtIiBmaWxsPSIjZDFkOWNhIiBmaWxsLW9wYWNpdHk9Ii4wNSIgZmlsbC1ydWxlPSJldmVub2RkIi8+PC9zdmc=')]">
        </div>

        <img src="assets/halaman2-ataskiri.png" alt="kiri atas"
            class="absolute top-0 left-0 h-[120px] md:h-[200px] w-auto z-10 pointer-events-none">
        <img src="assets/halaman2-ataskanan.png" alt="kanan atas"
            class="absolute top-0 right-0 h-[120px] md:h-[200px] w-auto z-10 pointer-events-none">
        <img src="assets/halaman2-bawahkanan.png" alt="kiri bawah"
            class="absolute bottom-0 left-0 -scale-x-100 h-[150px] md:h-[300px] w-auto z-10 pointer-events-none">
        <img src="assets/halaman2-bawahkiri.png" alt="kanan bawah"
            class="absolute bottom-0 right-0 -scale-x-100 h-[150px] md:h-[300px] w-auto z-10 pointer-events-none">

        <div class="max-w-5xl mx-auto px-4 sm:px-8 w-full relative z-20">
            <div
                class="bg-sage/40 backdrop-blur-sm p-6 md:p-10 rounded-[2rem] shadow-sm border border-white/50 flex flex-col">
                <h2 class="font-script text-5xl md:text-6xl text-center mb-6 md:mb-8 text-brown"><?= e(c('rsvp.wishes_title')) ?></h2>

                <!-- Form Input Ucapan -->
                <form id="wishes-form"
                    class="flex flex-col gap-3 md:gap-4 font-sans mb-6 md:mb-8 max-w-2xl mx-auto w-full">
                    <input type="text" id="wish-name" placeholder="Nama" required
                        class="p-3.5 md:p-4 border border-white/60 rounded-xl bg-white/90 focus:outline-none focus:border-brown focus:ring-1 focus:ring-brown transition shadow-sm text-sm md:text-base">
                    <textarea id="wish-text" rows="3" placeholder="Tuliskan ucapan anda" maxlength="300" required
                        class="p-3.5 md:p-4 border border-white/60 rounded-xl bg-white/90 focus:outline-none focus:border-brown focus:ring-1 focus:ring-brown transition shadow-sm resize-none text-sm md:text-base"></textarea>

                    <div
                        class="flex flex-col sm:flex-row justify-between items-center text-xs md:text-sm text-gray-600 mt-2 gap-4">
                        <span id="char-count">Sisa huruf: 300</span>
                        <button type="submit"
                            class="bg-brown text-white px-6 py-2.5 rounded-xl hover:bg-brown/80 transition shadow-md font-semibold tracking-wide cursor-pointer w-full sm:w-auto">
                            Kirim Ucapan
                        </button>
                    </div>
                </form>

                <!-- Grid Ucapan -->
                <div id="wishes-list"
                    class="grid grid-cols-1 md:grid-cols-2 gap-4 overflow-y-auto max-h-[300px] md:max-h-[380px] p-2 font-sans no-scrollbar">
<?php foreach ($wishes as $w): ?>
<div class="bg-white/90 p-4 md:p-5 rounded-2xl shadow-sm flex items-start gap-4"><div class="w-10 h-10 md:w-12 md:h-12 bg-sage rounded-full flex items-center justify-center text-brown font-bold text-lg md:text-xl shrink-0"><?= e(mb_strtoupper(mb_substr($w['name'], 0, 1))) ?></div><div class="flex-1 min-w-0"><p class="font-bold text-gray-800 text-sm md:text-base mb-1 truncate"><?= e($w['name']) ?></p><p class="text-gray-600 text-xs md:text-sm leading-relaxed"><?= e($w['message']) ?></p></div></div>
<?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer Simple -->
    <footer class="bg-brown text-white text-center py-6 font-sans text-xs md:text-sm relative z-30">
        <p>&copy; 2026 Wedding Invitation of <?= e(c('cover.couple')) ?>. All rights reserved.</p>
    </footer>

    <!-- Script Navbar & Komponen Interaktif -->
    <script>window.GUEST_CODE = <?= json_encode($guest['code'] ?? '') ?>; window.EVENT_DATE = <?= json_encode(c('countdown.date')) ?>;</script>
    <script src="script.js"></script>
</body>

</html>