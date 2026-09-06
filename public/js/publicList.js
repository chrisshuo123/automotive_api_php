import { getAllCars, BASEURL } from './script.js';

const carListEl = document.getElementById('carList');

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

(async function init() {
    const allCars = await getAllCars();
    const approvedCars = allCars.filter(car => car.status === 'Approved');

    if(approvedCars.length === 0) {
        carListEl.innerHTML = `<div class="no-cars">No approved cars found.</div>`;
        return;
    }

    carListEl.innerHTML = approvedCars.map(renderCarCard).join('');
})();