<!-- Dashboard View -->
<?php $page_title = "Dashboard"; ?>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Today's Sales</h5>
                <h2 class="text-success">$2,345.50</h2>
                <p class="text-muted">23 transactions</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Total Revenue</h5>
                <h2 class="text-info">$12,456.89</h2>
                <p class="text-muted">Last 30 days</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Transactions</h5>
                <h2 class="text-primary">156</h2>
                <p class="text-muted">This hour</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Inventory Value</h5>
                <h2 class="text-warning">$125,450</h2>
                <p class="text-muted">Total stock</p>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Sales Trend (Last 7 Days)</h5>
            </div>
            <div class="card-body">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Sales by Category</h5>
            </div>
            <div class="card-body">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Low Stock Items</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-warning mb-3">
                    <i class="fas fa-exclamation-triangle"></i> 
                    12 items need reordering
                </div>
                <table class="table table-sm table-hover">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Stock</th>
                            <th>Reorder Level</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Product A</td>
                            <td><span class="badge badge-danger">5</span></td>
                            <td>10</td>
                        </tr>
                        <tr>
                            <td>Product B</td>
                            <td><span class="badge badge-warning">8</span></td>
                            <td>10</td>
                        </tr>
                        <tr>
                            <td>Product C</td>
                            <td><span class="badge badge-danger">3</span></td>
                            <td>20</td>
                        </tr>
                    </tbody>
                </table>
                <a href="/pos-system/inventory" class="btn btn-sm btn-primary">View All</a>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Recent Transactions</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm table-hover">
                    <thead>
                        <tr>
                            <th>Sale ID</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>#SO-001</td>
                            <td>John Doe</td>
                            <td>$234.50</td>
                            <td><span class="badge badge-success">Completed</span></td>
                        </tr>
                        <tr>
                            <td>#SO-002</td>
                            <td>Jane Smith</td>
                            <td>$456.75</td>
                            <td><span class="badge badge-success">Completed</span></td>
                        </tr>
                        <tr>
                            <td>#SO-003</td>
                            <td>Guest</td>
                            <td>$123.45</td>
                            <td><span class="badge badge-success">Completed</span></td>
                        </tr>
                    </tbody>
                </table>
                <a href="/pos-system/sales" class="btn btn-sm btn-primary">View All</a>
            </div>
        </div>
    </div>
</div>

<script>
    // Sales Trend Chart
    const ctx = document.getElementById('salesChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Daily Sales',
                    data: [2000, 2500, 2200, 3000, 2800, 3500, 2340],
                    borderColor: '#3498db',
                    backgroundColor: 'rgba(52, 152, 219, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 4,
                    pointBackgroundColor: '#3498db'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    // Category Chart
    const catCtx = document.getElementById('categoryChart');
    if (catCtx) {
        new Chart(catCtx, {
            type: 'doughnut',
            data: {
                labels: ['Electronics', 'Groceries', 'Clothing', 'Other'],
                datasets: [{
                    data: [45, 25, 20, 10],
                    backgroundColor: ['#3498db', '#27ae60', '#f39c12', '#e74c3c']
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
</script>
