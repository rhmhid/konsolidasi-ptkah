'use strict';

pinActiveMenu()
pinTopActiveMenu()
// minimizeSidebar(true)

function pinActiveMenu ()
{
    const currentURL = window.location.href.split('?')[0]
    const $links = $('#kt_app_sidebar_menu_scroll .menu a.menu-link')

    $links.each(function ()
    {
        const $link = $(this)
        const href = $link.attr('href')

        if ($link[0].pathname === '/')
        {
            if (href === currentURL.slice(0, -1))
            {
                $link.addClass('active')

                return
            }
        }
        else 
        {
            const splittedHref = href.split('/')
            const lastSegmentIndex = splittedHref.length - 1

            if (!href.endsWith('/public') && currentURL.includes(href) && currentURL.split('/')[lastSegmentIndex] == splittedHref[lastSegmentIndex])
            {
                $link.addClass('active')

                const $menuItem = $link.closest('.menu-item')

                if ($menuItem) pinParentActiveMenu($menuItem)

                return
            }
        }
    })
}

function pinParentActiveMenu ($menuItem)
{
    const $menuSub = $menuItem.closest('.menu-sub')
    const $parentMenuItem = $menuSub.closest('.menu-item')

    if ($parentMenuItem.length)
    {
        $parentMenuItem.addClass('hover')
        $parentMenuItem.addClass('show')

        pinParentActiveMenu($parentMenuItem)
    }
}

function pinTopActiveMenu ()
{
    const urlSearchParams = new URLSearchParams(window.location.search)

    if (urlSearchParams.has('landing-url')) return

    const currentURL = window.location.href
    const $menuLinks = $('#kt_app_header_wrapper a.menu-link')

    $menuLinks.each(function ()
    {
        const $link = $(this)
        const $menuItem = $link.parent('.menu-item')
        const href = $link.attr('href')

        const splittedHref = href.split('/')
        const lastSegmentIndex = splittedHref.length - 1

        if (!href.endsWith('/public') && currentURL.includes(href) && window.location.href.split('/')[lastSegmentIndex] == splittedHref[lastSegmentIndex])
        {
            if (!$menuItem.parent('.menu-sub').length) $menuItem.addClass('here')
            else
            {
                $link.addClass('active')

                pinTopParentActiveMenu($menuItem)
            }

            return false
        }
    })
}

function pinTopParentActiveMenu ($menuItem)
{
    const $menuSub = $menuItem.parent('.menu-sub')
    const $parentMenuItem = $menuSub.parent('.menu-item')
    const $menu = $parentMenuItem.parent('.menu')

    $parentMenuItem.addClass('here')

    if (!$menu.length) pinTopParentActiveMenu($parentMenuItem)
}

function minimizeSidebar (sidebar)
{  
    const minimize = sidebar == true ? 'off' : 'on'

    $('body').attr('data-kt-app-sidebar-minimize', minimize)
}

function setupDataTable (tableSelector, ajaxOptions = {}, options = {})
{
    const finalOptions = {
        processing: true,
        responsive: true,
        serverSide: true,
        language: {
            url: dttbSrcTranslation,
        },

        ajax: {
            error: function (err) {
                swalShowMessage('Error!', err?.responseJSON?.message || 'Failed to fetch data.', 'error')
            },

            ...ajaxOptions,
        },

        ...options,
    }

    if (!finalOptions.drawCallback)
    {
        finalOptions.drawCallback = function (settings)
        {
            initAsyncTooltip();

            if (finalOptions.pagingType && finalOptions.pagingType === 'simple') toggleDTTBNextPagination(settings);

            $(document).trigger('dt.drawCallback', settings);
        }
    }

    // Hide count info.
    if (finalOptions.pagingType && finalOptions.pagingType === 'simple') finalOptions.info = false;

    return $(tableSelector).DataTable(finalOptions);
}

function initAsyncTooltip ()
{
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('.dataTable [data-bs-toggle="tooltip"]'));

    tooltipTriggerList.map(function(tooltipTriggerEl)
    {
        return new bootstrap.Tooltip(tooltipTriggerEl,
        {
            trigger: 'hover',
        })
    })
}

function toggleDTTBNextPagination (settings)
{
    const {json, nTable, _iDisplayLength} = settings;
    const {data} = json;

    const $table = $(nTable);
    const $nextBtn = $table.closest('.dataTables_wrapper').find('.page-item.next');

    if (data.length < _iDisplayLength) $nextBtn.addClass('disabled');
    else $nextBtn.removeClass('disabled');
}

function checkRequired (form)
{
    if (!form.checkValidity())
    {
        swalShowMessage('Inputan Belum Lengkap', 'Harap mengisi inputan yang wajib diisi', 'warning');
        form.classList.add('was-validated');

        return false;
    }
    else return true;
}

function doAjax (url, data, method)
{
    return $.ajax({
        url,
        type        : method,
        data,
        cache       : false,
        contentType : false,
        processData : false,
        async       : false,
        cache       : false
    });
}

function setupSimpleDataTable (tableId, url, token, data, columns, order = 0, pagingType = 'simple', sort = [])
{
    const options = {
        processing  : true,
        serverSide  : true,
        info        : false,
        pagingType,
        responsive  : true,
        ajax        :
                    {
                        url,
                        type        : 'GET',
                        data,
                        error       : function (xhr, error, code)
                                    {
                                        swalShowMessage('Gagal mengambil data',code || error, 'warning');
                                    },

                        dataSrc     : function (json)
                                    {
                                        if (json.data)
                                        {
                                            if ($table.page.len() > json.data.length) hideNext(tableId);
                                            else
                                            {
                                                showNext(tableId);
                                                json.data;
                                            }

                                            return json.data;
                                        }
                                        else return [];
                                    }
                    },

                    sort,
                    order: [],
                    columns: columns,
    }

    if (Array.isArray(order)) options.order = order;
    else
    {
        if (order >= 0)
        {
            options.order = [
                [order, 'asc']
            ]
        }
    }

    $table = $(tableId).DataTable(options);

    return $table;

    function hideNext ()
    {
        setTimeout(
            function ()
            {
                $(`${tableId}_next`).hide();
            }, 0);
    }

    function showNext ()
    {
        setTimeout(
            function ()
            {
                $(`${tableId}_next`).show();
            }, 0);
    }
}

function validasiForm (form)
{
    const $form = $(form);
    let valid = true;

    $form.find('[required]:not([multiple])').each(function ()
    {
        const $field = $(this);
        const val = $field.val();
        const tag = $field.prop('tagName').toLowerCase();
        const type = tag == 'input' ? $field.attr('type').toLowerCase() : null;
        const name = $field.attr('name');

        if (type === 'checkbox' || type === 'radio')
        {
            if (!$(`[name="${name}"]:checked`).length) valid = false;
            else $field.addClass('border-danger');
        }
        else if (!$field.val())
        {
            valid = false;

            $field.addClass('border-danger');
        }
        else $field.removeClass('border-danger');
    });

    $form.find('[required][multiple]').each(function ()
    {
        const $field = $(this);
        const val = $field.val();
        const tag = $field.prop('tagName').toLowerCase();
        const type = tag == 'input' ? $field.attr('type').toLowerCase() : null;

        if (tag === 'select' && !$field.val().length)
        {
            valid = false;

            $field.addClass('border-danger');
        }
        else if (type === 'checkbox')
        {
            const $checks = $(`[name="${name}"]`);
            let passed = false;

            if ($(`[name="${name}"]:checked`).length) passed = true;
            else valid = false;

            $checks.each(function ()
            {
                const $check = $(this);

                passed === true ? $check.removeClass('border-danger') : $check.addClass('border-danger');
            })
        }
        else $field.removeClass('border-danger');
    });

    if (!valid)
        swalShowMessage('Inputan Belum Lengkap', 'Harap mengisi inputan yang wajib diisi', 'warning');

    return valid;
}

function validatePassword (password)
{
    const outputMessage = {
        ok: true,
        message: null,
    }

    if (password.length < 6)
    {
        outputMessage.ok = false;
        outputMessage.message = "Minimal harus mempunyai 6 karakter.";

        return outputMessage;
    }

    /*if (!/\d/.test(password))
    {
        outputMessage.ok = false;
        outputMessage.message = "Minimal harus mempunyai 1 angka.";

        return outputMessage;
    }*/

    return outputMessage;
}

function openModal (idmodal = 'myModal', tpl, param = null, html = false)
{
    if ( tpl )
    {
        if ( !html )
        {
            tpl = eval(tpl);
            tpl = tpl.innerHTML.trim();

            if ( param && typeof(param) == 'object')
            {
                tpl = $.parseHTML(tpl);

                $.each(param, function (k, v)
                {
                    if ( $(tpl).find('#' + k).val() !== undefined )
                        $(tpl).find('#' + k).attr({value: v});
                });

                tpl = tpl[0].outerHTML;
            }
        }

        /* set modal content dari template*/
        $('#' + idmodal).find('.modal-content').html(tpl);

        /* show modal */
        $('#' + idmodal).modal('show');
    }
    else
    {
        swalShowMessage('Info', 'Template cannot be null', 'warning');
        return false;
    }
}

function closeModal (idmodal = 'myModal')
{
    setTimeout(function ()
    {
        $('#' + idmodal).modal('hide')

        $("body.modal-open").removeAttr("style")

        $("body").removeClass("modal-open")

    }, 111);
}

function FormatMoney ()
{
    $('.currency').each(function ()
    {
        let $nilai = $(this).val()
        let $precision = $(this).attr('data-precision')

        if ( $precision === undefined ) $precision = 2

        $(this).val(accounting.formatMoney($nilai, "", $precision))
    });
}

function ResetMoney ()
{
    $('.currency').each(function ()
    {
        let $nilai = $(this).val();

        $(this).val(accounting.unformat($nilai));
    });
}

function SetFormatMoney (obj, precision = 2)
{
    $(obj).val(accounting.formatMoney($(obj).val(), "", precision));
}

function ResetFormat (obj)
{
    $(obj).val(accounting.unformat($(obj).val()));
}

function MoneyFormat (val, precision = 2)
{
    let $ret = accounting.formatMoney(val, "", precision);

    return $ret;
}
function ResetFormatVal (val)
{
    let $ret = accounting.unformat(val);

    return $ret;
}

function parsePhone (phone, addZero = false) 
{
    if (phone.slice(0, 3) === '+62')
        phone = phone.replace('+62', '')
    else if (phone.slice(0, 2) === '62')
        phone = phone.replace('62', '')
    else if (phone.slice(0, 2) === '08')
        phone = phone.replace('08', '8')
    else
        phone = phone

    phone = $.trim(phone.replace(/-/g, ''))

    if (addZero) phone = '0' + phone;

    return phone
}

$(document).on(
{
    focus       : function ()
                {
                    // Handle focus...
                    ResetFormat(this);
                },

    paste       : function (event)
                {
                    // Handle paste...
                    if (event.originalEvent.clipboardData.getData('text').match(/[^\d]/))
                        event.preventDefault();
                },

    keypress    : function (event)
                {
                    // Handle keypress...
                    var charCode = (event.which) ? event.which : event.keyCode;

                    if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
                        event.preventDefault();
                    else if ( charCode == 46 )
                    {
                        if ( this.value.toString().includes('.') )
                            event.preventDefault();
                    }
                    else
                    {
                        if ( this.value.toString().includes('.') )
                        {
                            var x = this.value.split('.')

                            if ( x[1].length > 2 )
                                this.value = this.value.slice(0, -1)
                        }
                    }
                },

    blur       : function ()
                {
                    // Handle blur...
                    let $precision = $(this).attr('data-precision')

                    if ( $precision === undefined ) $precision = 2

                    SetFormatMoney(this, $precision);
                },
}, '.currency');

$(document).on(
{
    paste       : function (event)
                {
                    // Handle paste...
                    if (event.originalEvent.clipboardData.getData('text').match(/[^\d]/))
                        event.preventDefault();
                },

    keypress    : function (event)
                {
                    // Handle keypress...
                    var charCode = (event.which) ? event.which : event.keyCode;

                    if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
                        event.preventDefault();
                    else if ( charCode == 46 )
                    {
                        if ( this.value.toString().includes('.') )
                            event.preventDefault();
                    }
                    else
                    {
                        if ( this.value.toString().includes('.') )
                        {
                            x = this.value.split('.')

                            if ( x[1].length > 2 )
                                this.value = this.value.slice(0, -1)
                        }
                    }
                }
}, '.number-only');

// Fix dropdown tenggelam dalam datatable.
// $('body').on('show.bs.dropdown', '.dataTable [data-bs-toggle="dropdown"]', function ()
/*$(document).on('show.bs.dropdown', '.dataTable [data-bs-toggle="dropdown"]', function ()
{
    $(this).css("overflow", "visible")
}).on('hide.bs.dropdown', '.dataTable [data-bs-toggle="dropdown"]', function ()
{
    $(this).css("overflow", "auto")
})
*/
/*$('.dataTable [data-bs-toggle="dropdown"]').on('show.bs.dropdown', function ()
{
    $(this).css("overflow", "visible")
}).on('hide.bs.dropdown', function ()
{
    $(this).css("overflow", "auto")
})*/

// Fix Tooltip nyangkut ketika button di klik / buka modal.
$(document).on('click', '.dataTable [data-bs-toggle="tooltip"]', function ()
{
    const tooltip = bootstrap.Tooltip.getInstance(this)

    tooltip.hide()
})

function GetAlfabet (itemType) /*{{{*/
{
    let arrAlfabet = [];

    arrAlfabet[0] = "A";
    arrAlfabet[1] = "B";
    arrAlfabet[2] = "C";
    arrAlfabet[3] = "D";
    arrAlfabet[4] = "E";
    arrAlfabet[5] = "F";
    arrAlfabet[6] = "G";
    arrAlfabet[7] = "H";
    arrAlfabet[8] = "I";
    arrAlfabet[9] = "J";
    arrAlfabet[10] = "K";
    arrAlfabet[11] = "L";
    arrAlfabet[12] = "M";
    arrAlfabet[13] = "N";
    arrAlfabet[14] = "O";
    arrAlfabet[15] = "P";
    arrAlfabet[16] = "Q";
    arrAlfabet[17] = "R";
    arrAlfabet[18] = "S";
    arrAlfabet[19] = "T";
    arrAlfabet[20] = "U";
    arrAlfabet[21] = "V";
    arrAlfabet[22] = "W";
    arrAlfabet[23] = "X";
    arrAlfabet[24] = "Y";
    arrAlfabet[25] = "Z";

    let Alfabet = arrAlfabet[itemType] ? arrAlfabet[itemType] : 'Tidak Ditemukan';

    return Alfabet;
}

function showLoading ()
{
    Swal.fire({
        title: 'Silakan Tunggu ...',
        showConfirmButton: false,
        allowOutsideClick: false,

        didOpen: () => {
            Swal.showLoading()
        },
    });
}

function swalShowMessage (title, message, icon)
{
    return Swal.fire({
        title: title,
        html: message,
        icon: icon
    })
}

function getDatesBetween (startDate, endDate)
{
    var startDate = new Date(startDate)
    var endDate = new Date(endDate)

    let dates = []
    let currentDate = new Date(startDate)

    while (currentDate <= endDate)
    {
        dates.push(new Date(currentDate))
        currentDate.setDate(currentDate.getDate() + 1)
    }

    return dates
}

function exportExcel ({ name, url, params })
{
    return new Promise((resolve, reject) => {
        $.ajax({
            url,
            data: params || null,
            xhrFields: {
                responseType: 'blob'
            },
            success: function (res) {
                const blob = new Blob([res])
                const url = window.URL.createObjectURL(blob)
                const link = document.createElement('a')

                link.href = url
                link.setAttribute('download', name)

                document.body.appendChild(link)

                link.click()
                link.remove()

                resolve()
            },
            error: function (err) {
                swalShowMessage('Error!', err?.responseJSON?.message || 'Failed to create report.', 'error')

                reject(err)
            },
        })
    })
}