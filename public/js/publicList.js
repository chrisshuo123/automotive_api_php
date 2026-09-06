import { getAllCars, BASEURL } from './script.js';

const carListEl = document.getElementById('carList');
const searchInput = document.getElementById('searchInput');
const searchBtn = document.getElementById('searchBtn');

let allCars = [];  // Populated once on load, then filtered in memory

function renderCarCard(car) {
    return `
        <div class="car-card">
            <h2>${car.nama_mobil}</h2>
            <img src="${BASEURL}/img/${car.nama_foto ?? 'default.jpg'}" style="width: 100%; max-width: 800px; height: auto;">
            <div class="car-details">
                <p><b>Brand: </b>${car.merek ?? 'Not Specified'}</p>
                <p><b>Jenis: </b>${car.jenis ?? 'Not Specified'}</p>
                <p><b>Horse Power: </b>${car.horse_power ?? 'N/A'} CC</p>
            </div>
        </div>
    `;
}

function renderTable(data) {
    if (!data || data.length === 0) {
        carListEl.innerHTML = '<div class="no-cars">No approved cars found.</div>';
        return;
    }
    carListEl.innerHTML = data.map(renderCarCard).join('');
}

function filterData() {
    const searchTerm = searchInput.value.toLowerCase().trim();

    if(searchTerm === '') {
        renderTable(allCars);
        return;
    }

    const filtered = allCars.filter((car) => {
        const nameLower = car.nama_mobil.toLowerCase();

        // Search filterring
        const matchesSearch = searchTerm === '' ||
                nameLower.includes(searchTerm);

        return matchesSearch;
    });

    console.log('sebelum sort, filtered[0]: ', filtered[0]?.nama_mobil);

    renderTable(filtered);
}

searchBtn.addEventListener('click', filterData);
searchInput.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
        e.preventDefault();
        filterData();
    }
});
searchInput.addEventListener('keyup', (e) => {
    if (e.key === 'Backspace' || e.key === 'Delete') {
        filterData();
    }
});

(async function init() {
    const rawCars = await getAllCars();
    allCars = rawCars.filter(car => car.status === 'Approved');  // <- simpan HASIL FILTER ke allCars

    renderTable(allCars);
})();