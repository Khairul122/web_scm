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
                      <a class="nav-link active ps-0" id="home-tab" data-bs-toggle="tab" href="#overview" role="tab" aria-controls="overview" aria-selected="true">Dashboard Pengepul</a>
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
                            <p class="statistics-title">Total Pembelian</p>
                            <h3 class="rate-percentage" id="totalPurchases">0</h3>
                          </div>
                          <div>
                            <p class="statistics-title">Stok Kopi</p>
                            <h3 class="rate-percentage" id="coffeeStock">0 kg</h3>
                          </div>
                          <div>
                            <p class="statistics-title">Pendapatan</p>
                            <h3 class="rate-percentage" id="totalEarnings">Rp 0</h3>
                          </div>
                          <div class="d-none d-md-block">
                            <p class="statistics-title">Pesanan Pending</p>
                            <h3 class="rate-percentage" id="pendingOrders">0</h3>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Cards Row -->
                    <div class="row">
                      <div class="col-lg-8 d-flex flex-column">
                        <div class="row flex-grow">
                          <div class="col-12 grid-margin stretch-card">
                            <div class="card card-rounded">
                              <div class="card-body">
                                <div class="d-sm-flex justify-content-between align-items-start">
                                  <div>
                                    <h4 class="card-title card-title-dash">Grafik Pembelian Bulanan</h4>
                                    <p class="card-subtitle card-subtitle-dash">Volume pembelian kopi per bulan</p>
                                  </div>
                                </div>
                                <div class="chartjs-bar-wrapper">
                                  <canvas id="purchaseChart"></canvas>
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
                                      <h4 class="card-title card-title-dash">Status Stok</h4>
                                    </div>
                                    <canvas class="my-auto" id="stockChart"></canvas>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Recent Transactions -->
                    <div class="row">
                      <div class="col-lg-12 d-flex flex-column">
                        <div class="row flex-grow">
                          <div class="col-12 grid-margin stretch-card">
                            <div class="card card-rounded">
                              <div class="card-body">
                                <div class="d-sm-flex justify-content-between align-items-start">
                                  <div>
                                    <h4 class="card-title card-title-dash">Transaksi Terbaru</h4>
                                    <p class="card-subtitle card-subtitle-dash">Pembelian dan penjualan terbaru</p>
                                  </div>
                                  <div>
                                    <button class="btn btn-primary btn-lg text-white mb-0 me-0" type="button">
                                      <i class="mdi mdi-plus"></i>Tambah Transaksi
                                    </button>
                                  </div>
                                </div>
                                <div class="table-responsive mt-1">
                                  <table class="table select-table">
                                    <thead>
                                      <tr>
                                        <th>ID Transaksi</th>
                                        <th>Jenis</th>
                                        <th>Produk</th>
                                        <th>Kuantitas</th>
                                        <th>Harga</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                      </tr>
                                    </thead>
                                    <tbody id="recentTransactionsTable">
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
      loadPengepulDashboard();
    });

    async function loadPengepulDashboard() {
      try {
        const response = await fetch('?api=dashboard&path=pengepul');
        const data = await response.json();
        
        console.log('Pengepul Dashboard Data:', data);
        
        if (data.stats) {
          updatePengepulStatistics(data.stats);
          createPengepulCharts(data.stats);
        }
        
        if (data.recent_orders) {
          updateRecentTransactions(data.recent_orders);
        }
        
      } catch (error) {
        console.error('Error loading pengepul dashboard:', error);
      }
    }

    function updatePengepulStatistics(stats) {
      document.getElementById('totalPurchases').textContent = stats.total_purchases || 0;
      document.getElementById('coffeeStock').textContent = (stats.coffee_stock || 0) + ' kg';
      document.getElementById('totalEarnings').textContent = 'Rp ' + (stats.total_earnings || 0).toLocaleString();
      document.getElementById('pendingOrders').textContent = stats.pending_orders || 0;
    }

    function createPengepulCharts(stats) {
      // Purchase Chart
      const purchaseCtx = document.getElementById('purchaseChart').getContext('2d');
      new Chart(purchaseCtx, {
        type: 'line',
        data: {
          labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
          datasets: [{
            label: 'Pembelian (kg)',
            data: stats.monthly_purchases || [0, 0, 0, 0, 0, 0],
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 2,
            fill: true
          }]
        }
      });

      // Stock Chart
      const stockCtx = document.getElementById('stockChart').getContext('2d');
      new Chart(stockCtx, {
        type: 'doughnut',
        data: {
          labels: ['Arabica', 'Robusta', 'Liberica'],
          datasets: [{
            data: stats.stock_by_type || [0, 0, 0],
            backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56']
          }]
        }
      });
    }

    function updateRecentTransactions(transactions) {
      const tbody = document.getElementById('recentTransactionsTable');
      tbody.innerHTML = '';
      
      transactions.forEach(transaction => {
        const row = tbody.insertRow();
        row.innerHTML = `
          <td>${transaction.id}</td>
          <td><span class="badge badge-${transaction.type === 'purchase' ? 'info' : 'success'}">${transaction.type === 'purchase' ? 'Beli' : 'Jual'}</span></td>
          <td>${transaction.product_name}</td>
          <td>${transaction.quantity} kg</td>
          <td>Rp ${transaction.price.toLocaleString()}</td>
          <td><span class="badge badge-${getStatusClass(transaction.status)}">${transaction.status}</span></td>
          <td>${new Date(transaction.created_at).toLocaleDateString()}</td>
        `;
      });
    }

    function getStatusClass(status) {
      switch(status) {
        case 'completed': return 'success';
        case 'pending': return 'warning';
        case 'cancelled': return 'danger';
        default: return 'primary';
      }
    }
  </script>

  <?php include 'template/script.php'; ?>
</body>

</html>