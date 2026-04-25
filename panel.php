<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: index.html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ASKi Clan - Admin Panel</title>
<link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;500;600;700&family=Share+Tech+Mono&family=Exo+2:wght@300;400;600&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  :root {
    --bg: #0a0b0d; --bg2: #111318; --bg3: #181c24;
    --accent: #e8a020; --accent2: #c47a10;
    --red: #c0392b; --green: #2ecc71;
    --text: #d4d8e0; --muted: #7a8090; --border: #2a2e3a;
  }
  body { background: var(--bg); color: var(--text); font-family: 'Exo 2', sans-serif; font-weight: 300; min-height: 100vh; display: flex; }
  body::before { content: ''; position: fixed; inset: 0; background: repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(0,0,0,0.03) 2px, rgba(0,0,0,0.03) 4px); pointer-events: none; z-index: 9999; }

  /* SIDEBAR */
  .sidebar { width: 220px; background: var(--bg2); border-right: 1px solid var(--border); display: flex; flex-direction: column; position: fixed; height: 100vh; }
  .sidebar-logo { padding: 1.5rem; border-bottom: 1px solid var(--border); }
  .sidebar-logo .logo { font-family: 'Rajdhani', sans-serif; font-size: 20px; font-weight: 700; letter-spacing: 3px; color: var(--accent); text-transform: uppercase; }
  .sidebar-logo .logo span { color: var(--text); }
  .sidebar-logo .sub { font-family: 'Share Tech Mono', monospace; font-size: 9px; letter-spacing: 2px; color: var(--red); text-transform: uppercase; margin-top: 2px; }
  .sidebar-nav { flex: 1; padding: 1rem 0; }
  .nav-item { display: flex; align-items: center; gap: 10px; padding: 10px 1.5rem; font-family: 'Rajdhani', sans-serif; font-size: 13px; font-weight: 600; letter-spacing: 2px; color: var(--muted); text-transform: uppercase; cursor: pointer; transition: all 0.2s; border-left: 2px solid transparent; }
  .nav-item:hover { color: var(--text); background: rgba(255,255,255,0.03); }
  .nav-item.active { color: var(--accent); border-left-color: var(--accent); background: rgba(232,160,32,0.05); }
  .nav-icon { font-size: 16px; }
  .sidebar-user { padding: 1rem 1.5rem; border-top: 1px solid var(--border); }
  .user-info { font-family: 'Share Tech Mono', monospace; font-size: 10px; color: var(--muted); margin-bottom: 8px; }
  .user-info span { color: var(--accent); }
  .logout-btn { width: 100%; font-family: 'Rajdhani', sans-serif; font-size: 11px; font-weight: 600; letter-spacing: 2px; text-transform: uppercase; padding: 8px; border: 1px solid var(--red); background: transparent; color: var(--red); cursor: pointer; transition: all 0.2s; }
  .logout-btn:hover { background: var(--red); color: #fff; }

  /* MAIN */
  .main { margin-left: 220px; flex: 1; padding: 2rem; }
  .topbar { display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border); }
  .page-title { font-family: 'Rajdhani', sans-serif; font-size: 22px; font-weight: 700; letter-spacing: 3px; color: #fff; text-transform: uppercase; }
  .page-title span { color: var(--accent); }
  .server-status { display: flex; align-items: center; gap: 8px; font-family: 'Share Tech Mono', monospace; font-size: 11px; color: var(--green); }
  .status-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--green); animation: pulse 2s infinite; }
  @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }

  /* TABS */
  .tab-content { display: none; }
  .tab-content.active { display: block; }

  /* STATS */
  .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
  .stat-card { background: var(--bg2); border: 1px solid var(--border); padding: 1.2rem; position: relative; clip-path: polygon(0 0, calc(100% - 12px) 0, 100% 12px, 100% 100%, 0 100%); }
  .stat-card::before { content: ''; position: absolute; top: 0; right: 0; width: 0; height: 0; border-style: solid; border-width: 0 12px 12px 0; border-color: transparent var(--accent) transparent transparent; }
  .stat-card.red::before { border-color: transparent var(--red) transparent transparent; }
  .stat-card.green::before { border-color: transparent var(--green) transparent transparent; }
  .stat-num { font-family: 'Rajdhani', sans-serif; font-size: 36px; font-weight: 700; color: var(--accent); line-height: 1; }
  .stat-card.red .stat-num { color: var(--red); }
  .stat-card.green .stat-num { color: var(--green); }
  .stat-label { font-family: 'Share Tech Mono', monospace; font-size: 10px; letter-spacing: 2px; color: var(--muted); text-transform: uppercase; margin-top: 4px; }

  /* TABLE */
  .panel-card { background: var(--bg2); border: 1px solid var(--border); margin-bottom: 1.5rem; }
  .panel-card-header { padding: 1rem 1.5rem; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }
  .panel-card-title { font-family: 'Rajdhani', sans-serif; font-size: 14px; font-weight: 600; letter-spacing: 3px; color: var(--accent); text-transform: uppercase; }
  .panel-card-body { padding: 1.5rem; }
  table { width: 100%; border-collapse: collapse; }
  th { font-family: 'Share Tech Mono', monospace; font-size: 10px; letter-spacing: 2px; color: var(--muted); text-transform: uppercase; padding: 8px 12px; text-align: left; border-bottom: 1px solid var(--border); }
  td { font-family: 'Exo 2', sans-serif; font-size: 13px; color: var(--text); padding: 10px 12px; border-bottom: 1px solid rgba(42,46,58,0.5); }
  tr:last-child td { border-bottom: none; }
  tr:hover td { background: rgba(255,255,255,0.02); }
  .badge { font-family: 'Share Tech Mono', monospace; font-size: 10px; letter-spacing: 1px; padding: 3px 8px; text-transform: uppercase; }
  .badge-red { background: rgba(192,57,43,0.15); color: var(--red); border: 1px solid rgba(192,57,43,0.3); }
  .badge-green { background: rgba(46,204,113,0.1); color: var(--green); border: 1px solid rgba(46,204,113,0.3); }
  .badge-gold { background: rgba(232,160,32,0.1); color: var(--accent); border: 1px solid rgba(232,160,32,0.3); }
  .action-btn { font-family: 'Rajdhani', sans-serif; font-size: 11px; font-weight: 600; letter-spacing: 1px; text-transform: uppercase; padding: 4px 12px; border: 1px solid; background: transparent; cursor: pointer; transition: all 0.2s; margin-right: 4px; }
  .action-btn.danger { border-color: var(--red); color: var(--red); }
  .action-btn.danger:hover { background: var(--red); color: #fff; }
  .action-btn.success { border-color: var(--green); color: var(--green); }
  .action-btn.success:hover { background: var(--green); color: #000; }
  .action-btn.primary { border-color: var(--accent); color: var(--accent); }
  .action-btn.primary:hover { background: var(--accent); color: #000; }

  /* FORM */
  .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem; }
  .form-group { margin-bottom: 1rem; }
  .form-label { font-family: 'Share Tech Mono', monospace; font-size: 10px; letter-spacing: 2px; color: var(--muted); text-transform: uppercase; display: block; margin-bottom: 6px; }
  .form-input, .form-select { width: 100%; background: var(--bg3); border: 1px solid var(--border); color: var(--text); font-family: 'Share Tech Mono', monospace; font-size: 13px; padding: 10px 14px; outline: none; transition: border-color 0.2s; }
  .form-input:focus, .form-select:focus { border-color: var(--accent); }
  .form-select option { background: var(--bg3); }
  .submit-btn { font-family: 'Rajdhani', sans-serif; font-size: 13px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; padding: 10px 28px; border: 1px solid var(--accent); background: var(--accent); color: #000; cursor: pointer; transition: all 0.2s; clip-path: polygon(8px 0%, 100% 0%, calc(100% - 8px) 100%, 0% 100%); }
  .submit-btn:hover { background: transparent; color: var(--accent); }
  .submit-btn.red { border-color: var(--red); background: var(--red); color: #fff; }
  .submit-btn.red:hover { background: transparent; color: var(--red); }

  /* SERVER CONTROL */
  .server-controls { display: flex; gap: 1rem; flex-wrap: wrap; }
  .control-btn { flex: 1; min-width: 140px; padding: 1.5rem; background: var(--bg3); border: 1px solid var(--border); text-align: center; cursor: pointer; transition: all 0.2s; }
  .control-btn:hover { border-color: var(--accent); }
  .control-btn.red:hover { border-color: var(--red); }
  .control-btn-icon { font-size: 28px; margin-bottom: 0.5rem; }
  .control-btn-label { font-family: 'Rajdhani', sans-serif; font-size: 14px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; }
  .control-btn-sub { font-family: 'Share Tech Mono', monospace; font-size: 10px; color: var(--muted); margin-top: 4px; }

  /* ALERT */
  .alert { font-family: 'Share Tech Mono', monospace; font-size: 12px; padding: 10px 14px; margin-bottom: 1rem; display: none; }
  .alert.show { display: block; }
  .alert-success { background: rgba(46,204,113,0.1); border: 1px solid rgba(46,204,113,0.3); color: var(--green); }
  .alert-error { background: rgba(192,57,43,0.1); border: 1px solid rgba(192,57,43,0.3); color: var(--red); }

  .console-box { background: #000; border: 1px solid var(--border); padding: 1rem; font-family: 'Share Tech Mono', monospace; font-size: 12px; color: var(--green); height: 200px; overflow-y: auto; margin-top: 1rem; }
  .console-line::before { content: '> '; color: var(--accent); }
</style>
</head>
<body>

<div class="sidebar">
  <div class="sidebar-logo">
    <div class="logo">ASKi<span>clan</span></div>
    <div class="sub">// Admin Panel</div>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-item active" onclick="showTab('dashboard')"><span class="nav-icon">📊</span> Dashboard</div>
    <div class="nav-item" onclick="showTab('bans')"><span class="nav-icon">🔨</span> Ban Yönetimi</div>
    <div class="nav-item" onclick="showTab('admins')"><span class="nav-icon">👤</span> Admin Yönetimi</div>
    <div class="nav-item" onclick="showTab('server')"><span class="nav-icon">🖥️</span> Sunucu Kontrolü</div>
  </nav>
  <div class="sidebar-user">
    <div class="user-info">Giriş: <span><?php echo htmlspecialchars($_SESSION['admin']); ?></span></div>
    <button class="logout-btn" onclick="window.location.href='api/logout.php'">Çıkış Yap</button>
  </div>
</div>

<div class="main">
  <div class="topbar">
    <div class="page-title"><span>//</span> <span id="pageTitle">Dashboard</span></div>
    <div class="server-status"><div class="status-dot"></div> Sunucu Online</div>
  </div>

  <!-- DASHBOARD -->
  <div class="tab-content active" id="tab-dashboard">
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-num" id="stat-players">0</div>
        <div class="stat-label">Online Oyuncu</div>
      </div>
      <div class="stat-card red">
        <div class="stat-num" id="stat-bans">0</div>
        <div class="stat-label">Toplam Ban</div>
      </div>
      <div class="stat-card green">
        <div class="stat-num" id="stat-admins">0</div>
        <div class="stat-label">Admin Sayısı</div>
      </div>
      <div class="stat-card">
        <div class="stat-num">128</div>
        <div class="stat-label">Tickrate</div>
      </div>
    </div>

    <div class="panel-card">
      <div class="panel-card-header">
        <div class="panel-card-title">// Son Banlar</div>
      </div>
      <div class="panel-card-body">
        <table>
          <thead><tr><th>Steam ID</th><th>Oyuncu</th><th>Sebep</th><th>Süre</th><th>Admin</th><th>Tarih</th></tr></thead>
          <tbody id="recent-bans"><tr><td colspan="6" style="color:var(--muted);text-align:center;font-family:Share Tech Mono,monospace;font-size:11px;">Yükleniyor...</td></tr></tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- BAN YÖNETİMİ -->
  <div class="tab-content" id="tab-bans">
    <div class="panel-card">
      <div class="panel-card-header"><div class="panel-card-title">// Yeni Ban Ekle</div></div>
      <div class="panel-card-body">
        <div class="alert alert-success" id="ban-success">Ban başarıyla eklendi!</div>
        <div class="alert alert-error" id="ban-error">Hata oluştu!</div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Steam ID</label>
            <input type="text" class="form-input" id="ban-steamid" placeholder="STEAM_0:0:12345678">
          </div>
          <div class="form-group">
            <label class="form-label">Oyuncu Adı</label>
            <input type="text" class="form-input" id="ban-name" placeholder="Oyuncu adı">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Sebep</label>
            <input type="text" class="form-input" id="ban-reason" placeholder="Hile, küfür, vb.">
          </div>
          <div class="form-group">
            <label class="form-label">Süre</label>
            <select class="form-select" id="ban-duration">
              <option value="60">1 Saat</option>
              <option value="1440">1 Gün</option>
              <option value="10080">1 Hafta</option>
              <option value="43200">1 Ay</option>
              <option value="0">Kalıcı</option>
            </select>
          </div>
        </div>
        <button class="submit-btn red" onclick="addBan()">Ban Ekle</button>
      </div>
    </div>

    <div class="panel-card">
      <div class="panel-card-header"><div class="panel-card-title">// Aktif Banlar</div></div>
      <div class="panel-card-body">
        <table>
          <thead><tr><th>Steam ID</th><th>Oyuncu</th><th>Sebep</th><th>Süre</th><th>Admin</th><th>İşlem</th></tr></thead>
          <tbody id="ban-list"><tr><td colspan="6" style="color:var(--muted);text-align:center;font-family:Share Tech Mono,monospace;font-size:11px;">Yükleniyor...</td></tr></tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- ADMİN YÖNETİMİ -->
  <div class="tab-content" id="tab-admins">
    <div class="panel-card">
      <div class="panel-card-header"><div class="panel-card-title">// Yeni Admin Ekle</div></div>
      <div class="panel-card-body">
        <div class="alert alert-success" id="admin-success">Admin başarıyla eklendi!</div>
        <div class="alert alert-error" id="admin-error">Hata oluştu!</div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Kullanıcı Adı</label>
            <input type="text" class="form-input" id="admin-username" placeholder="admin_adi">
          </div>
          <div class="form-group">
            <label class="form-label">Şifre</label>
            <input type="password" class="form-input" id="admin-password" placeholder="Güçlü şifre">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Steam ID</label>
            <input type="text" class="form-input" id="admin-steamid" placeholder="STEAM_0:0:12345678">
          </div>
          <div class="form-group">
            <label class="form-label">Yetki Seviyesi</label>
            <select class="form-select" id="admin-level">
              <option value="1">Moderatör</option>
              <option value="2">Admin</option>
              <option value="3">Süper Admin</option>
            </select>
          </div>
        </div>
        <button class="submit-btn" onclick="addAdmin()">Admin Ekle</button>
      </div>
    </div>

    <div class="panel-card">
      <div class="panel-card-header"><div class="panel-card-title">// Admin Listesi</div></div>
      <div class="panel-card-body">
        <table>
          <thead><tr><th>Kullanıcı Adı</th><th>Steam ID</th><th>Yetki</th><th>Son Giriş</th><th>İşlem</th></tr></thead>
          <tbody id="admin-list"><tr><td colspan="5" style="color:var(--muted);text-align:center;font-family:Share Tech Mono,monospace;font-size:11px;">Yükleniyor...</td></tr></tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- SUNUCU KONTROLÜ -->
  <div class="tab-content" id="tab-server">
    <div class="panel-card">
      <div class="panel-card-header"><div class="panel-card-title">// Sunucu Kontrol</div></div>
      <div class="panel-card-body">
        <div class="alert alert-success" id="server-success"></div>
        <div class="alert alert-error" id="server-error"></div>
        <div class="server-controls">
          <div class="control-btn" onclick="serverCmd('restart')">
            <div class="control-btn-icon">🔄</div>
            <div class="control-btn-label">Restart</div>
            <div class="control-btn-sub">Sunucuyu yeniden başlat</div>
          </div>
          <div class="control-btn red" onclick="serverCmd('stop')">
            <div class="control-btn-icon">⛔</div>
            <div class="control-btn-label">Durdur</div>
            <div class="control-btn-sub">Sunucuyu kapat</div>
          </div>
          <div class="control-btn" onclick="serverCmd('start')">
            <div class="control-btn-icon">▶️</div>
            <div class="control-btn-label">Başlat</div>
            <div class="control-btn-sub">Sunucuyu başlat</div>
          </div>
        </div>
      </div>
    </div>

    <div class="panel-card">
      <div class="panel-card-header"><div class="panel-card-title">// Harita Değiştir</div></div>
      <div class="panel-card-body">
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Harita</label>
            <select class="form-select" id="map-select">
              <option value="de_dust2">de_dust2</option>
              <option value="de_inferno">de_inferno</option>
              <option value="de_mirage">de_mirage</option>
              <option value="de_nuke">de_nuke</option>
              <option value="de_overpass">de_overpass</option>
              <option value="de_vertigo">de_vertigo</option>
              <option value="de_ancient">de_ancient</option>
              <option value="de_anubis">de_anubis</option>
            </select>
          </div>
        </div>
        <button class="submit-btn" onclick="changeMap()">Haritayı Değiştir</button>
      </div>
    </div>

    <div class="panel-card">
      <div class="panel-card-header"><div class="panel-card-title">// Konsol</div></div>
      <div class="panel-card-body">
        <div style="display:flex;gap:1rem;">
          <input type="text" class="form-input" id="console-cmd" placeholder="rcon komutu yazın..." style="flex:1;">
          <button class="submit-btn" onclick="sendRcon()">Gönder</button>
        </div>
        <div class="console-box" id="console-output">
          <div class="console-line">Panel başlatıldı.</div>
        </div>
      </div>
    </div>
  </div>

</div>

<script>
function showTab(tab) {
  document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
  document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
  document.getElementById('tab-' + tab).classList.add('active');
  event.currentTarget.classList.add('active');
  const titles = { dashboard: 'Dashboard', bans: 'Ban Yönetimi', admins: 'Admin Yönetimi', server: 'Sunucu Kontrolü' };
  document.getElementById('pageTitle').textContent = titles[tab];
  if (tab === 'bans') loadBans();
  if (tab === 'admins') loadAdmins();
  if (tab === 'dashboard') loadDashboard();
}

function api(url, data) {
  return fetch(url, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data)
  }).then(r => r.json());
}

function showAlert(id, type, msg) {
  const el = document.getElementById(id);
  el.className = 'alert alert-' + type + ' show';
  el.textContent = msg;
  setTimeout(() => el.classList.remove('show'), 3000);
}

function loadDashboard() {
  api('api/stats.php', {}).then(data => {
    if (data.success) {
      document.getElementById('stat-players').textContent = data.players || 0;
      document.getElementById('stat-bans').textContent = data.bans || 0;
      document.getElementById('stat-admins').textContent = data.admins || 0;
      const tbody = document.getElementById('recent-bans');
      if (data.recent_bans && data.recent_bans.length > 0) {
        tbody.innerHTML = data.recent_bans.map(b => `
          <tr>
            <td>${b.steamid}</td>
            <td>${b.name}</td>
            <td>${b.reason}</td>
            <td>${b.duration == 0 ? 'Kalıcı' : b.duration + ' dk'}</td>
            <td>${b.admin}</td>
            <td>${b.created_at}</td>
          </tr>`).join('');
      } else {
        tbody.innerHTML = '<tr><td colspan="6" style="color:var(--muted);text-align:center;font-family:Share Tech Mono,monospace;font-size:11px;">Kayıt yok</td></tr>';
      }
    }
  });
}

function loadBans() {
  api('api/bans.php', { action: 'list' }).then(data => {
    const tbody = document.getElementById('ban-list');
    if (data.success && data.bans.length > 0) {
      tbody.innerHTML = data.bans.map(b => `
        <tr>
          <td><span style="font-family:Share Tech Mono,monospace;font-size:11px;">${b.steamid}</span></td>
          <td>${b.name}</td>
          <td>${b.reason}</td>
          <td>${b.duration == 0 ? '<span class="badge badge-red">Kalıcı</span>' : b.duration + ' dk'}</td>
          <td>${b.admin}</td>
          <td><button class="action-btn success" onclick="unban(${b.id})">Unban</button></td>
        </tr>`).join('');
    } else {
      tbody.innerHTML = '<tr><td colspan="6" style="color:var(--muted);text-align:center;font-family:Share Tech Mono,monospace;font-size:11px;">Ban kaydı yok</td></tr>';
    }
  });
}

function addBan() {
  const data = {
    action: 'add',
    steamid: document.getElementById('ban-steamid').value,
    name: document.getElementById('ban-name').value,
    reason: document.getElementById('ban-reason').value,
    duration: document.getElementById('ban-duration').value
  };
  if (!data.steamid || !data.reason) { showAlert('ban-error', 'error', 'Steam ID ve sebep zorunlu!'); return; }
  api('api/bans.php', data).then(r => {
    if (r.success) { showAlert('ban-success', 'success', 'Ban eklendi!'); loadBans(); }
    else showAlert('ban-error', 'error', r.message || 'Hata!');
  });
}

function unban(id) {
  if (!confirm('Bu banı kaldırmak istediğinize emin misiniz?')) return;
  api('api/bans.php', { action: 'remove', id }).then(r => {
    if (r.success) { showAlert('ban-success', 'success', 'Ban kaldırıldı!'); loadBans(); }
    else showAlert('ban-error', 'error', 'Hata!');
  });
}

function loadAdmins() {
  api('api/admins.php', { action: 'list' }).then(data => {
    const tbody = document.getElementById('admin-list');
    const levels = { 1: 'Moderatör', 2: 'Admin', 3: 'Süper Admin' };
    const badges = { 1: 'badge-green', 2: 'badge-gold', 3: 'badge-red' };
    if (data.success && data.admins.length > 0) {
      tbody.innerHTML = data.admins.map(a => `
        <tr>
          <td>${a.username}</td>
          <td><span style="font-family:Share Tech Mono,monospace;font-size:11px;">${a.steamid || '-'}</span></td>
          <td><span class="badge ${badges[a.level]}">${levels[a.level]}</span></td>
          <td>${a.last_login || 'Hiç'}</td>
          <td><button class="action-btn danger" onclick="deleteAdmin(${a.id})">Sil</button></td>
        </tr>`).join('');
    } else {
      tbody.innerHTML = '<tr><td colspan="5" style="color:var(--muted);text-align:center;font-family:Share Tech Mono,monospace;font-size:11px;">Admin yok</td></tr>';
    }
  });
}

function addAdmin() {
  const data = {
    action: 'add',
    username: document.getElementById('admin-username').value,
    password: document.getElementById('admin-password').value,
    steamid: document.getElementById('admin-steamid').value,
    level: document.getElementById('admin-level').value
  };
  if (!data.username || !data.password) { showAlert('admin-error', 'error', 'Kullanıcı adı ve şifre zorunlu!'); return; }
  api('api/admins.php', data).then(r => {
    if (r.success) { showAlert('admin-success', 'success', 'Admin eklendi!'); loadAdmins(); }
    else showAlert('admin-error', 'error', r.message || 'Hata!');
  });
}

function deleteAdmin(id) {
  if (!confirm('Bu admini silmek istediğinize emin misiniz?')) return;
  api('api/admins.php', { action: 'delete', id }).then(r => {
    if (r.success) { showAlert('admin-success', 'success', 'Admin silindi!'); loadAdmins(); }
    else showAlert('admin-error', 'error', 'Hata!');
  });
}

function serverCmd(cmd) {
  const msgs = { restart: 'Sunucu yeniden başlatılıyor...', stop: 'Sunucu durduruluyor...', start: 'Sunucu başlatılıyor...' };
  if (!confirm(msgs[cmd] + ' Emin misiniz?')) return;
  api('api/server.php', { action: cmd }).then(r => {
    if (r.success) showAlert('server-success', 'success', r.message || 'İşlem başarılı!');
    else showAlert('server-error', 'error', r.message || 'Hata!');
  });
}

function changeMap() {
  const map = document.getElementById('map-select').value;
  api('api/server.php', { action: 'changelevel', map }).then(r => {
    if (r.success) showAlert('server-success', 'success', map + ' haritasına geçildi!');
    else showAlert('server-error', 'error', 'Harita değiştirilemedi!');
  });
}

function sendRcon() {
  const cmd = document.getElementById('console-cmd').value;
  if (!cmd) return;
  const box = document.getElementById('console-output');
  const line = document.createElement('div');
  line.className = 'console-line';
  line.textContent = cmd;
  box.appendChild(line);
  api('api/server.php', { action: 'rcon', command: cmd }).then(r => {
    const res = document.createElement('div');
    res.style.color = r.success ? '#d4d8e0' : 'var(--red)';
    res.style.paddingLeft = '14px';
    res.textContent = r.output || r.message || 'Komut gönderildi.';
    box.appendChild(res);
    box.scrollTop = box.scrollHeight;
  });
  document.getElementById('console-cmd').value = '';
}

loadDashboard();
</script>
</body>
</html>
