<!-- ADD/EDIT MEMBER POPUP MODAL -->
<div id="addMemberModal" class="modal-overlay">
    <div class="modal-content">
        <!-- FIXED HEADER (Outside Scroll) -->
        <div class="modal-header">
            <h2 class="pop-up-header" id="modalTitle">Add New Member</h2>
            <i class="fa-solid fa-xmark close-modal" id="closeModal"></i>
        </div>

        <!-- SCROLLABLE BODY (Form Only) -->
        <form class="modal-form" id="memberForm">
            @csrf
            <input type="hidden" id="edit_user_id" name="User_id">

            <div class="form-group">
                <label>Full Name</label>
                <input type="text" id="fullname" name="fullname" placeholder="Enter Fullname" required>
            </div>
            <div class="form-group">
                <label>Username</label>
                <input type="text" id="username" name="username" placeholder="Enter Username" required>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" id="email" name="email" placeholder="Enter Email" required>
            </div>
            <div class="form-group">
                <label>Contact Number</label>
                <input type="text" id="contact" name="contact" placeholder="Contact Number" required>
            </div>
            <div class="form-group">
                <label>Gender</label>
                <select name="gender" id="gender" required>
                    <option value="" disabled selected>Select your gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="others">Others</option>
                </select>
            </div>
            <div class="form-group">
                <label>Role</label>
                <select id="role" name="role" required>
                    <option value="" disabled selected hidden>Select Role</option>
                    <option value="Admin">Admin</option>
                    <option value="Member">Member</option>
                    <option value="Finance Officer">Finance Officer</option>
                    <option value="Choir Leader">Choir Leader</option>
                </select>
            </div>
        </form>

        <!-- FIXED FOOTER (Outside Scroll - Always Visible) -->
        <div class="modal-footer">
            <button type="button" class="cancel-btn" id="cancelBtn">Cancel</button>
            <!-- Note: the form="memberForm" attribute links this button to the form above -->
            <button type="submit" form="memberForm" id="save-btn" class="save-btn">Save Member</button>
        </div>
    </div>
</div>

<!-- CONFIRMATION DIALOG -->
<dialog class="dialog" id="confirmDialog">
    <p><i class="fa-solid fa-triangle-exclamation"></i> <span id="dialogMessage">Are you sure?</span></p>
    <div class="dialog-buttons">
        <button id="cancelDelete">Cancel</button>
        <button id="confirmDelete">Yes</button>
    </div>
</dialog>