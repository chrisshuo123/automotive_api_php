<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <meta name="twitter:widgets:autoload" content="off">
    <link rel="stylesheet" href="../public/css/style-crud.css">
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <title>Car CRUD Editor</title>
</head>
<body>
    <div class="container">
        <h1>Add new car</h1>
        <form id="insertCarForm" action="<?= BASEURL ?>/crud/insertCar" method="POST">
            <div class="form-group">
                <label for="nama_mobil">Car Name: </label>
                <input type="text" id="nama_mobil" name="nama_mobil" required>
            </div>

            <div class="form-group">
                <label for="merek">Brand: </label>
                <select id="merek" name="idMerek_fk" required>
                    <option value="">Select a brand</option>
                    <?php
                    // Ambil data dari database tabel 'merek'
                    $merekList = $data['merekList'];
                    foreach($merekList as $merek): ?>
                        <option value="<?= $merek['idmerek'] ?>"><?= $merek['namamerek'] ?></option>
                    <?php endforeach; ?>
                    <!-- <option value="1">Toyota</option> -->
                    <!-- Options will be populated by JavaScript -->
                </select>
            </div>

            <div class="form-group">
                <label for="jenis">Type: </label>
                <select id="jenis" name="idJenis_fk" required>
                    <option value="">Select a type</option>
                    <?php
                    // Ambil data dari database tabel 'jenis'
                    $jenisList = $data['jenisList'];
                    foreach($jenisList as $jenis): ?>
                        <option value="<?= $jenis['idjenis'] ?>"><?= $jenis['namajenis'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="horse_power">Horse Power: </label>
                <input type="number" id="horse_power" name="horse_power" required>
            </div>

            <div class="form-group">
                <label for="image">Image: </label>
                <input type="file" id="image_car" name="image" accept="image/*" required>
            </div>

            <!-- <div class="form-group">
                <label for="status">Status: </label>
                <select id="status" name="status" required>
                    <option value="">Select a status</option>
                    <!-- Options will be populated by Javascript -->
                <!-- </select>
            </div> -->

            <button type="submit">Submit</button>
        </form>
        <button type="button" class="second-button" style="margin-top: 10px;" data-modal="addBrand">Add Brand</button>
        <button type="button" class="second-button" data-modal="addType">Add Type</button>
        <button type="button" class="second-button" data-modal="editBrand">Edit Brand</button>
        <button type="button" class="second-button" data-modal="editType">Edit Type</button>
        <button type="button" class="second-button" data-modal="deleteBrand">Delete Brand</button>
        <button type="button" class="second-button" data-modal="deleteType">Delete Type</button>

        <div id="message" class="message"></div>

        <h2>Car List</h2>
        <!-- Search and Filter Section -->
        <div class="search-filter-container">
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Search by car title...">
                <button>Search</button>
            </div>

            <div class="filter-box" style="margin-bottom: 75px;">
                <label for="statusFilter">Status:</label>
                <!-- <select id="statusFilter" onchange="filterCars()"> -->
                <select id="statusFilter">
                    <option value="">All Status</option> <!-- '' and 'all' are treated as "show all" -->
                    <option value="approved">Approved</option>
                    <option value="need preview">Need Preview</option>
                </select>
            </div>

            <div>
                <label for="alphabetSort">Sort</label>
                <select id="alphabetSort">
                    <option value="">Default</option>
                    <option value="ascending">A-Z sort</option>
                    <option value="descending">Z-A sort</option>
                </select>
            </div>

            <div>
                <button id="clearBtn" title="Clear All Filters" class="clearBtn">
                X
                </button>
            </div>
        </div>

        <div id="carList">
            <?php if(isset($data['carList']) && !empty($data['carList'])): ?>
                <?php foreach($data['carList'] as $carList): ?>
                <div class="car-item">
                    <div class="flex-crud">
                        <div style="width: 200px;">
                            <h3><?= $carList['nama_mobil']; ?></h3>
                            <p><b>Brand: </b> <?= $carList['merek'] ?? 'Not Specified' ?></p>
                            <!-- 'merek' didapat dari Home_model::getAllCars() saat left join -->
                            <p><b>Type: </b> <?= $carList['jenis'] ?? 'Not Specified' ?></p>
                            <!-- 'jenis' didapat dari Home_model::getAllCars() saat left join -->
                            <p><b>Horse Power: </b> <?= $carList['horse_power'] ?? 'N/A' ?></p>
                            <!-- <p><b>Horse Power: </b> ${car.horse_power ?? 'N/A'}</p> -->
                            <p><b>Status: </b><br>
                                <span style="background-color: <?= ($carList['status'] ?? '') == 'Approved' ? 'green' : 'yellow' ?> ;
                                color: <?= ($carList['status'] ?? '') == 'Approved' ? 'white' : 'black' ?>;
                                padding: 5px; border-radius: 5px;">
                                    <?= $carList['status'] ?? 'Not Specified' ?>
                                </span>
                            </p>
                            <!-- <p><b>Status: </b><br><span style="background-color:${statusColor}; color: ${statusColor === 'yellow' ? 'black' : 'white'}; padding: 5px; border-radius: 5px;">${statusDisplay}</span></p> -->
                            <!-- This is for Edit Button in Panel Update Menu -->
                            <div class="flex-crud-button">
                                <button class="edit-btn" data-id="<?= $carList['idcars'] ?>" onClick="editCar(<?= $carList['idcars'] ?>)">Edit</button>
                                <button class="delete-btn" data-id="<?= $carList['idcars'] ?>" onClick="deleteCar(<?= $carList['idcars'] ?>)">Delete</button>
                            </div>
                        </div>
                        <div>
                            <!-- <img src="../public/img/yaris.jpg" style="width: 75%; max-width: 800px; height: auto; margin-left: 150px;"> -->
                            <img src="../public/img/<?= $carList['imageCar'] ?? 'default.jpg' ?>" style="width: 75%; max-width: 800px; height: auto; margin-left: 150px;">
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No cars found.</p>
            <?php endif; ?>
        </div>
        <!-- <div id="noResults" style="display:none;">
            <div>No Results Found</div>
        </div> -->
    </div>

    <div id="universalModal" class="modal" style="display:none">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h2 id="modalTitle"></h2>
            <form id="universalForm">
                <div id="modalFieldsContainer"></div>
                <!-- <p>Add another car brand.  Here's are the list of car brands already registered in the system:</p> -->
                <!-- foreach loopings -->
                <!--<input type="hidden" id="edit_id">
                <label>Car Name:</label>
                <input type="text" id="edit_nama_mobil" required>-->
                <button type="submit">Save</button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> -->
    <script type="module" src="http://localhost/automotive_api_php/public/js/script.js"></script>
    <script type="module" src="http://localhost/automotive_api_php/public/js/filter.js"></script>
    <script type="module" src="http://localhost/automotive_api_php/public/js/modalEdit.js"></script>
    <script type="module" src="http://localhost/automotive_api_php/public/js/modal.js"></script>
</body>
</html>