// public/js/admin-dashboard.js

let html5QrCode = null;

const tabInfo = {
    'beranda': {
        title: 'Beranda Dashboard',
        subtitle: 'Pusat kontrol dan operasional klub renang'
    },
    'kehadiran-atlet': {
        title: 'Kehadiran Atlet',
        subtitle: 'Kelola dan pantau presensi latihan atlet'
    },
    'kehadiran-pelatih': {
        title: 'Kehadiran Pelatih',
        subtitle: 'Kelola dan pantau presensi latihan pelatih'
    },
    'scanner-kamera': {
        title: 'Scan Absensi (Kamera)',
        subtitle: 'Pemindai QR Code otomatis kehadiran latihan'
    },
    'kelola-akun': {
        title: 'Kelola Akun & Barcode',
        subtitle: 'Pendaftaran anggota baru dan cetak kartu QR'
    }
};

function switchTab(target) {
    const tabs = Object.keys(tabInfo);

    tabs.forEach(tab => {
        const sec = document.getElementById('tab-' + tab);
        const btn = document.getElementById('btn-' + tab);

        if (sec && btn) {
            if (tab === target) {
                sec.classList.remove('hidden');
                btn.classList.add('bg-[#0275d8]', 'text-white');
                btn.classList.remove('text-slate-300', 'hover:bg-white/10', 'hover:text-white');
            } else {
                sec.classList.add('hidden');
                btn.classList.remove('bg-[#0275d8]', 'text-white');
                btn.classList.add('text-slate-300', 'hover:bg-white/10', 'hover:text-white');
            }
        }
    });

    // Update Judul & Subjudul di Header Atas
    const pageTitle = document.getElementById('page-title');
    const pageSubtitle = document.getElementById('page-subtitle');
    if (pageTitle && tabInfo[target]) {
        pageTitle.textContent = tabInfo[target].title;
        pageSubtitle.textContent = tabInfo[target].subtitle;
    }

    if (target !== 'scanner-kamera') {
        stopScanner();
    }
}

function showQrModal(name, role, token) {
    document.getElementById('modalName').textContent = name;
    document.getElementById('modalRole').textContent = role;
    document.getElementById('modalToken').textContent = token;
    document.getElementById('modalQrImg').src = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(token)}`;
    document.getElementById('qrModal').classList.remove('hidden');
}

function closeQrModal() {
    document.getElementById('qrModal').classList.add('hidden');
}

function handleCreateAccount(e) {
    e.preventDefault();
    const name = document.getElementById('acc_name').value;
    const role = document.getElementById('acc_role').value;
    const phone = document.getElementById('acc_phone').value || '-';
    const token = 'TB-' + (role === 'pelatih' ? 'P' : 'M') + '-' + Math.floor(100 + Math.random() * 900);

    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td class="py-3 px-3 font-semibold text-slate-900">${name}</td>
        <td class="py-3 px-3"><span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-sky-50 text-sky-700 border border-sky-200">${role}</span></td>
        <td class="py-3 px-3 font-mono text-slate-500">${token}</td>
        <td class="py-3 px-3 text-slate-500">${phone}</td>
        <td class="py-3 px-3 text-center">
            <button onclick="showQrModal('${name}', '${role}', '${token}')" class="bg-slate-100 hover:bg-sky-50 text-sky-600 border border-slate-200 px-3 py-1.5 rounded-lg text-xs font-semibold">Lihat QR</button>
        </td>
    `;
    document.getElementById('tabel-akun-body').prepend(tr);
    alert(`Akun ${name} (${role}) berhasil didaftarkan! Token: ${token}`);
    e.target.reset();
}

function handleManualAbsen(e) {
    e.preventDefault();
    const nama = document.getElementById('manual_nama').value;
    const sesi = document.getElementById('manual_sesi').value;
    const status = document.getElementById('manual_status').value;
    const now = new Date();
    const timeStr = String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0') + ' WIB';

    tambahDataKeTabelPresensi(nama, timeStr, sesi, status, 'tabel-presensi-body');
    alert(`Presensi atlet ${nama} tersimpan.`);
}

function handleManualAbsenPelatih(e) {
    e.preventDefault();
    const nama = document.getElementById('manual_pelatih_nama').value;
    const sesi = document.getElementById('manual_pelatih_sesi').value;
    const status = document.getElementById('manual_pelatih_status').value;
    const now = new Date();
    const timeStr = String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0') + ' WIB';

    tambahDataKeTabelPresensi(nama, timeStr, sesi, status, 'tabel-presensi-pelatih-body');
    alert(`Presensi pelatih ${nama} tersimpan.`);
}

function startScanner() {
    if (!html5QrCode) {
        html5QrCode = new Html5Qrcode("reader");
    }
    html5QrCode.start(
        { facingMode: "environment" },
        { fps: 10, qrbox: { width: 250, height: 250 } },
        (decodedText) => {
            prosesHasilScan(decodedText);
            html5QrCode.pause();
            setTimeout(() => {
                if (html5QrCode) html5QrCode.resume();
            }, 3000);
        }
    ).catch(err => alert("Kamera error: " + err));
}

function stopScanner() {
    if (html5QrCode) {
        html5QrCode.stop().then(() => {
            document.getElementById('reader').innerHTML = '';
            html5QrCode = null;
        }).catch(() => {});
    }
}

function prosesHasilScan(token) {
    const now = new Date();
    const jam = now.getHours();
    const menit = now.getMinutes();
    const totalMenitSekarang = (jam * 60) + menit;
    const batasMenit1530 = (15 * 60) + 30;
    const timeStr = String(jam).padStart(2, '0') + ':' + String(menit).padStart(2, '0') + ' WIB';

    let status = 'HADIR';
    let notes = 'Hadir Tepat Waktu';

    if (totalMenitSekarang > batasMenit1530) {
        const selisih = totalMenitSekarang - batasMenit1530;
        status = 'TELAT';
        notes = `Terlambat ${selisih} Menit (Batas 15:30 WIB)`;
    }

    document.getElementById('scan-placeholder').classList.add('hidden');
    document.getElementById('scan-result-card').classList.remove('hidden');
    document.getElementById('res-name').textContent = "Member (" + token + ")";
    document.getElementById('res-token').textContent = "Token: " + token;
    document.getElementById('res-time').textContent = timeStr;
    document.getElementById('res-notes').textContent = notes;

    const badge = document.getElementById('res-status-badge');
    const box = document.getElementById('result-box');
    badge.textContent = status;

    if (status === 'HADIR') {
        box.className = "p-4 rounded-xl border bg-emerald-50 border-emerald-200 text-emerald-900";
        badge.className = "px-2.5 py-1 rounded-full text-xs font-bold uppercase bg-emerald-200 text-emerald-800";
    } else {
        box.className = "p-4 rounded-xl border bg-red-50 border-red-200 text-red-900";
        badge.className = "px-2.5 py-1 rounded-full text-xs font-bold uppercase bg-red-200 text-red-800";
    }

    // Otomatis arahkan ke tabel pelatih jika token diawali 'TB-P-', selain itu ke tabel atlet
    const targetTabel = token.toUpperCase().startsWith('TB-P-') 
        ? 'tabel-presensi-pelatih-body' 
        : 'tabel-presensi-body';

    tambahDataKeTabelPresensi("Member (" + token + ")", timeStr, "Sesi Sore (15:30 WIB)", status, targetTabel);
}

function tambahDataKeTabelPresensi(nama, waktu, sesi, status, targetBodyId = 'tabel-presensi-body') {
    const tbody = document.getElementById(targetBodyId);
    if (!tbody) return;

    const badgeClass = (status === 'TELAT' || status === 'ALFA')
        ? 'bg-red-50 text-red-700 border border-red-200'
        : (status === 'IZIN' || status === 'SAKIT')
        ? 'bg-amber-50 text-amber-700 border border-amber-200'
        : 'bg-emerald-50 text-emerald-700 border border-emerald-200';

    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td class="py-3 px-3 font-semibold text-slate-900">${nama}</td>
        <td class="py-3 px-3 text-slate-500">${waktu}</td>
        <td class="py-3 px-3">${sesi}</td>
        <td class="py-3 px-3 text-center"><span class="px-2.5 py-1 rounded-full text-[10px] font-semibold ${badgeClass}">${status}</span></td>
    `;
    tbody.prepend(tr);
}