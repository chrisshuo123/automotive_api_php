import { BASEURL, getAllCars, deleteCar, getAllBrands, getAllTypes } from './script.js';
import { initPagination } from './pagination.js';

const carListEl = document.getElementById('carList');
const searchInput = document.getElementById('searchInput');
const brandFilter = document.getElementById('brandFilter');
const typeFilter = document.getElementById('typeFilter');
const statusFilter = document.getElementById('statusFilter');
const sortSelect = document.getElementById('alphabetSort');
const clearBtn = document.getElementById("clearBtn");

let allCars = [];  // Populated once on load, then filtered in memory

function renderCarItem(car) {
    const status = car.status ?? 'Not Specified';
    const bgColor = status === 'Approved' ? 'green' : 'yellow';
    const textColor = status === 'Approved' ? 'white' : 'black';

    return `
        <div class="car-item">
            <div class="flex-crud">
                <div style="width: 200px;">
                    <h3>${car.nama_mobil}</h3>
                    <p><b>Brand: </b>${car.merek ?? 'Not Specified'}</p>
                    <p><b>Type: </b>${car.jenis ?? 'Not Specified'}</p>
                    <p><b>Horse Power: </b>${car.horse_power ?? 'N/A'}</p>
                    <p><b>Status: </b><br>
                        <span style="background-color: ${bgColor}; color: ${textColor}; padding: 5px; border-radius: 5px;">
                            ${status}
                        </span>
                    </p>
                    <div class="flex-crud-button">
                        <button class="edit-btn" data-modal="editCar"
                            data-idcars="${car.idcars}"
                            data-carname="${car.nama_mobil}"
                            data-carbrand="${car.idmerek_fk}"
                            data-cartype="${car.idjenis_fk}"
                            data-carhorsepower="${car.horse_power}"
                            data-carstatus="${car.idstatus_fk}">
                            Edit
                        </button>
                        <button class="delete-btn" data-id="${car.idcars}">Delete</button>
                    </div>
                </div>
                <div>
                    <img src="${BASEURL}/img/${car.nama_foto ?? 'default.jpg'}"
                        style="width: 75%; max-width: 800px; height: auto; margin-left: 150px;">
                </div>
            </div>
        </div>
    `;
    // NOTE: The 'Edit Car' Button now adjusts according to the car-crud.php's edit car button, by adding more data-id, called from modalEdit.js on editCar modalConfigs var.
}

const pagination = initPagination({
    containerEl: carListEl,
    itemsPerPage: 5,
    renderItem: renderCarItem
});

function renderTable(data) {
    if (!data || data.length === 0) {
        carListEl.innerHTML = '<p>No cars found.</p>';
        return;
    }
    carListEl.innerHTML = data.map(renderCarItem).join('');
}

function filterData() {
    const searchTerm = searchInput.value.toLowerCase().trim();
    const statusValue = statusFilter.value;
    const brandValue = brandFilter.value;
    const typeValue = typeFilter.value;
    const sortValue = sortSelect.value;

    console.log('sortValue: ', sortValue);

    if(searchTerm === '' && (statusValue === '' || statusValue === 'all')
        && (brandValue === '' || brandValue === 'all')
        && (typeValue === '' || typeValue === 'all')
        && (sortValue === '')) {
        // renderTable(allCars);
        pagination.setData(allCars); // <-- Ganti dari renderTable(allCars)
        return;
    }

    const filtered = allCars.filter((car) => {
        const nameLower = car.nama_mobil.toLowerCase();
        const brandLower = car.merek.toLowerCase();
        const typeLower = car.jenis.toLowerCase();
        const statusLower = (car.status ?? '').toLowerCase();

        // Search Filterring
        const matchesSearch = searchTerm === '' ||
            nameLower.includes(searchTerm) ||
            brandLower.includes(searchTerm) ||
            typeLower.includes(searchTerm) ||
            statusLower.includes(searchTerm);

        // Brand Filterring
        const matchesBrand = brandValue === '' || brandValue === 'all' ||
            brandLower === brandValue.toLowerCase();

        // type Filterring
        const matchesType = typeValue === '' || typeValue === 'all' ||
            typeLower === typeValue.toLowerCase();

        // Status Filterring
        const matchesStatus = statusValue === '' || statusValue === 'all' ||
            statusLower === statusValue.toLowerCase();

        return matchesSearch && matchesBrand && matchesType && matchesStatus;
    });

    console.log('sebelum sort, filtered[0]: ', filtered[0]?.nama_mobil);

    // Sort A-Z / Z-A
    if(sortValue === 'ascending') {
        console.log('menjalankan sort ascending');
        filtered.sort((a,b) => a.nama_mobil.localeCompare(b.nama_mobil));
    } else if(sortValue === 'descending') {
        console.log('menjalankan sort descending');
        filtered.sort((a,b) => b.nama_mobil.localeCompare(a.nama_mobil));
    }

    console.log('setelah sort, filtered[0]: ', filtered[0]?.nama_mobil);

    // renderTable(filtered);
    pagination.setData(filtered);  // Ganti dari renderTable(filtered)
}

async function populateBrandFilter() {
    const brand = await getAllBrands();
    brandFilter.innerHTML = `<option value="">all</option>` +
        brand.map(b => `<option value="${b.label}">${b.label}</option>`).join('');
}

async function populateTypeFilter() {
    const type = await getAllTypes();
    typeFilter.innerHTML = `<option value="">all</option>` +
        type.map(t => `<option value="${t.label}">${t.label}</option>`).join('');
}

// Event Delegation for edit / delete, since rows are re-rendered dynamically
carListEl.addEventListener('click', (e) => {
    // const editBtn = e.target.closest('.edit-btn');  // editBtn sepenuhnya di script.js & modalEdit.js
    const delBtn = e.target.closest('.delete-btn');
    // if (editBtn) editCar(editBtn.dataset.id);
    if (delBtn) deleteCar(delBtn.dataset.id);
});

searchInput.addEventListener('input', filterData);
brandFilter.addEventListener('change', filterData);
typeFilter.addEventListener('change', filterData);
statusFilter.addEventListener('change', filterData);
sortSelect.addEventListener('change', filterData);

clearBtn.addEventListener('click', () => {
    searchInput.value = '';
    brandFilter.value = '';
    typeFilter.value = '';
    statusFilter.value = '';
    sortSelect.value = '';
    
    // Initial Render
    // renderTable(allCars);
    pagination.setData(allCars);  // <-- Ganti dari renderTable(allCars)
});

// Load once - PHP already rendered the first view, this just primes
// 'allCars' in memory so filtering doesn't need a new fetch every time
(async function init() {
    allCars = await getAllCars();

    await populateBrandFilter();
    await populateTypeFilter();
    // renderTable(allCars);
    pagination.setData(allCars);  // <-- Ganti dari renderTable(allCars)
    console.log('allCars[0]: ', allCars[0]); // cek bentuknya di sini dulu
})();