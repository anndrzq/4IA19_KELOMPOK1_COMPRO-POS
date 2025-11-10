document.addEventListener('DOMContentLoaded', function () {
    updateProductOptions();
    initSelect2();
    calculateAll();
    updateActionButtons();

    const tableBody = document.querySelector('#productTable tbody');

    function formatRupiah(angka) {
        angka = angka.toString().replace(/[^\d]/g, '');
        if (!angka || parseInt(angka) === 0) {
            return 'Rp 0,-';
        }
        return 'Rp ' + angka.replace(/\B(?=(\d{3})+(?!\d))/g, ".") + ',-';
    }

    function parseRupiah(rp) {
        return parseInt(rp.replace(/[^\d]/g, '')) || 0;
    }

    function updateProductOptions() {
        let selectedValues = [];
        $('.selectProduct').each(function () {
            let val = $(this).val();
            if (val) {
                selectedValues.push(val);
            }
        });

        $('.selectProduct').each(function () {
            let currentSelectedValue = $(this).val();
            $(this).find('option').each(function () {
                let optionValue = $(this).val();
                if (!optionValue) return;

                let stock = parseInt($(this).data('stock')) || 0;
                let optionText = $(this).data('name') || $(this).text().split(' - Habis')[
                    0];
                $(this).text(optionText);

                if (stock <= 0 && optionValue !== currentSelectedValue) {
                    $(this).prop('disabled', true);
                    $(this).text(optionText + ' - Habis');
                } else {
                    let isSelectedElsewhere = selectedValues.includes(optionValue);
                    let isSelectedHere = (optionValue === currentSelectedValue);
                    $(this).prop('disabled', isSelectedElsewhere && !isSelectedHere &&
                        stock > 0);
                }
            });
        });
    }


    function reinitAllSelect2() {
        $('.selectProduct.select2-hidden-accessible').each(function () {
            try {
                $(this).select2('destroy');
            } catch (e) {
                console.error("Error destroying select2:", e);
            }
        });
        updateProductOptions();
        initSelect2();
    }


    function initSelect2() {
        $('#customerType, select[name="payment_method"], select[name="member_id"], #paymentDetail').each(
            function () {
                if (!$(this).hasClass('select2-hidden-accessible')) {
                    $(this).select2({
                        width: '100%',
                        placeholder: 'Pilih Opsi',
                        allowClear: true,
                    });
                }
            });
        $('.selectProduct').each(function () {
            if (!$(this).hasClass('select2-hidden-accessible')) {
                $(this).select2({
                    width: '100%',
                    placeholder: 'Pilih Produk',
                    allowClear: true,
                    templateResult: function (option) {
                        return option.text;
                    },
                    templateSelection: function (option) {
                        return option.text.split(' - Habis')[0];
                    }
                });
            }
        });
    }

    $(document).on('change', 'select[name="payment_method"]', function () {
        const paymentMethod = $(this).val();
        const detailRow = $('#paymentDetailRow');
        const taxRow = $('#taxRow');
        const detailSelect = $('#paymentDetail');
        const payInput = $('#pay');

        if (detailSelect.hasClass('select2-hidden-accessible')) {
            try {
                detailSelect.select2('destroy');
            } catch (e) { }
        }

        let options = '<option></option>';

        if (paymentMethod === 'debit') {
            options += '<option value="debit_bca">Debit BCA (0.25% Biaya Admin)</option>';
            options += '<option value="debit_lain">Debit Bank Lain (1% Biaya Admin)</option>';
            detailRow.removeClass('d-none');
            taxRow.removeClass('d-none');
            payInput.prop('readonly', true);
        } else if (paymentMethod === 'credit') {
            options += '<option value="credit_bca">Credit BCA (1% Biaya Admin)</option>';
            options += '<option value="credit_lain">Credit Bank Lain (2.5% Biaya Admin)</option>';
            detailRow.removeClass('d-none');
            taxRow.removeClass('d-none');
            payInput.prop('readonly', true);
        } else {
            detailRow.addClass('d-none');
            taxRow.addClass('d-none');
            payInput.prop('readonly', false);
            if (paymentMethod) {
                payInput.val(formatRupiah(0));
            } else if (!paymentMethod) {
                payInput.val(formatRupiah(0));
            }
        }

        detailSelect.html(options);

        detailSelect.select2({
            width: '100%',
            placeholder: 'Pilih Detail',
            allowClear: true,
        });

        if (detailSelect.find('option').length === 2) {
            detailSelect.val(detailSelect.find('option:last').val());
        } else {
            detailSelect.val(null);
        }

        detailSelect.trigger('change');
        calculateAll();
    });

    $(document).on('change', '#paymentDetail', function () {
        calculateAll();
    });

    function updateActionButtons() {
        const rows = $('#productTable tbody tr');
        rows.each(function (index) {
            let actionCell = $(this).find('td').last();
            actionCell.html('');

            if (index === rows.length - 1) {
                actionCell.html(
                    '<button type="button" class="btn btn-success btn-sm addRow">+</button>');
            } else {
                actionCell.html(
                    '<button type="button" class="btn btn-danger btn-sm removeRow">-</button>');
            }
        });
    }

    function addRow() {
        let firstRowSelect = $(tableBody.rows[0]).find('.selectProduct');
        let firstRowWasSelect2 = firstRowSelect.hasClass('select2-hidden-accessible');

        if (firstRowWasSelect2) {
            try {
                firstRowSelect.select2('destroy');
            } catch (e) { }
        }

        let newRow = $(tableBody.rows[0]).clone();

        if (firstRowWasSelect2) {
            $(tableBody.rows[0]).find('.selectProduct').select2({
                width: '100%',
                placeholder: 'Pilih Produk',
                allowClear: true,
                templateResult: function (option) {
                    return option.text;
                },
                templateSelection: function (option) {
                    return option.text.split(' - Habis')[0];
                }
            });
        }


        newRow.find('.select2-container').remove();
        let select = newRow.find('.selectProduct');
        select.removeClass('select2-hidden-accessible').removeAttr('data-select2-id tabindex aria-hidden');
        select.find('option').removeAttr('data-select2-id');
        select.val('');

        newRow.find('input').val('');
        let qtyInput = newRow.find('.qty');
        qtyInput.val(1).removeAttr('max').prop('disabled', false);
        newRow.find('.discount').val(0);
        newRow.find('.product-image').attr('src', '');
        newRow.find('.price').val('Rp 0,-').data('raw', 0);
        newRow.find('.subtotal').val('Rp 0,-').data('raw', 0);

        $('#productTable tbody').append(newRow);

        reinitAllSelect2();
        updateActionButtons();
    }


    $(document).on('click', '.removeRow', function () {
        if ($('#productTable tbody tr').length > 1) {
            $(this).closest('tr').remove();
            calculateAll();
            reinitAllSelect2();
            updateActionButtons();
        } else {
            Swal.fire('Info', 'Minimal harus ada satu baris produk.', 'info');
        }
    });


    function calculateRow(row) {
        const price = $(row).find('.price').data('raw') || 0;
        let qty = parseInt($(row).find('.qty').val()) || 0;
        const stock = parseInt($(row).find('.qty').attr('max')) || 0;

        if (qty > stock && stock >= 0) {
            qty = stock;
            $(row).find('.qty').val(stock);
            if (stock > 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Stok Tidak Cukup',
                    text: 'Jumlah melebihi stok yang tersedia (' + stock +
                        '). Otomatis diatur ke ' + stock + '.',
                    timer: 2500,
                    showConfirmButton: false
                });
            }
        }
        if (qty < 1 && $(row).find('.selectProduct').val() && stock > 0) {
            qty = 1;
            $(row).find('.qty').val(1);
        } else if (stock === 0 && $(row).find('.selectProduct').val()) {
            qty = 0;
            $(row).find('.qty').val(0);
        }


        let discountInput = parseInt($(row).find('.discount').val()) || 0;
        let discountValue = discountInput <= 100 ? (price * qty * discountInput / 100) : discountInput;
        let subtotal = (price * qty) - discountValue;
        if (subtotal < 0) subtotal = 0;

        $(row).find('.subtotal').val(formatRupiah(subtotal));
        $(row).find('.subtotal').data('raw', subtotal);
        calculateAll();
    }

    function calculateAll() {
        let subtotal = 0;
        $('.subtotal').each(function () {
            subtotal += $(this).data('raw') || 0;
        });
        $('#total').val(formatRupiah(subtotal)).data('raw', subtotal);

        let taxPercent = 0;
        const paymentMethod = $('select[name="payment_method"]').val();
        const paymentDetail = $('#paymentDetail').val();

        if (paymentMethod === 'debit' || paymentMethod === 'credit') {
            switch (paymentDetail) {
                case 'debit_bca':
                    taxPercent = 0.25;
                    break;
                case 'debit_lain':
                    taxPercent = 1;
                    break;
                case 'credit_bca':
                    taxPercent = 1;
                    break;
                case 'credit_lain':
                    taxPercent = 2.5;
                    break;
            }
        }

        let taxAmount = (subtotal * taxPercent) / 100;
        $('#taxAmount').val(formatRupiah(taxAmount)).data('raw', taxAmount);

        let grandTotal = subtotal - taxAmount;
        if (grandTotal < 0) {
            grandTotal = 0;
        }
        $('#grandTotal').val(formatRupiah(grandTotal)).data('raw', grandTotal);

        if (paymentMethod === 'debit' || paymentMethod === 'credit') {
            $('#pay').val(formatRupiah(subtotal));
        }

        let payInputVal = $('#pay').val();
        let pay = parseRupiah(payInputVal);
        let change = pay - subtotal;
        $('#change').val(formatRupiah(change < 0 ? 0 : change));
    }


    $(document).on('select2:select', '.selectProduct', function () {
        let row = $(this).closest('tr');
        let selected = $(this).find('option:selected');
        let price = parseInt(selected.data('price')) || 0;
        let stock = parseInt(selected.data('stock'));

        if (isNaN(stock)) stock = 0;

        row.find('.product-image').attr('src', selected.data('image') || '');
        row.find('.price').val(formatRupiah(price)).data('raw', price);

        let qtyInput = row.find('.qty');
        qtyInput.attr('max', stock);

        if (stock <= 0) {
            qtyInput.val(0);
            qtyInput.prop('disabled', true);
            Swal.fire('Stok Habis', 'Stok produk ini sudah habis.', 'warning');
        } else {
            let currentQty = parseInt(qtyInput.val()) || 1;
            if (currentQty > stock) {
                qtyInput.val(stock);
            } else if (currentQty < 1) {
                qtyInput.val(1);
            }
            qtyInput.prop('disabled', false);
        }

        calculateRow(row);
        reinitAllSelect2();
    });


    $(document).on('select2:unselect', '.selectProduct', function () {
        let row = $(this).closest('tr');
        row.find('.product-image').attr('src', '');
        row.find('.price').val(formatRupiah(0)).data('raw', 0);
        let qtyInput = row.find('.qty');
        qtyInput.val(1);
        qtyInput.removeAttr('max');
        qtyInput.prop('disabled', false);
        row.find('.discount').val(0);

        calculateRow(row);
        reinitAllSelect2();
    });

    $(document).on('input', '.qty', function () {
        let row = $(this).closest('tr');
        calculateRow(row);
    });


    $(document).on('input', '.discount', function () {
        calculateRow($(this).closest('tr'));
    });


    $(document).on('click', '.addRow', function () {
        addRow();
    });

    $('#pay').on('input', function () {
        let digits = $(this).val().replace(/[^\d]/g, '');
        $(this).val(digits);
        calculateAll();
    });

    $('#pay').on('blur', function () {
        let value = parseRupiah($(this).val());
        $(this).val(formatRupiah(value));
    });


    $('#customerType').on('change', function () {
        $('#memberSelect').toggleClass('d-none', this.value !== 'member');
    });

    const HELD_TRANSACTIONS_KEY = 'heldTransactions';

    function clearForm() {
        $('#productTable tbody tr:gt(0)').remove();
        let firstRow = $('#productTable tbody tr:first');
        if (firstRow.find('.selectProduct').hasClass('select2-hidden-accessible')) {
            try {
                firstRow.find('.selectProduct').select2('destroy');
            } catch (e) { }
        }
        let select = firstRow.find('.selectProduct');
        select.removeClass('select2-hidden-accessible').removeAttr('data-select2-id tabindex aria-hidden');
        select.find('option').removeAttr('data-select2-id');
        select.val('');
        firstRow.find('.qty').val(1).removeAttr('max').prop('disabled', false);
        firstRow.find('.discount').val(0);
        firstRow.find('.product-image').attr('src', '');
        firstRow.find('.price').val('Rp 0,-').data('raw', 0);
        firstRow.find('.subtotal').val('Rp 0,-').data('raw', 0);
        $('#customerType').val(null).trigger('change');
        $('select[name="member_id"]').val(null).trigger('change');
        $('select[name="payment_method"]').val(null).trigger('change');
        $('#pay').val(formatRupiah(0));
        $('#memberSelect').addClass('d-none');

        $('#paymentDetailRow').addClass('d-none');
        $('#taxRow').addClass('d-none');
        $('#paymentDetail').html('<option></option>').val(null).trigger('change.select2');
        $('#taxAmount').val(formatRupiah(0)).data('raw', 0);
        $('#grandTotal').val(formatRupiah(0)).data('raw', 0);

        reinitAllSelect2();
        updateActionButtons();
        calculateAll();
        updateHoldButtonCount();
    }


    function updateRowFromData(row) {
        let selectElement = $(row).find('.selectProduct');
        let selectedVal = selectElement.val();
        let selectedOption = selectElement.find('option[value="' + selectedVal +
            '"]');


        if (!selectedVal || selectedOption.length === 0) {
            $(row).find('.product-image').attr('src', '');
            $(row).find('.price').val(formatRupiah(0)).data('raw', 0);
            let qtyInput = $(row).find('.qty');
            qtyInput.val(1).removeAttr('max').prop('disabled', false);
            $(row).find('.discount').val(0);
        } else {
            let price = parseInt(selectedOption.data('price')) || 0;
            let image = selectedOption.data('image');
            let stock = parseInt(selectedOption.data('stock'));
            if (isNaN(stock)) stock = 0;

            $(row).find('.product-image').attr('src', image || '');
            $(row).find('.price').val(formatRupiah(price)).data('raw', price);

            let qtyInput = $(row).find('.qty');
            let currentQty = parseInt(qtyInput.val());
            qtyInput.attr('max', stock);

            if (stock <= 0) {
                qtyInput.val(0).prop('disabled', true);
            } else {

                if (currentQty > stock) qtyInput.val(stock);
                if (currentQty < 1) qtyInput.val(1);
                qtyInput.prop('disabled', false);
            }
        }
        calculateRow(row);
    }



    function getHeldTransactions() {
        return JSON.parse(localStorage.getItem(HELD_TRANSACTIONS_KEY) || '[]');
    }

    function saveHeldTransactions(transactions) {
        localStorage.setItem(HELD_TRANSACTIONS_KEY, JSON.stringify(transactions));
        updateHoldButtonCount();
    }

    function updateHoldButtonCount() {
        const transactions = getHeldTransactions();
        $('#btnShowHold').html(
            `<i class="ri-pause-line"></i> Lihat Transaksi di-Hold (${transactions.length})`
        );
    }

    function showHoldList() {
        const transactions = getHeldTransactions();
        const listContainer = $('#holdList');
        listContainer.empty();

        if (transactions.length === 0) {
            listContainer.html('<p class="text-center">Tidak ada transaksi yang di-hold.</p>');
            return;
        }

        transactions.forEach((tx, index) => {
            let totalItems = tx.products.reduce((acc, p) => acc + p.qty, 0);
            listContainer.append(
                `<button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" data-index="${index}">
                            <div>
                                <strong>Transaksi #${tx.id}</strong> (${totalItems} item)
                                <small class="d-block text-muted">di-Hold pada: ${new Date(tx.timestamp).toLocaleTimeString()}</small>
                            </div>
                            <i class="ri-arrow-right-s-line"></i>
                        </button>`
            );
        });
    }

    function loadTransaction(index) {
        const transactions = getHeldTransactions();
        const tx = transactions[index];

        if (!tx) return;
        clearForm();
        $('#customerType').val(tx.customerType).trigger('change');
        if (tx.customerType === 'member') {
            $('select[name="member_id"]').val(tx.memberId).trigger('change');
        }

        $('select[name="payment_method"]').val(tx.paymentMethod).trigger('change');

        if (tx.paymentMethod === 'debit' || tx.paymentMethod === 'credit') {
            $('#paymentDetail').val(tx.paymentDetail).trigger('change');
        }

        tx.products.forEach((product, productIndex) => {
            let rowToPopulate;

            if (productIndex === 0) {
                rowToPopulate = $('#productTable tbody tr').first();
            } else {
                addRow();
                rowToPopulate = $('#productTable tbody tr').last();
            }

            rowToPopulate.find('.selectProduct').val(product.kdProduct);
            rowToPopulate.find('.qty').val(product.qty);
            rowToPopulate.find('.discount').val(product.discount);
        });

        reinitAllSelect2();

        $('#productTable tbody tr').each(function () {
            if ($(this).find('.selectProduct').val()) {
                let selectElement = $(this).find('.selectProduct');
                let selectedOption = selectElement.find('option:selected');
                let price = parseInt(selectedOption.data('price')) || 0;
                let stock = parseInt(selectedOption.data('stock'));
                if (isNaN(stock)) stock = 0;

                $(this).find('.product-image').attr('src', selectedOption.data('image') || '');
                $(this).find('.price').val(formatRupiah(price)).data('raw', price);

                let qtyInput = $(this).find('.qty');
                qtyInput.attr('max', stock);

                if (stock <= 0) {
                    qtyInput.val(0).prop('disabled', true);
                } else {
                    let currentQty = parseInt(qtyInput.val()) || 1;
                    if (currentQty > stock) qtyInput.val(stock);
                    else if (currentQty < 1) qtyInput.val(1);
                    qtyInput.prop('disabled', false);
                }
                calculateRow($(this));
            }
        });

        calculateAll();


        transactions.splice(index, 1);
        saveHeldTransactions(transactions);
        $('#holdModal').modal('hide');
    }


    updateHoldButtonCount();

    $('#btnHold').on('click', function () {
        let products = [];
        let hasProducts = false;

        $('#productTable tbody tr').each(function () {
            let kdProduct = $(this).find('.selectProduct').val();
            if (kdProduct) {
                let qty = parseInt($(this).find('.qty').val()) || 0;
                if (qty > 0) {
                    hasProducts = true;
                    products.push({
                        kdProduct: kdProduct,
                        qty: qty,
                        discount: parseInt($(this).find('.discount').val()) || 0
                    });
                } else {
                    console.warn("Product " + kdProduct +
                        " selected but has 0 quantity. Not holding this item.");
                }
            }
        });


        if (!hasProducts) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Tidak ada produk valid (dengan jumlah > 0) untuk di-hold.'
            });
            return;
        }

        const transaction = {
            id: Date.now(),
            timestamp: new Date().toISOString(),
            customerType: $('#customerType').val(),
            memberId: $('select[name="member_id"]').val(),
            paymentMethod: $('select[name="payment_method"]').val(),
            paymentDetail: $('#paymentDetail').val(),
            products: products
        };

        const transactions = getHeldTransactions();
        transactions.push(transaction);
        saveHeldTransactions(transactions);

        clearForm();
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: 'Transaksi berhasil di-hold.',
            timer: 1500,
            showConfirmButton: false
        });
    });

    $('#btnShowHold').on('click', function () {
        showHoldList();
        $('#holdModal').modal('show');
    });

    $(document).on('click', '#holdList .list-group-item', function () {
        let index = $(this).data('index');
        loadTransaction(index);
    });

    const form = document.getElementById('cashierForm');

    form.addEventListener('submit', function (event) {
        event.preventDefault();

        let missingFields = [];
        let stockError = false;

        const customerType = $('#customerType').val();
        if (!customerType) {
            missingFields.push('Jenis Transaksi');
        }

        if (customerType === 'member' && !$('select[name="member_id"]').val()) {
            missingFields.push('Pilih Member');
        }

        if (!$('select[name="payment_method"]').val()) {
            missingFields.push('Metode Pembayaran');
        }

        if ((customerType === 'debit' || customerType === 'credit') && !$('#paymentDetail').val()) {
            missingFields.push('Detail Pembayaran (BCA/Bank Lain)');
        }

        let hasProduct = false;
        $('#productTable tbody tr').each(function () {
            let productSelect = $(this).find('.selectProduct');
            if (productSelect.val()) {
                hasProduct = true;
                let qtyInput = $(this).find('.qty');
                let currentQty = parseInt(qtyInput.val()) || 0;
                let maxStock = parseInt(qtyInput.attr('max'));
                if (isNaN(maxStock)) maxStock = 0;

                if (currentQty > maxStock && maxStock >= 0) {
                    stockError = true;
                    let productName = $(this).find('option:selected').data('name') ||
                        productSelect.val();
                    missingFields.push(
                        `Jumlah ${productName} (${currentQty}) melebihi stok (${maxStock})`
                    );
                }
                if (currentQty <= 0) {
                    stockError = true;
                    let productName = $(this).find('option:selected').data('name') ||
                        productSelect.val();
                    missingFields.push(`Jumlah ${productName} minimal 1`);
                }
            }
        });
        if (!hasProduct) {
            missingFields.push('Produk (minimal 1 barang valid)');
        }

        let grandTotalAmount = parseRupiah($('#grandTotal').val());
        let paidAmount = parseRupiah($('#pay').val());

        if (paidAmount <= 0 && grandTotalAmount > 0) {
            missingFields.push('Jumlah Bayar harus diisi');
        } else if (paidAmount < grandTotalAmount) {
            missingFields.push('Jumlah Bayar Kurang (Total: ' + formatRupiah(grandTotalAmount) +
                ')');
        }

        if (missingFields.length > 0) {
            Swal.fire({
                icon: stockError ? 'error' : 'warning',
                title: stockError ? 'Kesalahan Input!' : 'Oops... Data Belum Lengkap!',
                html: 'Mohon perbaiki data berikut: <br><ul style="text-align: left; margin-top: 10px;"><li>' +
                    missingFields.join('</li><li>') +
                    '</li></ul>',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Baik'
            });
        } else {
            form.submit();
        }
    });

});
