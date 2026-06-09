/**
 * Keeps list rows, action buttons (data-*), and dropdowns in sync after CRUD.
 */
(function (window, $) {
    'use strict';

    if (!$) {
        return;
    }

    var ES = {};

    /**
     * Normalize any status value (boolean, string, int) to '1' or '0' string.
     * This prevents the boolean/string mismatch bug where data-status="true"
     * fails strict comparison with '1' in view modal handlers.
     */
    ES.normalizeStatus = function (val) {
        if (val === true || val === 1 || val === '1' || val === 'true') return '1';
        return '0';
    };

    ES.escapeHtml = function (str) {
        return String(str == null ? '' : str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    };

    ES.statusBadge = function (enabled, labels) {
        labels = labels || { on: 'Active', off: 'Inactive' };
        if (enabled == 1 || enabled === true || enabled === '1') {
            return '<span class="badge-status-enabled">' + labels.on + '</span>';
        }
        return '<span class="badge-status-disabled">' + labels.off + '</span>';
    };

    ES.applyButtonData = function (selector, data) {
        var $btns = $(selector);
        if (!$btns.length) {
            return;
        }

        $btns.each(function () {
            var $btn = $(this);
            Object.keys(data).forEach(function (key) {
                var val = data[key];
                if (val !== undefined && val !== null) {
                    $btn.attr('data-' + key, val);
                }
            });
        });
    };

    // -------------------------------------------------------------------------
    // Customers
    // -------------------------------------------------------------------------

    ES.customerButtonData = function (c) {
        // birthdate may come as a Carbon object serialised to { date: '...' } or plain string
        var birthdate = '';
        if (c.birthdate) {
            birthdate = typeof c.birthdate === 'object' && c.birthdate.date
                ? c.birthdate.date.substring(0, 10)
                : String(c.birthdate).substring(0, 10);
        }

        return {
            id: c.id,
            name: c.name,
            email: c.email,
            phone: c.phone || '',
            type: c.customer_type,
            gender: c.gender || '',
            birthdate: birthdate,
            address: c.address || '',
            'shipping-address': c.shipping_address || '',
            city: c.city || '',
            'pin-code': c.pin_code || '',
            state: c.state || '',
            country: c.country || '',
            landmark: c.landmark || '',
            'area-id': c.area_id || '',
            'area-name': c.area_name || '',
            'credit-days': c.credit_days || '',
            'credit-limit': c.credit_limit || '',
            'vat-registered': ES.normalizeStatus(c.vat_registered),
            'vat-number': c.vat_number || '',
            status: ES.normalizeStatus(c.status)
        };
    };

    ES.syncCustomerRow = function (c) {
        var id = c.id;
        var typeBadge = c.customer_type === 'company' ? 'info' : 'secondary';
        var typeLabel = c.customer_type.charAt(0).toUpperCase() + c.customer_type.slice(1);

        $('#name-' + id).text(c.name);
        $('#email-' + id).text(c.email);
        $('#phone-' + id).text(c.phone || '-');

        var $typeCell = $('#type-' + id);
        if ($typeCell.length) {
            $typeCell.html('<span class="badge bg-' + typeBadge + '">' + ES.escapeHtml(typeLabel) + '</span>');
        }

        var vatHtml = '';
        if (c.vat_registered) {
            vatHtml = '<span class="badge bg-success">Yes</span>';
            if (c.vat_number) {
                vatHtml += '<br><small class="text-muted" style="font-size: 11px;">' + ES.escapeHtml(c.vat_number) + '</small>';
            }
        } else {
            vatHtml = '<span class="badge bg-secondary">No</span>';
        }
        $('#vat-' + id).html(vatHtml);

        var btnData = ES.customerButtonData(c);
        ES.applyButtonData('.btn-customer-view[data-id="' + id + '"]', btnData);
        ES.applyButtonData('.btn-customer-edit[data-id="' + id + '"]', btnData);
        ES.applyButtonData('.btn-customer-toggle[data-id="' + id + '"]', {
            id: c.id,
            name: c.name,
            status: ES.normalizeStatus(c.status)
        });
        ES.applyButtonData('.btn-customer-delete[data-id="' + id + '"]', {
            id: c.id,
            name: c.name
        });
    };

    ES.syncCustomerStatus = function (id, status) {
        var s = ES.normalizeStatus(status);
        $('#status-container-' + id).html(ES.statusBadge(s, { on: 'Active', off: 'Inactive' }));
        $('.btn-customer-view[data-id="' + id + '"], .btn-customer-toggle[data-id="' + id + '"], .btn-customer-edit[data-id="' + id + '"]')
            .attr('data-status', s);
    };

    ES.customerOptionLabel = function (c) {
        return c.name + (c.customer_type === 'company' ? ' (Company)' : '');
    };

    ES.upsertCustomerDropdownOption = function ($select, c) {
        var $opt = $select.find('option[value="' + c.id + '"]');
        var label = ES.customerOptionLabel(c);

        if ($opt.length) {
            $opt.text(label)
                .attr('data-email', c.email || '')
                .attr('data-phone', c.phone || '')
                .attr('data-vat', c.vat_number || '');
        } else {
            $select.append($('<option>', {
                value: c.id,
                text: label,
                'data-email': c.email || '',
                'data-phone': c.phone || '',
                'data-vat': c.vat_number || ''
            }));
        }
    };

    ES.selectInvoiceCustomer = function (c) {
        var $select = $('#customer_id');
        if (!$select.length) {
            return;
        }

        ES.upsertCustomerDropdownOption($select, c);
        $select.val(String(c.id)).trigger('change');

        if ($.fn.select2 && $select.hasClass('select2-hidden-accessible')) {
            $select.trigger('change.select2');
        }

        var modalEl = document.getElementById('createCustomerModal');
        if (modalEl) {
            var modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) {
                modal.hide();
            }
        }
    };

    // -------------------------------------------------------------------------
    // Products
    // -------------------------------------------------------------------------

    ES.productButtonData = function (p) {
        var categoryName = p.category && p.category.name ? p.category.name : (p.category_name || '-');

        return {
            id: p.id,
            name: p.name,
            desc: p.description || '',
            category: p.category_id,
            'category-name': categoryName,
            qty: p.qty,
            purchase: p.purchase_price,
            selling: p.selling_price,
            vat: p.vat,
            moq: p.moq,
            status: ES.normalizeStatus(p.status),

            // New Fields
            'item-code': p.item_code || '',
            'regional-name': p.regional_name || '',
            'manufacturer-id': p.manufacturer_id || '',
            'item-class': p.item_class || '',
            'hsn-code': p.hsn_code || '',
            'unit': p.unit || '',
            'image': p.image || '',
            'is-weighing': ES.normalizeStatus(p.is_weighing_item),
            'purchase-tax-inclusive': ES.normalizeStatus(p.purchase_tax_inclusive),
            'sale-tax-inclusive': ES.normalizeStatus(p.sale_tax_inclusive),
            'cess-percentage': p.cess_percentage || 0,
            'additional-cess': p.additional_cess || 0,
            'discount-percentage': p.discount_percentage || 0
        };
    };

    ES.productViewButtonData = function (p) {
        var categoryName = p.category && p.category.name ? p.category.name : (p.category_name || '-');

        return {
            id: p.id,
            name: p.name,
            desc: p.description || '',
            category: categoryName,
            qty: p.qty,
            purchase: p.purchase_price,
            selling: p.selling_price,
            vat: p.vat,
            moq: p.moq,
            status: ES.normalizeStatus(p.status),
            'image': p.image || ''
        };
    };

    ES.syncProductRow = function (p) {
        var id = p.id;
        var categoryName = p.category && p.category.name ? p.category.name : (p.category_name || '-');

        $('#name-' + id).text(p.name);
        $('#category-' + id).text(categoryName);
        $('#qty-' + id).text(p.qty);
        $('#selling-' + id).text('£' + parseFloat(p.selling_price).toFixed(2));
        $('#vat-' + id).text(p.vat + '%');

        // Update all button data attributes
        ES.applyButtonData('.btn-product-edit[data-id="' + id + '"]', ES.productButtonData(p));
        ES.applyButtonData('.btn-product-view[data-id="' + id + '"]', ES.productViewButtonData(p));

        ES.applyButtonData('.btn-product-toggle[data-id="' + id + '"], .btn-product-delete[data-id="' + id + '"]', {
            id: p.id,
            name: p.name,
            status: ES.normalizeStatus(p.status)
        });

        $('.btn-product-toggle[data-id="' + id + '"]').attr('data-status', ES.normalizeStatus(p.status));
    };

    ES.syncProductStatus = function (id, status) {
        var s = ES.normalizeStatus(status);
        $('#status-container-' + id).html(ES.statusBadge(s, { on: 'Enabled', off: 'Disabled' }));
        $('.btn-product-view[data-id="' + id + '"], .btn-product-edit[data-id="' + id + '"], .btn-product-toggle[data-id="' + id + '"]')
            .attr('data-status', s);
    };

    // -------------------------------------------------------------------------
    // Categories
    // -------------------------------------------------------------------------

    ES.syncCategoryRow = function (category) {
        var id = category.id;
        var $row = $('#row-' + id);
        var currentStatus = $row.attr('data-status');
        var status = category.status === undefined || category.status === null
            ? ES.normalizeStatus(currentStatus)
            : ES.normalizeStatus(category.status);

        $('#name-' + id).text(category.name);
        $row.attr('data-name', String(category.name || '').toLowerCase());
        $row.attr('data-status', status);

        ES.applyButtonData('.btn-category-view[data-id="' + id + '"], .btn-category-edit[data-id="' + id + '"], .btn-category-toggle[data-id="' + id + '"], .btn-category-delete[data-id="' + id + '"]', {
            id: category.id,
            name: category.name,
            status: status
        });
    };

    ES.syncCategoryStatus = function (id, status) {
        var s = ES.normalizeStatus(status);
        $('#row-' + id).attr('data-status', s);
        $('#status-container-' + id).html(ES.statusBadge(s, { on: 'Enabled', off: 'Disabled' }));
        $('.btn-category-view[data-id="' + id + '"], .btn-category-edit[data-id="' + id + '"], .btn-category-toggle[data-id="' + id + '"]')
            .attr('data-status', s);
    };

    // -------------------------------------------------------------------------
    // Areas
    // -------------------------------------------------------------------------

    ES.syncAreaRow = function (area) {
        var id = area.id;
        var $row = $('#row-' + id);
        var currentStatus = $row.attr('data-status');
        var status = area.status === undefined || area.status === null
            ? ES.normalizeStatus(currentStatus)
            : ES.normalizeStatus(area.status);

        $('#name-' + id).text(area.name);
        $row.attr('data-name', String(area.name || '').toLowerCase());
        $row.attr('data-status', status);

        ES.applyButtonData('.btn-area-view[data-id="' + id + '"], .btn-area-edit[data-id="' + id + '"], .btn-area-toggle[data-id="' + id + '"], .btn-area-delete[data-id="' + id + '"]', {
            id: area.id,
            name: area.name,
            status: status
        });
    };

    ES.syncAreaStatus = function (id, status) {
        var s = ES.normalizeStatus(status);
        $('#row-' + id).attr('data-status', s);
        $('#status-container-' + id).html(ES.statusBadge(s, { on: 'Enabled', off: 'Disabled' }));
        $('.btn-area-view[data-id="' + id + '"], .btn-area-edit[data-id="' + id + '"], .btn-area-toggle[data-id="' + id + '"]')
            .attr('data-status', s);
    };

    // -------------------------------------------------------------------------
    // Manufacturers
    // -------------------------------------------------------------------------

    ES.syncManufacturerRow = function (m) {
        var id = m.id;
        var $row = $('#row-' + id);
        var currentStatus = $row.attr('data-status');
        var status = m.status === undefined || m.status === null
            ? ES.normalizeStatus(currentStatus)
            : ES.normalizeStatus(m.status);

        $('#name-' + id).text(m.name);
        $('#phone-' + id).text(m.phone || '-');
        $('#email-' + id).text(m.email || '-');
        $('#address-' + id).text(m.address || '-');
        $row.attr('data-name', String(m.name || '').toLowerCase());
        $row.attr('data-status', status);

        ES.applyButtonData(
            '.btn-manufacturer-view[data-id="' + id + '"], ' +
            '.btn-manufacturer-edit[data-id="' + id + '"], ' +
            '.btn-manufacturer-toggle[data-id="' + id + '"], ' +
            '.btn-manufacturer-delete[data-id="' + id + '"]',
            {
                id: m.id,
                name: m.name,
                phone: m.phone || '',
                email: m.email || '',
                address: m.address || '',
                status: status
            }
        );
    };

    ES.syncManufacturerStatus = function (id, status) {
        var s = ES.normalizeStatus(status);
        $('#row-' + id).attr('data-status', s);
        $('#status-container-' + id).html(ES.statusBadge(s, { on: 'Enabled', off: 'Disabled' }));
        $('.btn-manufacturer-view[data-id="' + id + '"], ' +
            '.btn-manufacturer-edit[data-id="' + id + '"], ' +
            '.btn-manufacturer-toggle[data-id="' + id + '"]')
            .attr('data-status', s);
    };

    // -------------------------------------------------------------------------
    // Vendors
    // -------------------------------------------------------------------------    

    ES.syncVendorRow = function (v) {
        var id = v.id;

        $('#name-' + id).text(v.name || '-');
        $('#company-' + id).text(v.company || '-');
        $('#phone-' + id).text(v.phone || '-');
        $('#email-' + id).text(v.email || '-');
        $('#city-' + id).text(v.city || '-');

        ES.applyButtonData(
            '.btn-vendor-view[data-id="' + id + '"], ' +
            '.btn-vendor-edit[data-id="' + id + '"], ' +
            '.btn-vendor-toggle[data-id="' + id + '"], ' +
            '.btn-vendor-delete[data-id="' + id + '"]',
            {
                id: v.id,
                name: v.name || '',
                company: v.company || '',
                phone: v.phone || '',
                email: v.email || '',
                'tax-reg-number': v.tax_reg_number || '',
                'address-line-1': v.address_line_1 || '',
                'address-line-2': v.address_line_2 || '',
                city: v.city || '',
                'pin-code': v.pin_code || '',
                state: v.state || '',
                country: v.country || '',
                status: ES.normalizeStatus(v.status)
            }
        );
    };

    ES.syncVendorStatus = function (id, status) {
        var s = ES.normalizeStatus(status);
        $('#status-container-' + id).html(ES.statusBadge(s, { on: 'Enabled', off: 'Disabled' }));
        $('.btn-vendor-view[data-id="' + id + '"], ' +
            '.btn-vendor-edit[data-id="' + id + '"], ' +
            '.btn-vendor-toggle[data-id="' + id + '"]')
            .attr('data-status', s);
    };

    window.EntitySync = ES;

})(window, window.jQuery);
