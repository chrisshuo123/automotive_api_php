<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../public/css/style-crud.css">
    <title>Insert Car</title>
</head>
<body>
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

        <button type="submit">Submit</button>
    </form>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> -->
    <script type="module" src="http://localhost/automotive_api_php/public/js/script.js"></script>
    <script type="module" src="http://localhost/automotive_api_php/public/js/filter.js"></script>
    <script type="module" src="http://localhost/automotive_api_php/public/js/modalEdit.js"></script>
    <script type="module" src="http://localhost/automotive_api_php/public/js/modal.js"></script>
</body>
</html>