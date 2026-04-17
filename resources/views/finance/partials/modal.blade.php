<!-- RECORD TRANSACTION POPUP MODAL -->
<div id="addRecordModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitle">Record Transaction</h2>
            <i class="fa-solid fa-xmark close-modal" id="closeModal"></i>
        </div>
        <form class="modal-form" id="financeForm">
            @csrf
            <input type="hidden" id="edit_tnx_id" name="Transaction_Id">

            <div class="form-group">
                <label>Description</label>
                <input type="text" name="description" id="description-input" placeholder="e.g. Weekly Tithes" required>
            </div>

            <div class="form-group">
                <label>Type</label>
                <select name="type" id="type-input" required>
                    <option value="" disabled selected hidden>Select Type</option>
                    <option value="Income">Income</option>
                    <option value="Expense">Expense</option>
                </select>
            </div>

            <div class="form-group">
                <label>Category</label>
                <select name="category_id" id="category-input" required>
                    <option value="" disabled selected hidden>Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->Category_Id }}">{{ $category->Category_Name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Amount</label>
                <div style="position: relative;">
                    <span class="peso-symbol" style="position: absolute; left: 10px; top: 10px; ">₱</span>
                    <input type="number" name="amount" id="amount-input" step="0.01" style="width:100%; padding-left: 30px;" placeholder="0.00" required>
                </div>
            </div>

            <div class="form-group">
                <label>Date</label>
                <input type="date" name="date" id="date-input" value="{{ date('Y-m-d') }}" required>
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status" id="status-input" required>
                    <option value="Completed">Completed</option>
                    <option value="Pending">Pending</option>
                </select>
            </div>

            <div class="modal-footer">
                <button type="button" class="cancel-btn" id="cancelBtn">Cancel</button>
                <button type="submit" class="record-btn" id="saveBtn">Record</button>
            </div>
        </form>
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