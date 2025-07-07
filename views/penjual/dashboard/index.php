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
                      <a class="nav-link active ps-0" id="home-tab" data-bs-toggle="tab" href="#overview" role="tab" aria-controls="overview" aria-selected="true">Dashboard Penjual</a>
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
                            <p class="statistics-title">Total Penjualan</p>
                            <h3 class="rate-percentage" id="totalSales">Rp 0</h3>
                          </div>
                          <div>
                            <p class="statistics-title">Pesanan Hari Ini</p>
                            <h3 class="rate-percentage" id="todayOrders">0</h3>
                          </div>
                          <div>
                            <p class="statistics-title">Produk Terjual</p>
                            <h3 class="rate-percentage" id="productsSold">0</h3>
                          </div>
                          <div class="d-none d-md-block">
                            <p class="statistics-title">Pelanggan</p>
                            <h3 class="rate-percentage" id="totalCustomers">0</h3>
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
                                    <h4 class="card-title card-title-dash">Grafik Penjualan</h4>
                                    <p class="card-subtitle card-subtitle-dash">Penjualan per bulan dalam 6 bulan terakhir</p>
                                  </div>
                                </div>
                                <div class="chartjs-bar-wrapper">
                                  <canvas id="salesChart"></canvas>
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
                                      <h4 class="card-title card-title-dash">Produk Terlaris</h4>
                                    </div>
                                    <canvas class="my-auto" id="topProductsChart"></canvas>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Recent Orders -->
                    <div class="row">
                      <div class="col-lg-12 d-flex flex-column">
                        <div class="row flex-grow">
                          <div class="col-12 grid-margin stretch-card">
                            <div class="card card-rounded">
                              <div class="card-body">
                                <div class="d-sm-flex justify-content-between align-items-start">
                                  <div>
                                    <h4 class="card-title card-title-dash">Pesanan Terbaru</h4>
                                    <p class="card-subtitle card-subtitle-dash">Pesanan yang perlu diproses</p>
                                  </div>
                                  <div>
                                    <button class="btn btn-primary btn-lg text-white mb-0 me-0" type="button">
                                      <i class="mdi mdi-plus"></i>Tambah Produk
                                    </button>
                                  </div>
                                </div>
                                <div class="table-responsive mt-1">
                                  <table class="table select-table">
                                    <thead>
                                      <tr>
                                        <th>Order ID</th>
                                        <th>Pelanggan</th>
                                        <th>Produk</th>
                                        <th>Jumlah</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                        <th>Aksi</th>
                                      </tr>
                                    </thead>
                                    <tbody id="recentOrdersTable">
                                      <tr>
                                        <td colspan="8" class="text-center">Loading...</td>
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
      loadPenjualDashboard();
    });

    async function loadPenjualDashboard() {
      try {
        const response = await fetch('?api=dashboard&path=penjual');
        const data = await response.json();
        
        console.log('Penjual Dashboard Data:', data);
        
        if (data.stats) {
          updatePenjualStatistics(data.stats);
          createPenjualCharts(data.stats);
        }
        
        if (data.recent_orders) {
          updateRecentOrders(data.recent_orders);
        }
        
      } catch (error) {
        console.error('Error loading penjual dashboard:', error);
      }
    }

    function updatePenjualStatistics(stats) {
      document.getElementById('totalSales').textContent = 'Rp ' + (stats.total_sales || 0).toLocaleString();
      document.getElementById('todayOrders').textContent = stats.today_orders || 0;
      document.getElementById('productsSold').textContent = stats.products_sold || 0;
      document.getElementById('totalCustomers').textContent = stats.total_customers || 0;
    }

    function createPenjualCharts(stats) {
      // Sales Chart
      const salesCtx = document.getElementById('salesChart').getContext('2d');
      new Chart(salesCtx, {
        type: 'line',
        data: {
          labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
          datasets: [{
            label: 'Penjualan (Rp)',
            data: stats.monthly_sales || [0, 0, 0, 0, 0, 0],
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4
          }]
        },
        options: {
          responsive: true,
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                callback: function(value) {
                  return 'Rp ' + value.toLocaleString();
                }
              }
            }
          }
        }
      });

      // Top Products Chart
      const topProductsCtx = document.getElementById('topProductsChart').getContext('2d');
      new Chart(topProductsCtx, {
        type: 'doughnut',
        data: {
          labels: stats.top_products_labels || ['Arabica Premium', 'Robusta Special', 'Blend House'],
          datasets: [{
            data: stats.top_products_data || [40, 35, 25],
            backgroundColor: ['#4CAF50', '#FF9800', '#2196F3']
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
    }

    function updateRecentOrders(orders) {
      const tbody = document.getElementById('recentOrdersTable');
      tbody.innerHTML = '';
      
      orders.forEach(order => {
        const row = tbody.insertRow();
        row.innerHTML = `
          <td>#${order.id}</td>
          <td>${order.customer_name}</td>
          <td>${order.product_name}</td>
          <td>${order.quantity}</td>
          <td>Rp ${order.total.toLocaleString()}</td>
          <td><span class="badge badge-${getStatusClass(order.status)}">${order.status}</span></td>
          <td>${new Date(order.created_at).toLocaleDateString()}</td>
          <td>
            <button class="btn btn-sm btn-info me-1" onclick="viewOrder(${order.id})">Lihat</button>
            <button class="btn btn-sm btn-success" onclick="processOrder(${order.id})">Proses</button>
          </td>
        `;
      });
    }

    function getStatusClass(status) {
      switch(status) {
        case 'completed': return 'success';
        case 'pending': return 'warning';
        case 'processing': return 'info';
        case 'cancelled': return 'danger';
        default: return 'primary';
      }
    }

    function viewOrder(orderId) {
      window.location.href = `?controller=Pesanan&action=detail&id=${orderId}`;
    }

    function processOrder(orderId) {
      if (confirm('Apakah Anda yakin ingin memproses pesanan ini?')) {
        // Process order logic
        fetch('?api=orders&path=process', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            order_id: orderId,
            status: 'processing'
          })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert('Pesanan berhasil diproses!');
            loadPenjualDashboard();
          } else {
            alert('Gagal memproses pesanan: ' + (data.error || 'Unknown error'));
          }
        })
        .catch(error => {
          console.error('Error processing order:', error);
          alert('Terjadi kesalahan saat memproses pesanan');
        });
      }
    }
  </script>

  <?php include 'template/script.php'; ?>
</body>

</html>