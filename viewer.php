<?php
require_once './database/database.php';

$conn = getConnection();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approved Ordinance Proposals</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="container mt-4">
        <h1 class="text-center mb-4">Approved Ordinance Proposals</h1>

        <!-- Filters -->
        <div class="row mb-4 align-items-end">
            <div class="col-md-3">
                <label for="filterFromDate" class="form-label">From Date</label>
                <input type="date" class="form-control" id="filterFromDate">
            </div>
            <div class="col-md-3">
                <label for="filterToDate" class="form-label">To Date</label>
                <input type="date" class="form-control" id="filterToDate">
            </div>
            <div class="col-md-3">
                <label for="filterCommittee" class="form-label">Committee</label>
                <select class="form-select" id="filterCommittee">
                    <option value="">All Committees</option>
                    <?php
                    $query = "SELECT id, name FROM committees ORDER BY name";
                    $result = $conn->query($query);
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['name']) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-3 text-end">
                <button class="btn btn-primary me-2" id="applyFilters">Apply Filters</button>
                <button class="btn btn-secondary" id="clearFilters">Clear Filters</button>
            </div>
        </div>

        <!-- Proposals Container -->
        <div id="proposals-container" class="row"></div>
        <div id="no-proposals-message" class="alert alert-info text-center d-none">
            No approved proposals available at the moment.
        </div>

        <!-- Pagination -->
        <nav>
            <ul class="pagination justify-content-center" id="pagination"></ul>
        </nav>
    </div>

    <!-- Details Modal -->
    <div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailsModalLabel">Proposal Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h5 id="modalProposalTitle" class="text-primary"></h5>
                    <p><strong>Committee:</strong> <span id="modalCommittee"></span></p>
                    <p><strong>Proposed by:</strong> <span id="modalCreatedBy"></span></p>
                    <p><strong>Date:</strong> <span id="modalProposalDate"></span></p>
                    <p><strong>Details:</strong></p>
                    <div id="modalDetails" class="border p-3 rounded bg-light text-wrap" style="white-space: pre-wrap;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <footer class="text-center mt-4">
        <p>&copy; <?php echo date('Y'); ?> LGU Ordinance System. All rights reserved.</p>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            let currentPage = 1;
            const itemsPerPage = 6;

            function fetchProposals(page = 1) {
                const fromDate = $('#filterFromDate').val();
                const toDate = $('#filterToDate').val();
                const committee = $('#filterCommittee').val();

                $.ajax({
                    url: './controller/store/viewer_controller.php',
                    type: 'POST',
                    data: {
                        fetch_approved_proposals: true,
                        page: page,
                        itemsPerPage: itemsPerPage,
                        fromDate: fromDate,
                        toDate: toDate,
                        committee: committee
                    },
                    success: function (response) {
                        try {
                            const result = typeof response === 'string' ? JSON.parse(response) : response;
                            if (result.status === 'success' && result.data.length > 0) {
                                const proposalsContainer = $('#proposals-container');
                                proposalsContainer.empty();
                                result.data.forEach(proposal => {
                                    const truncatedDetails = truncateWords(proposal.details, 20);
                                    const card = `
                                        <div class="col-md-6 mb-4">
                                            <div class="card shadow-sm">
                                                <div class="row g-0">
                                                    <div class="col-md-8">
                                                        <div class="card-body">
                                                            <h5 class="card-title text-primary">${proposal.proposal}</h5>
                                                            <h6 class="card-subtitle mb-2 text-muted">
                                                                Committee: ${proposal.committee_name || 'N/A'}
                                                            </h6>
                                                            <p class="card-text">
                                                                <strong>Brief Details:</strong><br>
                                                                ${truncatedDetails}
                                                            </p>
                                                            <p class="card-text">
                                                                <small class="text-muted">
                                                                    Proposed by: ${proposal.created_by || 'N/A'}<br>
                                                                    Date: ${new Date(proposal.proposal_date).toLocaleDateString()}
                                                                </small>
                                                            </p>
                                                            <button class="btn btn-info btn-sm view-details-btn" data-id="${proposal.id}" data-bs-toggle="modal" data-bs-target="#detailsModal">
                                                                <i class="fas fa-info-circle me-1"></i> View Details
                                                            </button>
                                                            ${proposal.file_path ? `
                                                                <a href="https://docs.google.com/document/d/${proposal.file_path}/preview" target="_blank" class="btn btn-primary btn-sm">
                                                                    <i class="fas fa-eye me-1"></i> View Document
                                                                </a>` : `
                                                                <span class="text-muted">No document available</span>`}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 d-flex align-items-center justify-content-center">
                                                        ${proposal.file_path ? `
                                                            <img src="https://drive.google.com/thumbnail?id=${proposal.file_path}" alt="Document Thumbnail" class="img-fluid rounded">` : `
                                                            <div class="text-muted">No Thumbnail</div>`}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>`;
                                    proposalsContainer.append(card);
                                });

                                // Update pagination
                                updatePagination(result.totalPages, page);
                                $('#no-proposals-message').addClass('d-none');
                            } else {
                                $('#proposals-container').empty();
                                $('#pagination').empty();
                                $('#no-proposals-message').removeClass('d-none');
                            }
                        } catch (e) {
                            console.error('Error parsing response:', e);
                            alert('An error occurred while fetching proposals.');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX Error:', error);
                        alert('Failed to fetch proposals.');
                    }
                });
            }

            function updatePagination(totalPages, currentPage) {
                const pagination = $('#pagination');
                pagination.empty();

                for (let i = 1; i <= totalPages; i++) {
                    const activeClass = i === currentPage ? 'active' : '';
                    pagination.append(`
                        <li class="page-item ${activeClass}">
                            <a class="page-link" href="#" data-page="${i}">${i}</a>
                        </li>
                    `);
                }
            }

            $(document).on('click', '.page-link', function (e) {
                e.preventDefault();
                const page = $(this).data('page');
                currentPage = page;
                fetchProposals(page);
            });

            $('#applyFilters').on('click', function () {
                currentPage = 1;
                fetchProposals(currentPage);
            });

            $('#clearFilters').on('click', function () {
                $('#filterFromDate').val('');
                $('#filterToDate').val('');
                $('#filterCommittee').val('');
                currentPage = 1;
                fetchProposals(currentPage);
            });

            // Helper function to truncate words
            function truncateWords(text, wordLimit) {
                if (!text) return 'No details available.';
                const words = text.split(' ');
                return words.length > wordLimit ? words.slice(0, wordLimit).join(' ') + '...' : text;
            }

            // Initial fetch
            fetchProposals(currentPage);
        });
    </script>
</body>

</html>
