<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ลงทะเบียน - RSP South Digital Board</title>

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="Register.css" />
</head>
<body>

  <div class="layout">
    
    <!-- ฝั่งซ้าย: รูปแบรนดิ้งและชื่อระบบ -->
    <aside class="panel-left">
      <div class="brand-area">
        <img src="./south_2.png" alt="RSP South Logo" class="brand-logo-img" onerror="this.style.display='none'">
        <p class="panel-desc">
          ระบบบริหารจัดการเนื้อหาและจอแสดงผลดิจิทัลสำหรับ Regional Science Park South
        </p>
      </div>

      <div class="screen-illo">
        <div class="illo-monitor">
          <div class="illo-content">
            <div class="illo-block wide"></div>
            <div class="illo-block tall"></div>
            <div class="illo-block"></div>
            <div class="illo-block"></div>
            <div class="illo-block"></div>
          </div>
        </div>
        <div class="illo-stand"></div>
        <div class="illo-base"></div>
      </div>

      <div>
        <p class="panel-footer-text">
          Regional Science Park South © 2026<br>
          Digital Signage Platform
        </p>
      </div>
    </aside>

    <!-- ฝั่งขวา: ฟอร์มกรอกข้อมูล -->
    <main class="panel-right">
      <div class="form-container">
        
        <div class="form-header">
          <p class="form-eyebrow">RSP South DigiBoard</p>
          <h1 class="form-heading">สร้างบัญชีใหม่</h1>
          <p class="form-subtext">
            กรอกข้อมูลเพื่อลงทะเบียนเข้าใช้งานระบบ<br>
            จัดการจอแสดงผล RSP South
          </p>
        </div>

        <form id="registerForm" method="POST" action="register_process.php">

          <!-- แถวสำหรับ ชื่อ-นามสกุล -->
          <div class="name-row">
            <div class="field">
              <label for="firstName">ชื่อ</label>
              <div class="input-wrap">
                <span class="input-icon">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="12" cy="8" r="4"/>
                    <path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/>
                  </svg>
                </span>
                <input type="text" id="firstName" name="first_name" required placeholder="ชื่อจริง" />
              </div>
            </div>

            <div class="field">
              <label for="lastName">นามสกุล</label>
              <div class="input-wrap">
                <span class="input-icon">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="12" cy="8" r="4"/>
                    <path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/>
                  </svg>
                </span>
                <input type="text" id="lastName" name="last_name" required placeholder="นามสกุล" />
              </div>
            </div>
          </div>

          <div class="field">
            <label for="username">ชื่อผู้ใช้งาน</label>
            <div class="input-wrap">
              <span class="input-icon">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                  <circle cx="12" cy="8" r="4"/>
                  <path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/>
                </svg>
              </span>
              <input type="text" id="username" name="username" required placeholder="ตั้งชื่อผู้ใช้งาน (Username)" />
            </div>
          </div>

          <div class="field">
            <label for="password">รหัสผ่าน</label>
            <div class="input-wrap">
              <span class="input-icon">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                  <rect x="3" y="11" width="18" height="11" rx="2"/>
                  <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
              </span>
              <input type="password" id="password" name="password" required placeholder="ตั้งรหัสผ่าน (อย่างน้อย 4 ตัวอักษร)" minlength="4" />
              <button type="button" class="toggle-pw" onclick="togglePassword('password')">👁</button>
            </div>
          </div>

          <div class="field">
            <label for="confirmPassword">ยืนยันรหัสผ่าน</label>
            <div class="input-wrap">
              <span class="input-icon">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                  <rect x="3" y="11" width="18" height="11" rx="2"/>
                  <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
              </span>
              <input type="password" id="confirmPassword" name="confirm_password" required placeholder="กรอกรหัสผ่านอีกครั้ง" />
              <button type="button" class="toggle-pw" onclick="togglePassword('confirmPassword')">👁</button>
            </div>
          </div>

          <button type="submit" class="btn-submit" id="submitBtn">
            ลงทะเบียน
          </button>

          <!-- แจ้งเตือนข้อผิดพลาดรหัสผ่านไม่ตรงกัน ปรับให้เข้ากับ CSS คลาส .msg-box และ .msg-error -->
          <div class="msg-box msg-error" id="errorMsg">
            รหัสผ่านและการยืนยันรหัสผ่านไม่ตรงกัน กรุณาตรวจสอบอีกครั้ง
          </div>

        </form>

        <div class="form-footer" id="formFooter">
          มีบัญชีอยู่แล้วใช่ไหม? 
          <!-- เชื่อมโยงไปยังหน้าล็อกอิน PHP ที่เปลี่ยนชื่อแล้ว -->
          <a href="login.php">เข้าสู่ระบบ</a>
        </div>

      </div>
    </main>

  </div>

  <script>
    // ฟังก์ชันสำหรับเปิด/ปิดการมองเห็นรหัสผ่าน
    function togglePassword(inputId) {
      const input = document.getElementById(inputId);
      input.type = input.type === 'password' ? 'text' : 'password';
    }

    document.getElementById('registerForm').addEventListener('submit', function(e) {
      const password = document.getElementById('password').value;
      const confirmPassword = document.getElementById('confirmPassword').value;
      const errorMsg = document.getElementById('errorMsg');
      const submitBtn = document.getElementById('submitBtn');

      errorMsg.classList.remove('show');

      // ตรวจสอบรหัสผ่านตรงกัน
      if (password !== confirmPassword) {
        e.preventDefault(); 
        errorMsg.classList.add('show');
        document.getElementById('confirmPassword').focus();
        return;
      }

      submitBtn.disabled = true;
      submitBtn.innerHTML = `
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="animation: spin 1s linear infinite; margin-right: 8px;">
          <line x1="12" y1="2" x2="12" y2="6"></line>
          <line x1="12" y1="18" x2="12" y2="22"></line>
          <line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line>
          <line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line>
          <line x1="2" y1="12" x2="6" y2="12"></line>
          <line x1="18" y1="12" x2="22" y2="12"></line>
          <line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line>
          <line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line>
        </svg>
        กำลังดำเนินการ...
      `;
    });
  </script>
</body>
</html>