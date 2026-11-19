// public/js/murid-dashboard.js

const muridTabInfo = {
    'beranda': {
        title: 'Beranda Saya',
        subtitle: 'Ringkasan aktivitas dan presensi latihan'
    },
    'kehadiran-atlet': {
        title: 'Kartu QR & Riwayat',
        subtitle: 'Tunjukkan QR Code untuk absensi dan pantau riwayat kehadiranmu'
    },
    'best-time': {
        title: 'Best Time Saya',
        subtitle: 'Catatan waktu terbaik pribadimu berdasarkan gaya dan jarak'
    },
    'event': {
        title: 'Daftar Event',
        subtitle: 'Jadwal kompetisi dan kejuaraan renang mendatang'
    }
};

function toggleMuridSidebar(open) {
    const sidebar = document.getElementById('murid-sidebar');
    const overlay = document.getElementById('murid-sidebar-overlay');
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
    if (!document.getElementById('tab-' + target)) {
        target = 'beranda';
    }

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
    if (pageTitle && muridTabInfo[target]) {
        pageTitle.textContent = muridTabInfo[target].title;
        pageSubtitle.textContent = muridTabInfo[target].subtitle;
    }

    toggleMuridSidebar(false);
}

document.addEventListener('DOMContentLoaded', () => {
    const hash = window.location.hash.replace('#', '');
    if (hash && muridTabInfo[hash]) {
        switchTab(hash);
    } else {
        switchTab('beranda');
    }
});