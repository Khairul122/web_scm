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
                      <a class="nav-link active ps-0" id="home-tab" data-bs-toggle="tab" href="#overview" role="tab" aria-controls="overview" aria-selected="true">Overview</a>
                    </li>
                  </ul>
                  <div>
                    <div class="btn-wrapper">
                      <a href="#" class="btn btn-otline-dark align-items-center"><i class="icon-share"></i> Share</a>
                      <a href="#" class="btn btn-otline-dark"><i class="icon-printer"></i> Print</a>
                      <a href="#" class="btn btn-primary text-white me-0"><i class="icon-download"></i> Export</a>
                    </div>
                  </div>
                </div>
                <div class="tab-content tab-content-basic">
                  <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview">
                    
                    <!-- Statistics Cards -->
                    <div class="row">
                      <div class="col-sm-12">
                        <div class="statistics-details d-flex align-items-center justify-content-between">
                          <div>
                            <p class="statistics-title">Total Users</p>
                            <h3 class="rate-percentage" id="totalUsers">0</h3>
                          </div>
                          <div>
                            <p class="statistics-title">Total Orders</p>
                            <h3 class="rate-percentage" id="totalOrders">0</h3>
                          </div>
                          <div>
                            <p class="statistics-title">Total Revenue</p>
                            <h3 class="rate-percentage" id="totalRevenue">Rp 0</h3>
                          </div>
                          <div class="d-none d-md-block">
                            <p class="statistics-title">Active Suppliers</p>
                            <h3 class="rate-percentage" id="activeSuppliers">0</h3>
                          </div>
                          <div class="d-none d-md-block">
                            <p class="statistics-title">Products</p>
                            <h3 class="rate-percentage" id="totalProducts">0</h3>
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
                                    <h4 class="card-title card-title-dash">Revenue Overview</h4>
                                    <p class="card-subtitle card-subtitle-dash">Monthly revenue comparison</p>
                                  </div>
                                </div>
                                <div class="chartjs-bar-wrapper">
                                  <canvas id="revenueChart"></canvas>
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
                                      <h4 class="card-title card-title-dash">User Roles Distribution</h4>
                                    </div>
                                    <canvas class="my-auto" id="rolesChart"></canvas>
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
                                    <h4 class="card-title card-title-dash">Recent Orders</h4>
                                    <p class="card-subtitle card-subtitle-dash">Latest transactions in the system</p>
                                  </div>
                                  <div>
                                    <button class="btn btn-primary btn-lg text-white mb-0 me-0" type="button">
                                      <i class="mdi mdi-account-plus"></i>View All
                                    </button>
                                  </div>
                                </div>
                                <div class="table-responsive mt-1">
                                  <table class="table select-table">
                                    <thead>
                                      <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Product</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                      </tr>
                                    </thead>
                                    <tbody id="recentOrdersTable">
                                      <tr>
                                        <td colspan="6" class="text-center">Loading...</td>
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
      loadAdminDashboard();
    });

    async function loadAdminDashboard() {
      try {
        const response = await fetch('?api=dashboard&path=admin');
        const data = await response.json();
        
        console.log('Admin Dashboard Data:', data);
        
        if (data.stats) {
          updateStatistics(data.stats);
          createCharts(data.stats);
        }
        
        if (data.recent_orders) {
          updateRecentOrders(data.recent_orders);
        }
        
      } catch (error) {
        console.error('Error loading admin dashboard:', error);
      }
    }

    function updateStatistics(stats) {
      document.getElementById('totalUsers').textContent = stats.total_users || 0;
      document.getElementById('totalOrders').textContent = stats.total_orders || 0;
      document.getElementById('totalRevenue').textContent = 'Rp ' + (stats.total_revenue || 0).toLocaleString();
      document.getElementById('activeSuppliers').textContent = stats.active_suppliers || 0;
      document.getElementById('totalProducts').textContent = stats.total_products || 0;
    }

    function createCharts(stats) {
      // Revenue Chart
      const revenueCtx = document.getElementById('revenueChart').getContext('2d');
      new Chart(revenueCtx, {
        type: 'bar',
        data: {
          labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
          datasets: [{
            label: 'Revenue',
            data: stats.monthly_revenue || [0, 0, 0, 0, 0, 0],
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
          }]
        }
      });

      // Roles Chart
      const rolesCtx = document.getElementById('rolesChart').getContext('2d');
      new Chart(rolesCtx, {
        type: 'doughnut',
        data: {
          labels: ['Pengepul', 'Roasting', 'Penjual', 'Pembeli'],
          datasets: [{
            data: stats.role_distribution || [0, 0, 0, 0],
            backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0']
          }]
        }
      });
    }

    function updateRecentOrders(orders) {
      const tbody = document.getElementById('recentOrdersTable');
      tbody.innerHTML = '';
      
      orders.forEach(order => {
        const row = tbody.insertRow();
        row.innerHTML = `
          <td>${order.id}</td>
          <td>${order.customer_name}</td>
          <td>${order.product_name}</td>
          <td>Rp ${order.amount.toLocaleString()}</td>
          <td><span class="badge badge-${getStatusClass(order.status)}">${order.status}</span></td>
          <td>${new Date(order.created_at).toLocaleDateString()}</td>
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