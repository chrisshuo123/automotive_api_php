import { getAllCars, BASEURL } from './script.js';

const carListEl = document.getElementById('carList');
const CAR_PER_PAGE = 5;

// Bikin ini agar pagination.js jadi murni generik
export function initPagination({ containerEl, itemsPerPage, renderItem }) {
    // Saat menggunakan Pagination sekaligus Filter:
    let currentPage = 1;
    let currentData = [];   // Will hold either all data (company) or filtered data (filteredData)

    function renderPage(page) {
        if(!currentData || currentData.length === 0) {
            containerEl.innerHTML = `<div class="no-cars">No approved cars found.</div>`;
            renderPagination();
            return;
        }

        currentPage = page;
        const start = (page - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const pageItems = currentData.slice(start, end);  // ganti ke currentData, sesuai dengan init disekitar line 1 ini.

        containerEl.innerHTML = pageItems.map(renderItem).join('');  // containerEl + renderItem
        renderPagination();
    }

    function renderPagination() {
        const totalPages = Math.ceil(currentData.length / itemsPerPage);
        let paginationEl = document.getElementById('pagination');

        if(!paginationEl) {
            paginationEl = document.createElement('div');
            paginationEl.id = 'pagination';
            paginationEl.style.cssText = 'display: flex; justify-content: center; align-items: center; gap: 8px; margin-top: 20px;';
            containerEl.insertAdjacentElement('afterend', paginationEl);
        }

        // If no data or only 1 page, hide pagination
        if(totalPages <= 1) {
            paginationEl.style.display = 'none';
            return;
        } else {
            paginationEl.style.display = 'flex';
        }

        // Generate page numbers
        let pageNumbersHTML = '';
        
        let startPage = Math.max(1, currentPage - 3);
        let endPage = Math.min(totalPages, currentPage + 3);

        // Adjust if near the end
        if (currentPage > totalPages - 4) {
            startPage = Math.max(1, totalPages - 6);
        }

        // Always show first page
        if (startPage > 1) {
            pageNumbersHTML += `<button class="page-btn" data-page="1">1</button>`;
            if(startPage > 2) {
                pageNumbersHTML += `<span class="ellipsis">...</span>`;
            }
        }

        // Show middle pages
        for(let i = startPage; i <= endPage; i++) {
            const isActive = i === currentPage ? 'active' : '';
            pageNumbersHTML += `<button class="page-btn ${isActive}" data-page="${i}">${i}</button>`;
        }

        // Always show last page
        if (endPage < totalPages) {
            if(endPage < totalPages - 1) {
                pageNumbersHTML += `<span class="ellipsis">...</span>`;
            }
            pageNumbersHTML += `<button class="page-btn" data-page="${totalPages}">${totalPages}</button>`;
        }

        paginationEl.innerHTML = `
            <button id="prevBtn" ${currentPage === 1 ? 'disabled' : ''}>Previous</button>
            ${pageNumbersHTML}
            <button id="nextBtn" ${currentPage === totalPages ? 'disabled' : ''}>Next</button>
        `;

        // Page number click handlers
        paginationEl.querySelectorAll('.page-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const page = parseInt(btn.dataset.page);
                if(page !== currentPage) renderPage(page);
            });
        });

        // Handle Previous and Next buttons with proper cloning to avoid duplicates

        // Previous button handler
        document.getElementById('prevBtn')?.addEventListener('click', () => {
            if (currentPage > 1) renderPage(currentPage - 1);
        });
        // Next button handler
        document.getElementById('nextBtn')?.addEventListener('click', () => {
            if (currentPage < totalPages) renderPage(currentPage + 1);
        });
    }

    function setData(data) {
        currentData = data || [];
        currentPage = 1;
        renderPage(1);
    }

    return { setData };
}

// Tidak dipakai, karena sudah ada innerHTML pada publicList.js sehingga pagination.js ini sifatnya lebih kearah Generik.
// function renderCarCard(carItem) {
//     return `
//         <div class="car-card">
//             <h2>${carItem.nama_mobil}</h2>
//             <img src="${BASEURL}/img/${carItem.nama_foto ?? 'default.jpg'}" style="width: 100%; max-width: 800px; height: auto;">
//             <div class="car-details">
//                 <p><b>Brand: </b>${carItem.merek ?? 'Not Specified'}</p>
//                 <p><b>Jenis: </b>${carItem.jenis ?? 'Not Specified'}</p>
//                 <p><b>Horse Power: </b>${carItem.horse_power ?? 'N/A'} CC</p>
//             </div>
//         </div>
//     `;
// }

// This time when using Pagination + Filter features:
// Export for use in other files
// export { currentData, renderPage };