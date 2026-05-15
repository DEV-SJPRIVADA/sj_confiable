/**
 * Desplegable con casillas para servicios (múltiple) o paquete (único).
 */
(function () {
    'use strict';

    function closePicklist(el) {
        var panel = el.querySelector('.picklist-checkbox__panel');
        var trigger = el.querySelector('.picklist-checkbox__trigger');
        if (!panel || !trigger) {
            return;
        }
        panel.hidden = true;
        trigger.setAttribute('aria-expanded', 'false');
        el.classList.remove('is-open');
    }

    function closeAllPicklists(except) {
        document.querySelectorAll('[data-picklist].is-open').forEach(function (el) {
            if (el !== except) {
                closePicklist(el);
            }
        });
    }

    function initPicklist(el) {
        var multiple = el.hasAttribute('data-picklist-multiple');
        var max = parseInt(el.getAttribute('data-picklist-max') || '5', 10);
        var inputName = el.getAttribute('data-picklist-name') || '';
        var trigger = el.querySelector('.picklist-checkbox__trigger');
        var panel = el.querySelector('.picklist-checkbox__panel');
        var labelEl = el.querySelector('.picklist-checkbox__label');
        var inputsHost = el.querySelector('.picklist-checkbox__inputs');
        var checkboxes = Array.prototype.slice.call(
            el.querySelectorAll('.picklist-checkbox__option input[type="checkbox"]')
        );

        if (!trigger || !panel || !labelEl || !inputsHost) {
            return;
        }

        var placeholder = labelEl.getAttribute('data-placeholder') || labelEl.textContent.trim();

        function getSelectedValues() {
            return checkboxes.filter(function (cb) { return cb.checked; }).map(function (cb) {
                return cb.value;
            });
        }

        function getSelectedLabels(values) {
            return values.map(function (v) {
                var cb = checkboxes.find(function (c) { return c.value === v; });
                if (!cb) {
                    return '';
                }
                var textEl = cb.closest('.picklist-checkbox__option');
                return textEl
                    ? textEl.querySelector('.picklist-checkbox__option-text').textContent.trim()
                    : '';
            }).filter(Boolean);
        }

        function updateLabel() {
            var selected = getSelectedValues();
            var texts = getSelectedLabels(selected);

            if (selected.length === 0) {
                labelEl.textContent = placeholder;
                labelEl.classList.remove('is-selected');
            } else if (multiple && selected.length > 2) {
                labelEl.textContent = selected.length + ' servicios seleccionados';
                labelEl.classList.add('is-selected');
            } else {
                labelEl.textContent = texts.join(', ');
                labelEl.classList.add('is-selected');
            }
        }

        function syncInputs() {
            inputsHost.innerHTML = '';
            var selected = getSelectedValues();

            if (multiple) {
                selected.forEach(function (v) {
                    var inp = document.createElement('input');
                    inp.type = 'hidden';
                    inp.name = inputName;
                    inp.value = v;
                    inputsHost.appendChild(inp);
                });
            } else if (selected[0]) {
                var single = document.createElement('input');
                single.type = 'hidden';
                single.name = inputName;
                single.value = selected[0];
                inputsHost.appendChild(single);
            }

            updateLabel();
            el.dispatchEvent(new CustomEvent('picklist:change', { bubbles: true }));
        }

        el.picklistClear = function () {
            checkboxes.forEach(function (cb) {
                cb.checked = false;
            });
            syncInputs();
        };

        el.picklistSetDisabled = function (disabled) {
            el.classList.toggle('is-disabled', !!disabled);
            checkboxes.forEach(function (cb) {
                cb.disabled = !!disabled;
            });
            trigger.disabled = !!disabled;
            if (disabled) {
                closePicklist(el);
            }
        };

        el.picklistGetCount = function () {
            return getSelectedValues().length;
        };

        checkboxes.forEach(function (cb) {
            cb.addEventListener('change', function () {
                if (el.classList.contains('is-disabled')) {
                    cb.checked = false;
                    return;
                }
                if (!multiple) {
                    if (cb.checked) {
                        checkboxes.forEach(function (other) {
                            if (other !== cb) {
                                other.checked = false;
                            }
                        });
                    }
                } else {
                    var n = checkboxes.filter(function (c) { return c.checked; }).length;
                    if (n > max) {
                        cb.checked = false;
                        return;
                    }
                }
                syncInputs();
            });
        });

        trigger.addEventListener('click', function (e) {
            e.preventDefault();
            if (el.classList.contains('is-disabled')) {
                return;
            }
            var willOpen = panel.hidden;
            closeAllPicklists(el);
            if (willOpen) {
                panel.hidden = false;
                trigger.setAttribute('aria-expanded', 'true');
                el.classList.add('is-open');
            }
        });

        syncInputs();
    }

    function initServicioPaqueteMutex(form) {
        var svcRoot = form.querySelector('[data-role="servicios-multiple"]');
        var paqRoot = form.querySelector('[data-role="paquete"]');
        if (!svcRoot || !paqRoot) {
            return;
        }

        function sync() {
            var hasPaq = paqRoot.picklistGetCount() > 0;
            var hasSvc = svcRoot.picklistGetCount() > 0;

            if (hasPaq) {
                svcRoot.picklistClear();
                svcRoot.picklistSetDisabled(true);
                paqRoot.picklistSetDisabled(false);
            } else if (hasSvc) {
                paqRoot.picklistClear();
                paqRoot.picklistSetDisabled(true);
                svcRoot.picklistSetDisabled(false);
            } else {
                svcRoot.picklistSetDisabled(false);
                paqRoot.picklistSetDisabled(false);
            }
        }

        svcRoot.addEventListener('picklist:change', sync);
        paqRoot.addEventListener('picklist:change', sync);
        sync();
    }

    function initCreateFormValidation(form) {
        var msg = document.getElementById('msgServicioPaquete');
        if (!msg) {
            return;
        }

        form.addEventListener('submit', function (e) {
            var svcRoot = form.querySelector('[data-role="servicios-multiple"]');
            var paqRoot = form.querySelector('[data-role="paquete"]');
            var svcCount = svcRoot ? svcRoot.picklistGetCount() : 0;
            var paqCount = paqRoot ? paqRoot.picklistGetCount() : 0;
            var ok = (svcCount >= 1 && svcCount <= 5 && paqCount === 0)
                || (paqCount === 1 && svcCount === 0);

            msg.classList.toggle('d-none', ok);
            if (!ok) {
                e.preventDefault();
            }
        });
    }

    function initEditFormValidation(form) {
        var err = document.getElementById('serviciosEditError');
        var svcRoot = form.querySelector('[data-role="servicios-multiple"]');
        if (!svcRoot) {
            return;
        }

        function validar() {
            var n = svcRoot.picklistGetCount();
            var ok = n >= 1 && n <= 5;
            if (err) {
                err.classList.toggle('d-none', ok);
            }
            return ok;
        }

        svcRoot.addEventListener('picklist:change', validar);
        form.addEventListener('submit', function (e) {
            if (!validar()) {
                e.preventDefault();
            }
        });
        validar();
    }

    document.addEventListener('click', function (e) {
        if (!e.target.closest('[data-picklist]')) {
            closeAllPicklists(null);
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeAllPicklists(null);
        }
    });

    document.querySelectorAll('[data-picklist]').forEach(initPicklist);

    var formCreate = document.getElementById('formNuevaSolicitudCliente');
    if (formCreate) {
        initServicioPaqueteMutex(formCreate);
        initCreateFormValidation(formCreate);
    }

    var formEdit = document.getElementById('formEditSolicitudCliente');
    if (formEdit) {
        initEditFormValidation(formEdit);
    }
})();
