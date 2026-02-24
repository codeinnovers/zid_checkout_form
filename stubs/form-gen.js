(function () {
    'use strict';

    /* =============================================================================
       Configuration
       TODO: make STORE_REFERENCE and BASE_URL dynamic (e.g. injected via script tag
       data attributes or a global variable set by the Zid app settings).
    ============================================================================= */
    const STORE_REFERENCE  = 'demo-store-001';                          // TODO: make dynamic
    const BASE_URL         = 'http://localhost:8000';                   // TODO: make dynamic
    const API_URL          = BASE_URL + '/api/v1/form-fields';
    const WEBHOOK_URL      = 'https://limra-softwares.com/api/checkout/custom-data';
    const FIELD_PREFIX     = 'zid_field_';
    const POLL_MS          = 300;

    /* =============================================================================
       API
    ============================================================================= */
    async function fetchFormFields() {
        const url      = API_URL + '?store_reference=' + encodeURIComponent(STORE_REFERENCE);
        const response = await fetch(url);

        if (!response.ok) {
            throw new Error('[ZidForms] API returned ' + response.status);
        }

        const json = await response.json();
        return Array.isArray(json.data) ? json.data : [];
    }

    /* =============================================================================
       HTML Builders
    ============================================================================= */
    function fieldId(name) {
        return FIELD_PREFIX + name;
    }

    function inputStyle() {
        return [
            'width:100%',
            'padding:10px 12px',
            'box-sizing:border-box',
            'border:1px solid #d1d5db',
            'border-radius:6px',
            'font-size:14px',
            'line-height:1.5',
            'color:#111827',
            'background:#fff',
            'margin-top:4px',
        ].join(';');
    }

    function renderLabel(field) {
        const text     = field.label;
        const required = field.is_required
            ? ' <span style="color:#ef4444;margin-left:2px">*</span>'
            : '';
        return '<label for="' + fieldId(field.name) + '" style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:2px;">'
            + text + required + '</label>';
    }

    function renderText(field) {
        return '<input type="text"'
            + ' id="'          + fieldId(field.name) + '"'
            + ' name="'        + field.name + '"'
            + ' placeholder="' + (field.placeholder || '') + '"'
            + ' value="'       + (field.default_value || '') + '"'
            + ' style="'       + inputStyle() + '"'
            + (field.is_required ? ' required' : '')
            + ' />';
    }

    function renderEmail(field) {
        return '<input type="email"'
            + ' id="'          + fieldId(field.name) + '"'
            + ' name="'        + field.name + '"'
            + ' placeholder="' + (field.placeholder || '') + '"'
            + ' style="'       + inputStyle() + '"'
            + (field.is_required ? ' required' : '')
            + ' />';
    }

    function renderPhone(field) {
        return '<input type="tel"'
            + ' id="'          + fieldId(field.name) + '"'
            + ' name="'        + field.name + '"'
            + ' placeholder="' + (field.placeholder || '') + '"'
            + ' style="'       + inputStyle() + '"'
            + (field.is_required ? ' required' : '')
            + ' />';
    }

    function renderNumber(field) {
        var rules = field.validation_rules || {};
        var attrs = '';
        if (rules.min_value !== undefined && rules.min_value !== null) attrs += ' min="' + rules.min_value + '"';
        if (rules.max_value !== undefined && rules.max_value !== null) attrs += ' max="' + rules.max_value + '"';

        return '<input type="number"'
            + ' id="'          + fieldId(field.name) + '"'
            + ' name="'        + field.name + '"'
            + ' placeholder="' + (field.placeholder || '') + '"'
            + ' value="'       + (field.default_value || '') + '"'
            + attrs
            + ' style="'       + inputStyle() + '"'
            + (field.is_required ? ' required' : '')
            + ' />';
    }

    function renderTextarea(field) {
        var rules   = field.validation_rules || {};
        var maxAttr = rules.max_length ? ' maxlength="' + rules.max_length + '"' : '';

        return '<textarea'
            + ' id="'          + fieldId(field.name) + '"'
            + ' name="'        + field.name + '"'
            + ' placeholder="' + (field.placeholder || '') + '"'
            + ' rows="3"'
            + maxAttr
            + ' style="'       + inputStyle() + ';resize:vertical;"'
            + (field.is_required ? ' required' : '')
            + '>'
            + (field.default_value || '')
            + '</textarea>';
    }

    function renderSelect(field) {
        var options = (field.options || []).map(function (opt) {
            return '<option value="' + opt.value + '">' + opt.label + '</option>';
        }).join('');

        return '<select'
            + ' id="'    + fieldId(field.name) + '"'
            + ' name="'  + field.name + '"'
            + ' style="' + inputStyle() + '"'
            + (field.is_required ? ' required' : '')
            + '>'
            + '<option value="">— Select —</option>'
            + options
            + '</select>';
    }

    function renderRadio(field) {
        return (field.options || []).map(function (opt, i) {
            var id = fieldId(field.name) + '_' + i;
            return '<label for="' + id + '" style="display:flex;align-items:center;gap:8px;margin-top:8px;font-size:14px;cursor:pointer;color:#374151;">'
                + '<input type="radio"'
                + ' id="'    + id + '"'
                + ' name="'  + field.name + '"'
                + ' value="' + opt.value + '"'
                + (field.is_required ? ' required' : '')
                + ' />'
                + opt.label
                + '</label>';
        }).join('');
    }

    function renderCheckbox(field) {
        return (field.options || []).map(function (opt, i) {
            var id = fieldId(field.name) + '_' + i;
            return '<label for="' + id + '" style="display:flex;align-items:center;gap:8px;margin-top:8px;font-size:14px;cursor:pointer;color:#374151;">'
                + '<input type="checkbox"'
                + ' id="'    + id + '"'
                + ' name="'  + field.name + '"'
                + ' value="' + opt.value + '"'
                + ' />'
                + opt.label
                + '</label>';
        }).join('');
    }

    function renderDate(field) {
        return '<input type="date"'
            + ' id="'    + fieldId(field.name) + '"'
            + ' name="'  + field.name + '"'
            + ' value="' + (field.default_value || '') + '"'
            + ' style="' + inputStyle() + '"'
            + (field.is_required ? ' required' : '')
            + ' />';
    }

    function renderFile(field) {
        return '<input type="file"'
            + ' id="'    + fieldId(field.name) + '"'
            + ' name="'  + field.name + '"'
            + ' style="' + inputStyle() + ';padding:6px;"'
            + (field.is_required ? ' required' : '')
            + ' />';
    }

    var fieldRenderers = {
        text:     renderText,
        email:    renderEmail,
        phone:    renderPhone,
        number:   renderNumber,
        textarea: renderTextarea,
        select:   renderSelect,
        radio:    renderRadio,
        checkbox: renderCheckbox,
        date:     renderDate,
        file:     renderFile,
    };

    function buildFieldElement(field) {
        var render = fieldRenderers[field.field_type] || renderText;

        var wrapper       = document.createElement('div');
        wrapper.style.cssText = 'margin-bottom:14px;';
        wrapper.innerHTML = renderLabel(field) + render(field);

        return wrapper;
    }

    function buildFormSection(fields) {
        var section       = document.createElement('div');
        section.id        = 'zid-custom-form';
        section.style.cssText = [
            'margin-top:20px',
            'padding:16px 20px',
            'border:1px solid #e5e7eb',
            'border-radius:8px',
            'background:#f9fafb',
        ].join(';');

        var heading       = document.createElement('p');
        heading.textContent  = 'Additional Information';
        heading.style.cssText = 'font-size:15px;font-weight:700;color:#111827;margin:0 0 14px;';
        section.appendChild(heading);

        fields.forEach(function (field) {
            section.appendChild(buildFieldElement(field));
        });

        return section;
    }

    /* =============================================================================
       Data Collection
    ============================================================================= */
    function collectFormData(fields) {
        var data = {};

        fields.forEach(function (field) {
            switch (field.field_type) {
                case 'checkbox':
                    data[field.name] = Array.from(
                        document.querySelectorAll('input[name="' + field.name + '"]:checked')
                    ).map(function (el) { return el.value; });
                    break;

                case 'radio':
                    var checked = document.querySelector('input[name="' + field.name + '"]:checked');
                    data[field.name] = checked ? checked.value : '';
                    break;

                case 'file':
                    var fileInput = document.getElementById(fieldId(field.name));
                    data[field.name] = (fileInput && fileInput.files[0]) ? fileInput.files[0] : null;
                    break;

                default:
                    var el = document.getElementById(fieldId(field.name));
                    data[field.name] = el ? el.value : '';
            }
        });

        return data;
    }

    /* =============================================================================
       Submission
    ============================================================================= */
    function submitFormData(fields) {
        var values   = collectFormData(fields);
        var formData = new FormData();

        Object.keys(values).forEach(function (key) {
            var value = values[key];

            if (Array.isArray(value)) {
                value.forEach(function (v) { formData.append(key + '[]', v); });
            } else if (value instanceof File) {
                formData.append(key, value);
            } else {
                formData.append(key, value !== null && value !== undefined ? value : '');
            }
        });

        console.log('[ZidForms] Submitting:', values);

        fetch(WEBHOOK_URL, { method: 'POST', body: formData })
            .then(function (res) {
                console.log('[ZidForms] Webhook response:', res.status);
            })
            .catch(function (err) {
                console.error('[ZidForms] Webhook error:', err);
            });
    }

    /* =============================================================================
       Polling Helpers
    ============================================================================= */
    function waitForElement(selector, callback) {
        var interval = setInterval(function () {
            var el = document.querySelector(selector);
            if (el) {
                clearInterval(interval);
                callback(el);
            }
        }, POLL_MS);
    }

    /* =============================================================================
       Bootstrap
    ============================================================================= */
    document.addEventListener('DOMContentLoaded', function () {
        fetchFormFields()
            .then(function (fields) {
                if (!fields.length) {
                    console.log('[ZidForms] No active fields configured for this store.');
                    return;
                }

                waitForElement('.payment-section', function (paymentSection) {
                    var formSection = buildFormSection(fields);
                    paymentSection.insertAdjacentElement('afterend', formSection);
                    console.log('[ZidForms] Injected ' + fields.length + ' field(s) into checkout.');

                    waitForElement('#review-order-confirm-button-bottom', function (confirmBtn) {
                        confirmBtn.addEventListener('click', function () {
                            submitFormData(fields);
                        });
                    });
                });
            })
            .catch(function (err) {
                console.error('[ZidForms] Failed to load form fields:', err);
            });
    });

})();
