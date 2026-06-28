/* ============================================================
   GREEN HAVEN – Admin JavaScript
   ============================================================ */

document.addEventListener('DOMContentLoaded', () => {
    
    // --- Mobile Sidebar Toggle ---
    const sidebar = document.getElementById('adminSidebar');
    const toggle = document.getElementById('sidebarToggle');
    
    if (toggle && sidebar) {
        toggle.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });
    }

    // --- Dynamic Search Filtering (Client-side simulation) ---
    const searchInputs = document.querySelectorAll('.admin-search-input');
    
    searchInputs.forEach(input => {
        input.addEventListener('keyup', function(e) {
            const query = e.target.value.toLowerCase();
            // Find nearest table
            const table = this.closest('.admin-content').querySelector('.data-table');
            if (table) {
                const rows = table.querySelectorAll('tbody tr');
                rows.forEach(row => {
                    const text = row.innerText.toLowerCase();
                    row.style.display = text.includes(query) ? '' : 'none';
                });
            }
        });
    });

    // --- Close Modals on Escape ---
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const openModals = document.querySelectorAll('.admin-modal-overlay.open');
            openModals.forEach(modal => {
                modal.classList.remove('open');
            });
        }
    });

    // --- Auto-generate Slug from Name (Products/Categories) ---
    const nameInput = document.querySelector('input[name="name"]');
    const slugInput = document.querySelector('input[name="slug"]');
    
    if (nameInput && slugInput && !slugInput.value) {
        nameInput.addEventListener('keyup', function() {
            slugInput.value = this.value
                .toLowerCase()
                .replace(/[^\w\s-]/g, '') // Remove non-word chars
                .replace(/[\s_-]+/g, '-') // Swap spaces for hyphens
                .replace(/^-+|-+$/g, ''); // Trim hyphens
        });
    }

    // --- Chart.js Global Defaults ---
    if (typeof Chart !== 'undefined') {
        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.color = '#6b7280';
        Chart.defaults.scale.grid.color = '#f3f4f6';
    }
});
