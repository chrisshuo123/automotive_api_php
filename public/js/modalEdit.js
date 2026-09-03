import { getAllBrands, getAllTypes, insertBrand, editBrand, deleteBrand } from './script.js';

$(function() {
    let brandList = [];
    let typeList = [];

    // Ambil data sekali di awal, simpan ke variabel lokal
    async function initModalData() {
        brandList = await getAllBrands();
        typeList = await getAllTypes();
    }
    initModalData();

    // Bungkus jadi fungsi yang return Promise, biar field.source() tetap konsisten
    function fetchBrands() {
        return Promise.resolve(brandList);
    }
    function fetchTypes() {
        return Promise.resolve(typeList);
    }

    // Config per modal type
    const modalConfigs = {
        addBrand: {
            title: "Add Brand",
            fields: [
                { id: "brandName", label: "Add Brand Name", type: "text", required: true }
            ],
            onSubmit: async (data) => {
                console.log("data yang diterima onSubmit: ", data);  // cek di sini dulu
                const result = await insertBrand(data.brandName);
                if(result.success) {
                    brandList.push({ value: result.data, label: data.brandName });
                    console.log("Added Brand: ", result);
                } else {
                    alert('Gagal menambah brand.');
                }
            }
        },
        addType: {
            title: "Add Type",
            fields: [
                { id: "typeName", label: "Add Car Type", type: "text", required: true }
            ],
            onSubmit: (data) => {
                const newId = typeList.length ? Math.max(...typeList.map(t => t.value)) + 1 : 1;
                typeList.push({ value: newId, label: data.typeName });
                console.log("Added Type: ", typeList[typeList.length - 1]);
            }
        },
        editBrand: {
            title: "Edit Brand",
            fields: [
                { id: "brandName", label: "Edit Car Brand", type: "select", required: true, source: fetchBrands },
                { id: "changeBrand", label: "Change Car Brand to", type: "text", required: true }
            ],
            onSubmit: async (data) => {
                const selectedId = Number(data.brandName);
                const newName = data.changeBrand;

                // const brand = brandList.find(b => b.value === selectedId);
                const result = await editBrand(selectedId, newName);

                if(result.success) {
                    const brand = brandList.find(b => b.value === selectedId);
                    if(brand) {
                        brand.label = newName;  // Update array-nya langsung
                        console.log("Updated Brand: ", brand);
                    }
                } else {
                    alert('Gagal mengubah brand.');
                }
            }
        },
        editType: {
            title: "Edit Type",
            fields: [
                { id: "typeName", label: "Edit Car Type", type: "select", required: true, source: fetchTypes },
                { id: "changeType", label: "Change Car Type to", type: "text", required: true }
            ],
            onSubmit: (data) => {
                const selectedId = Number(data.typeName);
                const newName = data.changeType;

                const type = typeList.find(t => t.value === selectedId);
                if(type) {
                    type.label = newName;
                    console.log("Updated Type: ", type);
                }
            }
        },
        deleteBrand: {
            title: "Delete Brand",
            fields: [
                { id: "brandName", label: "Delete Car Brand", type: "select", required: true, source: fetchBrands }
            ],
            onSubmit: async (data) => {
                const selectedId = Number(data.brandName);

                if(confirm('Are you sure you want to delete this brand?')) {
                    const result = await deleteBrand(selectedId);  // ini manggil fungsi import dari script.js
                    if(result.success) {
                        const index = brandList.findIndex(b => b.value === selectedId);
                        if(index !== -1) brandList.splice(index, 1); // sinkronkan state lokal
                        console.log("Deleted brand: ", selectedId);
                    } else {
                        alert('Gagal menghapus brand.');
                    }
                }
            }
        }
    }

    const $modalEl = document.getElementById("universalModal");
    // const bsModal = new bootstrap.Modal($modalEl);  // << Kalau pakai getbootstrap, nyalakan.
    function showModal($modalEl) {
        $modalEl.classList.add('show');
        $modalEl.style.display = 'block';
    }
    function hideModal($modalEl) {
        $modalEl.classList.remove('show');
        $modalEl.style.display = 'none';
    }

    // Build + Open a modal from config
    function openModal(configKey, data = {}) {
        const config = modalConfigs[configKey];
        const $fields = $("#modalFieldsContainer").empty();

        $("#modalTitle").text(config.title);

        config.fields.forEach(field => {
            let $input;

            if(field.type === "select") {
                const cleanLabel = field.label.replace('Edit', '');
                $input = $(`<select id="field_${field.id}" name="${field.id}" ${field.required ? "required" : ""}></select>`)
                .append(`<option value="">Select ${cleanLabel}</option>`);
                // populate options async if a source function is given
                if (field.source) {
                    console.log("field.source dipanggil untuk field: ", field.id);
                    field.source().then(options => {
                        console.log("options diterima: ", options);  // cek isi array options
                        console.log("options.length: ", options.length);  // cek kosong tidak
                        options.forEach(opt => {
                            console.log("opt: ", opt, "value: ", opt.value, "label: ", opt.label);  // Cek field opt benar tidak
                            $input.append(`<option value="${opt.value}">${opt.label}</option>`);
                        });
                        console.log("$input setelah diisi: ", $input[0]); // Cek elemen select-nya beneran keisi opsi tidak
                        if (data[field.id]) $input.val(data[field.id]);
                    }).catch(err => console.error("field.source() gagal: ", err));  // tangkap kalau promisenya reject.
                }
            } else if (field.type === 'hidden') {
                $input = $(`<input type="hidden" id="field_${field.id}" value="${data[field.id] ?? ""}">`);
            } else {
                $input = $(`
                    <input type="${field.type}" id="field_${field.id}"
                        ${field.required ? "required" : ""}
                        value="${data[field.id] ?? ""}">
                `);
            }

            const $wrapper = $(`<div style="margin-bottom: 1rem;"></div>`)
            if(field.label) $wrapper.append(`<label>${field.label}</label>`);
            $wrapper.append($input);
            $fields.append($wrapper);
        });

        // avoid stacking submit handlers on repeated opens
        // in JQuery, without this, when open the modal pop-up again may cause unexpected edit returns.
        $("#universalForm").off("submit").on("submit", async function (e) {    // Tambahkan async
            e.preventDefault();
            const result = {};
            config.fields.forEach(f => result[f.id] = $(`#field_${f.id}`).val());
            await config.onSubmit(result);   // Tanpa await, hideModal() bisa jalan duluan sebelum insertBrand() selesai fetch — modal keburu tertutup padahal request masih pending, dan kamu tidak akan lihat apakah insert-nya sukses atau gagal.
            // bsModal.hide();   // << Kalau pakai getbootstrap, nyalakan.
            hideModal($modalEl);

        });

        // bsModal.show();   // << Kalau pakai getbootstrap, nyalakan.
        showModal($modalEl);
    }

    // Wire up the triggers
    $("[data-modal]").on("click", function () {
        const key = $(this).data("modal");  // pulls all data-* attrs, e.g. data-id, data-nama.  Dalam case ini adalah data-modal.
        const data = $(this).data();
        openModal(key, data);
    });
});