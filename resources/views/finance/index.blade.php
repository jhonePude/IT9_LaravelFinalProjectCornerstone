@extends('layouts.app')
@section('title', 'Financial Overview')
<link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/finance.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* ================= SKELETON ANIMATIONS ================= */
        .skeleton-box {
            background: #e2e8f0;
            background: linear-gradient(90deg, #e2e8f0 25%, #f8fafc 50%, #e2e8f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
            border-radius: 8px;
            display: block;
        }
        @keyframes shimmer { 0% { background-position: -200% 0; } 100% { background-position: 200% 0; } }
        
        #actual-finance { display: none !important; }

        /* Transparent Scrollbar for Table */
        .user-table-scroll::-webkit-scrollbar { width: 0px; background: transparent; }
        .user-table-scroll { scrollbar-width: none; -ms-overflow-style: none; }

        /* Skeleton Layout Specifics */
        .skeleton-row {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            border-bottom: 1px solid #f1f5f9;
            gap: 15px;
        }
        
        #skeleton-finance .table-wrapper {
            background: white !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08) !important;
            border-radius: 20px;
            overflow: hidden;
        }
    </style>
@endpush

@section('content')
    <!-- ================= SKELETON STATE ================= -->
    <div id="skeleton-finance">
        <!-- Title Skeleton -->
        <div class="user-title" style="background: transparent; border: none;">
            <div class="skeleton-box" style="height: 40px; width: 300px; margin-top: 30px;"></div>
        </div>
        
        <!-- Cards Skeleton -->
        <div class="cards-container finance-cards" style="top: 100px; background: transparent;">
            <div class="skeleton-box" style="height: 160px; flex: 1;"></div>
            <div class="skeleton-box" style="height: 160px; flex: 1;"></div>
            <div class="skeleton-box" style="height: 160px; flex: 1;"></div>
        </div>

        <!-- Table Skeleton - Pushed down to clear the cards -->
        <div class="table-wrapper" style="margin-top: 320px;">
            <!-- Fake Header -->
            <div class="skeleton-box" style="height: 50px; width: 100%; border-radius: 0;"></div>
            
            <!-- Skeleton Rows matching Finance Columns -->
            @for($i=0; $i<6; $i++)
                <div class="skeleton-row">
                    <!-- ID Column -->
                    <div style="flex: 1;"><div class="skeleton-box" style="height: 15px; width: 60px;"></div></div>
                    <!-- Description Column -->
                    <div style="flex: 2;"><div class="skeleton-box" style="height: 15px; width: 80%;"></div></div>
                    <!-- Category Column -->
                    <div style="flex: 1;"><div class="skeleton-box" style="height: 15px; width: 70px;"></div></div>
                    <!-- Type Pillar -->
                    <div style="flex: 1;"><div class="skeleton-box" style="height: 22px; width: 60px; border-radius: 20px;"></div></div>
                    <!-- Amount Column -->
                    <div style="flex: 1;"><div class="skeleton-box" style="height: 15px; width: 70px;"></div></div>
                    <!-- Actions Column -->
                    <div style="flex: 1; display: flex; gap: 8px; justify-content: flex-end;">
                        <div class="skeleton-box" style="height: 30px; width: 30px; border-radius: 6px;"></div>
                        <div class="skeleton-box" style="height: 30px; width: 30px; border-radius: 6px;"></div>
                    </div>
                </div>
            @endfor
        </div>
    </div>

    <!-- ================= ACTUAL CONTENT ================= -->
    <div id="actual-finance">
        <div class="user-title"><p>Financial Overview</p></div>
        <div class="user-saying"><p>Track income, tithes, and expenses.</p></div>
        
        <div class="button-wrapper" id="openModal">
            <i class="fa-solid fa-receipt"></i>
            <button class="button" type="button">Record Transaction</button>
        </div>

        <div class="button-reports-wrapper" onclick="window.location.href='{{ route('finance.export') }}'">
            <i class="fa-solid fa-download"></i>
            <button class="button" type="button">Export Reports</button>
        </div>

        <div class="cards-container finance-cards">
            <div class="balance-card">
                <div class="card-header"><p class="label">Total Balance</p><span class="icon">💵</span></div>
                <div class="card-body">
                    <h1 class="balance-amount" id="stat-balance">₱0.00</h1>
                    <p class="trend positive"><i class="fa-solid fa-sort"></i> Funds Balance</p>
                </div>
            </div>
            <div class="income-card">
                <div class="card-header"><p class="label">Total Income</p><span class="icon" style="color: #10b981;"><i class="fa-solid fa-circle-arrow-down"></i></span></div>
                <div class="card-body">
                    <h1 class="balance-amount" id="stat-income">₱0.00</h1>
                    <p class="trend positive"><i class="fa-solid fa-caret-up"></i> Revenue </p>
                </div>
            </div>
            <div class="expense-card">
                <div class="card-header"><p class="label">Total Expenses</p><span class="icon" style="color: #ef4444;"><i class="fa-solid fa-circle-arrow-up"></i></span></div>
                <div class="card-body">
                    <h1 class="balance-amount" id="stat-expense">₱0.00</h1>
                    <p class="trend negative"><i class="fa-solid fa-caret-down"></i> Expenditure </p>
                </div>
            </div>
        </div>

        <div class="table-title"><p>Recent Transactions</p></div>
        <div class="search"><i class="fa-solid fa-magnifying-glass"></i><input type="text" id="searchInput" placeholder="Search Transactions..."></div>

        <div class="table-wrapper">
            <div class="user-table-scroll">
                <table class="user-table" id="financeTable">
                    <thead>
                        <tr><th>Transaction ID</th><th>Description</th><th>Category</th><th>Type</th><th>Date</th><th>Status</th><th>Amount</th><th>Actions</th></tr>
                    </thead>
                    <tbody id="finance-data"></tbody>
                </table>
            </div>
        </div>
        <div id="pagination-finance" class="pagination-container"></div>
    </div>

    @include('finance.partials.modal')
@endsection

@push('scripts')
<script>
    const financeDataBody = document.getElementById('finance-data');
    const financeForm = document.getElementById('financeForm');
    const modal = document.getElementById('addRecordModal');
    const modalTitle = document.getElementById('modalTitle');
    const confirmDialog = document.getElementById('confirmDialog');
    const confirmBtn = document.getElementById('confirmDelete');
    const cancelDeleteBtn = document.getElementById('cancelDelete');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    let financeCurrentPage = 1;
    const financePerPage = 10;
    let financeFullData = [];
    let tnxToDelete = null;

    // --- 1. MODAL CONTROLS ---
    document.getElementById('openModal').addEventListener('click', () => {
        financeForm.reset();
        document.getElementById('edit_tnx_id').value = '';
        modalTitle.innerText = 'Record Transaction';
        modal.classList.add('active');
    });

    const closeModal = () => modal.classList.remove('active');
    document.getElementById('closeModal').onclick = closeModal;
    document.getElementById('cancelBtn').onclick = closeModal;

    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });

    // --- 2. DATA LOADING ---
    function showRealFinance() {
        document.getElementById('skeleton-finance').style.display = 'none';
        document.getElementById('actual-finance').style.setProperty('display', 'block', 'important');
    }

    function loadFinanceData() {
        fetch("{{ url('/finance/data') }}")
            .then(res => res.json())
            .then(data => {
                document.getElementById('stat-income').innerText = `₱${data.stats.income}`;
                document.getElementById('stat-expense').innerText = `₱${data.stats.expense}`;
                document.getElementById('stat-balance').innerText = `₱${data.stats.balance}`;
                financeFullData = data.transactions;
                
                setTimeout(() => {
                    showRealFinance();
                    renderFinanceTable();
                }, 1200);
            });
    }

    // --- 3. RENDERING ---
    function renderFinanceTable() {
        const filter = document.getElementById('searchInput').value.toLowerCase();
        const filteredData = financeFullData.filter(tnx => {
            const id = `TXN-${tnx.Transaction_Id}`.toLowerCase();
            const desc = (tnx.Description || "").toLowerCase();
            const cat = (tnx.Category_Name || 'Other').toLowerCase();
            return id.includes(filter) || desc.includes(filter) || cat.includes(filter);
        });

        const start = (financeCurrentPage - 1) * financePerPage;
        const end = start + financePerPage;
        const paginated = filteredData.slice(start, end);

        financeDataBody.innerHTML = '';
        paginated.forEach(tnx => {
            const statusClass = tnx.Status === 'Completed' ? 'status-completed' : 'status-pending';
            const payBtn = tnx.Status === 'Pending' 
                ? `<button class="btn-pay-now" onclick="processPayment(${tnx.Transaction_Id})"><i class="fa-solid fa-credit-card"></i> Pay Now</button>` 
                : `<div class="paid-placeholder"><i class="fa-solid fa-check-double"></i> Settled</div>`;

            financeDataBody.innerHTML += `
                <tr>
                    <td>TXN-${tnx.Transaction_Id}</td>
                    <td class="tnx-desc">${tnx.Description}</td>
                    <td>${tnx.Category_Name || 'Other'}</td>
                    <td>${tnx.Type}</td>
                    <td>${tnx.Transaction_Date.split('T')[0]}</td>
                    <td><div class="status-pill ${statusClass}"><span class="dot"></span> ${tnx.Status}</div></td>
                    <td class="tnx-amount">₱${parseFloat(tnx.Amount).toLocaleString()}</td>
                    <td class="action-column">
                        <div class="action-container">
                            ${payBtn}
                            <i class="fa-regular fa-pen-to-square edit-icon" onclick='openEditTnx(${JSON.stringify(tnx)})'></i>
                            <i class="fa-solid fa-trash delete-icon" onclick="deleteTnx(${tnx.Transaction_Id})"></i>
                        </div>
                    </td>
                </tr>`;
        });

        setupPagination(filteredData.length, financePerPage, financeCurrentPage, 'pagination-finance', (p) => {
            financeCurrentPage = p;
            renderFinanceTable();
        });
    }

    window.openEditTnx = function(tnx) {
        modalTitle.innerText = 'Edit Transaction';
        document.getElementById('edit_user_id').value = tnx.Transaction_Id; // Note: check if ID should be edit_tnx_id
        document.getElementById('description-input').value = tnx.Description;
        document.getElementById('type-input').value = tnx.Type;
        document.getElementById('category-input').value = tnx.Category_Id;
        document.getElementById('amount-input').value = tnx.Amount;
        document.getElementById('date-input').value = tnx.Transaction_Date.split('T')[0];
        document.getElementById('status-input').value = tnx.Status;
        modal.classList.add('active');
    };

    window.deleteTnx = function(id) {
        tnxToDelete = id;
        confirmDialog.showModal(); 
    };

    confirmBtn.onclick = function() {
        fetch(`{{ url('/finance/delete') }}/${tnxToDelete}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken }
        }).then(() => { confirmDialog.close(); loadFinanceData(); });
    };

    if(cancelDeleteBtn) cancelDeleteBtn.onclick = () => confirmDialog.close();

    financeForm.onsubmit = function(e) {
        e.preventDefault();
        fetch("{{ url('/finance/store') }}", {
            method: 'POST',
            body: new FormData(this),
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        }).then(() => { closeModal(); loadFinanceData(); });
    };

    window.processPayment = function(id) {
        fetch(`{{ url('/finance/pay') }}/${id}`, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken } })
        .then(res => res.json()).then(data => {
            if(data.checkout_url) window.location.href = data.checkout_url;
        });
    };

    document.getElementById('searchInput').oninput = () => { financeCurrentPage = 1; renderFinanceTable(); };

    function setupPagination(totalItems, perPage, currentPage, containerId, onPageChange) {
        const totalPages = Math.ceil(totalItems / perPage);
        const container = document.getElementById(containerId);
        if(!container || totalPages <= 1) return container.innerHTML = '';
        container.innerHTML = '';
        for (let i = 1; i <= totalPages; i++) {
            const btn = document.createElement('button');
            btn.innerText = i;
            btn.className = (i === currentPage) ? 'active' : '';
            btn.onclick = () => onPageChange(i);
            container.appendChild(btn);
        }
    }

    loadFinanceData();
</script>
@endpush