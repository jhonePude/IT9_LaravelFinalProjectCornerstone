@extends('layouts.app')

@section('title', 'Events Management')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/events.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* ================= SKELETON CSS ================= */
        .skeleton-box {
            background: #e2e8f0;
            background: linear-gradient(90deg, #e2e8f0 25%, #f8fafc 50%, #e2e8f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
            border-radius: 12px;
            display: block;
        }
        @keyframes shimmer { 
            0% { background-position: -200% 0; } 
            100% { background-position: 200% 0; } 
        }

        .modal-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background-color: rgba(15, 23, 42, 0.5);
            backdrop-filter: blur(8px);
            z-index: 5000;
            overflow-y: auto;
            padding: 20px 0;
            align-items: flex-start;
            justify-content: center;
        }
        .modal-overlay.active { display: flex; }

        #actual-events { display: none !important; }
    </style>
@endpush

@section('content')
    <!-- ================= SKELETON STATE ================= -->
    <div id="skeleton-events">
        <div class="user-title" style="background: transparent; border: none;">
            <div class="skeleton-box" style="height: 40px; width: 300px; margin-top: 30px;"></div>
        </div>
        <div class="search-wrapper" style="top: 115px; background: transparent; box-shadow: none;">
            <div class="skeleton-box" style="height: 45px; width: 350px;"></div>
        </div>
        <div class="cards-container" style="top: 180px; background: transparent; display: flex; gap: 15px; flex-wrap: wrap;">
            @for($i=0; $i<6; $i++)
                <div class="skeleton-box" style="height: 320px; width: calc((100% / 3) - 17px); min-width: 300px;"></div>
            @endfor
        </div>
    </div>

    <!-- ================= ACTUAL CONTENT ================= -->
    <div id="actual-events">
        <div class="user-title"><p>Events Calendar</p></div>
        <div class="user-saying"><p>Upcoming church services, sacraments, and activities.</p></div>

        <div class="button-wrapper" id="openCreateBtn">
            <i class="fa-solid fa-plus"></i>
            <button class="button" type="button">Create Event</button>
        </div>

        <div class="search-wrapper">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="eventSearchInput" placeholder="Search by service or name...">
        </div>

        <div class="cards-container" id="eventCardsContainer"></div>
        <div id="pagination-events" class="pagination-container"></div>



        <dialog class="dialog" id="confirmDialog">
            <p><i class="fa-solid fa-triangle-exclamation"></i> <span id="dialogMessage">Are you sure?</span></p>
            <div class="dialog-buttons">
                <!-- These IDs MUST match your CSS and the JS above -->
                <button id="cancelDelete" type="button">Cancel</button>
                <button id="confirmDelete" type="button">Yes</button>
            </div>
        </dialog>
    </div>

    @include('events.partials.modals')
@endsection

@push('scripts')
<script>
    // --- GLOBAL VARIABLES ---
    const cardContainer = document.getElementById('eventCardsContainer');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const confirmDialog = document.getElementById('confirmDialog');
    const cancelBtnDialog = document.getElementById('cancelDelete');
    const confirmBtnDialog = document.getElementById('confirmDelete');
    
    let currentEventData = null; 
    let eventsCurrentPage = 1;
    const eventsPerPage = 6;
    let eventsFullData = [];

    // --- INITIALIZATION ON LOAD ---
    document.addEventListener('DOMContentLoaded', function() {
        loadEvents();

        // 1. CLICK BACKGROUND TO CLOSE LOGIC
        // Targets both DetailsModal and CreateEventModal
        const overlays = document.querySelectorAll('.modal-overlay');
        overlays.forEach(overlay => {
            overlay.addEventListener('click', function(event) {
                // If user clicks the blurred background and NOT the white modal box
                if (event.target === overlay) {
                    closeAllModals();
                }
            });
        });

        // 2. OPEN CREATE MODAL LOGIC
        const openBtn = document.getElementById('openCreateBtn');
        if (openBtn) {
            openBtn.onclick = () => {
                document.getElementById('eventForm').reset();
                document.getElementById('event_id').value = '';
                document.getElementById('modalHeading').innerText = "Create New Event";
                document.getElementById('submitBtn').innerText = "Create Event";
                document.getElementById('CreateEventModal').classList.add('active');
            };
        }

        // 3. SEARCH INPUT LOGIC
        const searchInput = document.getElementById('eventSearchInput');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                eventsCurrentPage = 1;
                renderEventsCards();
            });
        }

        // 4. DIALOG CANCEL LOGIC
        if (cancelBtnDialog) {
            cancelBtnDialog.addEventListener('click', (e) => {
                e.preventDefault();
                confirmDialog.close(); 
            });
        }
    });

    // --- DATA FETCHING ---
    function loadEvents() {
        fetch("{{ url('/events/data') }}")
            .then(res => res.json())
            .then(events => {
                eventsFullData = events;
                setTimeout(() => {
                    showRealEvents();
                    renderEventsCards();
                }, 1000); // Reduced delay for better UX
            });
    }

    function showRealEvents() {
        document.getElementById('skeleton-events').style.display = 'none';
        document.getElementById('actual-events').style.setProperty('display', 'block', 'important');
    }

    // --- RENDERING CARDS ---
    function renderEventsCards() {
        const searchTerm = document.getElementById('eventSearchInput').value.toLowerCase();
        const filteredData = eventsFullData.filter(event => {
            const title = (event.Title || "").toLowerCase();
            const category = (event.category ? event.category.Event_Category_Name : 'General').toLowerCase();
            return title.includes(searchTerm) || category.includes(searchTerm);
        });

        const start = (eventsCurrentPage - 1) * eventsPerPage;
        const end = start + eventsPerPage;
        const paginated = filteredData.slice(start, end);

        cardContainer.innerHTML = ''; 
        if (paginated.length === 0) {
            cardContainer.innerHTML = '<p style="margin-left: 20px; color: #64748b;">No events found.</p>';
        }

        paginated.forEach(event => {
            let banner = event.Image_Banner || 'church1.jpg';
            if (!banner.includes('http')) banner = `/images/${banner}`;

            cardContainer.innerHTML += `
                <div class="service-card" onclick='openDetails(${JSON.stringify(event)})'>
                    <div class="card-header">
                        <span class="service-badge">${event.category ? event.category.Event_Category_Name : 'General'}</span>
                        <img src="${banner}">
                    </div>
                    <div class="card-body">
                        <h2 class="event-title">${event.Title}</h2>
                        <div class="details-row"><i class="fa-regular fa-calendar icon"></i> <span>${event.Event_Date}</span></div>
                        <div class="card-footer">
                            <div class="registered-count"><i class="fa-solid fa-users icon-gray"></i> ${event.registrations_count} Registered</div>
                            <a href="javascript:void(0)" class="view-details">View Details</a>
                        </div>
                    </div>
                </div>`;
        });

        setupPagination(filteredData.length, eventsPerPage, eventsCurrentPage, 'pagination-events', (p) => {
            eventsCurrentPage = p;
            renderEventsCards();
        });
    }

    // --- VIEW DETAILS MODAL ---
    window.openDetails = function(event) {
        currentEventData = event;
        document.getElementById('modalTitle').innerText = event.Title;
        document.getElementById('modalDate').innerText = event.Event_Date;
        document.getElementById('modalTime').innerText = event.Event_Time;
        document.getElementById('modalLocation').innerText = event.Location;
        document.getElementById('modalCount').innerText = event.registrations_count + " Registered Members";
        document.getElementById('modalDescription').innerText = event.Description || "Join us for this special event.";
        
        let bannerSrc = event.Image_Banner;
        if (bannerSrc && !bannerSrc.includes('http')) { bannerSrc = `/images/${bannerSrc}`; }
        else if (!bannerSrc) { bannerSrc = 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?auto=format&fit=crop&w=600&q=80'; }
        
        document.getElementById('modalImg').src = bannerSrc;
        document.getElementById('DetailsModal').classList.add('active');
    };

    // --- DELETE FLOW ---
    document.getElementById('deleteEventBtn').onclick = () => {
        document.getElementById('dialogMessage').innerHTML = `Do you want to continue to <strong>delete</strong> this event?`;
        confirmDialog.showModal();

        confirmBtnDialog.onclick = () => {
            fetch("{{ url('/events/delete') }}/" + currentEventData.Event_Id, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken }
            }).then(() => {
                confirmDialog.close();
                closeAllModals();
                loadEvents();
            });
        };
    };

    // --- CREATE / EDIT FLOW ---
    document.getElementById('eventForm').onsubmit = function(e) {
        e.preventDefault();
        const eventId = document.getElementById('event_id').value;
        const actionText = eventId ? "update" : "create";
        
        document.getElementById('dialogMessage').innerHTML = `Are you sure you want to <strong>${actionText}</strong> this event?`;
        confirmDialog.showModal();

        confirmBtnDialog.onclick = () => {
            confirmDialog.close();
            fetch("{{ url('/events/store') }}", {
                method: 'POST',
                body: new FormData(document.getElementById('eventForm')),
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(() => {
                closeAllModals();
                loadEvents();
            });
        };
    };

    // --- SWITCH FROM DETAILS TO EDIT ---
    document.getElementById('editEventBtn').onclick = () => {
        document.getElementById('event_id').value = currentEventData.Event_Id;
        document.getElementById('title_input').value = currentEventData.Title;
        document.getElementById('date_input').value = currentEventData.Event_Date;
        document.getElementById('time_input').value = currentEventData.Event_Time;
        document.getElementById('location_input').value = currentEventData.Location;
        document.getElementById('category_input').value = currentEventData.Event_Category_Id;
        document.getElementById('description_input').value = currentEventData.Description;

        document.getElementById('modalHeading').innerText = "Edit Event";
        document.getElementById('submitBtn').innerText = "Update Event";
        
        document.getElementById('DetailsModal').classList.remove('active');
        document.getElementById('CreateEventModal').classList.add('active');
    };

    // --- HELPER FUNCTIONS ---
    window.closeAllModals = function() {
        document.querySelectorAll('.modal-overlay').forEach(m => m.classList.remove('active'));
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