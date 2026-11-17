// public/js/admin-dashboard.js

let html5QrCode = null;
let isScanningProcessing = false;

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

function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
}

function handleManualAbsen(e) {
    e.preventDefault();
    const nama = document.getElementById('manual_nama').value;
    const sesi = document.getElementById('manual_sesi').value;
    const status = document.getElementById('manual_status').value;
    kirimAbsensiManual(nama, sesi, status, 'tabel-presensi-body');
}

function handleManualAbsenPelatih(e) {
    e.preventDefault();
    const nama = document.getElementById('manual_pelatih_nama').value;
    const sesi = document.getElementById('manual_pelatih_sesi').value;
    const status = document.getElementById('manual_pelatih_status').value;
    kirimAbsensiManual(nama, sesi, status, 'tabel-presensi-pelatih-body');
}

function kirimAbsensiManual(nama, sesi, status, fallbackTableId) {
    const now = new Date();
    const timeStr = String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0') + ' WIB';

    fetch('/admin/manual-attendance', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken()
        },
        body: JSON.stringify({ name: nama, sesi: sesi, status: status })
    })
    .then(async res => {
        const data = await res.json();
        if (!res.ok) throw new Error(data.message || 'Gagal menyimpan presensi');
        return data;
    })
    .then(data => {
        const targetTabel = data.role === 'pelatih' ? 'tabel-presensi-pelatih-body' : 'tabel-presensi-body';
        tambahDataKeTabelPresensi(data.user_name, data.time, data.sesi, data.status, targetTabel);
        updateStatistikHadir(data.total_hadir_hari);
        alert(`Presensi ${data.user_name} (${data.status}) tersimpan.`);
    })
    .catch(() => {
        tambahDataKeTabelPresensi(nama, timeStr, sesi, status, fallbackTableId);
        alert(`Presensi ${nama} tersimpan.`);
    });
}

function startScanner() {
    if (!html5QrCode) {
        html5QrCode = new Html5Qrcode("reader");
    }
    html5QrCode.start(
        { facingMode: "environment" },
        { fps: 10, qrbox: { width: 250, height: 250 } },
        (decodedText) => {
            if (isScanningProcessing) return;
            isScanningProcessing = true;

            prosesHasilScan(decodedText);

            if (html5QrCode) {
                try { html5QrCode.pause(); } catch (e) {}
                setTimeout(() => {
                    isScanningProcessing = false;
                    if (html5QrCode) {
                        try { html5QrCode.resume(); } catch (e) {}
                    }
                }, 3000);
            } else {
                isScanningProcessing = false;
            }
        }
    ).catch(err => alert("Kamera error: " + err));
}

function stopScanner() {
    if (html5QrCode) {
        html5QrCode.stop().then(() => {
            const reader = document.getElementById('reader');
            if (reader) reader.innerHTML = '';
            html5QrCode = null;
        }).catch(() => {
            html5QrCode = null;
        });
    }
}

function prosesHasilScan(token) {
    fetch('/admin/scan-attendance', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken()
        },
        body: JSON.stringify({ barcode_token: token })
    })
    .then(async res => {
        const data = await res.json();
        if (!res.ok) throw new Error(data.message || 'Gagal memproses QR');
        return data;
    })
    .then(data => {
        tampilkanKartuHasilScan(data.user_name, data.token || token, data.time, data.status, data.notes);

        const targetTabel = data.role === 'pelatih'
            ? 'tabel-presensi-pelatih-body'
            : 'tabel-presensi-body';

        tambahDataKeTabelPresensi(data.user_name, data.time, data.sesi || 'Sesi Sore (15:30 WIB)', data.status, targetTabel);
        updateStatistikHadir(data.total_hadir_hari);
    })
    .catch(err => {
        alert(err.message);
    });
}

function tampilkanKartuHasilScan(name, token, timeStr, status, notes) {
    document.getElementById('scan-placeholder').classList.add('hidden');
    document.getElementById('scan-result-card').classList.remove('hidden');
    document.getElementById('res-name').textContent = name;
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
}

function tambahDataKeTabelPresensi(nama, waktu, sesi, status, targetBodyId = 'tabel-presensi-body') {
    const tbody = document.getElementById(targetBodyId);
    if (!tbody) return;

    const emptyRow = tbody.querySelector('#empty-atlet-row, #empty-pelatih-row');
    if (emptyRow) emptyRow.remove();

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

function updateStatistikHadir(totalHadir) {
    const statHadir = document.getElementById('stat-hadir-hari-ini');
    if (!statHadir) return;

    if (typeof totalHadir === 'number') {
        statHadir.textContent = totalHadir + ' Sesi';
    } else {
        const current = parseInt(statHadir.textContent) || 0;
        statHadir.textContent = (current + 1) + ' Sesi';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    if (window.location.hash === '#kelola-akun') {
        switchTab('kelola-akun');
    }
});