@extends('layouts.app')
@section('title', 'Members Overview')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/users.css') }}">
    <style>
        /* ================= SKELETON & UI FIXES ================= */
        .skeleton-box {
            background: #e2e8f0;
            background: linear-gradient(90deg, #e2e8f0 25%, #f8fafc 50%, #e2e8f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
            border-radius: 12px;
            display: block;
        }
        @keyframes shimmer { 0% { background-position: -200% 0; } 100% { background-position: 200% 0; } }
        
        /* Force hidden state for real content during load */
        #actual-members { display: none !important; }

        /* Transparent Scrollbar for Table */
        .user-table-scroll::-webkit-scrollbar { width: 0px; background: transparent; }
        .user-table-scroll { scrollbar-width: none; -ms-overflow-style: none; }

        /* Ensure skeleton containers don't show white backgrounds/shadows */
        #skeleton-users .user-title, 
        #skeleton-users .user-saying, 
        #skeleton-users .button-wrapper, 
        #skeleton-users .search-wrapper, 
        #skeleton-users .table-wrapper {
            background: transparent !important;
            box-shadow: none !important;
            border: none !important;
        }
    </style>
@endpush

@section('content')
    <!-- ================= SKELETON STATE ================= -->
    <div id="skeleton-users">
        <!-- Title Skeleton -->
        <div class="user-title">
            <div class="skeleton-box" style="height: 35px; width: 300px; margin-top: 30px;"></div>
        </div>
        <!-- Saying Skeleton -->
        <div class="user-saying" style="top: 85px;">
            <div class="skeleton-box" style="height: 18px; width: 380px;"></div>
        </div>
        <!-- Add Button Skeleton -->
        <div class="button-wrapper">
            <div class="skeleton-box" style="height: 45px; width: 140px; border-radius: 10px;"></div>
        </div>
        <!-- Search Wrapper Skeleton -->
        <div class="search-wrapper" style="top: 120px;">
            <div class="skeleton-box" style="height: 48px; width: 100%; border-radius: 12px;"></div>
        </div>
        <!-- Table Skeleton -->
        <div class="table-wrapper" style="top: 195px;">
            <div class="skeleton-box" style="height: 50px; width: 100%; margin-bottom: 10px; border-radius: 15px 15px 0 0;"></div>
            @for($i=0; $i<7; $i++)
                <div class="skeleton-box" style="height: 65px; width: 100%; margin-bottom: 10px;"></div>
            @endfor
        </div>
    </div>

    <!-- ================= ACTUAL CONTENT ================= -->
    <div id="actual-members">
        <div class="user-title"><p>Member Directory</p></div>
        <div class="user-saying"><p>Manage your church members and roles.</p></div>
        
        <div class="button-wrapper" id="openAddModalBtn">
            <button class="button" type="button"><i class="fa-solid fa-plus"></i> Add Member</button>
        </div>

        <div class="search-wrapper">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="searchInput" placeholder="Search by name, email or role...">
        </div>

        <div class="table-wrapper">
            <div class="user-table-scroll">
                <table class="user-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="user-data"></tbody>
                </table>
            </div>
        </div>
        <div id="pagination-members" class="pagination-container"></div>
    </div>

    @include('users.partials.modal') 
@endsection

@push('scripts')
<script>
    const userDataBody = document.getElementById('user-data');
    const confirmDialog = document.getElementById('confirmDialog');
    const searchInput = document.getElementById('searchInput');
    const modal = document.getElementById('addMemberModal');
    const csrfToken = '{{ csrf_token() }}';

    let membersCurrentPage = 1;
    const membersPerPage = 10;
    let membersFullData = [];

    // --- 1. INITIALIZATION ---
    document.addEventListener('DOMContentLoaded', () => {
        loadUsers();
        
        // Background Click-to-Close for Modal
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });

        // Dialog Cancel Logic
        const cancelDeleteBtn = document.getElementById('cancelDelete');
        if (cancelDeleteBtn) {
            cancelDeleteBtn.onclick = () => confirmDialog.close();
        }
    });

    function showRealContent() {
        document.getElementById('skeleton-users').style.display = 'none';
        document.getElementById('actual-members').style.setProperty('display', 'block', 'important');
    }

    function loadUsers() {
        fetch("{{ url('/users/data') }}")
            .then(res => res.json())
            .then(data => {
                membersFullData = data;
                setTimeout(() => {
                    showRealContent();
                    renderMembersTable();
                }, 1200);
            });
    }

    // --- 2. TABLE RENDERING ---
    function renderMembersTable() {
        const term = searchInput.value.toLowerCase();
        const filteredData = membersFullData.filter(user => {
            const name = (user.Fullname || "").toLowerCase();
            const email = (user.Email || "").toLowerCase();
            const role = (user.Role_Name || 'Member').toLowerCase();
            return name.includes(term) || email.includes(term) || role.includes(term);
        });

        const start = (membersCurrentPage - 1) * membersPerPage;
        const end = start + membersPerPage;
        const paginated = filteredData.slice(start, end);

        userDataBody.innerHTML = '';
        paginated.forEach(user => {
            const isInactive = user.Account_Status === 'Inactive';
            const statusLabel = isInactive ? 'Deactivated' : 'Active';
            const actionButtonText = isInactive ? 'Activate' : 'Deactivate';
            const actionIcon = isInactive ? 'fa-solid fa-user-check activate-mode' : 'fa-solid fa-user-slash deactivate-icon';
            
            let profileDisplay = user.Profile_Picture 
                ? `<img src="/images/${user.Profile_Picture}" class="user-avatar-img">` 
                : `<i class="fa-solid fa-user"></i>`;

            userDataBody.innerHTML += `
                <tr class="${isInactive ? 'row-disabled' : ''}">
                    <td>
                        <div class="user-info-cell">
                            <div class="avatar-circle">${profileDisplay}</div>
                            <div class="user-text">
                                <span class="user-name">${user.Fullname}</span>
                                <span class="user-email">${user.Email}</span>
                                <span class="user-contact">${user.Contact_Number}</span>
                            </div>
                        </div>
                    </td>
                    <td><span class="badge-role">${user.Role_Name || 'Member'}</span></td>
                    <td><div class="status-pill ${isInactive ? 'inactive' : 'active'}"><span class="status-dot"></span> ${statusLabel}</div></td>
                    <td>${user.created_at ? user.created_at.split('T')[0] : '2024-01-01'}</td>
                    <td class="action-column">
                        <div class="action-container">
                            <a href="#" onclick="openEditModal(event, ${JSON.stringify(user).replace(/"/g, '&quot;')})">
                                <i class="fa-regular fa-pen-to-square edit-icon"></i>
                                <span class="action-text"> Edit</span>
                            </a>
                            <a href="#" onclick="toggleAccount(event, ${user.User_id}, '${user.Account_Status}')">
                                <i class="${actionIcon}"></i>
                                <span class="action-text"> ${actionButtonText}</span>
                            </a>
                        </div>
                    </td>
                </tr>`;
        });

        setupPagination(filteredData.length, membersPerPage, membersCurrentPage, 'pagination-members', (p) => {
            membersCurrentPage = p;
            renderMembersTable();
        });
    }

    // --- 3. SEARCH & ACCOUNT TOGGLE ---
    searchInput.addEventListener('input', () => {
        membersCurrentPage = 1;
        renderMembersTable();
    });

    window.toggleAccount = function(event, userId, currentStatus) {
        event.preventDefault();
        const newStatus = (currentStatus === 'Active') ? 'Inactive' : 'Active';
        const word = (currentStatus === 'Active') ? 'deactivate' : 'activate';
        
        document.getElementById('dialogMessage').textContent = `Are you sure you want to ${word} this account?`;
        confirmDialog.showModal();

        document.getElementById('confirmDelete').onclick = () => {
            confirmDialog.close();
            fetch("{{ url('/users/toggle-status') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ User_id: userId, Account_Status: newStatus })
            }).then(() => loadUsers());
        };
    };

    // --- 4. MODAL CONTROLS ---
    document.getElementById('openAddModalBtn').onclick = () => {
        document.getElementById('memberForm').reset();
        document.getElementById('edit_user_id').value = "";
        document.getElementById('modalTitle').textContent = "Add New Member";
        modal.classList.add('active');
    };

    window.openEditModal = function(event, user) {
        event.preventDefault();
        document.getElementById('edit_user_id').value = user.User_id;
        document.getElementById('fullname').value = user.Fullname;
        document.getElementById('username').value = user.Username;
        document.getElementById('email').value = user.Email;
        document.getElementById('contact').value = user.Contact_Number;
        document.getElementById('gender').value = user.Gender;
        document.getElementById('role').value = user.Role_Name || "Member";
        
        document.getElementById('modalTitle').textContent = "Edit Member Details";
        modal.classList.add('active');
    };

    const closeModal = () => modal.classList.remove('active');
    document.getElementById('closeModal').onclick = closeModal;
    document.getElementById('cancelBtn').onclick = closeModal;

    // --- 5. FORM SUBMISSION ---
    document.getElementById('memberForm').onsubmit = function(e) {
        e.preventDefault();
        const userId = document.getElementById('edit_user_id').value;
        const targetUrl = userId ? "{{ url('/users/update') }}" : "{{ url('/users/store') }}";
        const action = userId ? "update" : "add";

        document.getElementById('dialogMessage').textContent = `Are you sure you want to ${action} this member?`;
        confirmDialog.showModal();

        document.getElementById('confirmDelete').onclick = () => {
            confirmDialog.close();
            fetch(targetUrl, { 
                method: 'POST', 
                headers: { 'X-CSRF-TOKEN': csrfToken }, 
                body: new FormData(this) 
            })
            .then(res => res.text())
            .then(data => {
                if (data.trim() === "success") { 
                    closeModal(); 
                    loadUsers(); 
                }
            });
        };
    };

    function setupPagination(totalItems, perPage, currentPage, containerId, onPageChange) {
        const totalPages = Math.ceil(totalItems / perPage);
        const container = document.getElementById(containerId);
        if(!container) return;
        container.innerHTML = '';
        if (totalPages <= 1) return;
        for (let i = 1; i <= totalPages; i++) {
            const btn = document.createElement('button');
            btn.innerText = i;
            btn.className = (i === currentPage) ? 'active' : '';
            btn.onclick = () => onPageChange(i);
            container.appendChild(btn);
        }
    }
</script>
@endpush