<?php
session_start();

// Proteksi halaman: Jika belum login, paksa ke login.php
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userRole = $_SESSION['role'] ?? 'user';
$userName = $_SESSION['user_name'] ?? 'Student / User';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ChargeGuard — Electric Motorcycle Charging Station</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="app">
    <!-- HEADER -->
    <header>
      <div class="brand">
        <div class="logo">⚡</div>
        <div>
          ChargeGuard<br>
          <span style="font-size:11px;font-weight:500;color:var(--muted)">Campus Charging Management</span>
        </div>
      </div>
      <div style="display: flex; align-items: center; gap: 12px;">
        <div class="role"><?php echo htmlspecialchars($userRole === 'admin' ? 'Station Admin' : 'Student / User'); ?></div>
        <a href="logout.php" class="btn danger" style="text-decoration: none; font-size: 12px; padding: 6px 12px;">Logout</a>
      </div>
    </header>

    <main>
      <!-- HERO -->
      <section class="hero">
        <div>
          <div class="eyebrow">Prototype v0.1</div>
          <h1>Charge fairly.<br>Park responsibly.</h1>
          <p>Charging listrik tetap gratis. Deposit hanya menjadi jaminan kepatuhan dan dapat dikembalikan sesuai kebijakan kampus.</p>
        </div>
        <div class="balance">
          <small>Security deposit</small>
          <strong id="balance">Rp100.000</strong>
          <span class="meta" id="balanceHint">Tidak ada potongan</span>
        </div>
      </section>

      <!-- TOMBOL USER/ADMIN SUDAH DIHILANGKAN DARI SINI -->

      <?php if ($userRole === 'user'): ?>
      <!-- ==================================================================== -->
      <!-- SECTION: USER VIEW (Hanya muncul jika Role = User)                   -->
      <!-- ==================================================================== -->
      <section id="user" class="view active">
        <div class="grid">
          <div class="card stat">
            <label>Available</label>
            <strong class="green" id="available">3</strong>
          </div>
          <div class="card stat">
            <label>Charging</label>
            <strong id="charging">2</strong>
          </div>
          <div class="card stat">
            <label>Reserved</label>
            <strong class="amber" id="reserved">1</strong>
          </div>
          <div class="card stat">
            <label>My violations</label>
            <strong class="red" id="myViolations">0</strong>
          </div>
        </div>

        <div class="station card">
          <div class="station-head">
            <h2>Station A — Gedung Kampus</h2>
            <span>6 slots · free charging</span>
          </div>
          <div class="slots" id="slots"></div>
        </div>

        <div class="card panel">
          <h2>My charging activity</h2>
          <div class="row">
            <div>
              <b>Last session</b>
              <div class="meta">Slot 02 · 13:05–15:02</div>
            </div>
            <span class="pill green">Completed</span>
          </div>
          <div class="row">
            <div>
              <b>Current reservation</b>
              <div class="meta" id="reservationText">No active reservation</div>
            </div>
            <span class="pill" id="reservationPill">None</span>
          </div>
        </div>

        <div class="callout">
          <h3>How the deposit works</h3>
          <p>Top-up is not a charging fee. The balance is held as a compliance guarantee. If a verified violation occurs, the applicable penalty is deducted and the user must restore the deposit before using the station again.</p>
          <div class="deposit">
            <input id="topup" type="number" min="0" step="1000" placeholder="Nominal top-up">
            <button class="btn" id="btnTopUp">Top up</button>
          </div>
        </div>
      </section>
      <?php endif; ?>

      <?php if ($userRole === 'admin'): ?>
      <!-- ==================================================================== -->
      <!-- SECTION: ADMIN VIEW (Hanya muncul jika Role = Admin)                  -->
      <!-- ==================================================================== -->
      <section id="admin" class="view active">
        <div class="grid">
          <div class="card stat">
            <label>Active users</label>
            <strong>47</strong>
          </div>
          <div class="card stat">
            <label>Today's sessions</label>
            <strong>31</strong>
          </div>
          <div class="card stat">
            <label>Open violations</label>
            <strong class="red" id="openViolations">2</strong>
          </div>
          <div class="card stat">
            <label>Station utilization</label>
            <strong class="green">68%</strong>
          </div>
        </div>

        <div class="card panel">
          <h2>Live station monitor</h2>
          <div class="alert">Rule engine: only vehicles actively charging may occupy a charging slot. Overstay and non-charging occupancy are flagged automatically.</div>
          <table class="table">
            <thead>
              <tr>
                <th>Slot</th>
                <th>Vehicle</th>
                <th>Status</th>
                <th>Duration</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody id="adminRows"></tbody>
          </table>
        </div>

        <div class="card panel">
          <h2>Violation policy</h2>
          <div class="row">
            <div>
              <b>Park without charging</b>
              <div class="meta">Verified after configurable grace period</div>
            </div>
            <span>−Rp25.000</span>
          </div>
          <div class="row">
            <div>
              <b>Overstay after charging</b>
              <div class="meta">Grace period after charging completes</div>
            </div>
            <span>−Rp15.000</span>
          </div>
          <div class="row">
            <div>
              <b>Unauthorized gasoline vehicle</b>
              <div class="meta">Requires staff/sensor verification</div>
            </div>
            <span>−Rp50.000</span>
          </div>
        </div>
      </section>
      <?php endif; ?>

    </main>
  </div>

  <!-- Passing PHP session role ke JS variable -->
  <script>
    const USER_ROLE = "<?php echo $userRole; ?>";
  </script>
  <script src="script.js"></script>
</body>
</html>