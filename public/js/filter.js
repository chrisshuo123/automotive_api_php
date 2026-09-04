import { getAllCars, deleteCar } from './script.js';

const carListEl = document.getElementById('carList');
const searchInput = document.getElementById('searchInput');
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
                            data-idcars="<?= $carList['idcars'] ?>"
                            data-carname="<?= $carList['nama_mobil'] ?>"
                            data-carbrand="<?= $carList['idmerek_fk'] ?>"
                            data-cartype="<?= $carList['idjenis_fk'] ?>"
                            data-carhorsepower="<?= $carList['horse_power'] ?>"
                            data-carstatus="<?= $carList['idstatus_fk'] ?>">
                            Edit
                        </button>
                        <button class="delete-btn" data-id="${car.idcars}">Delete</button>
                    </div>
                </div>
                <div>
                    <img src="../public/img/${car.imageCar ?? 'default.jpg'}"
                        style="width: 75%; max-width: 800px; height: auto; margin-left: 150px;">
                </div>
            </div>
        </div>
    `;
    // NOTE: The 'Edit Car' Button now adjusts according to the car-crud.php's edit car button, by adding more data-id, called from modalEdit.js on editCar modalConfigs var.
}

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
    const sortValue = sortSelect.value;

    console.log('sortValue: ', sortValue);

    if(searchTerm === '' && (statusValue === '' || statusValue === 'all') && (sortValue === '')) {
        renderTable(allCars);
        return;
    }

    const filtered = allCars.filter((car) => {
        const nameLower = car.nama_mobil.toLowerCase();
        const statusLower = (car.status ?? '').toLowerCase();

        // Search Filterring
        const matchesSearch = searchTerm === '' ||
            nameLower.includes(searchTerm) ||
            statusLower.includes(searchTerm);

        // Status Filterring
        const matchesStatus = statusValue === '' || statusValue === 'all' ||
            statusLower === statusValue.toLowerCase();

        return matchesSearch && matchesStatus;
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

    renderTable(filtered);
}

// Event Delegation for edit / delete, since rows are re-rendered dynamically
carListEl.addEventListener('click', (e) => {
    // const editBtn = e.target.closest('.edit-btn');  // editBtn sepenuhnya di script.js & modalEdit.js
    const delBtn = e.target.closest('.delete-btn');
    // if (editBtn) editCar(editBtn.dataset.id);
    if (delBtn) deleteCar(delBtn.dataset.id);
});

searchInput.addEventListener('input', filterData);
statusFilter.addEventListener('change', filterData);
sortSelect.addEventListener('change', filterData);

clearBtn.addEventListener('click', () => {
    searchInput.value = '';
    statusFilter.value = '';
    sortSelect.value = '';
    
    // Initial Render
    renderTable(allCars);
});

// Load once - PHP already rendered the first view, this just primes
// 'allCars' in memory so filtering doesn't need a new fetch every time
(async function init() {
    allCars = await getAllCars();
    console.log('allCars[0]: ', allCars[0]); // cek bentuknya di sini dulu
})();