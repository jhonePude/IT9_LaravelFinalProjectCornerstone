<!-- CREATE/EDIT MODAL -->
<div id="CreateEventModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalHeading">Create New Event</h2>
            <i class="fa-solid fa-xmark close-modal" onclick="closeAllModals()"></i>
        </div>
        <form id="eventForm" class="modal-form">
            @csrf
            <input type="hidden" name="Event_Id" id="event_id">
            <div class="form-group">
                <label>Event Title</label>
                <input type="text" name="title" id="title_input" placeholder="e.g. Easter Sunday Service" required>
            </div>
            <div class="form-group">
                <label>Date</label>
                <input type="date" name="date" id="date_input" required>
            </div>
            <div class="form-group">
                <label>Time</label>
                <input type="time" name="time" id="time_input" required>
            </div>
            <div class="form-group">
                <label>Location</label>
                <input type="text" name="location" id="location_input" placeholder="e.g. Main Sanctuary" required>
            </div>
            <div class="form-group">
                <label>Category</label>
                <select name="category_id" id="category_input" required>
                    <option value="" disabled selected hidden>Select Category</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->Event_Category_Id }}">{{ $cat->Event_Category_Name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" id="description_input" placeholder="Enter event details..." style="width:100%; height:80px; border-radius:8px; border:1px solid #e2e8f0; padding:10px; font-family:inherit; outline:none;"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="cancel-btn" onclick="closeAllModals()">Cancel</button>
                <button type="submit" class="create-btn" id="submitBtn">Create Event</button>
            </div>
        </form>
    </div>
</div>

<!-- DETAILS MODAL -->
<div id="DetailsModal" class="modal-overlay">
    <div class="modal-content details-modal-content">
        <div class="modal-banner"><img src="" id="modalImg"></div>
        <div class="modal-header">
            <h2 id="modalTitle"></h2>
            <i class="fa-solid fa-xmark close-modal" onclick="closeAllModals()"></i>
        </div>
        <div class="modal-body">
            <div class="detail-info">
                <p><strong><i class="fa-regular fa-calendar" style="color:#10b981; margin-right:8px;"></i> Date:</strong> <span id="modalDate"></span></p>
                <p><strong><i class="fa-regular fa-clock" style="color:#10b981; margin-right:8px;"></i> Time:</strong> <span id="modalTime"></span></p>
                <p><strong><i class="fa-solid fa-location-dot" style="color:#10b981; margin-right:8px;"></i> Location:</strong> <span id="modalLocation"></span></p>
                <p><strong><i class="fa-solid fa-users" style="color:#10b981; margin-right:8px;"></i> Registrations:</strong> <span id="modalCount"></span></p>
            </div>
            <div class="about-section">
                <h3 style="font-size: 1.1rem; margin-bottom: 10px; color:#1e293b;">About this Event</h3>
                <p id="modalDescription" style="font-size: 0.95rem; color: #64748b; line-height: 1.5;"></p>
            </div>
        </div>
        <div class="modal-footer footer-between">
            <button type="button" class="edit-btn" id="editEventBtn" style="background-color:#2563eb; color:white; border:none; padding:12px; border-radius:8px; flex:1; font-weight:bold; cursor:pointer;"><i class="fa-solid fa-pen"></i> Edit</button>
            <button type="button" class="delete-btn" id="deleteEventBtn" style="background-color:#dc2626; color:white; border:none; padding:12px; border-radius:8px; flex:1; font-weight:bold; cursor:pointer;"><i class="fa-solid fa-trash"></i> Delete</button>
        </div>
    </div>
</div>

<!-- CORNERSTONE STANDARD DIALOG -->
<dialog class="dialog" id="confirmDialog">
    <p id="dialogMessage" style="display: flex; align-items: center; gap: 12px; font-size: 1.1rem; color: #1f2937;">
        <i class="fa-solid fa-triangle-exclamation" style="color: #eab308; font-size: 2.5rem;"></i> 
        <span>Are you sure?</span>
    </p>
    <div class="dialog-buttons" style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 20px;">
        <button id="cancelDeleteBtn" style="background-color: #f3f4f6; color: #4b5563; padding: 10px 25px; border-radius: 8px; border:none; cursor:pointer; font-weight:600;">Cancel</button>
        <button id="confirmDeleteBtn" style="background-color: rgba(15, 23, 42, 0.909); color: white; padding: 10px 25px; border-radius: 8px; border:none; cursor:pointer; font-weight:600; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">Yes</button>
    </div>
</dialog>