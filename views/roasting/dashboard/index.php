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
              <div class="home-tab">
                <div class="d-sm-flex align-items-center justify-content-between border-bottom">
                  <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                      <a class="nav-link active ps-0" id="home-tab" data-bs-toggle="tab" href="#overview" role="tab" aria-controls="overview" aria-selected="true">Dashboard Roasting</a>
                    </li>
                  </ul>
                </div>
                <div class="tab-content tab-content-basic">
                  <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview">
                    
                    <!-- Statistics Cards -->
                    <div class="row">
                      <div class="col-sm-12">
                        <div class="statistics-details d-flex align-items-center justify-content-between">
                          <div>
                            <p class="statistics-title">Kopi Diproses</p>
                            <h3 class="rate-percentage" id="processedCoffee">0 kg</h3>
                          </div>
                          <div>
                            <p class="statistics-title">Batch Selesai</p>
                            <h3 class="rate-percentage" id="completedBatches">0</h3>
                          </div>
                          <div>
                            <p class="statistics-title">Pendapatan</p>
                            <h3 class="rate-percentage" id="roastingRevenue">Rp 0</h3>
                          </div>
                          <div class="d-none d-md-block">
                            <p class="statistics-title">Batch Aktif</p>
                            <h3 class="rate-percentage" id="activeBatches">0</h3>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Charts Row -->
                    <div class="row">
                      <div class="col-lg-8 d-flex flex-column">
                        <div class="row flex-grow">
                          <div class="col-12 grid-margin stretch-card">
                            <div class="card card-rounded">
                              <div class="card-body">
                                <div class="d-sm-flex justify-content-between align-items-start">
                                  <div>
                                    <h4 class="card-title card-title-dash">Produksi Bulanan</h4>
                                    <p class="card-subtitle card-subtitle-dash">Volume kopi yang diproses per bulan</p>
                                  </div>
                                </div>
                                <div class="chartjs-bar-wrapper">
                                  <canvas id="productionChart"></canvas>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-lg-4 d-flex flex-column">
                        <div class="row flex-grow">
                          <div class="col-12 grid-margin stretch-card">
                            <div class="card card-rounded">
                              <div class="card-body">
                                <div class="row">
                                  <div class="col-lg-12">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                      <h4 class="card-title card-title-dash">Jenis Roasting</h4>
                                    </div>
                                    <canvas class="my-auto" id="roastTypeChart"></canvas>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Recent Activities -->
                    <div class="row">
                      <div class="col-lg-12 d-flex flex-column">
                        <div class="row flex-grow">
                          <div class="col-12 grid-margin stretch-card">
                            <div class="card card-rounded">
                              <div class="card-body">
                                <div class="d-sm-flex justify-content-between align-items-start">
                                  <div>
                                    <h4 class="card-title card-title-dash">Aktivitas Roasting Terbaru</h4>
                                    <p class="card-subtitle card-subtitle-dash">Proses roasting yang sedang atau baru selesai</p>
                                  </div>
                                  <div>
                                    <button class="btn btn-primary btn-lg text-white mb-0 me-0" type="button">
                                      <i class="mdi mdi-plus"></i>Mulai Roasting
                                    </button>
                                  </div>
                                </div>
                                <div class="table-responsive mt-1">
                                  <table class="table select-table">
                                    <thead>
                                      <tr>
                                        <th>Batch ID</th>
                                        <th>Jenis Kopi</th>
                                        <th>Berat (kg)</th>
                                        <th>Level Roast</th>
                                        <th>Status</th>
                                        <th>Waktu Mulai</th>
                                        <th>Aksi</th>
                                      </tr>
                                    </thead>
                                    <tbody id="roastingActivitiesTable">
                                      <tr>
                                        <td colspan="7" class="text-center">Loading...</td>
                                      </tr>
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
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      loadRoastingDashboard();
    });

    async function loadRoastingDashboard() {
      try {
        const response = await fetch('?api=dashboard&path=roasting');
        const data = await response.json();
        
        console.log('Roasting Dashboard Data:', data);
        
        if (data.stats) {
          updateRoastingStatistics(data.stats);
          createRoastingCharts(data.stats);
        }
        
        if (data.recent_orders) {
          updateRoastingActivities(data.recent_orders);
        }
        
      } catch (error) {
        console.error('Error loading roasting dashboard:', error);
      }
    }

    function updateRoastingStatistics(stats) {
      document.getElementById('processedCoffee').textContent = (stats.processed_coffee || 0) + ' kg';
      document.getElementById('completedBatches').textContent = stats.completed_batches || 0;
      document.getElementById('roastingRevenue').textContent = 'Rp ' + (stats.revenue || 0).toLocaleString();
      document.getElementById('activeBatches').textContent = stats.active_batches || 0;
    }

    function createRoastingCharts(stats) {
      // Production Chart
      const productionCtx = document.getElementById('productionChart').getContext('2d');
      new Chart(productionCtx, {
        type: 'bar',
        data: {
          labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
          datasets: [{
            label: 'Produksi (kg)',
            data: stats.monthly_production || [0, 0, 0, 0, 0, 0],
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            borderColor: 'rgba(255, 99, 132, 1)',
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          scales: {
            y: {
              beginAtZero: true
            }
          }
        }
      });

      // Roast Type Chart
      const roastTypeCtx = document.getElementById('roastTypeChart').getContext('2d');
      new Chart(roastTypeCtx, {
        type: 'doughnut',
        data: {
          labels: ['Light Roast', 'Medium Roast', 'Dark Roast'],
          datasets: [{
            data: stats.roast_type_distribution || [0, 0, 0],
            backgroundColor: ['#FFA726', '#FF7043', '#5D4037']
          }]
        },
        options: {
          responsive: true
        }
      });
    }

    function updateRoastingActivities(activities) {
      const tbody = document.getElementById('roastingActivitiesTable');
      tbody.innerHTML = '';
      
      activities.forEach(activity => {
        const row = tbody.insertRow();
        row.innerHTML = `
          <td>${activity.batch_id}</td>
          <td>${activity.coffee_type}</td>
          <td>${activity.weight} kg</td>
          <td>${activity.roast_level}</td>
          <td><span class="badge badge-${getStatusClass(activity.status)}">${activity.status}</span></td>
          <td>${new Date(activity.start_time).toLocaleString()}</td>
          <td>
            <button class="btn btn-sm btn-primary">Detail</button>
          </td>
        `;
      });
    }

    function getStatusClass(status) {
      switch(status) {
        case 'completed': return 'success';
        case 'processing': return 'warning';
        case 'failed': return 'danger';
        default: return 'primary';
      }
    }
  </script>

  <?php include 'template/script.php'; ?>
</body>

</html>