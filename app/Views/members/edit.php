<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <a href="<?= \Core\Helper::baseUrl('modules/members/directory'); ?>" class="btn btn-sm btn-outline-secondary rounded-pill mb-2">
            <i class="bi bi-arrow-left me-1"></i> Back to Directory
        </a>
        <h4 class="fw-bold mb-1 text-dark">Edit Society Member</h4>
        <p class="text-muted small mb-0">Update personal details and membership settings.</p>
    </div>
</div>

<form action="<?= \Core\Helper::baseUrl('modules/members/update'); ?>" method="POST" id="editForm">
    <?= \Core\CSRF::getFormField(); ?>
    <input type="hidden" name="id" value="<?= $member['id'] ?? '' ?>">
    <input type="hidden" name="party_id" value="<?= $member['party_id'] ?? '' ?>">

    <div class="row g-4">
        <!-- Main Form Column -->
        <div class="col-12 col-lg-12">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-person-badge text-success me-2"></i> 1. Personal Information</h6>
                </div>
                <div class="card-body pt-0">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Registration Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control fw-bold text-success" name="member_no" required value="<?= htmlspecialchars($member['member_no'] ?? '') ?>">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold small">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="full_name" required placeholder="Enter member full name" value="<?= htmlspecialchars($member['full_name'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">NIC / National ID <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nic" required placeholder="NIC number" value="<?= htmlspecialchars($member['nic'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Date of Birth <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="dob" required value="<?= htmlspecialchars($member['dob'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Gender <span class="text-danger">*</span></label>
                            <select class="form-select" name="gender" required>
                                <option value="Male" <?= ($member['gender'] ?? '') === 'Male' ? 'selected' : '' ?>>Male</option>
                                <option value="Female" <?= ($member['gender'] ?? '') === 'Female' ? 'selected' : '' ?>>Female</option>
                                <option value="Other" <?= ($member['gender'] ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Contact Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="phone" required placeholder="Phone number" value="<?= htmlspecialchars($member['phone'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">WhatsApp Number</label>
                            <input type="text" class="form-control" name="whatsapp" placeholder="WhatsApp number" value="<?= htmlspecialchars($member['whatsapp'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Email Address</label>
                            <input type="email" class="form-control" name="email" placeholder="Email address" value="<?= htmlspecialchars($member['email'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Occupation</label>
                            <input type="text" class="form-control" name="occupation" placeholder="Occupation" value="<?= htmlspecialchars($member['occupation'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Agricultural Sectors</label>
                            <div class="border rounded p-2 d-flex flex-wrap gap-1 align-items-center bg-white" id="sector_tags_container" style="min-height: 38px; cursor: text;">
                                <?php 
                                $current_sectors = isset($member['agricultural_sector']) && $member['agricultural_sector'] !== '' ? explode(',', $member['agricultural_sector']) : [];
                                foreach($current_sectors as $cs): 
                                    $cs = trim($cs);
                                    if(empty($cs)) continue;
                                ?>
                                <span class="badge bg-success d-flex align-items-center gap-1 sector-tag mb-1 mt-1">
                                    <?= htmlspecialchars($cs) ?>
                                    <button type="button" class="btn-close btn-close-white" style="font-size: 0.5em;" onclick="this.parentElement.remove()"></button>
                                    <input type="hidden" name="agricultural_sectors[]" value="<?= htmlspecialchars($cs) ?>">
                                </span>
                                <?php endforeach; ?>
                                <div class="dropdown" style="flex-grow: 1;">
                                    <input type="text" id="agri_sector_input" class="border-0 shadow-none p-0 m-0 w-100" placeholder="Type and press Enter or +" autocomplete="off" style="outline: none; background: transparent;">
                                    <ul class="dropdown-menu w-100 shadow-sm" id="sector_suggestions" style="max-height: 200px; overflow-y: auto; display: none; position: absolute;"></ul>
                                </div>
                                <button class="btn btn-sm btn-outline-success border-0" type="button" id="add_sector_btn" style="display:none;" title="Add new sector">
                                    <i class="bi bi-plus-lg"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Address <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="address" required placeholder="Home address" value="<?= htmlspecialchars($member['address'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">City <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="city" required placeholder="City name" value="<?= htmlspecialchars($member['city'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Status</label>
                            <select class="form-select" name="status">
                                <option value="ACTIVE" <?= ($member['status'] ?? '') === 'ACTIVE' ? 'selected' : '' ?>>ACTIVE</option>
                                <option value="INACTIVE" <?= ($member['status'] ?? '') === 'INACTIVE' ? 'selected' : '' ?>>INACTIVE</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Heir Information Section -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-people text-success me-2"></i> 2. Heir Information</h6>
                </div>
                <div class="card-body pt-0">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Heir Name</label>
                            <input type="text" class="form-control" name="heir_name" placeholder="Full name of the heir" value="<?= htmlspecialchars($member['heir_name'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Heir NIC</label>
                            <input type="text" class="form-control" name="heir_nic" placeholder="Heir's NIC number" value="<?= htmlspecialchars($member['heir_nic'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Heir Contact Number</label>
                            <input type="text" class="form-control" name="heir_contact_number" placeholder="Phone number" value="<?= htmlspecialchars($member['heir_contact_number'] ?? '') ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Heir Address</label>
                            <input type="text" class="form-control" name="heir_address" placeholder="Heir's address" value="<?= htmlspecialchars($member['heir_address'] ?? '') ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Society Information Section -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-shield-lock-fill text-success me-2"></i> 3. Society Settings</h6>
                </div>
                <div class="card-body pt-0">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Notes</label>
                            <textarea class="form-control" name="notes" rows="3" placeholder="Additional cooperative notes..."><?= htmlspecialchars($member['notes'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Panel -->
            <div class="card border-0 shadow-sm rounded-4 mt-4">
                <div class="card-body p-3 text-center">
                    <button type="submit" class="btn btn-primary rounded-pill w-100" style="background-color: #1b4332; border-color: #1b4332;">
                        <i class="bi bi-save me-1"></i> Update Member
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('sector_tags_container');
    if(!container) return;
    
    const input = document.getElementById('agri_sector_input');
    const addBtn = document.getElementById('add_sector_btn');
    const suggestionsBox = document.getElementById('sector_suggestions');
    
    const availableSectors = <?= json_encode(array_column($sectors ?? [], 'name')) ?>;
    
    function addTag(value) {
        value = value.trim();
        if(!value) return;
        
        const existing = Array.from(container.querySelectorAll('input[name="agricultural_sectors[]"]')).map(i => i.value.toLowerCase());
        if(existing.includes(value.toLowerCase())) {
            input.value = '';
            hideSuggestions();
            return;
        }

        const span = document.createElement('span');
        span.className = 'badge bg-success d-flex align-items-center gap-1 sector-tag mt-1 mb-1';
        span.innerHTML = `
            ${value}
            <button type="button" class="btn-close btn-close-white" style="font-size: 0.5em;" onclick="this.parentElement.remove()"></button>
            <input type="hidden" name="agricultural_sectors[]" value="${value}">
        `;
        
        container.insertBefore(span, container.querySelector('.dropdown'));
        input.value = '';
        hideSuggestions();
    }

    function hideSuggestions() {
        suggestionsBox.style.display = 'none';
        addBtn.style.display = 'none';
    }

    function showSuggestions(val) {
        val = val.trim().toLowerCase();
        suggestionsBox.innerHTML = '';
        
        if(!val) {
            hideSuggestions();
            return;
        }

        let hasExactMatch = false;
        let matchCount = 0;

        availableSectors.forEach(sector => {
            if(sector.toLowerCase().includes(val)) {
                if(sector.toLowerCase() === val) hasExactMatch = true;
                
                const li = document.createElement('li');
                const a = document.createElement('a');
                a.className = 'dropdown-item py-1 px-2';
                a.href = '#';
                a.textContent = sector;
                a.onclick = function(e) {
                    e.preventDefault();
                    addTag(sector);
                };
                li.appendChild(a);
                suggestionsBox.appendChild(li);
                matchCount++;
            }
        });

        suggestionsBox.style.display = matchCount > 0 ? 'block' : 'none';
        addBtn.style.display = !hasExactMatch ? 'block' : 'none';
    }

    input.addEventListener('input', function() {
        showSuggestions(this.value);
    });

    input.addEventListener('keydown', function(e) {
        if(e.key === 'Enter') {
            e.preventDefault();
            if(this.value.trim()) {
                addTag(this.value);
            }
        }
    });

    addBtn.addEventListener('click', function() {
        if(input.value.trim()) {
            addTag(input.value);
        }
    });

    container.addEventListener('click', function(e) {
        if(e.target === container) {
            input.focus();
        }
    });

    document.addEventListener('click', function(e) {
        if(!container.contains(e.target)) {
            hideSuggestions();
        }
    });
});
</script>
