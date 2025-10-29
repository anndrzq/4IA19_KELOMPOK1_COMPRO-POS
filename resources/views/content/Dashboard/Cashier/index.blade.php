@extends('layouts.master')

@push('page-script')
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
                $('.selectProduct').each(function() {
                    let val = $(this).val();
                    if (val) {
                        selectedValues.push(val);
                    }
                });

                $('.selectProduct').each(function() {
                    let currentSelectedValue = $(this).val();
                    $(this).find('option').each(function() {
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
                $('.selectProduct.select2-hidden-accessible').each(function() {
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
                $('#customerType, select[name="payment_method"], select[name="member_id"]').each(function() {
                    if (!$(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2({
                            width: '100%',
                            placeholder: 'Pilih Opsi',
                            allowClear: true,
                        });
                    }
                });
                $('.selectProduct').each(function() {
                    if (!$(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2({
                            width: '100%',
                            placeholder: 'Pilih Produk',
                            allowClear: true,
                            templateResult: function(option) {
                                return option.text;
                            },
                            templateSelection: function(option) {
                                return option.text.split(' - Habis')[0];
                            }
                        });
                    }
                });
            }

            function updateActionButtons() {
                const rows = $('#productTable tbody tr');
                rows.each(function(index) {
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
                    } catch (e) {}
                }

                let newRow = $(tableBody.rows[0]).clone();

                if (firstRowWasSelect2) {
                    $(tableBody.rows[0]).find('.selectProduct').select2({
                        width: '100%',
                        placeholder: 'Pilih Produk',
                        allowClear: true,
                        templateResult: function(option) {
                            return option.text;
                        },
                        templateSelection: function(option) {
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


            $(document).on('click', '.removeRow', function() {
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
                let total = 0;
                $('.subtotal').each(function() {
                    total += $(this).data('raw') || 0;
                });
                $('#total').val(formatRupiah(total)).data('raw', total);

                let payInputVal = $('#pay').val();
                let pay = parseRupiah(payInputVal);

                let change = pay - total;
                $('#change').val(formatRupiah(change < 0 ? 0 : change));
            }

            $(document).on('select2:select', '.selectProduct', function() {
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


            $(document).on('select2:unselect', '.selectProduct', function() {
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

            $(document).on('input', '.qty', function() {
                let row = $(this).closest('tr');
                calculateRow(row);
            });


            $(document).on('input', '.discount', function() {
                calculateRow($(this).closest('tr'));
            });


            $(document).on('click', '.addRow', function() {
                addRow();
            });

            $('#pay').on('input', function() {
                let digits = $(this).val().replace(/[^\d]/g, '');
                $(this).val(digits);
                calculateAll();
            });

            $('#pay').on('blur', function() {
                let value = parseRupiah($(this).val());
                $(this).val(formatRupiah(value));
            });


            $('#customerType').on('change', function() {
                $('#memberSelect').toggleClass('d-none', this.value !== 'member');
            });

            const HELD_TRANSACTIONS_KEY = 'heldTransactions';

            function clearForm() {
                $('#productTable tbody tr:gt(0)').remove();
                let firstRow = $('#productTable tbody tr:first');
                if (firstRow.find('.selectProduct').hasClass('select2-hidden-accessible')) {
                    try {
                        firstRow.find('.selectProduct').select2('destroy');
                    } catch (e) {}
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

                $('#productTable tbody tr').each(function() {
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

            $('#btnHold').on('click', function() {
                let products = [];
                let hasProducts = false;

                $('#productTable tbody tr').each(function() {
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

            $('#btnShowHold').on('click', function() {
                showHoldList();
                $('#holdModal').modal('show');
            });

            $(document).on('click', '#holdList .list-group-item', function() {
                let index = $(this).data('index');
                loadTransaction(index);
            });

            const form = document.getElementById('cashierForm');

            form.addEventListener('submit', function(event) {
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

                let hasProduct = false;
                $('#productTable tbody tr').each(function() {
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

                let totalAmount = parseRupiah($('#total').val());
                let paidAmount = parseRupiah($('#pay').val());

                if (paidAmount <= 0 && totalAmount > 0) {
                    missingFields.push('Jumlah Bayar harus diisi');
                } else if (paidAmount < totalAmount) {
                    missingFields.push('Jumlah Bayar Kurang (Total: ' + formatRupiah(totalAmount) + ')');
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
    </script>
@endpush
@section('content')
    <div class="page-content">
        <div class="container-fluid">

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h4 class="alert-heading">Terjadi Kesalahan!</h4>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Kasir Penjualan</h4>
                        <button type="button" class="btn btn-warning btn-sm" id="btnShowHold">
                            <i class="ri-pause-line"></i> Lihat Transaksi di-Hold (0)
                        </button>
                    </div>
                </div>
                <div class="card-body">

                    <form id="cashierForm" action="{{ route('cashier.store') }}" method="POST">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label>Jenis Transaksi</label>
                                <select class="form-select" name="customer_type" id="customerType"
                                    data-placeholder="-- Pilih Jenis Transaksi --">
                                    <option></option>
                                    <option value="umum">Umum</option>
                                    <option value="grosir">Grosir</option>
                                    <option value="member">Member</option>
                                </select>
                            </div>
                            <div class="col-md-4 d-none" id="memberSelect">
                                <label>Pilih Member</label>
                                <select class="form-select" name="member_id" data-placeholder="-- Pilih Member --">
                                    <option></option>
                                    @foreach ($members as $member)
                                        <option value="{{ $member->id }}">{{ $member->name }} - {{ $member->noWA }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>Metode Pembayaran</label>
                                <select class="form-select" name="payment_method" data-placeholder="-- Pilih Metode --">
                                    <option></option>
                                    <option value="cash">Cash</option>
                                    <option value="transfer">Transfer</option>
                                    <option value="qris">QRIS</option>
                                    <option value="debit">Debit</option>
                                </select>
                            </div>
                        </div>

                        <table class="table table-bordered align-middle" id="productTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Foto</th>
                                    <th>Produk</th>
                                    <th>Harga</th>
                                    <th>Qty</th>
                                    <th>Diskon (%) / Rp</th>
                                    <th>Subtotal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center">
                                        <img src="" class="product-image" width="50">
                                    </td>
                                    <td>
                                        <select name="KdProduct[]" class="form-select selectProduct">
                                            <option value="" disabled selected>Pilih Produk</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->KdProduct }}"
                                                    data-image="{{ asset('storage/' . $product->Photo) }}"
                                                    data-price="{{ $product->price }}" data-stock="{{ $product->stock }}"
                                                    data-name="{{ $product->nameProduct }}">
                                                    {{ $product->nameProduct }}
                                                </option>
                                            @endforeach
                                        </select>

                                    </td>
                                    <td> <input type="text" class="form-control price" disabled></td>
                                    <td><input type="number" class="form-control qty" name="qty[]" value="1"
                                            min="0"></td>
                                    <td>
                                        <input type="number" class="form-control discount" name="discount[]" value="0"
                                            min="0">
                                    </td>
                                    <td><input type="text" class="form-control subtotal" disabled></td>
                                    <td><button type="button" class="btn btn-success btn-sm addRow">+</button></td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="row mt-4">
                            <div class="col-md-4 offset-md-8">
                                <div class="mb-2">
                                    <label>Total</label>
                                    <input type="text" class="form-control" id="total" disabled>
                                </div>
                                <div class="mb-2">
                                    <label>Bayar</label>
                                    <input type="text" class="form-control" id="pay" name="pay" value="Rp 0,-">
                                </div>
                                <div class="mb-2">
                                    <label>Kembalian</label>
                                    <input type="text" class="form-control" id="change" disabled>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <button type="button" class="btn btn-secondary w-100"
                                            id="btnHold">Hold</button>
                                    </div>
                                    <div class="col-6">
                                        <button type="submit" class="btn btn-primary w-100">Selesaikan</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="holdModal" tabindex="-1" aria-labelledby="holdModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="holdModalLabel">Transaksi di-Hold</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="holdList" class="list-group">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
