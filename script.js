// ==========================================================================
// SECTION 1: STATE / DATA DUMMY APLIKASI
// ==========================================================================

// Array daftar slot stasiun pengisian beserta status awalnya
const slots = [
  { id: 1, status: "available", label: "Available", desc: "Ready to reserve" },
  { id: 2, status: "charging", label: "Charging", desc: "J 1234 AB · 42 min remaining" },
  { id: 3, status: "reserved", label: "Reserved", desc: "You · starts 15:30" },
  { id: 4, status: "charging", label: "Charging", desc: "B 7788 CD · 1h 05m" },
  { id: 5, status: "warning", label: "Needs attention", desc: "Occupied · not charging" },
  { id: 6, status: "available", label: "Available", desc: "Ready to reserve" },
];

// Variable keadaan aplikasi pengguna
let deposit = 100000; // Saldo jaminan awal pengguna
let violations = 0; // Jumlah pelanggaran yang tercatat pada pengguna
let reservation = null; // ID slot yang sedang direservasi oleh pengguna (null jika tidak ada)

// Helper function untuk format mata uang IDR
function money(n) {
  return "Rp" + n.toLocaleString("id-ID");
}

// ==========================================================================
// SECTION 2: RENDER UI UTAMA (STUDENT / USER VIEW)
// ==========================================================================

// Fungsi utama untuk memperbarui seluruh tampilan User berdasarkan kondisi data terbaru
function render() {
  const box = document.getElementById("slots");
  box.innerHTML = ""; // Bersihkan container slot sebelum render ulang

  // Generate kartu untuk setiap slot di tampilan User
  slots.forEach((s) => {
    const d = document.createElement("div");
    const statusClass = s.status === "charging" ? "busy" : s.status === "reserved" ? "reserve" : s.status === "warning" ? "warn" : "";
    d.className = "slot " + statusClass;

    d.innerHTML = `
      <div class="slot-top">
        <h3>Slot ${String(s.id).padStart(2, "0")}</h3>
        <span class="dot"></span>
      </div>
      <div class="status">${s.label}</div>
      <p>${s.desc}</p>
      ${s.status === "available" ? `<button class="btn" onclick="reserve(${s.id})">Reserve</button>` : s.status === "reserved" ? `<button class="btn secondary" onclick="cancelReservation()">Cancel</button>` : ""}
    `;
    box.appendChild(d);
  });

  // Memperbarui angka statistik di bagian atas
  document.getElementById("available").textContent = slots.filter((x) => x.status === "available").length;
  document.getElementById("charging").textContent = slots.filter((x) => x.status === "charging").length;
  document.getElementById("reserved").textContent = slots.filter((x) => x.status === "reserved").length;
  document.getElementById("myViolations").textContent = violations;

  // Memperbarui saldo deposit & catatan peringatan
  document.getElementById("balance").textContent = money(deposit);
  document.getElementById("balanceHint").textContent = deposit < 50000 ? "Deposit below minimum — restore to access" : "Available for access";

  // Memperbarui panel status reservasi pengguna
  const rt = document.getElementById("reservationText");
  const rp = document.getElementById("reservationPill");

  rt.textContent = reservation ? `Slot ${String(reservation).padStart(2, "0")} · starts now · 10 min grace` : "No active reservation";
  rp.textContent = reservation ? "Reserved" : "None";
  rp.className = "pill " + (reservation ? "amber" : "");

  // Panggil render tabel admin agar data admin ikut diperbarui
  renderAdmin();
}

// ==========================================================================
// SECTION 3: LOGIKA FITUR PENGGUNA (RESERVASI & TOP-UP)
// ==========================================================================

// Fungsi untuk reservasi slot pengisian daya
function reserve(id) {
  // Cek batas minimum saldo deposit
  if (deposit < 50000) {
    alert("Deposit belum memenuhi minimum Rp50.000. Silakan top up.");
    return;
  }
  // Cek apakah pengguna sudah memiliki reservasi aktif
  if (reservation) {
    alert("Anda sudah memiliki reservation aktif.");
    return;
  }

  const s = slots.find((x) => x.id === id);
  if (!s || s.status !== "available") return;

  // Ubah status slot menjadi 'reserved'
  s.status = "reserved";
  s.label = "Reserved";
  s.desc = "You · starts now";
  reservation = id;

  render(); // Render ulang tampilan
}

// Fungsi untuk membatalkan reservasi aktif
function cancelReservation() {
  if (!reservation) return;

  const s = slots.find((x) => x.id === reservation);
  if (s) {
    s.status = "available";
    s.label = "Available";
    s.desc = "Ready to reserve";
  }
  reservation = null;

  render(); // Render ulang tampilan
}

// Fungsi untuk menambah saldo deposit (Top-Up)
function topUp() {
  const input = document.getElementById("topup");
  const v = parseInt(input.value || 0, 10);

  if (v <= 0) return;

  deposit += v;
  input.value = "";
  render(); // Render ulang tampilan untuk memperbarui saldo
}

// ==========================================================================
// SECTION 4: RENDER UI & FITUR ADMIN (MONITORING & VERIFIKASI)
// ==========================================================================

// Fungsi merender baris tabel monitoring stasiun pada tampilan Admin
function renderAdmin() {
  const body = document.getElementById("adminRows");
  body.innerHTML = "";

  slots.forEach((s) => {
    let vehicle = s.status === "available" ? "—" : s.status === "reserved" ? "JOP-2026" : "B 1234 " + String(s.id).padStart(2, "0");
    let dur = s.status === "warning" ? "23 min" : s.status === "charging" ? "42 min" : "—";
    let action = s.status === "warning" ? '<button class="btn danger" onclick="applyPenalty()">Verify violation</button>' : "—";
    let pill = s.status === "warning" ? "red" : s.status === "charging" ? "green" : s.status === "reserved" ? "amber" : "";

    body.innerHTML += `
      <tr>
        <td>Slot ${String(s.id).padStart(2, "0")}</td>
        <td>${vehicle}</td>
        <td><span class="pill ${pill}">${s.label}</span></td>
        <td>${dur}</td>
        <td>${action}</td>
      </tr>
    `;
  });
}

// Fungsi Admin untuk memverifikasi denda/pelanggaran pengguna
function applyPenalty() {
  if (deposit < 25000) {
    alert("Deposit user tidak cukup untuk penalty.");
    return;
  }

  deposit -= 25000; // Potong denda dari saldo deposit
  violations++; // Tambah catatan pelanggaran
  document.getElementById("openViolations").textContent = Math.max(0, 2 + violations);

  // Ubah status slot berperingatan kembali menjadi kosong/Tersedia
  const s = slots.find((x) => x.status === "warning");
  if (s) {
    s.status = "available";
    s.label = "Available";
    s.desc = "Violation recorded";
  }

  render(); // Render ulang tampilan
  alert("Violation diverifikasi. Rp25.000 dipotong dari deposit.");
}

// ==========================================================================
// SECTION 5: EVENT LISTENERS & INITIALIZATION
// ==========================================================================

// Menambahkan event handler klik pada tombol Top-Up
document.getElementById("btnTopUp").addEventListener("click", topUp);

// Menambahkan handler klik pada Tab navigasi untuk perpindahan tampilan User/Admin
document.querySelectorAll(".tab").forEach((t) => {
  t.onclick = () => {
    // Hapus status aktif dari semua tab & section view
    document.querySelectorAll(".tab").forEach((x) => x.classList.remove("active"));
    document.querySelectorAll(".view").forEach((x) => x.classList.remove("active"));

    // Aktifkan tab & section yang dipilih
    t.classList.add("active");
    document.getElementById(t.dataset.view).classList.add("active");

    // Perbarui label peran pada header
    document.getElementById("roleLabel").textContent = t.dataset.view === "admin" ? "Station Admin" : "Student / User";
  };
});

// Menjalankan fungsi render awal saat pertama kali dimuat
render();
