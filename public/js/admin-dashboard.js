// public/js/admin-dashboard.js

let currentAdminTab = 'beranda';
let html5QrCode = null;
let isScanning = false;

const adminTabInfo = {
    'beranda': {
        title: 'Beranda Admin',
        subtitle: 'Ringkasan operasional klub, kehadiran, dan manajemen akun'
    },
    'scan-qr': {
        title: 'Scan QR Absensi',
        subtitle: 'Scan barcode / QR Code atlet atau pelatih menggunakan kamera'
    },
    'kehadiran-atlet': {
        title: 'Kehadiran Atlet',
        subtitle: 'Pantau presensi harian atlet dan input presensi manual'
    },
    'kehadiran-pelatih': {
        title: 'Kehadiran Pelatih',
        subtitle: 'Pantau presensi harian pelatih dan input presensi manual'
    },
    'event': {
        title: 'Daftar Event',
        subtitle: 'Jadwal kompetisi dan kejuaraan renang mendatang'
    },
    'akun-pelatih': {
        title: 'Akun Pelatih',
        subtitle: 'Kelola akun pelatih beserta penempatan kelompok latihannya'
    },
    'akun-atlet': {
        title: 'Akun Atlet',
        subtitle: 'Kelola akun atlet (murid) beserta penempatan kelompok latihannya'
    }
};

function toggleAdminSidebar(open) {
    const sidebar = document.getElementById('admin-sidebar');
    const overlay = document.getElementById('admin-sidebar-overlay');
    if (!sidebar || !overlay) return;

    if (open) {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
    } else {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    }
}

function switchAdminTab(target) {
    if (!adminTabInfo[target]) {
        target = 'beranda';
    }

    currentAdminTab = target;
    Object.keys(adminTabInfo).forEach(tab => {
        const sec = document.getElementById('tab-admin-' + tab);
        const btn = document.getElementById('btn-admin-' + tab);

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

    const title = document.getElementById('admin-page-title');
    const subtitle = document.getElementById('admin-page-subtitle');
    if (title && adminTabInfo[target]) {
        title.textContent = adminTabInfo[target].title;
        subtitle.textContent = adminTabInfo[target].subtitle;
    }

    if (target === 'scan-qr') {
        startCameraScanner();
    } else {
        stopCameraScanner();
    }

    toggleAdminSidebar(false);
}

function bukaKalenderAdmin() {
    const input = document.getElementById('admin-filter-tanggal');
    if (!input) return;
    if (typeof input.showPicker === 'function') {
        input.showPicker();
    } else {
        input.focus();
        input.click();
    }
}

function gantiTanggalAdmin(tanggalBaru) {
    if (!tanggalBaru) return;
    const url = new URL(window.location.href);
    url.searchParams.set('date', tanggalBaru);
    url.hash = currentAdminTab;
    window.location.href = url.toString();
}

function openAdminModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeAdminModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function openAdminPresensiModal(roleFilter) {
    const select = document.getElementById('admin_manual_nama');
    const title = document.getElementById('modal-presensi-title');
    const label = document.getElementById('label-admin-manual-nama');

    if (title) {
        title.textContent = roleFilter === 'pelatih' ? 'Input Presensi Pelatih' : 'Input Presensi Atlet';
    }
    if (label) {
        label.textContent = roleFilter === 'pelatih' ? 'Nama Pelatih' : 'Nama Atlet';
    }

    if (select) {
        let firstMatchValue = '';
        Array.from(select.options).forEach(opt => {
            const optRole = opt.getAttribute('data-role');
            if (!optRole) return;
            const match = optRole === roleFilter;
            opt.hidden = !match;
            opt.disabled = !match;
            if (match && !firstMatchValue) {
                firstMatchValue = opt.value;
            }
        });
        select.value = firstMatchValue;
    }

    openAdminModal('modal-admin-presensi');
}

function startCameraScanner() {
    const statusEl = document.getElementById('camera-status');
    if (typeof Html5Qrcode === 'undefined') {
        if (statusEl) statusEl.textContent = 'Library kamera tidak termuat. Gunakan input manual.';
        return;
    }

    if (isScanning) return;

    if (statusEl) statusEl.textContent = 'Menyalakan kamera...';
    html5QrCode = new Html5Qrcode('qr-reader');

    html5QrCode.start(
        { facingMode: 'environment' },
        { fps: 10, qrbox: { width: 220, height: 220 } },
        (decodedText) => {
            if (!isScanning) return;
            stopCameraScanner();
            prosesScanToken(decodedText.trim());
        },
        () => {}
    ).then(() => {
        isScanning = true;
        if (statusEl) statusEl.textContent = 'Kamera aktif. Arahkan QR / Barcode ke kotak scan.';
    }).catch(err => {
        isScanning = false;
        if (statusEl) statusEl.textContent = 'Kamera tidak dapat diakses (' + err + '). Gunakan input manual di bawah.';
    });
}

function stopCameraScanner() {
    if (html5QrCode && isScanning) {
        isScanning = false;
        html5QrCode.stop().then(() => {
            html5QrCode.clear();
        }).catch(() => {});
    }
}

function toggleFieldKelompok() {
    const role = document.getElementById('akun_role')?.value;
    const wrap = document.getElementById('wrap_akun_kelompok');
    if (wrap) {
        wrap.classList.toggle('hidden', role === 'admin');
    }
}

function getAdminCsrf() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
}

function openTambahAkun(defaultRole = 'pelatih') {
    document.getElementById('form-tambah-akun')?.reset();
    document.getElementById('akun_id').value = '';
    document.getElementById('akun_role').value = defaultRole;
    document.getElementById('modal-akun-title').textContent = defaultRole === 'murid' ? 'Tambah Akun Atlet' : 'Tambah Akun Pelatih';
    document.getElementById('btn-simpan-akun').textContent = 'Simpan Akun';

    const passInput = document.getElementById('akun_password');
    const passLabel = document.getElementById('label_akun_password');
    if (passInput) {
        passInput.required = true;
        passInput.placeholder = 'Minimal 6 karakter';
    }
    if (passLabel) passLabel.textContent = 'Password';

    toggleFieldKelompok();
    openAdminModal('modal-tambah-akun');
}

function editAkun(id, name, email, phone, role, kelompok) {
    document.getElementById('akun_id').value = id;
    document.getElementById('akun_nama').value = name || '';
    document.getElementById('akun_email').value = email || '';
    document.getElementById('akun_phone').value = phone || '';
    document.getElementById('akun_role').value = role || 'murid';
    document.getElementById('akun_kelompok').value = kelompok || 'Learn To Swim';

    const passInput = document.getElementById('akun_password');
    const passLabel = document.getElementById('label_akun_password');
    if (passInput) {
        passInput.value = '';
        passInput.required = false;
        passInput.placeholder = 'Kosongkan jika tidak ingin mengubah password';
    }
    if (passLabel) passLabel.textContent = 'Password Baru (Opsional)';

    document.getElementById('modal-akun-title').textContent = 'Edit Akun';
    document.getElementById('btn-simpan-akun').textContent = 'Update Akun';

    toggleFieldKelompok();
    openAdminModal('modal-tambah-akun');
}

function renderRowUser(u) {
    const kel = u.kelompok || 'Learn To Swim';
    const kelClass = kel === 'Champions'
        ? 'bg-amber-50 text-amber-700 border-amber-200'
        : 'bg-sky-50 text-sky-700 border-sky-200';

    const safeName = (u.name || '').replace(/'/g, "\\'");
    const safeEmail = (u.email || '').replace(/'/g, "\\'");
    const safePhone = (u.phone || '').replace(/'/g, "\\'");
    const safeKel = kel.replace(/'/g, "\\'");

    return `
        <td class="py-3 px-3 font-semibold text-slate-900">${u.name}</td>
        <td class="py-3 px-3 text-slate-600">${u.email}</td>
        <td class="py-3 px-3 text-slate-600">${u.phone || '-'}</td>
        <td class="py-3 px-3 text-center">
            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border ${kelClass}">${kel}</span>
        </td>
        <td class="py-3 px-3 text-center font-mono text-slate-600">${u.barcode_token || '-'}</td>
        <td class="py-3 px-3 text-center space-x-1.5">
            <button type="button"
                onclick="editAkun(${u.id}, '${safeName}', '${safeEmail}', '${safePhone}', '${u.role}', '${safeKel}')"
                class="bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 px-2.5 py-1 rounded-lg text-[11px] font-semibold transition">
                Edit
            </button>
            <button type="button"
                onclick="hapusAkun(${u.id}, '${safeName}')"
                class="bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 px-2.5 py-1 rounded-lg text-[11px] font-semibold transition">
                Hapus
            </button>
        </td>
    `;
}

function handleSimpanAkun(e) {
    e.preventDefault();

    const id = document.getElementById('akun_id').value;
    const isEdit = Boolean(id);

    const payload = {
        name: document.getElementById('akun_nama').value.trim(),
        email: document.getElementById('akun_email').value.trim(),
        phone: document.getElementById('akun_phone').value.trim(),
        role: document.getElementById('akun_role').value,
        kelompok: document.getElementById('akun_kelompok').value,
        password: document.getElementById('akun_password').value
    };

    const url = isEdit ? `/admin/user/${id}` : '/admin/store-user';
    const method = isEdit ? 'PUT' : 'POST';

    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': getAdminCsrf()
        },
        body: JSON.stringify(payload)
    })
    .then(async res => {
        const data = await res.json();
        if (!res.ok) throw new Error(data.message || `Server error (${res.status})`);
        return data;
    })
    .then(data => {
        const u = data.user;

        const existingRow = document.getElementById('row-user-' + u.id);
        if (existingRow) existingRow.remove();

        const targetTbodyId = u.role === 'pelatih' ? 'tabel-akun-pelatih' : (u.role === 'murid' ? 'tabel-akun-murid' : null);
        if (targetTbodyId) {
            const tbody = document.getElementById(targetTbodyId);
            if (tbody) {
                tbody.querySelector(u.role === 'pelatih' ? '#empty-akun-pelatih' : '#empty-akun-murid')?.remove();
                const tr = document.createElement('tr');
                tr.id = 'row-user-' + u.id;
                tr.innerHTML = renderRowUser(u);
                tbody.prepend(tr);
            }
        }

        closeAdminModal('modal-tambah-akun');
        e.target.reset();
        alert(data.message || 'Akun berhasil disimpan.');
    })
    .catch(err => alert('Gagal menyimpan akun: ' + err.message));
}

function hapusAkun(id, nama) {
    if (!confirm(`Yakin ingin menghapus akun "${nama}"?`)) return;

    fetch(`/admin/user/${id}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': getAdminCsrf()
        }
    })
    .then(async res => {
        const data = await res.json();
        if (!res.ok) throw new Error(data.message || `Gagal menghapus (${res.status})`);
        return data;
    })
    .then(data => {
        document.getElementById('row-user-' + id)?.remove();
        alert(data.message || 'Akun berhasil dihapus.');
    })
    .catch(err => alert('Gagal menghapus akun: ' + err.message));
}

function hitungStatusAdminEvent(tglMulai, tglSelesai) {
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    const start = new Date(tglMulai + 'T00:00:00');
    const end = tglSelesai ? new Date(tglSelesai + 'T00:00:00') : new Date(start.getTime());

    const bulanNama = ['JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN', 'JUL', 'AGU', 'SEP', 'OKT', 'NOV', 'DES'];
    const dayNum = String(start.getDate()).padStart(2, '0');
    const monthShort = bulanNama[start.getMonth()] || '';

    let statusText = 'Selesai';
    let statusClass = 'bg-slate-100 text-slate-500 border-slate-200';

    if (today >= start && today <= end) {
        statusText = 'Sedang berlangsung';
        statusClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
    } else if (today < start) {
        const diffDays = Math.round((start - today) / (1000 * 60 * 60 * 24));
        statusText = `${diffDays} hari lagi`;
        statusClass = 'bg-sky-50 text-sky-700 border-sky-200';
    }

    return { dayNum, monthShort, statusText, statusClass };
}

function handleAdminSimpanEvent(e) {
    e.preventDefault();

    const payload = {
        nama_event: document.getElementById('admin_ev_nama').value.trim(),
        tingkat: document.getElementById('admin_ev_tingkat').value,
        lokasi: document.getElementById('admin_ev_lokasi').value.trim(),
        tanggal_mulai: document.getElementById('admin_ev_tgl_mulai').value,
        tanggal_selesai: document.getElementById('admin_ev_tgl_selesai').value || null
    };

    fetch('/pelatih/store-event', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': getAdminCsrf()
        },
        body: JSON.stringify(payload)
    })
    .then(async res => {
        const data = await res.json();
        if (!res.ok) throw new Error(data.message || `Server error (${res.status})`);
        return data;
    })
    .then(data => {
        const ev = data.event;
        const container = document.getElementById('admin-daftar-event-container');
        if (container) {
            container.querySelector('#admin-empty-event-card')?.remove();

            const info = hitungStatusAdminEvent(ev.tanggal_mulai, ev.tanggal_selesai);
            const safeNama = (ev.nama_event || '').replace(/'/g, "\\'");

            const card = document.createElement('div');
            card.id = 'admin-card-event-' + ev.id;
            card.className = 'bg-white text-slate-800 rounded-2xl p-5 border border-slate-200 shadow-sm hover:border-sky-300 transition flex flex-col sm:flex-row sm:items-center justify-between gap-4';
            card.innerHTML = `
                <div class="flex items-start sm:items-center gap-4">
                    <div class="w-14 h-14 rounded-xl bg-sky-50 border border-sky-200 flex flex-col items-center justify-center shrink-0">
                        <span class="text-lg font-extrabold text-[#0275d8] leading-none">${info.dayNum}</span>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-sky-600 mt-1">${info.monthShort}</span>
                    </div>
                    <div>
                        <h4 class="text-sm sm:text-base font-bold text-slate-900 leading-snug">${ev.nama_event}</h4>
                        <p class="text-xs text-slate-500 mt-1">${ev.tingkat} · ${ev.lokasi}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2.5 self-end sm:self-center shrink-0">
                    <span class="px-3.5 py-1.5 rounded-full text-[11px] font-semibold border ${info.statusClass}">${info.statusText}</span>
                    <button type="button" onclick="hapusAdminEvent(${ev.id}, '${safeNama}')"
                        class="bg-red-50 hover:bg-red-100 text-red-700 px-2.5 py-1.5 rounded-xl text-xs border border-red-200 transition" title="Hapus Event">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </div>
            `;
            container.prepend(card);
        }

        closeAdminModal('modal-admin-event');
        e.target.reset();
        alert(data.message || 'Event berhasil ditambahkan.');
    })
    .catch(err => alert('Gagal menambah event: ' + err.message));
}

function hapusAdminEvent(id, nama) {
    if (!confirm(`Yakin ingin menghapus event "${nama}"?`)) return;

    fetch(`/pelatih/event/${id}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': getAdminCsrf()
        }
    })
    .then(async res => {
        const data = await res.json();
        if (!res.ok) throw new Error(data.message || `Gagal menghapus (${res.status})`);
        return data;
    })
    .then(data => {
        document.getElementById('admin-card-event-' + id)?.remove();

        const container = document.getElementById('admin-daftar-event-container');
        if (container && container.children.length === 0) {
            container.innerHTML = `<div id="admin-empty-event-card" class="bg-white rounded-3xl p-8 text-center text-slate-400 text-xs border border-slate-200">Belum ada event kejuaraan yang terdaftar.</div>`;
        }

        alert(data.message || 'Event berhasil dihapus.');
    })
    .catch(err => alert('Gagal menghapus event: ' + err.message));
}

function appendAttendanceRow(data) {
    const isCoach = data.role === 'pelatih';
    const tbody = document.getElementById(isCoach ? 'tabel-admin-pelatih-att' : 'tabel-admin-atlet-att');
    if (!tbody) return;

    tbody.querySelector(isCoach ? '#empty-admin-pelatih-att' : '#empty-admin-atlet-att')?.remove();

    Array.from(tbody.querySelectorAll('tr')).forEach(row => {
        const firstCell = row.querySelector('td');
        if (firstCell && firstCell.textContent.trim() === data.user_name.trim()) {
            row.remove();
        }
    });

    const badgeClass = (data.status === 'TELAT' || data.status === 'ALFA')
        ? 'bg-red-50 text-red-700 border-red-200'
        : (data.status === 'IZIN' || data.status === 'SAKIT')
        ? 'bg-amber-50 text-amber-700 border-amber-200'
        : 'bg-emerald-50 text-emerald-700 border-emerald-200';

    const kel = data.kelompok || 'Learn To Swim';
    const kelClass = kel === 'Champions'
        ? 'bg-amber-50 text-amber-700 border-amber-200'
        : 'bg-sky-50 text-sky-700 border-sky-200';

    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td class="py-3 px-3 font-semibold text-slate-900">${data.user_name}</td>
        <td class="py-3 px-3 text-center">
            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border ${kelClass}">${kel}</span>
        </td>
        <td class="py-3 px-3 text-slate-500">${data.time}</td>
        <td class="py-3 px-3">${data.sesi}</td>
        <td class="py-3 px-3 text-center">
            <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold border ${badgeClass}">${data.status}</span>
        </td>
    `;
    tbody.prepend(tr);

    const statHadir = document.getElementById('stat-admin-hadir');
    if (statHadir && typeof data.total_hadir_hari === 'number') {
        statHadir.textContent = data.total_hadir_hari + ' Orang';
    }
}

function handleAdminManualAbsen(e) {
    e.preventDefault();

    const payload = {
        name: document.getElementById('admin_manual_nama').value,
        sesi: document.getElementById('admin_manual_sesi').value,
        status: document.getElementById('admin_manual_status').value
    };

    fetch('/admin/manual-attendance', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': getAdminCsrf()
        },
        body: JSON.stringify(payload)
    })
    .then(async res => {
        const data = await res.json();
        if (!res.ok) throw new Error(data.message || `Server error (${res.status})`);
        return data;
    })
    .then(data => {
        appendAttendanceRow(data);
        closeAdminModal('modal-admin-presensi');
        e.target.reset();
        alert(`Presensi ${data.user_name} (${data.status}) tersimpan.`);
    })
    .catch(err => alert('Gagal menyimpan presensi: ' + err.message));
}

function handleAdminScanBarcode(e) {
    e.preventDefault();
    const token = document.getElementById('admin_scan_token').value.trim();
    if (!token) return;
    prosesScanToken(token, e.target);
}

function prosesScanToken(token, formEl = null) {
    fetch('/admin/scan-attendance', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': getAdminCsrf()
        },
        body: JSON.stringify({ barcode_token: token })
    })
    .then(async res => {
        const data = await res.json();
        if (!res.ok) throw new Error(data.message || `Server error (${res.status})`);
        return data;
    })
    .then(data => {
        appendAttendanceRow(data);
        if (formEl) formEl.reset();
        alert(data.message || 'Scan berhasil.');
        if (currentAdminTab === 'scan-qr') {
            startCameraScanner();
        }
    })
    .catch(err => {
        alert('Scan gagal: ' + err.message);
        if (currentAdminTab === 'scan-qr') {
            startCameraScanner();
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const hash = window.location.hash.replace('#', '');
    if (hash && adminTabInfo[hash]) {
        switchAdminTab(hash);
    }
});