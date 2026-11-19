// public/js/pelatih-dashboard.js

let currentActiveTab = 'beranda';

const pelatihTabInfo = {
    'beranda': {
        title: 'Beranda Pelatih',
        subtitle: 'Monitoring atlet bimbingan & jadwal sesi renang'
    },
    'kehadiran-atlet': {
        title: 'Kehadiran Atlet',
        subtitle: 'Catat dan pantau presensi manual atlet bimbingan'
    },
    'data-atlet': {
        title: 'Data Atlet',
        subtitle: 'Biodata lengkap, fisik, dan informasi sekolah atlet'
    },
    'event': {
        title: 'Daftar Event',
        subtitle: 'Jadwal kompetisi dan kejuaraan renang mendatang'
    },
    'program-latihan': {
        title: 'Program Latihan',
        subtitle: 'Upload file program & keterangan latihan khusus Champions'
    },
    'best-time': {
        title: 'Best Time Atlet',
        subtitle: 'Rekam dan pantau catatan waktu terbaik atlet Champions'
    }
};

function togglePelatihSidebar(open) {
    const sidebar = document.getElementById('pelatih-sidebar');
    const overlay = document.getElementById('pelatih-sidebar-overlay');
    if (!sidebar || !overlay) return;

    if (open) {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
    } else {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    }
}

function switchTab(target) {
    const targetSec = document.getElementById('tab-' + target);
    const targetBtn = document.getElementById('btn-' + target);

    if (!targetSec || !targetBtn) {
        target = document.getElementById('tab-beranda') ? 'beranda' : 'kehadiran-atlet';
    }

    currentActiveTab = target;

    document.querySelectorAll('.dashboard-panel').forEach(sec => {
        sec.classList.add('hidden');
    });

    document.querySelectorAll('.nav-tab').forEach(btn => {
        btn.classList.remove('is-active', 'bg-[#0275d8]', 'text-white');
        btn.classList.add('is-inactive', 'text-slate-300', 'hover:bg-white/10', 'hover:text-white');
    });

    const activeSec = document.getElementById('tab-' + target);
    const activeBtn = document.getElementById('btn-' + target);

    if (activeSec) activeSec.classList.remove('hidden');
    if (activeBtn) {
        activeBtn.classList.add('is-active', 'bg-[#0275d8]', 'text-white');
        activeBtn.classList.remove('is-inactive', 'text-slate-300', 'hover:bg-white/10', 'hover:text-white');
    }

    const pageTitle = document.getElementById('page-title');
    const pageSubtitle = document.getElementById('page-subtitle');
    if (pageTitle && pelatihTabInfo[target]) {
        pageTitle.textContent = pelatihTabInfo[target].title;
        pageSubtitle.textContent = pelatihTabInfo[target].subtitle;
    }

    togglePelatihSidebar(false);
}

function bukaKalenderTanggal() {
    const inputTanggal = document.getElementById('filter-tanggal');
    if (!inputTanggal) return;

    if (typeof inputTanggal.showPicker === 'function') {
        inputTanggal.showPicker();
    } else {
        inputTanggal.focus();
        inputTanggal.click();
    }
}

function gantiTanggalRiwayat(tanggalBaru) {
    if (!tanggalBaru) return;
    const url = new URL(window.location.href);
    url.searchParams.set('date', tanggalBaru);
    url.hash = currentActiveTab;
    window.location.href = url.toString();
}

function pilihOpsiPertama(selectId) {
    const select = document.getElementById(selectId);
    if (!select) return;
    const firstValid = Array.from(select.options).find(opt => !opt.disabled && opt.value !== '');
    if (firstValid) {
        select.value = firstValid.value;
    }
}

function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;

    if (modalId === 'modal-kehadiran') {
        pilihOpsiPertama('pelatih_manual_nama');
    } else if (modalId === 'modal-best-time') {
        pilihOpsiPertama('bt_nama_atlet');
    }

    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function openTambahDataAtlet() {
    resetFormDataAtlet();
    openModal('modal-data-atlet');
}

function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
}

function handleManualAbsenPelatihMurid(e) {
    e.preventDefault();
    const nama = document.getElementById('pelatih_manual_nama').value;
    const sesi = document.getElementById('pelatih_manual_sesi').value;
    const status = document.getElementById('pelatih_manual_status').value;

    if (!nama) {
        alert('Belum ada atlet terdaftar di kelompok ini.');
        return;
    }

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
        if (!res.ok) {
            throw new Error(data.message || `Server error (${res.status})`);
        }
        return data;
    })
    .then(data => {
        tambahBarisKehadiranPelatih(data.user_name, data.time, data.sesi, data.status);
        closeModal('modal-kehadiran');
        e.target.reset();
        pilihOpsiPertama('pelatih_manual_nama');
        alert(`Presensi ${data.user_name} (${data.status}) berhasil tersimpan ke database!`);
    })
    .catch(err => {
        alert('Gagal menyimpan ke database: ' + err.message);
    });
}

function tambahBarisKehadiranPelatih(nama, waktu, sesi, status) {
    const tbody = document.getElementById('tabel-presensi-pelatih-murid-body');
    if (!tbody) return;

    const emptyRow = tbody.querySelector('#empty-pelatih-murid-row');
    if (emptyRow) emptyRow.remove();

    Array.from(tbody.querySelectorAll('tr')).forEach(row => {
        const firstCell = row.querySelector('td');
        if (firstCell && firstCell.textContent.trim() === nama.trim()) {
            row.remove();
        }
    });

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
        <td class="py-3 px-3 text-center"><span class="px-2.5 py-1 rounded-full text-[10px] font-semibold border ${badgeClass}">${status}</span></td>
    `;
    tbody.prepend(tr);
}

function renderRowDataAtlet(d) {
    const safeName = (d.name || '').replace(/'/g, "\\'");
    const safeNik = (d.nik || '').replace(/'/g, "\\'");
    const safeNisnas = (d.nisnas || '').replace(/'/g, "\\'");
    const safeTtl = (d.ttl || '').replace(/'/g, "\\'");
    const safePhone = (d.phone || '').replace(/'/g, "\\'");
    const safeEmail = (d.email || '').replace(/'/g, "\\'");
    const safeTinggi = d.tinggi_badan ?? '';
    const safeBerat = d.berat_badan ?? '';
    const safeSekolah = (d.sekolah || '').replace(/'/g, "\\'");
    const safeKelas = (d.kelas || '').replace(/'/g, "\\'");

    return `
        <td class="py-3 px-3 font-semibold text-slate-900">${d.name || '-'}</td>
        <td class="py-3 px-3 font-mono text-slate-600">${d.nik || '-'}</td>
        <td class="py-3 px-3 font-mono text-slate-600">${d.nisnas || '-'}</td>
        <td class="py-3 px-3 text-slate-600">${d.ttl || '-'}</td>
        <td class="py-3 px-3 text-slate-600">${d.phone || '-'}</td>
        <td class="py-3 px-3 text-slate-600">${d.email || '-'}</td>
        <td class="py-3 px-3 text-center text-slate-700">${d.tinggi_badan ? d.tinggi_badan + ' cm' : '-'}</td>
        <td class="py-3 px-3 text-center text-slate-700">${d.berat_badan ? d.berat_badan + ' kg' : '-'}</td>
        <td class="py-3 px-3 text-slate-700">${d.sekolah || '-'}</td>
        <td class="py-3 px-3 text-center">
            <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-sky-50 text-sky-700 border border-sky-200">
                ${d.kelas || '-'}
            </span>
        </td>
        <td class="py-3 px-3 text-center space-x-1.5">
            <button type="button"
                onclick="editDataAtlet(${d.id}, '${safeName}', '${safeNik}', '${safeNisnas}', '${safeTtl}', '${safePhone}', '${safeEmail}', '${safeTinggi}', '${safeBerat}', '${safeSekolah}', '${safeKelas}')"
                class="bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 px-2.5 py-1 rounded-lg text-[11px] font-semibold transition">
                Edit
            </button>
            <button type="button"
                onclick="hapusDataAtlet(${d.id}, '${safeName}')"
                class="bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 px-2.5 py-1 rounded-lg text-[11px] font-semibold transition">
                Hapus
            </button>
        </td>
    `;
}

function editDataAtlet(id, name, nik, nisnas, ttl, phone, email, tinggi, berat, sekolah, kelas) {
    document.getElementById('data_atlet_id').value = id || '';
    document.getElementById('data_atlet_nama').value = name || '';
    document.getElementById('data_atlet_nik').value = nik || '';
    document.getElementById('data_atlet_nisnas').value = nisnas || '';
    document.getElementById('data_atlet_ttl').value = ttl || '';
    document.getElementById('data_atlet_hp').value = phone || '';
    document.getElementById('data_atlet_gmail').value = email || '';
    document.getElementById('data_atlet_tinggi').value = tinggi || '';
    document.getElementById('data_atlet_berat').value = berat || '';
    document.getElementById('data_atlet_sekolah').value = sekolah || '';
    document.getElementById('data_atlet_kelas').value = kelas || '';

    const modalTitle = document.getElementById('modal-data-atlet-title');
    if (modalTitle) modalTitle.textContent = 'Edit Data Atlet';

    const btnSimpan = document.getElementById('btn-simpan-data-atlet');
    if (btnSimpan) btnSimpan.textContent = 'Update Data Atlet';

    openModal('modal-data-atlet');
}

function resetFormDataAtlet() {
    const form = document.getElementById('form-data-atlet');
    if (form) form.reset();

    const idInput = document.getElementById('data_atlet_id');
    if (idInput) idInput.value = '';

    const modalTitle = document.getElementById('modal-data-atlet-title');
    if (modalTitle) modalTitle.textContent = 'Input Data Atlet';

    const btnSimpan = document.getElementById('btn-simpan-data-atlet');
    if (btnSimpan) btnSimpan.textContent = 'Simpan Data Atlet';
}

function hapusDataAtlet(id, nama) {
    if (!confirm(`Yakin ingin menghapus data atlet "${nama}"?`)) return;

    fetch(`/pelatih/data-atlet/${id}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken()
        }
    })
    .then(async res => {
        const data = await res.json();
        if (!res.ok) {
            throw new Error(data.message || `Gagal menghapus (${res.status})`);
        }
        return data;
    })
    .then(data => {
        const row = document.getElementById('row-data-atlet-' + id);
        if (row) row.remove();

        const tbody = document.getElementById('tabel-data-atlet-body');
        if (tbody && tbody.querySelectorAll('tr').length === 0) {
            tbody.innerHTML = `<tr id="empty-data-atlet-row"><td colspan="11" class="py-6 text-center text-slate-400">Belum ada data atlet terdaftar.</td></tr>`;
        }

        alert(data.message || 'Data atlet berhasil dihapus.');
    })
    .catch(err => {
        alert('Gagal menghapus data atlet: ' + err.message);
    });
}

function handleSimpanDataAtlet(e) {
    e.preventDefault();

    const payload = {
        id: document.getElementById('data_atlet_id').value || null,
        name: document.getElementById('data_atlet_nama').value.trim(),
        nik: document.getElementById('data_atlet_nik').value.trim(),
        nisnas: document.getElementById('data_atlet_nisnas').value.trim(),
        ttl: document.getElementById('data_atlet_ttl').value.trim(),
        phone: document.getElementById('data_atlet_hp').value.trim(),
        email: document.getElementById('data_atlet_gmail').value.trim(),
        tinggi_badan: document.getElementById('data_atlet_tinggi').value.trim(),
        berat_badan: document.getElementById('data_atlet_berat').value.trim(),
        sekolah: document.getElementById('data_atlet_sekolah').value.trim(),
        kelas: document.getElementById('data_atlet_kelas').value.trim()
    };

    fetch('/pelatih/store-data-atlet', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken()
        },
        body: JSON.stringify(payload)
    })
    .then(async res => {
        const data = await res.json();
        if (!res.ok) {
            throw new Error(data.message || `Server error (${res.status})`);
        }
        return data;
    })
    .then(data => {
        const d = data.data_atlet;
        const tbody = document.getElementById('tabel-data-atlet-body');
        if (tbody) {
            const emptyRow = tbody.querySelector('#empty-data-atlet-row');
            if (emptyRow) emptyRow.remove();

            let tr = document.getElementById('row-data-atlet-' + d.id);
            if (tr) {
                tr.innerHTML = renderRowDataAtlet(d);
            } else {
                tr = document.createElement('tr');
                tr.id = 'row-data-atlet-' + d.id;
                tr.innerHTML = renderRowDataAtlet(d);
                tbody.prepend(tr);
            }
        }

        closeModal('modal-data-atlet');
        resetFormDataAtlet();
        alert(data.message || 'Data atlet berhasil disimpan ke database.');
    })
    .catch(err => {
        alert('Gagal menyimpan data atlet: ' + err.message);
    });
}

function hitungStatusEvent(tglMulai, tglSelesai) {
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

function handleSimpanEvent(e) {
    e.preventDefault();

    const payload = {
        nama_event: document.getElementById('ev_nama').value.trim(),
        tingkat: document.getElementById('ev_tingkat').value,
        lokasi: document.getElementById('ev_lokasi').value.trim(),
        tanggal_mulai: document.getElementById('ev_tgl_mulai').value,
        tanggal_selesai: document.getElementById('ev_tgl_selesai').value || null
    };

    fetch('/pelatih/store-event', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken()
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
        const container = document.getElementById('daftar-event-container');
        if (container) {
            container.querySelector('#empty-event-card')?.remove();

            const info = hitungStatusEvent(ev.tanggal_mulai, ev.tanggal_selesai);
            const safeNama = (ev.nama_event || '').replace(/'/g, "\\'");

            const card = document.createElement('div');
            card.id = 'card-event-' + ev.id;
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
                    <button type="button" onclick="hapusEvent(${ev.id}, '${safeNama}')"
                        class="bg-red-50 hover:bg-red-100 text-red-700 px-2.5 py-1.5 rounded-xl text-xs border border-red-200 transition" title="Hapus Event">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </div>
            `;
            container.prepend(card);
        }

        closeModal('modal-event');
        e.target.reset();
        alert(data.message || 'Event berhasil ditambahkan.');
    })
    .catch(err => alert('Gagal menambah event: ' + err.message));
}

function hapusEvent(id, nama) {
    if (!confirm(`Yakin ingin menghapus event "${nama}"?`)) return;

    fetch(`/pelatih/event/${id}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken()
        }
    })
    .then(async res => {
        const data = await res.json();
        if (!res.ok) throw new Error(data.message || `Gagal menghapus (${res.status})`);
        return data;
    })
    .then(data => {
        document.getElementById('card-event-' + id)?.remove();

        const container = document.getElementById('daftar-event-container');
        if (container && container.children.length === 0) {
            container.innerHTML = `<div id="empty-event-card" class="bg-white rounded-3xl p-8 text-center text-slate-400 text-xs border border-slate-200">Belum ada event kejuaraan yang terdaftar.</div>`;
        }

        alert(data.message || 'Event berhasil dihapus.');
    })
    .catch(err => alert('Gagal menghapus event: ' + err.message));
}

function handleSimpanProgram(e) {
    e.preventDefault();

    const fileInput = document.getElementById('prog_file');
    if (!fileInput || !fileInput.files.length) {
        alert('Silakan pilih file program latihan terlebih dahulu.');
        return;
    }

    const formData = new FormData();
    formData.append('kelompok', document.getElementById('prog_kelompok').value.trim());
    formData.append('fase', document.getElementById('prog_fase').value.trim());
    formData.append('tanggal', document.getElementById('prog_tanggal').value);
    formData.append('judul_minggu', document.getElementById('prog_judul').value.trim());
    formData.append('target_event', document.getElementById('prog_target_event').value.trim());
    formData.append('alat', document.getElementById('prog_alat').value.trim());
    formData.append('file_program', fileInput.files[0]);

    const btn = document.getElementById('btn-simpan-program');
    if (btn) btn.disabled = true;

    fetch('/pelatih/store-program', {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken()
        },
        body: formData
    })
    .then(async res => {
        const data = await res.json();
        if (!res.ok) throw new Error(data.message || `Server error (${res.status})`);
        return data;
    })
    .then(data => {
        const p = data.program;
        const container = document.getElementById('daftar-program-container');
        if (container) {
            const emptyCard = container.querySelector('#empty-program-card');
            if (emptyCard) emptyCard.remove();

            const safeJudul = (p.judul_minggu || '').replace(/'/g, "\\'");
            const card = document.createElement('div');
            card.id = 'card-program-' + p.id;
            card.className = 'bg-white text-slate-800 rounded-3xl p-6 shadow-sm border border-slate-200 hover:border-sky-300 transition flex flex-col justify-between space-y-4';
            card.innerHTML = `
                <div class="space-y-3">
                    <div class="flex items-center justify-between gap-2 flex-wrap">
                        <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-sky-50 text-[#0275d8] border border-sky-200">${p.kelompok}</span>
                        <span class="text-[11px] text-slate-500"><i class="fa-regular fa-calendar mr-1 text-sky-600"></i>${p.tanggal}</span>
                    </div>
                    ${p.fase ? `<div class="inline-block px-3 py-1 rounded-lg bg-amber-50 border border-amber-200 text-amber-700 text-[10px] font-bold uppercase tracking-wider">${p.fase}</div>` : ''}
                    <div>
                        <h4 class="text-base sm:text-lg font-bold text-slate-900 leading-snug">${p.judul_minggu}</h4>
                        ${p.target_event ? `<p class="text-xs text-slate-500 mt-1">${p.target_event}</p>` : ''}
                    </div>
                    ${p.alat ? `<p class="text-xs text-slate-600 leading-relaxed"><span class="text-slate-800 font-semibold">Alat klub dipakai:</span> ${p.alat}</p>` : ''}
                </div>
                <div class="pt-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-2">
                    <div class="flex items-center gap-2 flex-wrap">
                        ${p.file_path ? `<a href="${p.file_path}" target="_blank" class="inline-flex items-center space-x-1.5 bg-sky-50 hover:bg-[#0275d8] text-sky-700 hover:text-white text-xs font-semibold px-3.5 py-2 rounded-xl border border-sky-200 transition"><i class="fa-solid fa-file-arrow-down"></i><span>Unduh / Lihat File</span></a>` : ''}
                    </div>
                    <button type="button" onclick="hapusProgram(${p.id}, '${safeJudul}')"
                        class="inline-flex items-center space-x-1 bg-red-50 hover:bg-red-100 text-red-700 text-xs font-semibold px-3 py-2 rounded-xl border border-red-200 transition">
                        <i class="fa-solid fa-trash-can"></i>
                        <span>Hapus</span>
                    </button>
                </div>
            `;
            container.prepend(card);
        }

        closeModal('modal-program');
        e.target.reset();
        alert(data.message || 'Program latihan berhasil diupload.');
    })
    .catch(err => alert('Gagal upload program: ' + err.message))
    .finally(() => {
        if (btn) btn.disabled = false;
    });
}

function hapusProgram(id, judul) {
    if (!confirm(`Yakin ingin menghapus program "${judul}"?`)) return;

    fetch(`/pelatih/program/${id}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken()
        }
    })
    .then(async res => {
        const data = await res.json();
        if (!res.ok) throw new Error(data.message || `Gagal menghapus (${res.status})`);
        return data;
    })
    .then(data => {
        const card = document.getElementById('card-program-' + id);
        if (card) card.remove();

        const container = document.getElementById('daftar-program-container');
        if (container && container.children.length === 0) {
            container.innerHTML = `<div id="empty-program-card" class="lg:col-span-2 bg-white rounded-3xl p-8 text-center text-slate-400 text-xs border border-slate-200">Belum ada program latihan yang diupload.</div>`;
        }

        alert(data.message || 'Program berhasil dihapus.');
    })
    .catch(err => alert('Gagal menghapus program: ' + err.message));
}

function handleSimpanBestTime(e) {
    e.preventDefault();

    const namaAtlet = document.getElementById('bt_nama_atlet').value;
    if (!namaAtlet) {
        alert('Belum ada atlet terdaftar di kelompok ini.');
        return;
    }

    const payload = {
        nama_atlet: namaAtlet,
        gaya: document.getElementById('bt_gaya').value,
        jarak: document.getElementById('bt_jarak').value,
        waktu: document.getElementById('bt_waktu').value.trim(),
        tanggal: document.getElementById('bt_tanggal').value,
        keterangan: document.getElementById('bt_keterangan').value.trim()
    };

    fetch('/pelatih/store-best-time', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken()
        },
        body: JSON.stringify(payload)
    })
    .then(async res => {
        const data = await res.json();
        if (!res.ok) throw new Error(data.message || `Server error (${res.status})`);
        return data;
    })
    .then(data => {
        const bt = data.best_time;
        const tbody = document.getElementById('tabel-best-time-body');
        if (tbody) {
            const emptyRow = tbody.querySelector('#empty-best-time-row');
            if (emptyRow) emptyRow.remove();

            const safeNama = (bt.nama_atlet || '').replace(/'/g, "\\'");
            const tr = document.createElement('tr');
            tr.id = 'row-best-time-' + bt.id;
            tr.innerHTML = `
                <td class="py-3 px-3 font-semibold text-slate-900">${bt.nama_atlet}</td>
                <td class="py-3 px-3">${bt.gaya}</td>
                <td class="py-3 px-3 text-center">${bt.jarak}</td>
                <td class="py-3 px-3 text-center">
                    <span class="px-2.5 py-1 rounded-full text-[11px] font-mono font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">${bt.waktu}</span>
                </td>
                <td class="py-3 px-3 text-slate-500">${bt.tanggal}</td>
                <td class="py-3 px-3 text-slate-600">${bt.keterangan || '-'}</td>
                <td class="py-3 px-3 text-center">
                    <button type="button" onclick="hapusBestTime(${bt.id}, '${safeNama}')"
                        class="bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 px-2.5 py-1 rounded-lg text-[11px] font-semibold transition">
                        Hapus
                    </button>
                </td>
            `;
            tbody.prepend(tr);
        }

        closeModal('modal-best-time');
        e.target.reset();
        pilihOpsiPertama('bt_nama_atlet');
        alert(data.message || 'Best Time berhasil disimpan.');
    })
    .catch(err => alert('Gagal menyimpan Best Time: ' + err.message));
}

function hapusBestTime(id, nama) {
    if (!confirm(`Yakin ingin menghapus catatan Best Time milik "${nama}"?`)) return;

    fetch(`/pelatih/best-time/${id}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken()
        }
    })
    .then(async res => {
        const data = await res.json();
        if (!res.ok) throw new Error(data.message || `Gagal menghapus (${res.status})`);
        return data;
    })
    .then(data => {
        const row = document.getElementById('row-best-time-' + id);
        if (row) row.remove();

        const tbody = document.getElementById('tabel-best-time-body');
        if (tbody && tbody.querySelectorAll('tr').length === 0) {
            tbody.innerHTML = `<tr id="empty-best-time-row"><td colspan="7" class="py-6 text-center text-slate-400">Belum ada catatan Best Time atlet.</td></tr>`;
        }

        alert(data.message || 'Catatan Best Time berhasil dihapus.');
    })
    .catch(err => alert('Gagal menghapus Best Time: ' + err.message));
}

document.addEventListener('DOMContentLoaded', () => {
    const hash = window.location.hash.replace('#', '');
    if (hash && document.getElementById('tab-' + hash)) {
        switchTab(hash);
    } else {
        switchTab(document.getElementById('tab-beranda') ? 'beranda' : 'kehadiran-atlet');
    }
});