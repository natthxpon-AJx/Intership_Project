<?php
// เริ่มต้นใช้งาน Session
session_start();

// เรียกใช้ไฟล์เชื่อมต่อฐานข้อมูล
require 'db.php'; 

// ดักจับผู้ใช้งาน: หากยังไม่ได้ล็อกอิน ให้เด้งกลับไปหน้า login.php
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// ตรวจสอบสิทธิ์: เฉพาะ admin เท่านั้นที่เข้าได้
if ($_SESSION['role'] !== 'admin') {
    echo "<script>
            alert('คุณไม่มีสิทธิ์เข้าถึงหน้านี้ เฉพาะผู้ดูแลระบบเท่านั้น');
            window.location.href = 'login.php';
          </script>";
    exit;
}

$admin_full_name = htmlspecialchars($_SESSION['first_name'] . " " . $_SESSION['last_name']);

// --- ส่วนที่เพิ่มใหม่: ดึงข้อมูลผู้ใช้งานทั้งหมดจากฐานข้อมูล ---
try {
    $sql = "SELECT * FROM users ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $all_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("ไม่สามารถดึงข้อมูลได้: " . $e->getMessage());
}

// ฟังก์ชันสำหรับแปลงวันที่ให้อ่านง่ายขึ้น (Optional)
function formatThaiDate($dateString) {
    $time = strtotime($dateString);
    return date('d/m/Y', $time);
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=1280, initial-scale=1.0" />
  <title>จัดการผู้ใช้งาน - RSP South Digital Board</title>

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="Account.css" />
</head>
<body>

  <div class="dashboard-layout">
    
    <!-- Sidebar (เมนูด้านซ้ายสำหรับเดสก์ท็อป) -->
    <aside class="sidebar">
      <div class="sidebar-header">
        <img src="./south_2.png" alt="Logo" class="logo-img" id="mainLogo" onerror="this.style.display='none'">
        <h2 class="logo-text">Digital Board</h2>
      </div>

      <nav class="sidebar-nav">
        <a href="#" class="nav-item active">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
          จัดการผู้ใช้งาน
        </a>
      </nav>

      <div class="sidebar-footer">
        <a href="logout.php" class="nav-item text-danger">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
          ออกจากระบบ
        </a>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      
      <!-- Header -->
      <header class="top-header">
        <div class="header-text">
          <h1 class="page-title">จัดการบัญชีผู้ใช้งาน</h1>
          <p class="page-subtitle">จัดการสิทธิ์และรายชื่อผู้ที่สามารถเข้าถึงระบบ Digital Board</p>
        </div>
        <div class="user-profile">
          <div class="avatar" id="mainAvatar">-</div>
          <div class="user-info">
            <span class="user-name" id="profileName"><?php echo $admin_full_name; ?></span>
            <span class="user-role">ผู้ดูแลระบบ</span>
          </div>
        </div>
      </header>

      <!-- Stats Cards -->
      <section class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon bg-accent-lt text-accent">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
          </div>
          <div class="stat-details">
            <p class="stat-label">ผู้ใช้งานทั้งหมด</p>
            <h3 class="stat-value" id="statTotal">0</h3>
          </div>
        </div>
        
        <div class="stat-card">
          <div class="stat-icon bg-success-lt text-success">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
          </div>
          <div class="stat-details">
            <p class="stat-label">บัญชีที่ใช้งานได้ (Active)</p>
            <h3 class="stat-value" id="statActive">0</h3>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon" style="background: var(--inactive-bg); color: var(--inactive);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>
          </div>
          <div class="stat-details">
            <p class="stat-label">บัญชีที่ถูกระงับ</p>
            <h3 class="stat-value" id="statInactive">0</h3>
          </div>
        </div>
      </section>

      <!-- Table Section -->
      <section class="table-section">
        
        <!-- Controls -->
        <div class="table-controls">
          <div class="search-filter-group">
            <div class="search-box">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
              <input type="text" id="searchInput" placeholder="ค้นหาชื่อ หรือ Username...">
            </div>
            <select class="filter-box" id="statusFilter" onchange="filterByStatus(this.value)">
              <option value="">ทุกสถานะ</option>
              <option value="active">ใช้งานได้</option>
              <option value="inactive">ถูกระงับ</option>
            </select>
          </div>
        </div>

        <!-- Data Table -->
        <div class="table-responsive">
          <table class="data-table">
            <thead>
              <tr>
                <th>ชื่อ-นามสกุล</th>
                <th>Username</th>
                <th>สิทธิ์การใช้งาน (Role)</th>
                <th>สถานะ (Status)</th>
                <th>วันที่ลงทะเบียน</th>
                <th class="text-right">จัดการ</th>
              </tr>
            </thead>
            <tbody id="userTableBody">
              
              <!-- วนลูปแสดงข้อมูลจริงจาก Database ด้วย PHP -->
              <?php foreach ($all_users as $user): 
                  $full_name = htmlspecialchars($user['first_name'] . ' ' . $user['last_name']);
                  $username = htmlspecialchars($user['username']);
                  $role = $user['role'];
                  $status = $user['status'];
                  $created_date = formatThaiDate($user['created_at']);
                  $row_id = 'row-' . $user['id'];
                  $is_suspended_class = ($status === 'inactive') ? 'suspended-row' : '';
              ?>
              <tr id="<?php echo $row_id; ?>" class="<?php echo $is_suspended_class; ?>">
                <td>
                  <div class="user-cell">
                    <!-- ตัวอักษรย่อจะถูกคำนวณด้วย JS ตามปกติ -->
                    <div class="avatar-sm bg-accent-lt text-accent">-</div>
                    <div>
                      <p class="name"><?php echo $full_name; ?></p>
                    </div>
                  </div>
                </td>
                <td><?php echo $username; ?></td>
                <td>
                  <select class="role-select <?php echo $role === 'admin' ? 'admin-role' : 'user-role'; ?>" onchange="updateRoleStyle(this)">
                    <option value="user" <?php echo $role === 'user' ? 'selected' : ''; ?>>ผู้ใช้ทั่วไป</option>
                    <option value="admin" <?php echo $role === 'admin' ? 'selected' : ''; ?>>ผู้ดูแลระบบ</option>
                  </select>
                </td>
                <td>
                  <?php if ($status === 'active'): ?>
                    <span class="status-badge badge-active">ใช้งานได้</span>
                  <?php else: ?>
                    <span class="status-badge badge-inactive">ถูกระงับ</span>
                  <?php endif; ?>
                </td>
                <td><?php echo $created_date; ?></td>
                <td class="text-right action-btns">
                  <button class="btn-icon" title="ประวัติการเข้าใช้งาน" onclick="openHistoryModal('<?php echo $full_name; ?>', '<?php echo $username; ?>')">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                  </button>
                  <button class="btn-icon" title="แก้ไข" onclick="openEditModal('<?php echo $row_id; ?>')">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                  </button>
                  <button class="btn-icon btn-reject text-danger" title="ระงับ/ลบ" onclick="deleteUser('<?php echo $row_id; ?>')">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                  </button>
                </td>
              </tr>
              <?php endforeach; ?>
              <!-- จบการวนลูป PHP -->

            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="pagination">
          <p class="text-soft text-sm" id="paginationInfo">แสดง 0 ถึง 0 จาก 0 รายการ</p>
          <div class="page-controls" id="paginationControls">
            <!-- ปุ่มจะถูกสร้างอัตโนมัติด้วย JavaScript -->
          </div>
        </div>

      </section>

    </main>
  </div>

  <!-- Modal ประวัติการเข้าใช้งาน -->
  <div id="historyModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title">ประวัติการเข้าใช้งาน - <span id="historyUserName"></span></h2>
        <button class="btn-close" onclick="closeHistoryModal()">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="data-table history-table">
            <thead>
              <tr>
                <th>วันที่-เวลา</th>
                <th>IP Address</th>
                <th>พื้นที่เข้าใช้งาน</th>
                <th>อุปกรณ์ / เบราว์เซอร์</th>
                <th>สถานะ</th>
              </tr>
            </thead>
            <tbody id="historyTableBody">
              <!-- ข้อมูลจำลองสำหรับประวัติ -->
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal แก้ไขข้อมูลผู้ใช้งาน -->
  <div id="editModal" class="modal">
    <div class="modal-content" style="max-width: 450px;">
      <div class="modal-header">
        <h2 class="modal-title">แก้ไขข้อมูลผู้ใช้งาน</h2>
        <button class="btn-close" onclick="closeEditModal()">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label>ชื่อ-นามสกุล</label>
          <input type="text" id="editName" class="form-control" placeholder="กรอกชื่อ-นามสกุล">
        </div>
        <div class="form-group">
          <label>Username (ไม่สามารถแก้ไขได้)</label>
          <input type="text" id="editUsername" class="form-control" disabled>
        </div>
        <div class="form-group">
          <label>สถานะบัญชี</label>
          <select id="editStatus" class="form-control">
            <option value="active">ใช้งานได้ (Active)</option>
            <option value="inactive">ถูกระงับ (Inactive)</option>
          </select>
        </div>
        <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 2rem;">
          <button onclick="closeEditModal()" style="padding: 10px 16px; border: 1px solid var(--border-dk); background: white; border-radius: 8px; cursor: pointer; font-weight: 500;">ยกเลิก</button>
          <button onclick="saveEditUser()" style="padding: 10px 16px; border: none; background: var(--accent); color: white; border-radius: 8px; cursor: pointer; font-weight: 600;">บันทึกการเปลี่ยนแปลง</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    // ─── ฟังก์ชันคำนวณตัวอักษรย่อสำหรับภาพโปรไฟล์ (Avatar Initials) ───
    function getInitials(name) {
      if (!name) return '?';
      name = name.trim();
      let firstChar = name.charAt(0);
      
      const leadingVowels = ['เ', 'แ', 'โ', 'ใ', 'ไ'];
      
      if (leadingVowels.includes(firstChar) && name.length > 1) {
        return name.charAt(1).toUpperCase();
      }
      
      return firstChar.toUpperCase();
    }

    // ─── ฟังก์ชันสร้างตัวย่อรูปโปรไฟล์ทั้งหมดในตารางและ Header ───
    function initializeAvatars() {
      const rows = document.querySelectorAll('#userTableBody tr:not(.empty-state-row)');
      rows.forEach(row => {
        const nameEl = row.querySelector('.name');
        const avatarEl = row.querySelector('.avatar-sm');
        if (nameEl && avatarEl) {
          avatarEl.textContent = getInitials(nameEl.textContent);
        }
      });

      const profileNameEl = document.getElementById('profileName');
      const mainAvatarEl = document.getElementById('mainAvatar');
      if (profileNameEl && mainAvatarEl) {
        mainAvatarEl.textContent = getInitials(profileNameEl.textContent);
      }
    }

    // ฟังก์ชันคำนวณหาจำนวนผู้ดูแลระบบ (Admin) ที่อยู่ในสถานะ Active
    function getActiveAdminCount() {
      const rows = document.querySelectorAll('#userTableBody tr:not(.empty-state-row)');
      let count = 0;
      rows.forEach(row => {
        const roleSelect = row.querySelector('.role-select');
        const statusBadge = row.querySelector('.status-badge');
        const isAdmin = roleSelect && roleSelect.value === 'admin';
        const isActive = statusBadge && statusBadge.classList.contains('badge-active');
        if (isAdmin && isActive) {
          count++;
        }
      });
      return count;
    }

    // ฟังก์ชันสำหรับเปลี่ยน Style ของตัวเลือก Role 
    function updateRoleStyle(selectElement) {
      const row = selectElement.closest('tr');
      const statusBadge = row.querySelector('.status-badge');
      const isActive = statusBadge && statusBadge.classList.contains('badge-active');

      if (selectElement.value === 'user') {
        if (isActive && getActiveAdminCount() < 1) {
          alert('ไม่สามารถเปลี่ยนบทบาทได้ เนื่องจากต้องมีผู้ดูแลระบบที่ใช้งานได้อย่างน้อย 1 คนในระบบ');
          selectElement.value = 'admin'; 
          selectElement.className = 'role-select admin-role';
          return;
        }
        selectElement.className = 'role-select user-role';
      } else {
        selectElement.className = 'role-select admin-role';
      }
      updateStatistics();
    }

    // ฟังก์ชันอัปเดตสถิติจำนวนผู้ใช้งานตามจริง
    function updateStatistics() {
      const rows = document.querySelectorAll('#userTableBody tr:not(.empty-state-row)');
      let total = 0;
      let active = 0;
      let inactive = 0;

      rows.forEach(row => {
        total++;
        const statusBadge = row.querySelector('.status-badge');
        if (statusBadge) {
          if (statusBadge.classList.contains('badge-active')) {
            active++;
          } else if (statusBadge.classList.contains('badge-inactive')) {
            inactive++;
          }
        }
      });

      document.getElementById('statTotal').textContent = total;
      document.getElementById('statActive').textContent = active;
      document.getElementById('statInactive').textContent = inactive;
    }

    // เรียกใช้งานฟังก์ชันเมื่อเริ่มโหลดหน้าเว็บ
    document.addEventListener('DOMContentLoaded', () => {
      initializeAvatars(); 
      updateStatistics();
      renderTable();
    });

    // สคริปต์ลบ/ระงับผู้ใช้งาน (อัปเดตเฉพาะ UI - อนาคตต้องต่อ API หรือ PHP)
    function deleteUser(rowId) {
      const row = document.getElementById(rowId);
      if(!row) return;

      const roleSelect = row.querySelector('.role-select');
      const statusBadge = row.querySelector('.status-badge');
      const isAdmin = roleSelect && roleSelect.value === 'admin';
      const isActive = statusBadge && statusBadge.classList.contains('badge-active');

      if (isAdmin && isActive && getActiveAdminCount() <= 1) {
        alert('ไม่สามารถลบหรือระงับผู้ใช้งานรายนี้ได้ เนื่องจากเป็นผู้ดูแลระบบที่ใช้งานได้คนสุดท้ายในระบบ');
        return;
      }

      if(confirm('คุณแน่ใจหรือไม่ว่าต้องการระงับหรือลบผู้ใช้งานรายนี้?')) {
        row.style.opacity = '0.5';
        setTimeout(() => {
          row.remove();
          updateStatistics();
          renderTable();
        }, 300);
      }
    }

    // ฟังก์ชันจัดการ Modal ประวัติการเข้าใช้งาน
    function openHistoryModal(userName, usernameId) {
      document.getElementById('historyUserName').textContent = `${userName} (@${usernameId})`;
      const modal = document.getElementById('historyModal');
      modal.style.display = 'flex';
      setTimeout(() => modal.classList.add('show'), 10);
      
      const tbody = document.getElementById('historyTableBody');
      
      // ตัวอย่างข้อมูลจำลอง
      let mockDataHTML = `
          <tr>
            <td>27 พ.ค. 2026, 09:30 น.</td>
            <td>192.168.1.45</td>
            <td>หาดใหญ่, สงขลา</td>
            <td>Chrome / Windows</td>
            <td><span class="status-badge badge-active">เข้าสู่ระบบสำเร็จ</span></td>
          </tr>
      `;
      tbody.innerHTML = mockDataHTML;
    }

    function closeHistoryModal() {
      const modal = document.getElementById('historyModal');
      modal.classList.remove('show');
      setTimeout(() => modal.style.display = 'none', 200);
    }
    
    // ─── ฟังก์ชันจัดการ Modal แก้ไขผู้ใช้งาน ───
    let currentRowEditingId = null;

    function openEditModal(rowId) {
      currentRowEditingId = rowId;
      const row = document.getElementById(rowId);
      
      const name = row.querySelector('.name').textContent;
      const username = row.querySelectorAll('td')[1].textContent;
      const statusBadge = row.querySelector('.status-badge');
      
      let status = 'active';
      if(statusBadge.classList.contains('badge-inactive')) {
        status = 'inactive';
      }

      document.getElementById('editName').value = name;
      document.getElementById('editUsername').value = username;
      document.getElementById('editStatus').value = status;

      const modal = document.getElementById('editModal');
      modal.style.display = 'flex';
      setTimeout(() => modal.classList.add('show'), 10);
    }

    function closeEditModal() {
      const modal = document.getElementById('editModal');
      modal.classList.remove('show');
      setTimeout(() => modal.style.display = 'none', 200);
      currentRowEditingId = null;
    }

    function saveEditUser() {
      if(!currentRowEditingId) return;
      
      const row = document.getElementById(currentRowEditingId);
      const newName = document.getElementById('editName').value.trim();
      const newStatus = document.getElementById('editStatus').value;
      
      if(newName === '') {
        alert('กรุณากรอกชื่อ-นามสกุล');
        return;
      }

      const roleSelect = row.querySelector('.role-select');
      const isAdmin = roleSelect && roleSelect.value === 'admin';
      const statusBadge = row.querySelector('.status-badge');
      const isCurrentlyActive = statusBadge && statusBadge.classList.contains('badge-active');

      if (isAdmin && isCurrentlyActive && newStatus === 'inactive') {
        if (getActiveAdminCount() <= 1) {
          alert('ไม่สามารถระงับผู้ดูแลระบบรายนี้ได้ เนื่องจากเป็นผู้ดูแลระบบที่ใช้งานได้คนสุดท้ายในระบบ');
          return;
        }
      }
      
      row.querySelector('.name').textContent = newName;
      row.querySelector('.avatar-sm').textContent = getInitials(newName);
      
      const statusTd = row.querySelectorAll('td')[3];
      if(newStatus === 'active') {
        statusTd.innerHTML = '<span class="status-badge badge-active">ใช้งานได้</span>';
        row.classList.remove('suspended-row');
      } else {
        statusTd.innerHTML = '<span class="status-badge badge-inactive">ถูกระงับ</span>';
        row.classList.add('suspended-row');
      }
      
      closeEditModal();
      updateStatistics(); 
      renderTable(); 
      // หมายเหตุ: ปัจจุบันเป็นการอัปเดตบนหน้าจอ (UI) เท่านั้น ต้องทำ Backend API เพื่อบันทึกลง SQL จริง
    }

    window.onclick = function(event) {
      const historyModal = document.getElementById('historyModal');
      const editModal = document.getElementById('editModal');
      if (event.target == historyModal) {
        closeHistoryModal();
      }
      if (event.target == editModal) {
        closeEditModal();
      }
    }

    // ─── ตัวแปรสำหรับ Pagination และ Search ───
    let currentPage = 1;
    const rowsPerPage = 10; 
    let searchQuery = '';
    let statusQuery = '';

    function filterByStatus(statusValue) {
      statusQuery = statusValue;
      currentPage = 1;
      renderTable();
    }

    // ─── ฟังก์ชันจัดรูปแบบตาราง (กรองข้อมูล + แบ่งหน้า) ───
    function renderTable() {
      const tbody = document.getElementById('userTableBody');
      const rows = tbody.querySelectorAll('tr:not(.empty-state-row)');
      
      const existingEmpty = tbody.querySelector('.empty-state-row');
      if (existingEmpty) existingEmpty.remove();

      let matchedRows = [];

      rows.forEach(row => {
        const nameEl = row.querySelector('.name');
        const usernameEl = row.querySelectorAll('td')[1];
        const statusBadge = row.querySelector('.status-badge');

        const name = nameEl ? nameEl.textContent.toLowerCase() : '';
        const username = usernameEl ? usernameEl.textContent.toLowerCase() : '';
        
        let matchText = name.includes(searchQuery) || username.includes(searchQuery);
        let matchStatus = true;

        if (statusQuery === 'active') {
          matchStatus = statusBadge && statusBadge.classList.contains('badge-active');
        } else if (statusQuery === 'inactive') {
          matchStatus = statusBadge && statusBadge.classList.contains('badge-inactive');
        }

        if (matchText && matchStatus) {
          matchedRows.push(row);
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });

      const totalItems = matchedRows.length;

      if (totalItems === 0) {
        const emptyRow = document.createElement('tr');
        emptyRow.className = 'empty-state-row';
        emptyRow.innerHTML = `<td colspan="6" class="empty-row-text">ไม่พบข้อมูลบัญชีผู้ใช้งานในระบบขณะนี้</td>`;
        tbody.appendChild(emptyRow);
        
        document.getElementById('paginationInfo').textContent = `แสดง 0 ถึง 0 จาก 0 รายการ`;
        document.getElementById('paginationControls').innerHTML = '';
        return;
      }

      const totalPages = Math.ceil(totalItems / rowsPerPage) || 1;
      if (currentPage > totalPages) currentPage = totalPages;

      const startIndex = (currentPage - 1) * rowsPerPage;
      const endIndex = Math.min(startIndex + rowsPerPage, totalItems);

      matchedRows.forEach((row, index) => {
        if (index >= startIndex && index < endIndex) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });

      const displayStart = totalItems === 0 ? 0 : startIndex + 1;
      document.getElementById('paginationInfo').textContent = `แสดง ${displayStart} ถึง ${endIndex} จาก ${totalItems} รายการ`;

      const paginationControls = document.getElementById('paginationControls');
      if (paginationControls) {
        let buttonsHTML = `<button class="btn-page" ${currentPage === 1 ? 'disabled' : ''} onclick="changePage(${currentPage - 1})">ก่อนหน้า</button>`;
        
        for (let i = 1; i <= totalPages; i++) {
          buttonsHTML += `<button class="btn-page ${i === currentPage ? 'active' : ''}" onclick="changePage(${i})">${i}</button>`;
        }
        
        buttonsHTML += `<button class="btn-page" ${currentPage === totalPages ? 'disabled' : ''} onclick="changePage(${currentPage + 1})">ถัดไป</button>`;
        
        paginationControls.innerHTML = buttonsHTML;
      }
    }

    function changePage(page) {
      currentPage = page;
      renderTable();
    }

    document.getElementById('searchInput').addEventListener('input', function(e) {
      searchQuery = e.target.value.toLowerCase();
      currentPage = 1;
      renderTable();
    });
  </script>
</body>
</html>