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
              
              <div class="d-sm-flex align-items-center justify-content-between border-bottom mb-4">
                <h3 class="mb-0">Ekspedisi Management</h3>
                <div>
                  <a href="/web_scm/ekspedisi/import" class="btn btn-success me-2">
                    <i class="mdi mdi-download"></i> Import API
                  </a>
                  <a href="/web_scm/ekspedisi/create" class="btn btn-primary">
                    <i class="mdi mdi-plus"></i> Tambah Kurir
                  </a>
                </div>
              </div>

              <?php if (isset($data['error']) && !empty($data['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                  <?php echo htmlspecialchars($data['error']); ?>
                  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
              <?php endif; ?>

              <?php if (isset($data['success']) && !empty($data['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                  <?php echo htmlspecialchars($data['success']); ?>
                  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
              <?php endif; ?>

              <div class="card">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title">Daftar Kurir Ekspedisi</h4>
                    <div class="btn-group" role="group">
                      <a href="/web_scm/ekspedisi" class="btn btn-outline-primary <?php echo !$data['current_status'] ? 'active' : ''; ?>">Semua</a>
                      <a href="/web_scm/ekspedisi?status=active" class="btn btn-outline-success <?php echo $data['current_status'] === 'active' ? 'active' : ''; ?>">Aktif</a>
                    </div>
                  </div>
                  
                  <div class="table-responsive">
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th>Kode</th>
                          <th>Nama Kurir</th>
                          <th>Status</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if (!empty($data['kurir'])): ?>
                          <?php foreach ($data['kurir'] as $kurir): ?>
                            <tr id="kurir-<?php echo $kurir['id']; ?>">
                              <td><?php echo htmlspecialchars($kurir['id']); ?></td>
                              <td><span class="badge bg-secondary"><?php echo htmlspecialchars($kurir['kode']); ?></span></td>
                              <td><?php echo htmlspecialchars($kurir['nama']); ?></td>
                              <td>
                                <span class="badge <?php echo $kurir['status'] === 'aktif' ? 'bg-success' : 'bg-danger'; ?>" id="status-<?php echo $kurir['id']; ?>">
                                  <?php echo $kurir['status'] === 'aktif' ? 'Aktif' : 'Non-aktif'; ?>
                                </span>
                              </td>
                              <td>
                                <div class="btn-group" role="group">
                                  <a href="/web_scm/ekspedisi/edit/<?php echo $kurir['id']; ?>" class="btn btn-sm btn-warning">
                                    <i class="mdi mdi-pencil"></i>
                                  </a>
                                  <button onclick="toggleStatus(<?php echo $kurir['id']; ?>, '<?php echo $kurir['status']; ?>')" 
                                          class="btn btn-sm <?php echo $kurir['status'] === 'aktif' ? 'btn-secondary' : 'btn-success'; ?>" 
                                          id="toggle-btn-<?php echo $kurir['id']; ?>"
                                          title="<?php echo $kurir['status'] === 'aktif' ? 'Nonaktifkan' : 'Aktifkan'; ?>">
                                    <i class="mdi <?php echo $kurir['status'] === 'aktif' ? 'mdi-pause' : 'mdi-play'; ?>" 
                                       id="toggle-icon-<?php echo $kurir['id']; ?>"></i>
                                  </button>
                                  <button onclick="deleteKurir(<?php echo $kurir['id']; ?>)" class="btn btn-sm btn-danger">
                                    <i class="mdi mdi-delete"></i>
                                  </button>
                                </div>
                              </td>
                            </tr>
                          <?php endforeach; ?>
                        <?php else: ?>
                          <tr>
                            <td colspan="5" class="text-center">Tidak ada data kurir</td>
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

  <script>
    function toggleStatus(id, currentStatus) {
      const newStatus = currentStatus === 'aktif' ? 'nonaktif' : 'aktif';
      const confirmMessage = currentStatus === 'aktif' ? 'Nonaktifkan kurir ini?' : 'Aktifkan kurir ini?';
      
      if (confirm(confirmMessage)) {
        fetch('/api/kurir', {
          method: 'PATCH',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify({
            id: id,
            status: newStatus
          })
        })
        .then(response => {
          if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
          }
          return response.json();
        })
        .then(data => {
          if (data.success || data.message) {
            updateStatusDisplay(id, newStatus);
            showAlert('Status berhasil diubah!', 'success');
          } else {
            showAlert('Gagal mengubah status: ' + (data.error || 'Unknown error'), 'error');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          showAlert('Terjadi kesalahan saat mengubah status', 'error');
        });
      }
    }

    function deleteKurir(id) {
      if (confirm('Apakah Anda yakin ingin menghapus kurir ini?')) {
        fetch('/api/kurir', {
          method: 'DELETE',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify({
            id: id
          })
        })
        .then(response => {
          if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
          }
          return response.json();
        })
        .then(data => {
          if (data.success || data.message) {
            document.getElementById(`kurir-${id}`).remove();
            showAlert('Kurir berhasil dihapus!', 'success');
          } else {
            showAlert('Gagal menghapus kurir: ' + (data.error || 'Unknown error'), 'error');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          showAlert('Terjadi kesalahan saat menghapus kurir', 'error');
        });
      }
    }

    function updateStatusDisplay(id, newStatus) {
      const statusBadge = document.getElementById(`status-${id}`);
      const toggleBtn = document.getElementById(`toggle-btn-${id}`);
      const toggleIcon = document.getElementById(`toggle-icon-${id}`);
      
      if (newStatus === 'aktif') {
        statusBadge.className = 'badge bg-success';
        statusBadge.textContent = 'Aktif';
        toggleBtn.className = 'btn btn-sm btn-secondary';
        toggleBtn.title = 'Nonaktifkan';
        toggleIcon.className = 'mdi mdi-pause';
      } else {
        statusBadge.className = 'badge bg-danger';
        statusBadge.textContent = 'Non-aktif';
        toggleBtn.className = 'btn btn-sm btn-success';
        toggleBtn.title = 'Aktifkan';
        toggleIcon.className = 'mdi mdi-play';
      }
      
      toggleBtn.setAttribute('onclick', `toggleStatus(${id}, '${newStatus}')`);
    }

    function showAlert(message, type) {
      const alertContainer = document.querySelector('.content-wrapper .row .col-sm-12');
      const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
      
      const alertHTML = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
          ${message}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      `;
      
      const tempDiv = document.createElement('div');
      tempDiv.innerHTML = alertHTML;
      const alertElement = tempDiv.firstElementChild;
      
      const existingAlerts = alertContainer.querySelectorAll('.alert');
      existingAlerts.forEach(alert => alert.remove());
      
      alertContainer.insertBefore(alertElement, alertContainer.children[1]);
      
      setTimeout(() => {
        if (alertElement.parentNode) {
          alertElement.remove();
        }
      }, 5000);
    }
  </script>

  <?php include 'template/script.php'; ?>
</body>

</html>