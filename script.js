var spareparts = [];

function displayAllParts(dataToRender) {
    if (!dataToRender) {
        dataToRender = spareparts;
    }

    var partList = document.getElementById('partList');
    if (!partList) return;

    partList.innerHTML = '';

    for (var i = 0; i < dataToRender.length; i++) {
        var barang_dipilih = dataToRender[i];

        var hargaIDR = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(barang_dipilih.harga);

        partList.innerHTML += `
            <div class="car-card">
                <div class="badge ${barang_dipilih.tipe.toLowerCase()}">${barang_dipilih.kategori}</div>
                <img src="${barang_dipilih.gambar}" alt="${barang_dipilih.nama}">
                <div class="card-info">
                    <h3>${barang_dipilih.nama}</h3>
                    <p class="brand">${barang_dipilih.brand}</p>
                    <p class="price">${hargaIDR}</p>
                    <div class="card-buttons">
                        <button onclick="viewPartDetail(${barang_dipilih.id})" class="btn-detail">Detail</button>
                        <button onclick="addToCart(${barang_dipilih.id})" class="btn-cart">Cart🛒
                            <i class="fas fa-shopping-cart"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
    }
    // --- EFEK 3D TILT HOLOGRAM (PURE JS) ---
    var cards = document.querySelectorAll('.car-card');
    
    cards.forEach(function(card) {
        card.addEventListener('mousemove', function(e) {
            var rect = card.getBoundingClientRect();
            var x = e.clientX - rect.left; // Posisi X kursor di dalam kartu
            var y = e.clientY - rect.top;  // Posisi Y kursor di dalam kartu
            
            var centerX = rect.width / 2;
            var centerY = rect.height / 2;
            
            // Kalkulasi rotasi (Maksimal 10 derajat agar tidak terlalu miring)
            var rotateX = ((y - centerY) / centerY) * -10; 
            var rotateY = ((x - centerX) / centerX) * 10;
            
            card.style.transform = 'perspective(1000px) rotateX(' + rotateX + 'deg) rotateY(' + rotateY + 'deg) scale3d(1.02, 1.02, 1.02)';
            card.style.transition = 'none'; // Matikan transisi saat kursor bergerak biar responsif
            card.style.zIndex = '10';       // Angkat kartu ke atas
        });
        
        card.addEventListener('mouseleave', function() {
            // Kembalikan kartu ke posisi normal dengan transisi mulus saat kursor pergi
            card.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)';
            card.style.transition = 'transform 0.5s ease';
            card.style.zIndex = '1';
        });
    });
}

var searchBar = document.getElementById('searchBar');

if (searchBar) {
    searchBar.addEventListener('keyup', function (e) {
        var keyword = e.target.value.toLowerCase();
        var hasil_filter = [];

        // looping biasa aja biar keliatan ngerjain sendiri
        for (var i = 0; i < spareparts.length; i++) {
            var barang = spareparts[i];

            if (
                barang.nama.toLowerCase().indexOf(keyword) !== -1 ||
                barang.brand.toLowerCase().indexOf(keyword) !== -1
            ) {
                hasil_filter.push(barang);
            }
        }

        displayAllParts(hasil_filter);
    });
}

function displayPartDetail() {
    var params = new URLSearchParams(window.location.search);
    var partId = params.get('id');
    var item = null;

    // looping buat nyari barang sesuai ID-nya
    for (var i = 0; i < spareparts.length; i++) {
        if (spareparts[i].id == partId) {
            item = spareparts[i];
            break;
        }
    }

    if (item) {
        var detailContainer = document.getElementById('carDetail');
        if (!detailContainer) return;

        var specHtml = '';

        for (var key in item.spesifikasi) {
            if (item.spesifikasi.hasOwnProperty(key)) {
                specHtml += `<li><strong>${key.toUpperCase()}:</strong> ${item.spesifikasi[key]}</li>`;
            }
        }

        var hargaIDR = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR'
        }).format(item.harga);

        detailContainer.innerHTML = `
            <div class="detail-wrapper">
                <div class="detail-left">
                    <img src="${item.gambar}" class="detail-img">
                </div>
                <div class="detail-right">
                    <span class="badge">${item.kategori}</span>
                    <h1>${item.nama}</h1>
                    <h2 class="price-detail">${hargaIDR}</h2>
                    <hr>
                    <h3>Technical Specs:</h3>
                    <ul class="spec-list">${specHtml}</ul>
                    <div class="description">
                        <h3>Description:</h3>
                        <p>${item.deskripsi}</p>
                    </div>
                    <button onclick="addToCart(${item.id})" class="btn-buy">Add to Garage Cart</button>
                </div>
            </div>
        `;
    }
}

function viewPartDetail(id) {
    window.location.href = 'detail.html?id=' + id;
}

let cart = JSON.parse(localStorage.getItem('grandong_cart')) || [];

function updateCartUI() {
    var cartCount = document.getElementById('cart-count');

    if (cartCount) {
        // Hitung total kuantitas barang, bukan cuma panjang array-nya
        var totalItems = 0;
        for (var i = 0; i < cart.length; i++) {
            totalItems += cart[i].quantity;
        }
        cartCount.innerText = totalItems;
    }

    // DISERAGAMKAN: Pakai 'grandong_cart' biar gak tabrakan!
    localStorage.setItem('grandong_cart', JSON.stringify(cart));
}

function addToCart(partId) {
    var product = null;

    // Gunakan == agar ID string dari HTML cocok dengan ID number di array
    for (var i = 0; i < spareparts.length; i++) {
        if (spareparts[i].id == partId) { 
            product = spareparts[i];
            break;
        }
    }

    if (product) {
        var existingItem = null;

        // Gunakan == juga di pengecekan item keranjang
        for (var j = 0; j < cart.length; j++) {
            if (cart[j].id == partId) {
                existingItem = cart[j];
                break;
            }
        }

        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            var barang_baru = {};
            for (var key in product) {
                barang_baru[key] = product[key];
            }

            // Normalisasi: Pastikan property 'nama' selalu ada 
            // baik dari database (nama_part) maupun hardcode array (nama)
            if (!barang_baru.nama && barang_baru.nama_part) {
                barang_baru.nama = barang_baru.nama_part;
            }

            barang_baru.quantity = 1;
            cart.push(barang_baru);
        }

        // Simpan data ke storage browser
        saveCart();
        
        // Ambil nama untuk display toast gahar
        var namaTampil = product.nama || product.nama_part;
        showGaharToast(namaTampil + ' Tucked In Garage 🏎️🔥');
        updateCartCount();
    } else {
        console.error("Komponen dengan ID " + partId + " gagal dimasukkan ke sarang Grandong.");
    }
}

// --- SISTEM DISKON ---
let persenDiskon = 0; // Default 0 (Gak ada diskon)

function terapkanDiskon() {
    var kode = document.getElementById('kode-promo').value.toUpperCase().trim();
    if (kode === 'UAS100') { // Kode rahasia untuk demo
        persenDiskon = 0.10; // Diskon 10%
        showGaharToast("KODE DITERIMA! Diskon 10% Aktif 🔥");
    } else {
        persenDiskon = 0;
        showGaharToast("KODE TIDAK VALID ATAU EXPIRED!");
    }
    tampilkanPesanan(); // Hitung ulang total
}

function saveCart() {
    localStorage.setItem('grandong_cart', JSON.stringify(cart));
}

function updateCartCount() {
    var cartCountElement = document.getElementById('cart-count');

    if (cartCountElement) {
        var totalItems = 0;

        for (var i = 0; i < cart.length; i++) {
            totalItems = totalItems + cart[i].quantity;
        }

        cartCountElement.innerText = totalItems;
    }
}

// Fungsi hapus barang (pastikan ini ada di script.js)
function hapusBarang(id) {
    Swal.fire({
        title: 'YAKIN MAU BUANG?',
        text: "Barang ini bakal keluar dari garasi kamu, Bray! 💀",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ff2800', // Merah gahar
        cancelButtonColor: '#333',     // Hitam/Abu gelap
        confirmButtonText: 'YA, BUANG!',
        cancelButtonText: 'GAK JADI',
        background: '#1a1a1a',         // Background pop-up item
        color: '#ffffff',              // Warna teks putih
        iconColor: '#ff2800'
    }).then((result) => {
        if (result.isConfirmed) {
            // Logika hapus barang yang lama
            cart = cart.filter(function(item) {
                return item.id !== id;
            });

            saveCart();
            tampilkanPesanan();
            updateCartCount();

            // Kasih feedback kalau udah kehapus
            showGaharToast("BARANG DIBUANG DARI GARASI! 🔥");
        }
    });
}

// Fungsi utama buat nampilin list di cart.html
function tampilkanPesanan() {
    var wadah_list = document.getElementById('cart-items-list');
    var total_harga_elemen = document.getElementById('cart-total-price');
    
    if (!wadah_list) return;

    // Ambil data terbaru dari storage biar ga kosong
    var data_paling_baru = localStorage.getItem('grandong_cart');
    if (data_paling_baru) {
        cart = JSON.parse(data_paling_baru);
    }

    if (!cart || cart.length === 0) {
        wadah_list.innerHTML = '<p style="text-align:center; padding: 50px; font-family: Inter;">Wah, garasi kamu masih kosong nih, Bray!</p>';
        if (total_harga_elemen) total_harga_elemen.innerText = "Rp 0";
        return;
    }

    var html_keranjang = '';
    var total_belanja = 0;

    for (var i = 0; i < cart.length; i++) {
        var item = cart[i];
        total_belanja += item.harga * item.quantity;

        var format_rp = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(item.harga);

        html_keranjang += `
            <div class="cart-item">
                <img src="${item.gambar}" alt="${item.nama}">
                <div class="item-info">
                    <h4>${item.nama}</h4>
                    <p>${item.brand} | Jumlah: ${item.quantity}</p>
                    <p style="color: #27ae60; font-weight: bold;">${format_rp}</p>
                </div>
                <button class="btn-remove" onclick="hapusBarang(${item.id})">Hapus</button>
            </div>
        `;
    }

    wadah_list.innerHTML = html_keranjang;
    if (total_harga_elemen) {
        total_harga_elemen.innerText = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(total_belanja);
    }

    var subtotal = 0;
    for (var i = 0; i < cart.length; i++) {
        subtotal += cart[i].harga * cart[i].quantity;
    }

    var nominalDiskon = subtotal * persenDiskon;
    var subtotalSetelahDiskon = subtotal - nominalDiskon;
    var pajak = subtotalSetelahDiskon * 0.11; 
    var total_akhir = subtotalSetelahDiskon + pajak;

    if (document.getElementById('cart-subtotal')) {
        document.getElementById('cart-subtotal').innerText = formatRupiah(subtotal);
        
        var barisDiskon = document.getElementById('baris-diskon');
        if (persenDiskon > 0) {
            barisDiskon.style.display = 'flex';
            document.getElementById('cart-diskon').innerText = "- " + formatRupiah(nominalDiskon);
        } else {
            barisDiskon.style.display = 'none';
        }

        document.getElementById('cart-pajak').innerText = formatRupiah(pajak);
        document.getElementById('cart-total-price').innerText = formatRupiah(total_akhir);
    }
}

function formatRupiah(angka) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(angka);
}

function checkoutWhatsApp() {
    if (cart.length === 0) {
        showGaharToast("Pilih barang dulu baru checkout! 🛒");
        return;
    }

    // Tangkap semua elemen
    var inputNama = document.getElementById('wa-nama');
    var inputKontak = document.getElementById('wa-kontak');
    var inputKota = document.getElementById('wa-kota');
    var inputKodePos = document.getElementById('wa-kodepos');
    var inputAlamat = document.getElementById('wa-alamat');

    // PENGAMAN: Pastikan form tidak hilang
    if (!inputNama || !inputKontak || !inputKota || !inputKodePos || !inputAlamat) {
        alert("Sistem gagal membaca form! Pastikan HTML sudah benar.");
        return;
    }

    // Ekstrak teks (trim untuk hilangkan spasi kosong)
    var nama = inputNama.value.trim();
    var kontak = inputKontak.value.trim();
    var kota = inputKota.value.trim();
    var kodepos = inputKodePos.value.trim();
    var alamat = inputAlamat.value.trim();

    // VALIDASI KETAT: Jika ada satu saja yang kosong, tolak checkout!
    if (nama === "" || kontak === "" || kota === "" || kodepos === "" || alamat === "") {
        Swal.fire({
            icon: 'warning',
            title: 'Data Pengiriman Belum Lengkap!',
            text: 'Tolong isi semua kotak data pengiriman sebelum memproses pesanan, Bray!',
            background: '#1a1a1a',
            color: '#ffffff',
            confirmButtonColor: '#ff2800'
        });
        return;
    }

    var nomor_wa = "6281316338598";
    
    // FORMATTING INVOICE YANG RAPI DAN GAHAR
    var pesan = "💀 *INVOICE GRANDONG GARAGE* 💀\n";
    pesan += "============================\n\n";
    
    pesan += "*DATA PEMBELI:*\n";
    pesan += "Nama: " + nama + "\n";
    pesan += "Kontak: " + kontak + "\n";
    pesan += "Kota: " + kota + " (" + kodepos + ")\n";
    pesan += "Alamat: " + alamat + "\n\n";
    
    pesan += "============================\n";
    pesan += "*DAFTAR BELANJAAN:*\n";

    // --- GANTI HITUNGAN DI DALAM checkoutWhatsApp() JADI INI ---
    var subtotal = 0;
    for (var i = 0; i < cart.length; i++) {
        pesan += "- " + cart[i].nama + " (" + cart[i].quantity + "x)\n";
        subtotal += cart[i].harga * cart[i].quantity;
    }

    var nominalDiskon = subtotal * persenDiskon;
    var subtotalSetelahDiskon = subtotal - nominalDiskon;
    var pajak = subtotalSetelahDiskon * 0.11;
    var total = subtotalSetelahDiskon + pajak;

    pesan += "\n============================\n";
    pesan += "Subtotal: " + formatRupiah(subtotal) + "\n";
    if (persenDiskon > 0) {
        pesan += "Diskon Promo: - " + formatRupiah(nominalDiskon) + "\n";
    }
    pesan += "PPN (11%): " + formatRupiah(pajak) + "\n";
    pesan += "*TOTAL AKHIR: " + formatRupiah(total) + "*\n";
    pesan += "============================\n\n";

    // --- EASTER EGG: SUARA ENGINE START NO PROBLEM ---
    var engineSound = new Audio('https://www.myinstants.com/media/sounds/engine-start-no-problem.mp3');
    engineSound.volume = 0.8; // Set volume 80% biar pas menderu gak terlalu pecah
    engineSound.play();
    
    // Tampilkan toast tambahan
    showGaharToast("STARTING ENGINE... 🏁");

    var link_wa = "https://wa.me/" + nomor_wa + "?text=" + encodeURIComponent(pesan);
    window.open(link_wa, '_blank');
}

window.onload = function () {
    // 1. Ambil data keranjang dari storage
    var simpenan = localStorage.getItem('grandong_cart');
    if (simpenan) {
        try {
            cart = JSON.parse(simpenan);
        } catch (e) {
            cart = [];
        }
    } else {
        cart = [];
    }

    // 2. Update angka counter di navbar
    updateCartCount();

    // 3. Eksekusi khusus halaman Keranjang (Cart) - Tidak butuh fetch DB
    if (document.getElementById('cart-items-list')) {
        setTimeout(function() {
            tampilkanPesanan();
        }, 50);
        return; 
    }

    // 4. Eksekusi khusus halaman Katalog & Detail - Wajib Fetch DB dulu!
    var partList = document.getElementById('partList');
    
    // TAMPILKAN ANIMASI TENGKORAK GAHAR DI SINI!
    if (partList) {
        partList.innerHTML = `
            <div class="loader-container">
                <svg class="skull-loader" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                    <!-- Kepala Atas -->
                    <g fill="#ff0000">
                        <path d="M20,50 C20,15 80,15 80,50 C80,65 70,70 65,70 L35,70 C30,70 20,65 20,50 Z" />
                        <!-- Mata (Bolong Hitam) -->
                        <circle cx="35" cy="45" r="9" fill="#1a1a1a" />
                        <circle cx="65" cy="45" r="9" fill="#1a1a1a" />
                        <!-- Hidung -->
                        <path d="M50,55 L45,67 L55,67 Z" fill="#1a1a1a" />
                    </g>
                    <!-- Rahang Bawah (Yang Bisa Mangap) -->
                    <g class="skull-jaw" fill="#ff0000">
                        <path d="M35,73 L65,73 L58,90 L42,90 Z" />
                        <!-- Garis Gigi -->
                        <line x1="42" y1="73" x2="42" y2="90" stroke="#1a1a1a" stroke-width="2" />
                        <line x1="50" y1="73" x2="50" y2="90" stroke="#1a1a1a" stroke-width="2" />
                        <line x1="58" y1="73" x2="58" y2="90" stroke="#1a1a1a" stroke-width="2" />
                    </g>
                </svg>
                <div class="loader-text">AWAKENING GRANDONG...</div>
            </div>
        `;
    }

    // TAHAN 1.2 DETIK SEBELUM FETCH DATA
    setTimeout(function() {
        fetch('get_parts.php')
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                if (data.error) {
                    if (partList) partList.innerHTML = '<p style="color:red; text-align:center;">Error: ' + data.error + '</p>';
                    return;
                }

                // Suntikkan data dari database ke variabel global JS
                spareparts = data;

                // Render halaman sesuai posisi user saat ini
                if (document.getElementById('partList')) {
                    displayAllParts(spareparts); // Kartu baru digambar setelah 1.2 detik
                } 
                if (document.getElementById('carDetail')) {
                    displayPartDetail();
                } 
            })
            .catch(function(err) {
                if (partList) partList.innerHTML = '<p style="color:red; text-align:center;">Koneksi gagal: ' + err + '</p>';
                console.error("Fetch error:", err);
            });
    }, 1200); 
};

// ==========================================
// FUNGSI NOTIFIKASI TOAST GAHAR
// ==========================================
function showGaharToast(pesan) {
    var container = document.getElementById('toast-container');
    // Jika di cart.html ga ada container toast, buat otomatis biar ga bikin script crash!
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        document.body.appendChild(container);
    }

    var toast = document.createElement('div');
    toast.className = 'toast-gahar';
    toast.innerHTML = `Nice! <span>${pesan}</span>`;
    container.appendChild(toast);

    setTimeout(function () {
        toast.classList.add('toast-exit');
        setTimeout(function () {
            toast.remove();
        }, 400);
    }, 3000);
}

function terapkanFilterDanSort() {
    // 1. Tangkap nilai dari kedua dropdown
    var kategoriDipilih = document.getElementById('kategori-select').value;
    var sortDipilih = document.getElementById('sort-select').value;

    var hasilData = [];

    // 2. PROSES FILTERING KATEGORI
    if (kategoriDipilih === 'Semua') {
        hasilData = spareparts.slice(); // Salin semua data jika pilih "All Parts"
    } else {
        hasilData = spareparts.filter(function(item) {
            return item.kategori.toLowerCase() === kategoriDipilih.toLowerCase();
        });
    }

    // 3. PROSES SORTING HARGA
    if (sortDipilih === 'termurah') {
        hasilData.sort(function(a, b) {
            return a.harga - b.harga; // Urutkan dari kecil ke besar
        });
    } else if (sortDipilih === 'termahal') {
        hasilData.sort(function(a, b) {
            return b.harga - a.harga; // Urutkan dari besar ke kecil
        });
    }
    // Jika 'default', array dibiarkan sesuai urutan asli dari database

    // 4. LEMPAR KE UI
    displayAllParts(hasilData);
}
