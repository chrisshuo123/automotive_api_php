export function openEditModal() {
    document.getElementById('universalModal').style.display = 'flex';
}

export function closeEditModal() {
    document.getElementById('universalModal').style.display = 'none';
}

export function setupModalListeners() {
    // Close with X button
    document.querySelector('.close-modal').addEventListener('click', closeEditModal);
    
    // Close when clicking outside
    window.addEventListener('click', (e) => {
        const modal = document.getElementById('universalModal');
        if (e.target === modal) {
            closeEditModal();
        }
    });
}

// Call the setupModalListeners() method again to make the close pop-up modal functions:
setupModalListeners();