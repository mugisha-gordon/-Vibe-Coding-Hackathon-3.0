// Dashboard functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize Bootstrap tabs properly
    const tabLinks = document.querySelectorAll('.nav-link[data-bs-toggle="tab"]');
    tabLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            
            // Remove active class from all tabs and links
            document.querySelectorAll('.tab-pane').forEach(tab => {
                tab.classList.remove('show', 'active');
            });
            tabLinks.forEach(l => l.classList.remove('active'));
            
            // Add active class to clicked link and target tab
            this.classList.add('active');
            const targetTab = document.getElementById(targetId);
            if (targetTab) {
                targetTab.classList.add('show', 'active');
            }
        });
    });

    // Ensure overview tab is active by default
    const overviewTab = document.querySelector('.nav-link[href="#overview"]');
    if (overviewTab) {
        overviewTab.click();
    }

    // Initialize charts
    initializeCharts();

    // Handle sidebar toggle
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');
    
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        });
    }

    // Handle date range changes
    const dateRange = document.getElementById('dateRange');
    if (dateRange) {
        dateRange.addEventListener('change', function() {
            updateDashboardStats(this.value);
        });
    }
});

// Initialize charts
function initializeCharts() {
    // Donations Chart
    const donationsCtx = document.getElementById('donationsChart');
    if (donationsCtx) {
        new Chart(donationsCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Donations',
                    data: [12000, 19000, 15000, 25000, 22000, 30000],
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 0 // Disable animations
                },
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            display: true,
                            drawBorder: false
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    // Volunteers Chart
    const volunteersCtx = document.getElementById('volunteersChart');
    if (volunteersCtx) {
        new Chart(volunteersCtx, {
            type: 'doughnut',
            data: {
                labels: ['Active', 'Inactive', 'Pending'],
                datasets: [{
                    data: [65, 20, 15],
                    backgroundColor: [
                        'rgb(75, 192, 192)',
                        'rgb(255, 99, 132)',
                        'rgb(255, 205, 86)'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 0 // Disable animations
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    }
                },
                cutout: '70%'
            }
        });
    }
}

// Show alert messages
function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    const container = document.querySelector('.main-content');
    container.insertBefore(alertDiv, container.firstChild);
    
    // Auto dismiss after 5 seconds
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}

// Update dashboard statistics
function updateDashboardStats(range) {
    fetch(`actions/get_stats.php?range=${range}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update statistics
                Object.keys(data.stats).forEach(key => {
                    const element = document.getElementById(`stat-${key}`);
                    if (element) {
                        element.textContent = data.stats[key];
                    }
                });
                
                // Update charts if they exist
                updateCharts(data.stats);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('danger', 'Failed to update statistics');
        });
}

// Update charts with new data
function updateCharts(stats) {
    // Update donations chart
    const donationsChart = Chart.getChart('donationsChart');
    if (donationsChart && stats.monthly_donations) {
        donationsChart.data.datasets[0].data = stats.monthly_donations;
        donationsChart.update();
    }

    // Update volunteers chart
    const volunteersChart = Chart.getChart('volunteersChart');
    if (volunteersChart && stats.volunteer_stats) {
        volunteersChart.data.datasets[0].data = [
            stats.volunteer_stats.active,
            stats.volunteer_stats.inactive,
            stats.volunteer_stats.pending
        ];
        volunteersChart.update();
    }
}

// Update volunteer status
function updateVolunteerStatus(id, status) {
    if (confirm(`Are you sure you want to ${status} this volunteer request?`)) {
        fetch(`actions/update_volunteer_status.php?id=${id}&status=${status}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', `Volunteer request ${status} successfully`);
                    location.reload();
                } else {
                    showAlert('danger', data.error || 'Failed to update volunteer status');
                }
            });
    }
}

// Function to update dashboard statistics
function updateDashboardStats(range) {
    const loadingOverlay = document.querySelector('.loading-overlay');
    if (loadingOverlay) {
        loadingOverlay.style.display = 'flex';
    }

    fetch('actions/get_stats.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ range: range })
    })
    .then(response => response.json())
    .then(data => {
        // Update statistics
        Object.keys(data).forEach(key => {
            const element = document.getElementById(`stat-${key}`);
            if (element) {
                element.textContent = data[key];
            }
        });

        // Update charts if they exist
        updateCharts(data);
    })
    .catch(error => {
        console.error('Error:', error);
    })
    .finally(() => {
        if (loadingOverlay) {
            loadingOverlay.style.display = 'none';
        }
    });
}

function updateCharts(data) {
    // Update donations chart
    const donationsChart = Chart.getChart('donationsChart');
    if (donationsChart && data.monthly_donations) {
        donationsChart.data.datasets[0].data = data.monthly_donations;
        donationsChart.update();
    }

    // Update volunteers chart
    const volunteersChart = Chart.getChart('volunteersChart');
    if (volunteersChart && data.volunteer_stats) {
        volunteersChart.data.datasets[0].data = [
            data.volunteer_stats.active,
            data.volunteer_stats.pending,
            data.volunteer_stats.inactive
        ];
        volunteersChart.update();
    }
}

// Update donation status with animation
function updateDonationStatus(donationId, status) {
    if (!confirm('Are you sure you want to update this donation status?')) {
        return;
    }

    const statusCell = document.querySelector(`#donation-${donationId} .donation-status`);
    if (statusCell) {
        statusCell.style.opacity = '0';
    }

    fetch('actions/update_donation.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `donation_id=${donationId}&status=${status}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (statusCell) {
                statusCell.textContent = status;
                statusCell.className = `donation-status status-${status}`;
                setTimeout(() => {
                    statusCell.style.opacity = '1';
                }, 50);
            }
            showAlert('success', 'Donation status updated successfully');
            updateDashboardStats(); // Refresh stats
        } else {
            showAlert('danger', data.error || 'Failed to update donation status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'An error occurred while updating the donation status');
    });
}

// Update child status with animation
function updateChildStatus(childId, status) {
    if (!confirm('Are you sure you want to update this child status?')) {
        return;
    }

    const statusCell = document.querySelector(`#child-${childId} .child-status`);
    if (statusCell) {
        statusCell.style.opacity = '0';
    }

    fetch('actions/update_child.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `child_id=${childId}&status=${status}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (statusCell) {
                statusCell.textContent = status;
                statusCell.className = `child-status status-${status}`;
                setTimeout(() => {
                    statusCell.style.opacity = '1';
                }, 50);
            }
            showAlert('success', 'Child status updated successfully');
            updateDashboardStats(); // Refresh stats
        } else {
            showAlert('danger', data.error || 'Failed to update child status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'An error occurred while updating the child status');
    });
}

// Enhanced form submission handler with validation
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function(e) {
        if (!this.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.add('was-validated');
            
            // Add shake animation to invalid fields
            this.querySelectorAll(':invalid').forEach(field => {
                field.classList.add('shake');
                setTimeout(() => field.classList.remove('shake'), 500);
            });
        }
    });
});

// Enhanced alert function with animations
function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.style.opacity = '0';
    alertDiv.style.transform = 'translateY(-20px)';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    const alertContainer = document.querySelector('.alert-container') || document.createElement('div');
    if (!document.querySelector('.alert-container')) {
        alertContainer.className = 'alert-container position-fixed top-0 end-0 p-3';
        document.body.appendChild(alertContainer);
    }
    
    alertContainer.appendChild(alertDiv);
    
    // Animate in
    setTimeout(() => {
        alertDiv.style.opacity = '1';
        alertDiv.style.transform = 'translateY(0)';
    }, 50);
    
    // Auto dismiss with fade out
    setTimeout(() => {
        alertDiv.style.opacity = '0';
        alertDiv.style.transform = 'translateY(-20px)';
        setTimeout(() => alertDiv.remove(), 300);
    }, 5000);
}

// Enhanced date range filter with loading state
const dateRangeSelect = document.getElementById('dateRange');
if (dateRangeSelect) {
    dateRangeSelect.addEventListener('change', function() {
        const loadingOverlay = document.createElement('div');
        loadingOverlay.className = 'loading-overlay';
        loadingOverlay.innerHTML = '<div class="spinner-border text-primary" role="status"></div>';
        document.querySelector('.main-content').appendChild(loadingOverlay);

        fetch(`actions/get_stats.php?range=${this.value}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateDashboardStats(data.stats);
                } else {
                    showAlert('danger', 'Failed to update statistics');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'An error occurred while updating statistics');
            })
            .finally(() => {
                loadingOverlay.remove();
            });
    });
}

// Handle sidebar navigation
const navLinks = document.querySelectorAll('.nav-link');
navLinks.forEach(link => {
    link.addEventListener('click', function(e) {
        // Remove active class from all links
        navLinks.forEach(l => l.classList.remove('active'));
        // Add active class to clicked link
        this.classList.add('active');
    });
});

// Handle mobile sidebar toggle
const mobileToggle = document.createElement('button');
mobileToggle.className = 'mobile-toggle d-md-none';
mobileToggle.innerHTML = '<i class="fas fa-bars"></i>';
document.querySelector('.dashboard-header').prepend(mobileToggle);

mobileToggle.addEventListener('click', function() {
    const sidebar = document.querySelector('.sidebar');
    sidebar.classList.toggle('active');
});

// Close sidebar when clicking outside on mobile
document.addEventListener('click', function(event) {
    if (window.innerWidth <= 768) {
        if (!sidebar.contains(event.target) && !mobileToggle.contains(event.target)) {
            sidebar.classList.remove('active');
        }
    }
});

// Handle tab changes
const tabLinks = document.querySelectorAll('.nav-link[data-bs-toggle="tab"]');
tabLinks.forEach(link => {
    link.addEventListener('shown.bs.tab', function(event) {
        // Update active state
        tabLinks.forEach(l => l.classList.remove('active'));
        this.classList.add('active');
    });
});

// Load tab content
function loadTabContent(tabId) {
    const contentDiv = document.querySelector('.tab-content');
    fetch(`actions/get_tab_content.php?tab=${tabId}`)
        .then(response => response.text())
        .then(html => {
            contentDiv.innerHTML = html;
            if (tabId === 'overview') {
                initializeCharts();
            }
        });
}

// Handle donation status updates
function updateDonationStatus(id, status) {
    fetch('actions/update_donation_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            id: id,
            status: status
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Refresh donations table
            loadTabContent('donations');
        } else {
            alert('Error updating donation status: ' + data.message);
        }
    });
}

// Handle event management
function manageEvent(id, action) {
    fetch('actions/manage_event.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            id: id,
            action: action
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Refresh events table
            loadTabContent('events');
        } else {
            alert('Error managing event: ' + data.message);
        }
    });
}

// Handle staff status updates
function updateStaffStatus(id, status) {
    fetch('actions/update_staff_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            id: id,
            status: status
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Refresh staff table
            loadTabContent('staff');
        } else {
            alert('Error updating staff status: ' + data.message);
        }
    });
}

// Export functionality
function exportData(type) {
    window.location.href = `actions/export_${type}.php`;
}

// Search functionality
function searchTable(type, query) {
    fetch(`actions/search_${type}.php?query=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            const tableBody = document.querySelector(`#${type}Table tbody`);
            tableBody.innerHTML = data.html;
        });
}

// Pagination functionality
function loadPage(type, page) {
    fetch(`actions/get_${type}.php?page=${page}`)
        .then(response => response.json())
        .then(data => {
            const tableBody = document.querySelector(`#${type}Table tbody`);
            tableBody.innerHTML = data.html;
            updatePagination(data.pagination);
        });
}

// Update pagination controls
function updatePagination(pagination) {
    const paginationContainer = document.querySelector('.pagination');
    let html = '';
    
    if (pagination.current_page > 1) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadPage('${pagination.type}', ${pagination.current_page - 1})">Previous</a></li>`;
    }
    
    for (let i = 1; i <= pagination.total_pages; i++) {
        html += `<li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="loadPage('${pagination.type}', ${i})">${i}</a>
                </li>`;
    }
    
    if (pagination.current_page < pagination.total_pages) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadPage('${pagination.type}', ${pagination.current_page + 1})">Next</a></li>`;
    }
    
    paginationContainer.innerHTML = html;
} 