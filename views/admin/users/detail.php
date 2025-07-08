<?php include('template/header.php'); ?>

<body class="with-welcome-text">
  <div class="container-scroller">
    <?php include 'template/navbar.php'; ?>
    <div class="container-fluid page-body-wrapper">
      <?php include 'template/setting_panel.php'; ?>
      <?php include 'template/sidebar.php'; ?>
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="row">
            <div class="col-md-8">
              <div class="card">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title">Detail Pengguna</h4>
                    <div>
                      <?php if (isset($userData['id'])): ?>
                      <a href="?controller=User&action=edit&id=<?= $userData['id'] ?>" class="btn btn-warning btn-sm me-2">
                        <i class="mdi mdi-pencil"></i> Edit
                      </a>
                      <?php endif; ?>
                      <a href="?controller=User&action=index" class="btn btn-secondary btn-sm">
                        <i class="mdi mdi-arrow-left"></i> Kembali
                      </a>
                    </div>
                  </div>

                  <?php if (!isset($userData) || !$userData): ?>
                    <div class="alert alert-danger">
                      <h5>Data pengguna tidak ditemukan</h5>
                      <p>Silakan kembali ke halaman utama dan coba lagi.</p>
                    </div>
                  <?php else: ?>

                  <div class="table-responsive">
                    <table class="table table-borderless">
                      <tr>
                        <td width="200"><strong>ID</strong></td>
                        <td><?= $userData['id'] ?? '-' ?></td>
                      </tr>
                      <tr>
                        <td><strong>Nama Lengkap</strong></td>
                        <td><?= $userData['nama_lengkap'] ?? '-' ?></td>
                      </tr>
                      <tr>
                        <td><strong>Email</strong></td>
                        <td><?= $userData['email'] ?? '-' ?></td>
                      </tr>
                      <tr>
                        <td><strong>No. Telepon</strong></td>
                        <td><?= $userData['no_telepon'] ?? '-' ?></td>
                      </tr>
                      <tr>
                        <td><strong>Role</strong></td>
                        <td>
                          <span class="badge badge-info">
                            <?= ucfirst($userData['role'] ?? '-') ?>
                          </span>
                        </td>
                      </tr>
                      <tr>
                        <td><strong>Status</strong></td>
                        <td>
                          <span class="badge <?= ($userData['status'] ?? '') == 'aktif' ? 'badge-success' : 'badge-danger' ?>">
                            <?= ucfirst($userData['status'] ?? 'nonaktif') ?>
                          </span>
                        </td>
                      </tr>
                      <tr>
                        <td><strong>Alamat</strong></td>
                        <td><?= $userData['alamat'] ?? '-' ?></td>
                      </tr>
                      <?php if (!empty($userData['nama_toko'])): ?>
                      <tr>
                        <td><strong>Nama Toko</strong></td>
                        <td><?= $userData['nama_toko'] ?></td>
                      </tr>
                      <?php endif; ?>
                      <tr>
                        <td><strong>Tanggal Dibuat</strong></td>
                        <td><?= !empty($userData['created_at']) ? date('d/m/Y H:i', strtotime($userData['created_at'])) : '-' ?></td>
                      </tr>
                    </table>
                  </div>
                  <?php endif; ?>
                </div>
              </div>
            </div>

            <div class="col-md-4">
              <div class="card">
                <div class="card-body">
                  <h6 class="card-title">Aksi</h6>
                  <?php if (isset($userData['id'])): ?>
                  <div class="d-grid gap-2">
                    <button class="btn btn-primary btn-sm" onclick="resetPassword()">
                      <i class="mdi mdi-key"></i> Reset Password
                    </button>
                    <button class="btn btn-secondary btn-sm" onclick="toggleStatus()">
                      <i class="mdi mdi-toggle-switch"></i> 
                      <?= ($userData['status'] ?? '') == 'aktif' ? 'Nonaktifkan' : 'Aktifkan' ?>
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="deleteUser()">
                      <i class="mdi mdi-delete"></i> Hapus
                    </button>
                  </div>
                  <?php else: ?>
                  <p class="text-muted">Aksi tidak tersedia</p>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php if (isset($userData['id'])): ?>
  <div class="modal fade" id="resetPasswordModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Reset Password</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Password Baru</label>
            <input type="password" class="form-control" id="newPassword" minlength="6">
          </div>
          <div class="form-group">
            <label>Konfirmasi Password</label>
            <input type="password" class="form-control" id="confirmPassword">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="button" class="btn btn-primary" onclick="submitResetPassword()">Reset</button>
        </div>
      </div>
    </div>
  </div>

  <script>
  function resetPassword() {
    document.getElementById('newPassword').value = '';
    document.getElementById('confirmPassword').value = '';
    new bootstrap.Modal(document.getElementById('resetPasswordModal')).show();
  }

  function submitResetPassword() {
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    
    if (newPassword !== confirmPassword) {
      alert('Password tidak cocok');
      return;
    }
    
    if (newPassword.length < 6) {
      alert('Password minimal 6 karakter');
      return;
    }
    
    fetch(`?controller=User&action=resetPassword&id=<?= $userData['id'] ?>`, {
      method: 'PATCH',
      headers: {
        'Authorization': `Bearer ${getCookie('auth_token')}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ password: newPassword })
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        bootstrap.Modal.getInstance(document.getElementById('resetPasswordModal')).hide();
        alert('Password berhasil direset!');
      } else {
        alert('Gagal reset password: ' + (data.message || 'Unknown error'));
      }
    })
    .catch(error => {
      alert('Terjadi kesalahan saat reset password');
    });
  }

  function toggleStatus() {
    const currentStatus = '<?= $userData['status'] ?? 'nonaktif' ?>';
    const newStatus = currentStatus === 'aktif' ? 'nonaktif' : 'aktif';
    
    if (confirm(`Apakah Anda yakin ingin ${newStatus === 'aktif' ? 'mengaktifkan' : 'menonaktifkan'} pengguna ini?`)) {
      fetch(`?controller=User&action=updateStatus&id=<?= $userData['id'] ?>`, {
        method: 'PATCH',
        headers: {
          'Authorization': `Bearer ${getCookie('auth_token')}`,
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ status: newStatus })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          location.reload();
        } else {
          alert('Gagal mengubah status: ' + (data.message || 'Unknown error'));
        }
      })
      .catch(error => {
        alert('Terjadi kesalahan saat mengubah status');
      });
    }
  }

  function deleteUser() {
    if (confirm('Apakah Anda yakin ingin menghapus pengguna ini?')) {
      fetch(`?controller=User&action=delete&id=<?= $userData['id'] ?>`, {
        method: 'DELETE',
        headers: {
          'Authorization': `Bearer ${getCookie('auth_token')}`,
          'Content-Type': 'application/json'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('Pengguna berhasil dihapus!');
          window.location.href = '?controller=User&action=index';
        } else {
          alert('Gagal menghapus pengguna: ' + (data.message || 'Unknown error'));
        }
      })
      .catch(error => {
        alert('Terjadi kesalahan saat menghapus pengguna');
      });
    }
  }

  function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
  }
  </script>
  <?php endif; ?>

  <?php include 'template/script.php'; ?>
</body>
</html>