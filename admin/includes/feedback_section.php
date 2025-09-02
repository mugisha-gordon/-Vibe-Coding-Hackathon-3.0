<?php
// Get total number of feedback entries
$total_feedback_sql = "SELECT COUNT(*) as total FROM feedback";
$total_feedback_result = mysqli_query($conn, $total_feedback_sql);
$total_feedback = mysqli_fetch_assoc($total_feedback_result)['total'];

// Calculate total pages
$items_per_page = 10;
$total_pages = ceil($total_feedback / $items_per_page);
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $items_per_page;

// Get feedback entries with pagination
$feedback_sql = "SELECT * FROM feedback ORDER BY created_at DESC LIMIT $offset, $items_per_page";
$feedback_result = mysqli_query($conn, $feedback_sql);
?>

<div class="tab-pane fade" id="feedback">
    <div class="content-section">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Feedback Management</h2>
            <div class="d-flex gap-2">
                <div class="search-box">
                    <input type="text" id="feedbackSearch" class="form-control" placeholder="Search feedback...">
                    <i class="fas fa-search"></i>
                </div>
                <select class="form-select" id="feedbackStatus">
                    <option value="">All Status</option>
                    <option value="unread">Unread</option>
                    <option value="read">Read</option>
                    <option value="replied">Replied</option>
                </select>
            </div>
        </div>

        <!-- Feedback Table -->
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Subject</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($feedback_result) {
                        while($feedback = mysqli_fetch_assoc($feedback_result)) {
                            $status_class = $feedback['status'] === 'unread' ? 'warning' : 
                                          ($feedback['status'] === 'replied' ? 'success' : 'info');
                            echo "<tr>
                                <td>{$feedback['name']}</td>
                                <td>{$feedback['email']}</td>
                                <td>{$feedback['subject']}</td>
                                <td>" . substr($feedback['message'], 0, 50) . "...</td>
                                <td><span class='status-badge bg-{$status_class}'>{$feedback['status']}</span></td>
                                <td>" . date('M d, Y', strtotime($feedback['created_at'])) . "</td>
                                <td>
                                    <button class='action-btn btn-info' onclick='viewFeedback({$feedback['id']})'>
                                        <i class='fas fa-eye'></i>
                                    </button>
                                    <button class='action-btn btn-success' onclick='replyToFeedback({$feedback['id']})'>
                                        <i class='fas fa-reply'></i>
                                    </button>
                                    <button class='action-btn btn-danger' onclick='deleteFeedback({$feedback['id']})'>
                                        <i class='fas fa-trash'></i>
                                    </button>
                                </td>
                            </tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <div class="pagination-container">
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <?php if ($current_page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $current_page - 1; ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo $i === $current_page ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($current_page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $current_page + 1; ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Feedback View Modal -->
<div class="modal fade" id="feedbackViewModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Feedback Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="feedbackDetails"></div>
            </div>
        </div>
    </div>
</div>

<!-- Reply Modal -->
<div class="modal fade" id="replyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reply to Feedback</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="replyForm">
                    <input type="hidden" id="feedbackId" name="feedbackId">
                    <div class="mb-3">
                        <label class="form-label">Original Message</label>
                        <div id="originalMessage" class="form-control bg-light"></div>
                    </div>
                    <div class="mb-3">
                        <label for="replyMessage" class="form-label">Your Reply</label>
                        <textarea class="form-control" id="replyMessage" name="replyMessage" rows="4" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Send Reply</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// View feedback details
function viewFeedback(id) {
    fetch(`actions/get_feedback.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('feedbackDetails').innerHTML = `
                <p><strong>From:</strong> ${data.name} (${data.email})</p>
                <p><strong>Subject:</strong> ${data.subject}</p>
                <p><strong>Message:</strong></p>
                <div class="message-content">${data.message}</div>
                <p><strong>Date:</strong> ${new Date(data.created_at).toLocaleString()}</p>
                <p><strong>Status:</strong> <span class="status-badge bg-${data.status === 'unread' ? 'warning' : (data.status === 'replied' ? 'success' : 'info')}">${data.status}</span></p>
            `;
            new bootstrap.Modal(document.getElementById('feedbackViewModal')).show();
        });
}

// Reply to feedback
function replyToFeedback(id) {
    fetch(`actions/get_feedback.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('feedbackId').value = id;
            document.getElementById('originalMessage').textContent = data.message;
            new bootstrap.Modal(document.getElementById('replyModal')).show();
        });
}

// Handle reply form submission
document.getElementById('replyForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('actions/reply_to_feedback.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('replyModal')).hide();
            showAlert('success', 'Reply sent successfully');
            location.reload();
        } else {
            showAlert('danger', data.error || 'Failed to send reply');
        }
    });
});

// Delete feedback
function deleteFeedback(id) {
    if (confirm('Are you sure you want to delete this feedback?')) {
        fetch(`actions/delete_feedback.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', 'Feedback deleted successfully');
                    location.reload();
                } else {
                    showAlert('danger', data.error || 'Failed to delete feedback');
                }
            });
    }
}

// Search functionality
document.getElementById('feedbackSearch').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('#feedback tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});

// Status filter
document.getElementById('feedbackStatus').addEventListener('change', function(e) {
    const status = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('#feedback tbody tr');
    
    rows.forEach(row => {
        if (!status) {
            row.style.display = '';
            return;
        }
        
        const statusCell = row.querySelector('.status-badge');
        row.style.display = statusCell.textContent.toLowerCase() === status ? '' : 'none';
    });
});
</script> 