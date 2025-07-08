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
            <div class="col-sm-12">
              <div class="card">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">Kelola Pengguna</h4>
                    <div>
                        <a href="?controller=User&action=stats" class="btn btn-info btn-sm me-2">
                            <i class="mdi mdi-chart-bar"></i> Statistik
                        </a>
                        <a href="?controller=User&action=create" class="btn btn-primary btn-sm">
                            <i class="mdi mdi-plus"></i> Tambah Pengguna
                        </a>
                    </div>
                  </div>
                  
                  <?php if (isset($_GET['success'])): ?>
                      <div class="alert alert-success alert-dismissible fade show" role="alert">
                          <?php if ($_GET['success'] == '1'): ?>
                              Pengguna berhasil ditambahkan!
                          <?php elseif ($_GET['success'] == '2'): ?>
                              Pengguna berhasil diperbarui!
                          <?php endif; ?>
                          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                      </div>
                  <?php endif; ?>

                  <?php if (isset($_GET['error'])): ?>
                      <div class="alert alert-danger alert-dismissible fade show" role="alert">
                          Terjadi kesalahan. Silakan coba lagi.
                          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                      </div>
                  <?php endif; ?>

                  <div class="row mb-3">
                      <div class="col-md-6">
                          <div class="input-group">
                              <input type="text" class="form-control" id="searchInput" placeholder="Cari pengguna...">
                              <button class="btn btn-outline-secondary" type="button" onclick="searchUsers()">
                                  <i class="mdi mdi-magnify"></i>
                              </button>
                          </div>
                      </div>
                      <div class="col-md-6">
                          <select class="form-select" id="roleFilter" onchange="filterByRole()">
                              <option value="">Semua Role</option>
                              <option value="admin">Admin</option>
                              <option value="pembeli">Pembeli</option>
                              <option value="pengepul">Pengepul</option>
                              <option value="roasting">Roasting</option>
                              <option value="penjual">Penjual</option>
                          </select>
                      </div>
                  </div>

                  <div class="table-responsive">
                      <table class="table table-striped" id="usersTable">
                          <thead>
                              <tr>
                                  <th>ID</th>
                                  <th>Nama Lengkap</th>
                                  <th>Email</th>
                                  <th>No. Telepon</th>
                                  <th>Role</th>
                                  <th>Status</th>
                                  <th>Aksi</th>
                              </tr>
                          </thead>
                          <tbody>
                              <?php if (isset($response['data']['data']) && is_array($response['data']['data'])): ?>
                                  <?php foreach ($response['data']['data'] as $userData): ?>
                                      <tr id="row-<?= $userData['id'] ?>">
                                          <td><?= $userData['id'] ?></td>
                                          <td><?= $userData['nama_lengkap'] ?></td>
                                          <td><?= $userData['email'] ?></td>
                                          <td><?= $userData['no_telepon'] ?></td>
                                          <td>
                                              <span class="badge badge-info">
                                                  <?= ucfirst($userData['role']) ?>
                                              </span>
                                          </td>
                                          <td>
                                              <span class="badge <?= $userData['status'] == 'aktif' ? 'badge-success' : 'badge-danger' ?>">
                                                  <?= ucfirst($userData['status']) ?>
                                              </span>
                                          </td>
                                          <td>
                                              <div class="btn-group" role="group">
                                                  <a href="?controller=User&action=show&id=<?= $userData['id'] ?>" 
                                                     class="btn btn-info btn-sm" title="Detail">
                                                      <i class="mdi mdi-eye"></i>
                                                  </a>
                                                  <a href="?controller=User&action=edit&id=<?= $userData['id'] ?>" 
                                                     class="btn btn-warning btn-sm" title="Edit">
                                                      <i class="mdi mdi-pencil"></i>
                                                  </a>
                                                  <button class="btn btn-secondary btn-sm" 
                                                          onclick="toggleStatus(<?= $userData['id'] ?>, '<?= $userData['status'] ?>')" 
                                                          title="Toggle Status">
                                                      <i class="mdi mdi-toggle-switch<?= $userData['status'] == 'aktif' ? '' : '-off' ?>"></i>
                                                  </button>
                                                  <button class="btn btn-primary btn-sm" 
                                                          onclick="resetPassword(<?= $userData['id'] ?>)" title="Reset Password">
                                                      <i class="mdi mdi-key"></i>
                                                  </button>
                                                  <button class="btn btn-danger btn-sm" 
                                                          onclick="deleteUser(<?= $userData['id'] ?>)" title="Hapus">
                                                      <i class="mdi mdi-delete"></i>
                                                  </button>
                                              </div>
                                          </td>
                                      </tr>
                                  <?php endforeach; ?>
                              <?php else: ?>
                                  <tr>
                                      <td colspan="7" class="text-center">Tidak ada data pengguna</td>
                                  </tr>
                              <?php endif; ?>
                          </tbody>
                      </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="resetPasswordModal" tabindex="-1">
      <div class="modal-dialog">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title">Reset Password</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                  <form id="resetPasswordForm">
                      <div class="mb-3">
                          <label for="newPassword" class="form-label">Password Baru</label>
                          <input type="password" class="form-control" id="newPassword" required minlength="6">
                          <div class="form-text">Minimal 6 karakter</div>
                      </div>
                      <div class="mb-3">
                          <label for="confirmPassword" class="form-label">Konfirmasi Password</label>
                          <input type="password" class="form-control" id="confirmPassword" required>
                      </div>
                  </form>
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                  <button type="button" class="btn btn-primary" onclick="submitResetPassword()">Reset Password</button>
              </div>
          </div>
      </div>
  </div>

  <script>
  let currentUserId = null;

  $(document).ready(function() {
      $('#usersTable').DataTable({
          "language": {
              "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
          }
      });
  });

  function searchUsers() {
      const query = document.getElementById('searchInput').value;
      if (query.length < 2) {
          showAlert('warning', 'Masukkan minimal 2 karakter untuk pencarian');
          return;
      }
      
      fetch(`?controller=User&action=search&q=${encodeURIComponent(query)}`, {
          headers: {
              'Authorization': `Bearer ${getCookie('auth_token')}`
          }
      })
      .then(response => response.json())
      .then(data => {
          if (data.success) {
              updateTable(data.data);
          } else {
              showAlert('danger', 'Pencarian gagal: ' + (data.message || 'Unknown error'));
          }
      })
      .catch(error => {
          console.error('Error:', error);
          showAlert('danger', 'Terjadi kesalahan saat mencari');
      });
  }

  function filterByRole() {
      const role = document.getElementById('roleFilter').value;
      const url = role ? `?controller=User&action=index&role=${role}` : '?controller=User&action=index';
      window.location.href = url;
  }

  function toggleStatus(id, currentStatus) {
      const newStatus = currentStatus === 'aktif' ? 'nonaktif' : 'aktif';
      
      if (confirm(`Apakah Anda yakin ingin mengubah status pengguna menjadi ${newStatus}?`)) {
          fetch(`?controller=User&action=updateStatus&id=${id}`, {
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
                  showAlert('danger', 'Gagal mengubah status: ' + (data.message || 'Unknown error'));
              }
          })
          .catch(error => {
              console.error('Error:', error);
              showAlert('danger', 'Terjadi kesalahan saat mengubah status');
          });
      }
  }

  function resetPassword(id) {
      currentUserId = id;
      document.getElementById('newPassword').value = '';
      document.getElementById('confirmPassword').value = '';
      new bootstrap.Modal(document.getElementById('resetPasswordModal')).show();
  }

  function submitResetPassword() {
      const newPassword = document.getElementById('newPassword').value;
      const confirmPassword = document.getElementById('confirmPassword').value;
      
      if (newPassword !== confirmPassword) {
          showAlert('danger', 'Password tidak cocok');
          return;
      }
      
      if (newPassword.length < 6) {
          showAlert('danger', 'Password minimal 6 karakter');
          return;
      }
      
      fetch(`?controller=User&action=resetPassword&id=${currentUserId}`, {
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
              showAlert('success', 'Password berhasil direset!');
          } else {
              showAlert('danger', 'Gagal reset password: ' + (data.message || 'Unknown error'));
          }
      })
      .catch(error => {
          console.error('Error:', error);
          showAlert('danger', 'Terjadi kesalahan saat reset password');
      });
  }

  function deleteUser(id) {
      if (confirm('Apakah Anda yakin ingin menghapus pengguna ini?')) {
          fetch(`?controller=User&action=delete&id=${id}`, {
              method: 'DELETE',
              headers: {
                  'Authorization': `Bearer ${getCookie('auth_token')}`,
                  'Content-Type': 'application/json'
              }
          })
          .then(response => response.json())
          .then(data => {
              if (data.success) {
                  document.getElementById(`row-${id}`).remove();
                  showAlert('success', 'Pengguna berhasil dihapus!');
              } else {
                  showAlert('danger', 'Gagal menghapus pengguna: ' + (data.message || 'Unknown error'));
              }
          })
          .catch(error => {
              console.error('Error:', error);
              showAlert('danger', 'Terjadi kesalahan saat menghapus pengguna');
          });
      }
  }

  function updateTable(users) {
      const tbody = document.querySelector('#usersTable tbody');
      tbody.innerHTML = '';
      
      if (users.length === 0) {
          tbody.innerHTML = '<tr><td colspan="7" class="text-center">Tidak ada data pengguna</td></tr>';
          return;
      }
      
      users.forEach(userData => {
          const row = `
              <tr id="row-${userData.id}">
                  <td>${userData.id}</td>
                  <td>${userData.nama_lengkap}</td>
                  <td>${userData.email}</td>
                  <td>${userData.no_telepon}</td>
                  <td><span class="badge badge-info">${userData.role.charAt(0).toUpperCase() + userData.role.slice(1)}</span></td>
                  <td><span class="badge ${userData.status === 'aktif' ? 'badge-success' : 'badge-danger'}">${userData.status.charAt(0).toUpperCase() + userData.status.slice(1)}</span></td>
                  <td>
                      <div class="btn-group" role="group">
                          <a href="?controller=User&action=show&id=${userData.id}" class="btn btn-info btn-sm" title="Detail">
                              <i class="mdi mdi-eye"></i>
                          </a>
                          <a href="?controller=User&action=edit&id=${userData.id}" class="btn btn-warning btn-sm" title="Edit">
                              <i class="mdi mdi-pencil"></i>
                          </a>
                          <button class="btn btn-secondary btn-sm" onclick="toggleStatus(${userData.id}, '${userData.status}')" title="Toggle Status">
                              <i class="mdi mdi-toggle-switch${userData.status === 'aktif' ? '' : '-off'}"></i>
                          </button>
                          <button class="btn btn-primary btn-sm" onclick="resetPassword(${userData.id})" title="Reset Password">
                              <i class="mdi mdi-key"></i>
                          </button>
                          <button class="btn btn-danger btn-sm" onclick="deleteUser(${userData.id})" title="Hapus">
                              <i class="mdi mdi-delete"></i>
                          </button>
                      </div>
                  </td>
              </tr>
          `;
          tbody.insertAdjacentHTML('beforeend', row);
      });
  }

  function showAlert(type, message) {
      const alertHtml = `
          <div class="alert alert-${type} alert-dismissible fade show" role="alert">
              ${message}
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
      `;
      const cardBody = document.querySelector('.card-body');
      cardBody.insertAdjacentHTML('afterbegin', alertHtml);
  }

  function getCookie(name) {
      const value = `; ${document.cookie}`;
      const parts = value.split(`; ${name}=`);
      if (parts.length === 2) return parts.pop().split(';').shift();
  }
  </script>

  <?php include 'template/script.php'; ?>
</body>

</html>d>
                <td><span class="badge ${userData.status === 'aktif' ? 'bg-success' : 'bg-danger'}">${userData.status.charAt(0).toUpperCase() + userData.status.slice(1)}</span></td>
                <td>
                    <div class="btn-group" role="group">
                        <a href="?controller=User&action=show&id=${userData.id}" class="btn btn-info btn-sm" title="Detail">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="?controller=User&action=edit&id=${userData.id}" class="btn btn-warning btn-sm" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button class="btn btn-secondary btn-sm" onclick="toggleStatus(${userData.id}, '${userData.status}')" title="Toggle Status">
                            <i class="fas fa-toggle-${userData.status === 'aktif' ? 'on' : 'off'}"></i>
                        </button>
                        <button class="btn btn-primary btn-sm" onclick="resetPassword(${userData.id})" title="Reset Password">
                            <i class="fas fa-key"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="deleteUser(${userData.id})" title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        tbody.insertAdjacentHTML('beforeend', row);
    });
}

function showAlert(type, message) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    const cardBody = document.querySelector('.card-body');
    cardBody.insertAdjacentHTML('afterbegin', alertHtml);
}

function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
}
</script>

<?php echo View::load('admin/layout/footer'); ?>