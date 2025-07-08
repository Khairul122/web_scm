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
                    <h4 class="card-title mb-0">Statistik Pengguna</h4>
                    <a href="?controller=User&action=index" class="btn btn-secondary btn-sm">
                        <i class="mdi mdi-arrow-left"></i> Kembali
                    </a>
                  </div>

                  <div class="row">
                      <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
                          <div class="card">
                              <div class="card-body">
                                  <div class="row">
                                      <div class="col-9">
                                          <div class="d-flex align-items-center align-self-start">
                                              <h3 class="mb-0"><?= $stats['total_users'] ?? 0 ?></h3>
                                          </div>
                                      </div>
                                      <div class="col-3">
                                          <div class="icon icon-box-primary">
                                              <span class="mdi mdi-account-group icon-item"></span>
                                          </div>
                                      </div>
                                  </div>
                                  <h6 class="text-muted font-weight-normal">Total Pengguna</h6>
                              </div>
                          </div>
                      </div>
                      <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
                          <div class="card">
                              <div class="card-body">
                                  <div class="row">
                                      <div class="col-9">
                                          <div class="d-flex align-items-center align-self-start">
                                              <h3 class="mb-0"><?= $stats['active_users'] ?? 0 ?></h3>
                                          </div>
                                      </div>
                                      <div class="col-3">
                                          <div class="icon icon-box-success">
                                              <span class="mdi mdi-account-check icon-item"></span>
                                          </div>
                                      </div>
                                  </div>
                                  <h6 class="text-muted font-weight-normal">Pengguna Aktif</h6>
                              </div>
                          </div>
                      </div>
                      <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
                          <div class="card">
                              <div class="card-body">
                                  <div class="row">
                                      <div class="col-9">
                                          <div class="d-flex align-items-center align-self-start">
                                              <h3 class="mb-0"><?= $stats['inactive_users'] ?? 0 ?></h3>
                                          </div>
                                      </div>
                                      <div class="col-3">
                                          <div class="icon icon-box-danger">
                                              <span class="mdi mdi-account-remove icon-item"></span>
                                          </div>
                                      </div>
                                  </div>
                                  <h6 class="text-muted font-weight-normal">Pengguna Non-aktif</h6>
                              </div>
                          </div>
                      </div>
                      <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
                          <div class="card">
                              <div class="card-body">
                                  <div class="row">
                                      <div class="col-9">
                                          <div class="d-flex align-items-center align-self-start">
                                              <h3 class="mb-0"><?= $stats['new_users_this_month'] ?? 0 ?></h3>
                                          </div>
                                      </div>
                                      <div class="col-3">
                                          <div class="icon icon-box-info">
                                              <span class="mdi mdi-account-plus icon-item"></span>
                                          </div>
                                      </div>
                                  </div>
                                  <h6 class="text-muted font-weight-normal">Baru Bulan Ini</h6>
                              </div>
                          </div>
                      </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Distribusi Berdasarkan Role</h4>
                  <canvas id="roleChart"></canvas>
                </div>
              </div>
            </div>
            <div class="col-md-6 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Status Pengguna</h4>
                  <canvas id="statusChart"></canvas>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Detail Statistik per Role</h4>
                  <div class="table-responsive">
                      <table class="table table-striped">
                          <thead>
                              <tr>
                                  <th>Role</th>
                                  <th>Total</th>
                                  <th>Aktif</th>
                                  <th>Non-aktif</th>
                                  <th>Persentase Aktif</th>
                                  <th>Aksi</th>
                              </tr>
                          </thead>
                          <tbody>
                              <?php if (isset($stats['by_role']) && is_array($stats['by_role'])): ?>
                                  <?php foreach ($stats['by_role'] as $roleData): ?>
                                      <tr>
                                          <td>
                                              <span class="badge badge-info">
                                                  <?= ucfirst($roleData['role']) ?>
                                              </span>
                                          </td>
                                          <td><?= $roleData['total'] ?></td>
                                          <td><span class="badge badge-success"><?= $roleData['active'] ?></span></td>
                                          <td><span class="badge badge-danger"><?= $roleData['inactive'] ?></span></td>
                                          <td>
                                              <?php 
                                              $percentage = $roleData['total'] > 0 ? round(($roleData['active'] / $roleData['total']) * 100, 1) : 0;
                                              ?>
                                              <div class="progress" style="height: 20px;">
                                                  <div class="progress-bar" role="progressbar" 
                                                       style="width: <?= $percentage ?>%" 
                                                       aria-valuenow="<?= $percentage ?>" 
                                                       aria-valuemin="0" aria-valuemax="100">
                                                      <?= $percentage ?>%
                                                  </div>
                                              </div>
                                          </td>
                                          <td>
                                              <a href="?controller=User&action=index&role=<?= $roleData['role'] ?>" 
                                                 class="btn btn-sm btn-outline-primary">
                                                  <i class="mdi mdi-eye"></i> Lihat
                                              </a>
                                          </td>
                                      </tr>
                                  <?php endforeach; ?>
                              <?php else: ?>
                                  <tr>
                                      <td colspan="6" class="text-center">Tidak ada data statistik</td>
                                  </tr>
                              <?php endif; ?>
                          </tbody>
                      </table>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Pengguna Terbaru (10 Terakhir)</h4>
                  <?php if (isset($stats['recent_users']) && is_array($stats['recent_users'])): ?>
                      <?php foreach ($stats['recent_users'] as $recentUser): ?>
                          <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                              <div>
                                  <h6 class="mb-1"><?= $recentUser['nama_lengkap'] ?></h6>
                                  <small class="text-muted"><?= $recentUser['email'] ?></small><br>
                                  <span class="badge badge-info"><?= ucfirst($recentUser['role']) ?></span>
                              </div>
                              <div class="text-end">
                                  <small class="text-muted">
                                      <?= date('d/m/Y', strtotime($recentUser['created_at'])) ?>
                                  </small><br>
                                  <span class="badge <?= $recentUser['status'] == 'aktif' ? 'badge-success' : 'badge-danger' ?>">
                                      <?= ucfirst($recentUser['status']) ?>
                                  </span>
                              </div>
                          </div>
                      <?php endforeach; ?>
                  <?php else: ?>
                      <p class="text-muted">Tidak ada data pengguna terbaru</p>
                  <?php endif; ?>
                </div>
              </div>
            </div>
            <div class="col-md-6 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Ringkasan Aktivitas</h4>
                  <div class="row text-center">
                      <div class="col-4">
                          <h4 class="text-primary"><?= $stats['total_orders'] ?? 0 ?></h4>
                          <p class="mb-0">Total Pesanan</p>
                      </div>
                      <div class="col-4">
                          <h4 class="text-success"><?= $stats['total_products'] ?? 0 ?></h4>
                          <p class="mb-0">Total Produk</p>
                      </div>
                      <div class="col-4">
                          <h4 class="text-info"><?= $stats['active_sellers'] ?? 0 ?></h4>
                          <p class="mb-0">Penjual Aktif</p>
                      </div>
                  </div>
                  <hr>
                  <div class="row text-center">
                      <div class="col-6">
                          <h5 class="text-warning">Rp <?= number_format($stats['total_revenue'] ?? 0) ?></h5>
                          <p class="mb-0">Total Revenue</p>
                      </div>
                      <div class="col-6">
                          <h5 class="text-danger"><?= $stats['avg_order_value'] ?? 0 ?>%</h5>
                          <p class="mb-0">Growth Rate</p>
                      </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
  // Role Distribution Chart
  const roleCtx = document.getElementById('roleChart').getContext('2d');
  const roleData = <?= json_encode($stats['by_role'] ?? []) ?>;
  const roleLabels = roleData.map(item => item.role.charAt(0).toUpperCase() + item.role.slice(1));
  const roleCounts = roleData.map(item => item.total);

  new Chart(roleCtx, {
      type: 'doughnut',
      data: {
          labels: roleLabels,
          datasets: [{
              data: roleCounts,
              backgroundColor: [
                  '#FF6384',
                  '#36A2EB', 
                  '#FFCE56',
                  '#4BC0C0',
                  '#9966FF'
              ],
              borderWidth: 2
          }]
      },
      options: {
          responsive: true,
          plugins: {
              legend: {
                  position: 'bottom'
              }
          }
      }
  });

  // Status Chart
  const statusCtx = document.getElementById('statusChart').getContext('2d');
  new Chart(statusCtx, {
      type: 'bar',
      data: {
          labels: ['Aktif', 'Non-aktif'],
          datasets: [{
              label: 'Jumlah Pengguna',
              data: [<?= $stats['active_users'] ?? 0 ?>, <?= $stats['inactive_users'] ?? 0 ?>],
              backgroundColor: ['#28a745', '#dc3545'],
              borderColor: ['#28a745', '#dc3545'],
              borderWidth: 1
          }]
      },
      options: {
          responsive: true,
          scales: {
              y: {
                  beginAtZero: true,
                  ticks: {
                      stepSize: 1
                  }
              }
          }
      }
  });
  </script>

  <?php include 'template/script.php'; ?>
</body>

</html> class="text-muted"><?= $recentUser['email'] ?></small><br>
                                        <span class="badge bg-info"><?= ucfirst($recentUser['role']) ?></span>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted">
                                            <?= date('d/m/Y', strtotime($recentUser['created_at'])) ?>
                                        </small><br>
                                        <span class="badge <?= $recentUser['status'] == 'aktif' ? 'bg-success' : 'bg-danger' ?>">
                                            <?= ucfirst($recentUser['status']) ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">Tidak ada data pengguna terbaru</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Ringkasan Aktivitas</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <h4 class="text-primary"><?= $stats['total_orders'] ?? 0 ?></h4>
                            <p class="mb-0">Total Pesanan</p>
                        </div>
                        <div class="col-4">
                            <h4 class="text-success"><?= $stats['total_products'] ?? 0 ?></h4>
                            <p class="mb-0">Total Produk</p>
                        </div>
                        <div class="col-4">
                            <h4 class="text-info"><?= $stats['active_sellers'] ?? 0 ?></h4>
                            <p class="mb-0">Penjual Aktif</p>
                        </div>
                    </div>
                    <hr>
                    <div class="row text-center">
                        <div class="col-6">
                            <h5 class="text-warning">Rp <?= number_format($stats['total_revenue'] ?? 0) ?></h5>
                            <p class="mb-0">Total Revenue</p>
                        </div>
                        <div class="col-6">
                            <h5 class="text-danger"><?= $stats['avg_order_value'] ?? 0 ?>%</h5>
                            <p class="mb-0">Growth Rate</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Role Distribution Chart
const roleCtx = document.getElementById('roleChart').getContext('2d');
const roleData = <?= json_encode($stats['by_role'] ?? []) ?>;
const roleLabels = roleData.map(item => item.role.charAt(0).toUpperCase() + item.role.slice(1));
const roleCounts = roleData.map(item => item.total);

new Chart(roleCtx, {
    type: 'doughnut',
    data: {
        labels: roleLabels,
        datasets: [{
            data: roleCounts,
            backgroundColor: [
                '#FF6384',
                '#36A2EB', 
                '#FFCE56',
                '#4BC0C0',
                '#9966FF'
            ],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Status Chart
const statusCtx = document.getElementById('statusChart').getContext('2d');
new Chart(statusCtx, {
    type: 'bar',
    data: {
        labels: ['Aktif', 'Non-aktif'],
        datasets: [{
            label: 'Jumlah Pengguna',
            data: [<?= $stats['active_users'] ?? 0 ?>, <?= $stats['inactive_users'] ?? 0 ?>],
            backgroundColor: ['#28a745', '#dc3545'],
            borderColor: ['#28a745', '#dc3545'],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});
</script>

<?php echo View::load('admin/layout/footer'); ?>