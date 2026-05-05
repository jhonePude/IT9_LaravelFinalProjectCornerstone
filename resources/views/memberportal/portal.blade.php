@extends('layouts.app')
@section('title', 'My Portal')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/myPortal.css') }}">
    <link rel="icon" href="/images/logo.png?v=3">
    <style>
        .skeleton-box {
            background: #e2e8f0;
            background: linear-gradient(90deg, #e2e8f0 25%, #f8fafc 50%, #e2e8f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
            border-radius: 12px;
            display: block;
        }
        @keyframes shimmer { 0% { background-position: -200% 0; } 100% { background-position: 200% 0; } }
        #actual-portal { display: none !important; }
        @media (max-width: 768px) {
            #skeleton-portal {
                padding-top: 10px;
                margin-left: 10px !important;
                margin-right: 10px !important;
                width: calc(100% - 20px) !important;
            }
        }
    </style>
@endpush

@section('content')

    <div id="skeleton-portal">
        <div class="profile-card" style="background: white; border: none; margin-bottom: 20px;">
            <div class="profile-header" style="display: flex; align-items: center; gap: 20px;">
                <div class="skeleton-box" style="width: 85px; height: 85px; border-radius: 50%;"></div>
                <div style="flex: 1;">
                    <div class="skeleton-box" style="height: 25px; width: 60%; margin-bottom: 10px;"></div>
                    <div class="skeleton-box" style="height: 15px; width: 40%;"></div>
                </div>
            </div>
            <hr class="gold-divider" style="opacity: 0.1; margin: 20px 0;">
            <div class="profile-details-grid">
                <div class="skeleton-box" style="height: 40px; width: 100%;"></div>
                <div class="skeleton-box" style="height: 40px; width: 100%;"></div>
                <div class="skeleton-box" style="height: 40px; width: 100%;"></div>
                <div class="skeleton-box" style="height: 40px; width: 100%;"></div>
            </div>
        </div>
    </div>

    <div id="actual-portal">
        <div class="profile-card">
            <div class="profile-header">
                <input type="file" id="profileInput" style="display:none" accept="image/*">
                
                <div class="avatar" onclick="document.getElementById('profileInput').click()">
                    @php
                        $userPhoto = $user->Profile_Picture;
                        $photoPath = public_path('images/' . $userPhoto);
                        $displayPhoto = ($userPhoto && file_exists($photoPath)) ? asset('images/' . $userPhoto) : asset('images/profile-male.png');
                    @endphp
                    <img id="avatarImage" src="{{ $displayPhoto }}">
                    <div class="avatar-overlay"><i class="fa-solid fa-camera"></i></div>
                </div>
                
                <div class="profile-main">
                    <h2 class="profile-name">{{ $user->Fullname }}</h2>
                    <p class="profile-role">{{ $user->Role_Id == 1 ? 'Administrator' : 'Church Member' }}</p>
                    <div class="profile-status">
                        <span class="status-dot"></span>
                        <span class="status-label">ACTIVE ACCOUNT</span>
                    </div>
                </div>
                
                <div class="notif-container">
                    <button class="notif-btn" id="notifBell">
                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg>
                        @if($unreadNotifications > 0)
                            <span id="notif-badge">{{ $unreadNotifications }}</span>
                        @endif
                    </button>

                    <div class="notif-dropdown" id="notifDropdown">
                        <div class="notif-header">
                            <span>Notifications</span>
                            <a href="javascript:void(0)" onclick="markAllAsRead()" style="color: #2563eb; text-decoration: none; font-size: 11px;">Mark as read</a>
                        </div>
                        <div class="notif-list" id="notifListItems">
                            @forelse(auth()->user()->unreadNotifications as $notification)
                                <div class="notif-item">
                                    <strong>{{ $notification->data['title'] }}</strong>
                                    <p>{{ $notification->data['message'] }}</p>
                                    <small>{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                            @empty
                                <div style="padding: 20px; text-align: center; color: #94a3b8; font-size: 13px;">No new notifications</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
            <hr class="gold-divider">
            <div class="profile-details-grid">
                <div class="detail-item"><label>Email</label><p>{{ $user->Email }}</p></div>
                <div class="detail-item"><label>Phone</label><p>{{ $user->Contact_Number }}</p></div>
                <div class="detail-item"><label>Gender</label><p>{{ $user->Gender }}</p></div>
                <div class="detail-item"><label>Joined</label><p id="joinedCountDisplay">{{ $eventsJoined }} events</p></div>
            </div>
        </div>

        <div class="search-wrapper">
            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="portalSearch" placeholder="Search events by title or location...">
            </div>
            <button class="filter-btn" id="filterJoined">Joined</button>
            <button class="filter-btn active" id="filterUpcoming">Upcoming</button>
        </div>

        <div class="cards-container" id="portalCardsContainer"></div>
        <div id="pagination-portal" class="pagination-container"></div>
    </div>

    <div id="DetailsModal" class="modal-overlay" onclick="handleBackdropClick(event)">
        <div class="modal-content" onclick="event.stopPropagation()">
            <div class="modal-banner">
                <img src="" id="modalImg">
                <i class="fa-solid fa-circle-xmark close-icon" onclick="closeAllModals()"></i>
            </div>
            <div class="modal-body">
                <h2 id="modalTitle"></h2>
                <div class="modal-info"><i class="fa-regular fa-calendar"></i> <span id="modalDate"></span></div>
                <div class="modal-info"><i class="fa-regular fa-clock" style="color: #10b981;"></i> <span id="modalTime"></span></div>
                <div class="modal-info"><i class="fa-solid fa-location-dot"></i> <span id="modalLocation"></span></div>
                <p id="modalDescription"></p>
            </div>
        </div>
    </div>

    <dialog class="dialog" id="confirmDialog">
        <div class="dialog-content">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <p id="dialogMessage"></p>
        </div>
        <div class="dialog-actions">
            <button class="btn-cancel" onclick="document.getElementById('confirmDialog').close()">Cancel</button>
            <button class="btn-confirm" id="confirmBtn">Confirm</button>
        </div>
    </dialog>
@endsection

@push('scripts')
<script>
    const container = document.getElementById('portalCardsContainer');
    const searchInput = document.getElementById('portalSearch');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    let allEvents = [];
    let currentFilter = 'upcoming';
    let portalCurrentPage = 1;
    const portalPerPage = 6;

    function loadPortalEvents() {
        fetch("{{ route('portal.events.data') }}")
            .then(res => res.json())
            .then(events => {
                allEvents = events;
                setTimeout(() => {
                    document.getElementById('skeleton-portal').style.display = 'none';
                    document.getElementById('actual-portal').style.setProperty('display', 'block', 'important');
                    renderEvents();
                }, 1000);
            });
    }

    function renderEvents() {
        const term = searchInput.value.toLowerCase();
        const filtered = allEvents.filter(ev => {
            const matchesSearch = ev.Title.toLowerCase().includes(term) || ev.Location.toLowerCase().includes(term);
            if (currentFilter === 'joined') return matchesSearch && ev.is_joined;
            return matchesSearch;
        });

        const start = (portalCurrentPage - 1) * portalPerPage;
        const end = start + portalPerPage;
        const paginated = filtered.slice(start, end);

        container.innerHTML = '';
        if(paginated.length === 0) {
            container.innerHTML = '<p style="grid-column: 1/-1; text-align:center; padding: 20px; color:#94a3b8;">No events found.</p>';
        }

        paginated.forEach(event => {
            let banner = event.Image_Banner || 'church1.jpg';
            if (!banner.includes('http')) banner = `/images/${banner}`;
            const btnClass = event.is_joined ? 'leave-card-btn' : 'join-card-btn';
            
            container.innerHTML += `
                <div class="service-card" onclick='openDetails(${JSON.stringify(event)})'>
                    <div class="card-image">
                        <span class="badge">${event.category ? event.category.Event_Category_Name : 'General'}</span>
                        <img src="${banner}">
                    </div>
                    <div class="card-content">
                        <h2 class="card-title">${event.Title}</h2>
                        <div class="card-row"><i class="fa-regular fa-calendar"></i> ${event.Event_Date}</div>
                        <div class="card-row"><i class="fa-regular fa-clock"></i> ${formatTime(event.Event_Time)}</div>
                    </div>
                    <div class="card-footer">
                        <span class="reg-count">${event.registrations_count} Registered</span>
                        <button class="${btnClass}" onclick="toggleJoin(event, ${event.Event_Id}, ${event.is_joined})">
                            ${event.is_joined ? 'Joined' : 'Join'}
                        </button>
                    </div>
                </div>`;
        });

        setupPagination(filtered.length, portalPerPage, portalCurrentPage, 'pagination-portal', (p) => {
            portalCurrentPage = p;
            renderEvents();
        });
    }

    window.toggleJoin = (e, id, joined) => {
        e.stopPropagation();
        document.getElementById('dialogMessage').innerText = joined ? "Leave this event?" : "Join this event?";
        const diag = document.getElementById('confirmDialog');
        diag.showModal();
        document.getElementById('confirmBtn').onclick = () => {
            fetch("/member/portal/toggle-join", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ Event_Id: id })
            }).then(res => res.json()).then(data => {
                const countElement = document.getElementById('joinedCountDisplay');
                let currentCount = parseInt(countElement.innerText);
                data.status === 'joined' ? currentCount++ : currentCount--;
                countElement.innerText = currentCount + " events";
                diag.close(); 
                loadPortalEvents(); 
            });
        };
    };

    window.markAllAsRead = function() {
        fetch("{{ route('notifications.markRead') }}", {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' }
        }).then(res => res.json()).then(data => {
            if(data.status === 'success') {
                const badge = document.getElementById('notif-badge');
                if(badge) badge.style.display = 'none';
                document.getElementById('notifListItems').innerHTML = '<div style="padding: 20px; text-align: center; color: #94a3b8; font-size: 13px;">No new notifications</div>';
            }
        });
    };

    document.getElementById('profileInput').onchange = function() {
        if (this.files && this.files[0]) {
            let formData = new FormData();
            formData.append('profile_photo', this.files[0]);
            fetch("/member/portal/update-photo", {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken },
                body: formData
            }).then(res => res.json()).then(data => {
                if (data.success) window.location.reload();
                else alert("Upload failed. Please check folder permissions.");
            });
        }
    };

    window.closeAllModals = () => document.getElementById('DetailsModal').classList.remove('active');
    window.handleBackdropClick = (e) => { if(e.target.id === 'DetailsModal') closeAllModals(); };

    function formatTime(timeString) {
        if(!timeString) return "TBA";
        const [hour, minute] = timeString.split(':');
        const h = hour % 12 || 12;
        const ampm = hour >= 12 ? 'PM' : 'AM';
        return `${h}:${minute} ${ampm}`;
    }

    window.openDetails = (event) => {
        document.getElementById('modalTitle').innerText = event.Title;
        document.getElementById('modalDate').innerText = event.Event_Date;
        document.getElementById('modalTime').innerText = formatTime(event.Event_Time);
        document.getElementById('modalLocation').innerText = event.Location;
        document.getElementById('modalDescription').innerText = event.Description || "No details provided.";
        let banner = event.Image_Banner || 'church1.jpg';
        document.getElementById('modalImg').src = banner.includes('http') ? banner : `/images/${banner}`;
        document.getElementById('DetailsModal').classList.add('active');
    };

    function setupPagination(totalItems, perPage, currentPage, containerId, callback) {
        const totalPages = Math.ceil(totalItems / perPage);
        const container = document.getElementById(containerId);
        if(!container) return;
        container.innerHTML = '';
        if (totalPages <= 1) return;
        for (let i = 1; i <= totalPages; i++) {
            const btn = document.createElement('button');
            btn.innerText = i;
            btn.className = i === currentPage ? 'active' : 'pg-btn';
            btn.onclick = () => callback(i);
            container.appendChild(btn);
        }
    }

    searchInput.oninput = () => { portalCurrentPage = 1; renderEvents(); };
    document.getElementById('filterJoined').onclick = function() {
        document.getElementById('filterUpcoming').classList.remove('active');
        this.classList.add('active');
        currentFilter = 'joined';
        portalCurrentPage = 1;
        renderEvents();
    };
    document.getElementById('filterUpcoming').onclick = function() {
        document.getElementById('filterJoined').classList.remove('active');
        this.classList.add('active');
        currentFilter = 'upcoming';
        portalCurrentPage = 1;
        renderEvents();
    };

    const bell = document.getElementById('notifBell');
    const drop = document.getElementById('notifDropdown');
    bell.onclick = (e) => { e.stopPropagation(); drop.classList.toggle('active'); };
    document.addEventListener('click', (e) => { if(drop && !drop.contains(e.target)) drop.classList.remove('active'); });

    document.addEventListener('DOMContentLoaded', loadPortalEvents);
</script>
@endpush