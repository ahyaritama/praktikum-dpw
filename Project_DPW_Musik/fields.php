<?php
// Daftar semua konten undangan yang bisa diedit dari dashboard.
// Tipe: text | textarea | image | datetime
// Nilai default dipakai selama belum diubah di dashboard.

$FIELDS = [
  'cover' => ['label' => 'Cover', 'anchor' => 'cover', 'fields' => [
    'couple'        => ['Nama Pasangan (dipakai di seluruh undangan)', 'text', 'Mufid & Cia'],
    'intro'         => ['Teks di Atas Nama', 'textarea', "We invite you to\nThe Wedding of"],
    'guest_prefix'  => ['Teks di Atas Nama Tamu', 'text', 'Kepada Yth.'],
    'guest_default' => ['Nama Tamu (Default)', 'text', 'Nama Orang'],
    'guest_suffix'  => ['Teks di Bawah Nama', 'text', 'Di tempat'],
    'button'        => ['Tombol', 'text', 'Buka Undangan'],
  ]],
  'greeting' => ['label' => 'Greeting', 'anchor' => 'greeting', 'fields' => [
    'title'    => ['Judul', 'text', 'You Are Invited To'],
    'subtitle' => ['Sub Judul', 'text', 'Wedding Day'],
    'text'     => ['Deskripsi', 'textarea', 'Dengan penuh kebahagiaan, kami mengundang Anda untuk hadir dan menjadi bagian dari hari istimewa kami.'],
  ]],
  'mempelai' => ['label' => 'Mempelai', 'anchor' => 'mempelai', 'fields' => [
    'title'        => ['Judul', 'text', 'Bride & Groom'],
    'groom_photo'  => ['Foto Mempelai Pria', 'image', 'assets/groom.jpg'],
    'groom_name'   => ['Nama Mempelai Pria', 'text', 'Mufid Waladi'],
    'groom_label'  => ['Keterangan Pria', 'text', 'Precious Son of'],
    'groom_parents'=> ['Nama Orang Tua Pria', 'text', 'Hadi Ujan & Emilia'],
    'bride_photo'  => ['Foto Mempelai Wanita', 'image', 'assets/bride.png'],
    'bride_name'   => ['Nama Mempelai Wanita', 'text', 'Velicia Regina Ananta'],
    'bride_label'  => ['Keterangan Wanita', 'text', 'Lovely Daughter of'],
    'bride_parents'=> ['Nama Orang Tua Wanita', 'text', 'Riyanda Ahyaritama & Nurcahyani'],
  ]],
  'countdown' => ['label' => 'Countdown', 'anchor' => 'countdown', 'fields' => [
    'date'  => ['Tanggal & Jam Acara (hitung mundur)', 'datetime', '2026-10-02T08:00'],
    'photo' => ['Foto', 'image', 'assets/foto-sunset.png'],
  ]],
  'story' => ['label' => 'Love Story', 'anchor' => 'story', 'fields' => [
    'title'  => ['Judul', 'text', 'Our Love Story'],
    't1'     => ['Judul Kisah 1', 'text', 'Kenalan'],
    'p1'     => ['Isi Kisah 1', 'textarea', 'Berawal dari ketidaksengajaan di sebuah kedai kopi, cangkir yang tertukar dan obrolan singkat yang ternyata berlanjut. Dari sekadar menyapa, interaksi yang santai perlahan berubah jadi kebiasaan harian.'],
    't2'     => ['Judul Kisah 2', 'text', 'Jadian'],
    'p2'     => ['Isi Kisah 2', 'textarea', 'Seiring berjalannya waktu, kecocokan dan rasa nyaman makin terasa. Tanpa drama atau perayaan berlebihan, kami sepakat untuk mulai berjalan berdampingan dan saling mendukung satu sama lain.'],
    't3'     => ['Judul Kisah 3', 'text', 'Tunangan'],
    'p3'     => ['Isi Kisah 3', 'textarea', 'Kini, dengan penuh rasa syukur dan bahagia, kami melangkah ke babak baru dalam kisah kami, menuju sebuah janji untuk saling menemani, menjaga, dan mencintai dalam setiap langkah kehidupan.'],
  ]],
  'gallery' => ['label' => 'Galeri', 'anchor' => 'gallery', 'fields' => [
    'title'    => ['Judul', 'text', 'Our Gallery'],
    'subtitle' => ['Sub Judul', 'text', 'Moments of Love & Togetherness'],
    'g1'  => ['Foto 1', 'image', 'assets/foto.jpeg'],
    'g2'  => ['Foto 2', 'image', 'assets/foto2.jpeg'],
    'g3'  => ['Foto 3', 'image', 'assets/foto3.jpeg'],
    'g4'  => ['Foto 4', 'image', 'assets/foto4.jpeg'],
    'g5'  => ['Foto 5', 'image', 'assets/foto5.jpeg'],
    'g6'  => ['Foto 6', 'image', 'assets/foto6.jpeg'],
    'g7'  => ['Foto 7', 'image', 'assets/couple-field.png'],
    'g8'  => ['Foto 8', 'image', 'assets/sitting.png'],
    'g9'  => ['Foto 9', 'image', 'assets/bouquet.png'],
    'g10' => ['Foto 10', 'image', 'assets/bunga.jpeg'],
  ]],
  'location' => ['label' => 'Lokasi & Acara', 'anchor' => 'location', 'fields' => [
    'title'       => ['Judul', 'text', 'Lokasi'],
    'intro'       => ['Kalimat Pembuka', 'textarea', 'Dengan penuh rasa hormat kami mengharapkan kehadiran Bapak/Ibu/Saudara sekalian pada Acara Pernikahan kami yang akan kami laksanakan pada:'],
    'date_text'   => ['Tanggal (tampilan)', 'text', 'Jumat, 02 Oktober 2026'],
    'note'        => ['Catatan', 'textarea', 'Besar harapan kami akan kehadiran Bapak/Ibu/Saudara sekalian tentunya dengan memperhatikan anjuran dari Pemerintah, Untuk itu kami memberlakukan protokol kesehatan sebagaimana instruksi dibawah ini'],
    'akad_title'  => ['Acara 1 - Judul', 'text', 'Akad Nikah'],
    'akad_day'    => ['Acara 1 - Hari', 'text', 'Jumat'],
    'akad_date'   => ['Acara 1 - Tanggal', 'text', '02.10.2026'],
    'akad_time'   => ['Acara 1 - Jam', 'text', '08:00 - 10:00'],
    'resepsi_title' => ['Acara 2 - Judul', 'text', 'Resepsi'],
    'resepsi_day'   => ['Acara 2 - Hari', 'text', 'Jumat'],
    'resepsi_date'  => ['Acara 2 - Tanggal', 'text', '02.10.2026'],
    'resepsi_time'  => ['Acara 2 - Jam', 'text', '10:00 - Selesai'],
    'dress_text'  => ['Dress Code', 'textarea', 'We kindly ask that you wear these colors to our special day'],
    'venue_name'  => ['Nama Tempat', 'text', 'Hotel Pangeran'],
    'venue_address' => ['Alamat', 'textarea', 'Jln. Jendral Sudirman No.371-373, Cinta Raja, Sail, Kota Pekanbaru, Riau'],
    'map_button'  => ['Teks Tombol Peta', 'text', 'Buka di Peta'],
    'map_url'     => ['Link Google Maps', 'text', 'https://www.google.com/maps/place/Hotel+Pangeran/@0.5048192,101.4530477,17z'],
  ]],
  'rsvp' => ['label' => 'RSVP & Ucapan', 'anchor' => 'rsvp', 'fields' => [
    'title'        => ['Judul RSVP', 'text', 'RSVP'],
    'wishes_title' => ['Judul Ucapan', 'text', 'Wedding Wishes'],
  ]],
  'music' => ['label' => 'Musik', 'anchor' => 'cover', 'fields' => [
    'file' => ['Musik Latar (MP3)', 'audio', "assets/Elvis Presley - Can't Help Falling in Love (Lyrics) (1).mp3"],
  ]],
];
