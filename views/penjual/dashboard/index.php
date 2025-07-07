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
                    
                    <?php if (isset($data['error'])): ?>
                      <div class="alert alert-danger">
                        <?php echo htmlspecialchars($data['error']); ?>
                      </div>
                    <?php endif; ?>

                    <div class="row">
                      <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="card">
                          <div class="card-body">
                            <div class="d-flex justify-content-between">
                              <div>
                                <h4 class="card-title">Total Products</h4>
                                <h2 class="text-primary"><?php echo $data['stats']['total_products'] ?? 0; ?></h2>
                              </div>
                              <div class="text-primary">
                                <i class="mdi mdi-package-variant-closed mdi-36px"></i>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="card">
                          <div class="card-body">
                            <div class="d-flex justify-content-between">
                              <div>
                                <h4 class="card-title">Today's Sales</h4>
                                <h2 class="text-success">Rp <?php echo number_format($data['stats']['todays_sales'] ?? 0, 0, ',', '.'); ?></h2>
                              </div>
                              <div class="text-success">
                                <i class="mdi mdi-chart-line mdi-36px"></i>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="card">
                          <div class="card-body">
                            <div class="d-flex justify-content-between">
                              <div>
                                <h4 class="card-title">Orders Today</h4>
                                <h2 class="text-info"><?php echo $data['stats']['orders_today'] ?? 0; ?></h2>
                              </div>
                              <div class="text-info">
                                <i class="mdi mdi-cart-plus mdi-36px"></i>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="card">
                          <div class="card-body">
                            <div class="d-flex justify-content-between">
                              <div>
                                <h4 class="card-title">Monthly Revenue</h4>
                                <h2 class="text-warning">Rp <?php echo number_format($data['stats']['monthly_revenue'] ?? 0, 0, ',', '.'); ?></h2>
                              </div>
                              <div class="text-warning">
                                <i class="mdi mdi-currency-usd mdi-36px"></i>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="row mt-4">
                      <div class="col-12">
                        <div class="card">
                          <div class="card-body">
                            <h4 class="card-title">Recent Orders</h4>
                            <div class="table-responsive">
                              <table class="table table-striped">
                                <thead>
                                  <tr>
                                    <th>ID</th>
                                    <th>Customer</th>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <?php if (!empty($data['recent_orders'])): ?>
                                    <?php foreach ($data['recent_orders'] as $order): ?>
                                      <tr>
                                        <td><?php echo htmlspecialchars($order['id']); ?></td>
                                        <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                        <td><?php echo htmlspecialchars($order['product_name']); ?></td>
                                        <td><?php echo htmlspecialchars($order['quantity']); ?></td>
                                        <td>Rp <?php echo number_format($order['total'], 0, ',', '.'); ?></td>
                                        <td>
                                          <span class="badge bg-<?php echo $order['status'] == 'completed' ? 'success' : ($order['status'] == 'pending' ? 'warning' : 'danger'); ?>">
                                            <?php echo htmlspecialchars($order['status']); ?>
                                          </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($order['created_at']); ?></td>
                                      </tr>
                                    <?php endforeach; ?>
                                  <?php else: ?>
                                    <tr>
                                      <td colspan="7" class="text-center">No recent orders</td>
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
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php include 'template/script.php'; ?>
</body>

</html>