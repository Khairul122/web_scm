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
            <div class="col-lg-8">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title"><?= $mode == 'create' ? 'Tambah' : 'Edit' ?> Pengguna</h4>
                  
                  <?php if (isset($error)): ?>
                      <div class="alert alert-danger" role="alert">
                          <?= $error ?>
                      </div>
                  <?php endif; ?>

                  <form method="POST" action="?controller=User&action=<?= $mode == 'create' ? 'store' : 'update' ?><?= $mode == 'edit' ? '&id=' . $userData['id'] : '' ?>">
                      <div class="row">
                          <div class="col-md-6">
                              <div class="form-group">
                                  <label for="nama_lengkap">Nama Lengkap</label>
                                  <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" 
                                         value="<?= $userData['nama_lengkap'] ?? ($data['nama_lengkap'] ?? '') ?>" required>
                              </div>
                          </div>
                          <div class="col-md-6">
                              <div class="form-group">
                                  <label for="email">Email</label>
                                  <input type="email" class="form-control" id="email" name="email" 
                                         value="<?= $userData['email'] ?? ($data['email'] ?? '') ?>" required>
                              </div>
                          </div>
                      </div>

                      <div class="row">
                          <div class="col-md-6">
                              <div class="form-group">
                                  <label for="no_telepon">No. Telepon</label>
                                  <input type="text" class="form-control" id="no_telepon" name="no_telepon" 
                                         value="<?= $userData['no_telepon'] ?? ($data['no_telepon'] ?? '') ?>" required>
                              </div>
                          </div>
                          <div class="col-md-6">
                              <div class="form-group">
                                  <label for="role">Role</label>
                                  <select class="form-control" name="role" id="role" required onchange="toggleNamaToko()">
                                      <option value="">Pilih Role</option>
                                      <option value="admin" <?= (isset($userData['role']) && $userData['role'] == 'admin') || (isset($data['role']) && $data['role'] == 'admin') ? 'selected' : '' ?>>
                                          Admin
                                      </option>
                                      <option value="pembeli" <?= (isset($userData['role']) && $userData['role'] == 'pembeli') || (isset($data['role']) && $data['role'] == 'pembeli') ? 'selected' : '' ?>>
                                          Pembeli
                                      </option>
                                      <option value="pengepul" <?= (isset($userData['role']) && $userData['role'] == 'pengepul') || (isset($data['role']) && $data['role'] == 'pengepul') ? 'selected' : '' ?>>
                                          Pengepul
                                      </option>
                                      <option value="roasting" <?= (isset($userData['role']) && $userData['role'] == 'roasting') || (isset($data['role']) && $data['role'] == 'roasting') ? 'selected' : '' ?>>
                                          Roasting
                                      </option>
                                      <option value="penjual" <?= (isset($userData['role']) && $userData['role'] == 'penjual') || (isset($data['role']) && $data['role'] == 'penjual') ? 'selected' : '' ?>>
                                          Penjual
                                      </option>
                                  </select>
                              </div>
                          </div>
                      </div>

                      <div class="form-group">
                          <label for="alamat">Alamat</label>
                          <textarea class="form-control" id="alamat" name="alamat" rows="3" required><?= $userData['alamat'] ?? ($data['alamat'] ?? '') ?></textarea>
                      </div>

                      <div class="row">
                          <div class="col-md-6">
                              <div class="form-group" id="namaToko" style="display: none;">
                                  <label for="nama_toko">Nama Toko</label>
                                  <input type="text" class="form-control" id="nama_toko" name="nama_toko" 
                                         value="<?= $userData['nama_toko'] ?? ($data['nama_toko'] ?? '') ?>">
                                  <small class="form-text text-muted">Khusus untuk Pengepul, Roasting, dan Penjual</small>
                              </div>
                          </div>
                          <div class="col-md-6">
                              <div class="form-group">
                                  <label for="status">Status</label>
                                  <select class="form-control" name="status" id="status" required>
                                      <option value="aktif" <?= (isset($userData['status']) && $userData['status'] == 'aktif') || (isset($data['status']) && $data['status'] == 'aktif') || (!isset($userData['status']) && !isset($data['status'])) ? 'selected' : '' ?>>
                                          Aktif
                                      </option>
                                      <option value="nonaktif" <?= (isset($userData['status']) && $userData['status'] == 'nonaktif') || (isset($data['status']) && $data['status'] == 'nonaktif') ? 'selected' : '' ?>>
                                          Non-aktif
                                      </option>
                                  </select>
                              </div>
                          </div>
                      </div>

                      <div class="row">
                          <div class="col-md-6">
                              <div class="form-group">
                                  <label for="password">
                                      Password <?= $mode == 'edit' ? '(Kosongkan jika tidak ingin mengubah)' : '' ?>
                                  </label>
                                  <input type="password" class="form-control" id="password" name="password" 
                                         <?= $mode == 'create' ? 'required' : '' ?> minlength="6">
                                  <small class="form-text text-muted">Minimal 6 karakter</small>
                              </div>
                          </div>
                          <div class="col-md-6">
                              <div class="form-group">
                                  <label for="confirm_password">Konfirmasi Password</label>
                                  <input type="password" class="form-control" id="confirm_password" 
                                         <?= $mode == 'create' ? 'required' : '' ?>>
                                  <small class="form-text text-muted">Ulangi password yang sama</small>
                              </div>
                          </div>
                      </div>

                      <div class="d-flex justify-content-end">
                          <a href="?controller=User&action=index" class="btn btn-secondary me-2">Batal</a>
                          <button type="submit" class="btn btn-primary" onclick="return validateForm()">
                              <?= $mode == 'create' ? 'Tambah' : 'Update' ?> Pengguna
                          </button>
                      </div>
                  </form>
                </div>
              </div>
            </div>

            <div class="col-lg-4">
              <div class="card">
                <div class="card-body">
                  <h6 class="card-title">Informasi Role</h6>
                  <div class="mb-3">
                      <strong>Admin:</strong><br>
                      <small class="text-muted">Memiliki akses penuh ke sistem.</small>
                  </div>
                  <div class="mb-3">
                      <strong>Pembeli:</strong><br>
                      <small class="text-muted">Dapat melakukan pembelian produk.</small>
                  </div>
                  <div class="mb-3">
                      <strong>Pengepul:</strong><br>
                      <small class="text-muted">Mengelola pembelian dari petani.</small>
                  </div>
                  <div class="mb-3">
                      <strong>Roasting:</strong><br>
                      <small class="text-muted">Mengelola proses roasting kopi.</small>
                  </div>
                  <div class="mb-3">
                      <strong>Penjual:</strong><br>
                      <small class="text-muted">Mengelola penjualan produk akhir.</small>
                  </div>
                </div>
              </div>

              <div class="card mt-3">
                <div class="card-body">
                  <h6 class="card-title">Tips</h6>
                  <div class="d-flex align-items-center mb-2">
                      <i class="mdi mdi-check text-success me-2"></i>
                      <small>Email harus unik</small>
                  </div>
                  <div class="d-flex align-items-center mb-2">
                      <i class="mdi mdi-check text-success me-2"></i>
                      <small>No. telepon harus unik</small>
                  </div>
                  <div class="d-flex align-items-center mb-2">
                      <i class="mdi mdi-check text-success me-2"></i>
                      <small>Password minimal 6 karakter</small>
                  </div>
                  <div class="d-flex align-items-center">
                      <i class="mdi mdi-check text-success me-2"></i>
                      <small>Nama toko wajib untuk role tertentu</small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
  function toggleNamaToko() {
      const role = document.getElementById('role').value;
      const namaTokoDiv = document.getElementById('namaToko');
      const namaTokoInput = document.getElementById('nama_toko');
      
      if (role === 'pengepul' || role === 'roasting' || role === 'penjual') {
          namaTokoDiv.style.display = 'block';
          namaTokoInput.required = true;
      } else {
          namaTokoDiv.style.display = 'none';
          namaTokoInput.required = false;
          namaTokoInput.value = '';
      }
  }

  function validateForm() {
      const password = document.getElementById('password').value;
      const confirmPassword = document.getElementById('confirm_password').value;
      const mode = '<?= $mode ?>';
      
      if (mode === 'create' || password !== '') {
          if (password !== confirmPassword) {
              alert('Password dan konfirmasi password tidak cocok!');
              return false;
          }
          
          if (password.length < 6) {
              alert('Password minimal 6 karakter!');
              return false;
          }
      }
      
      const role = document.getElementById('role').value;
      const namaToko = document.getElementById('nama_toko').value;
      
      if ((role === 'pengepul' || role === 'roasting' || role === 'penjual') && namaToko.trim() === '') {
          alert('Nama toko wajib diisi untuk role ' + role + '!');
          return false;
      }
      
      return true;
  }

  document.addEventListener('DOMContentLoaded', function() {
      toggleNamaToko();
      
      const passwordInput = document.getElementById('password');
      const confirmPasswordInput = document.getElementById('confirm_password');
      
      function checkPasswordMatch() {
          if (confirmPasswordInput.value !== '') {
              if (passwordInput.value !== confirmPasswordInput.value) {
                  confirmPasswordInput.setCustomValidity('Password tidak cocok');
              } else {
                  confirmPasswordInput.setCustomValidity('');
              }
          }
      }
      
      passwordInput.addEventListener('input', checkPasswordMatch);
      confirmPasswordInput.addEventListener('input', checkPasswordMatch);
  });
  </script>

  <?php include 'template/script.php'; ?>
</body>

</html>