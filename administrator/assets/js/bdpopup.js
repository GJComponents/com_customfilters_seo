/**
 * Usefull for forms as it does not open a new window and what is set here can
 * be submited with the rest form
 *
 * @author Sakis Terzis
 * @license GNU/GPL v.2
 * @copyright Copyright (C) 2021 breakDesigns.net. All rights reserved
 */

function displayPopup(id) {
    let showModalButton = 'show_popup' + id;
    let hideTags = 'hide_popup' + id;
    let closeBtn = 'close_btn' + id;
    let closeElements = [document.getElementById(hideTags), document.getElementById(closeBtn)];

    document.getElementById(showModalButton).addEventListener('click', function () {
        closeOpenPopups();
        document.getElementById('window' + id).classList.remove('cfhide');
    });

    showSettingsByDisplayType(id);

    if (closeElements.length > 0) {
        Array.each(closeElements, function (e) {
            if (e != null) {
                e.addEvent('click', function () {
                    var elname = 'window' + id;
                    document.getElementById(elname).classList.add('cfhide');
                });
            }
        });
    }
}

function showSettingsByDisplayType(id) {
    // hide irrelevant settings
    let selected_val = document.getElementById('type_id' + id).getElement(':selected').value;

    //more than 1 selected
    if (selected_val.indexOf(',')) {
        selected_val = selected_val.split(',');
    } else {
        selected_val = [selected_val];
    }
    let adv_settings_window = document.getElementById('window' + id);

    if (adv_settings_window) {
        let setting_rows = adv_settings_window.querySelectorAll('li');
        setting_rows.forEach(function (row) {
            selected_val.forEach(function (selected) {
                var row_class = row.getAttribute('class');
                if (row_class) {
                    if (row_class.indexOf('setting') > -1 && row_class.indexOf('setting' + selected) == -1) {
                        row.classList.add('cfhide');
                    } else {
                        row.classList.remove('cfhide');
                    }
                }
            });
        });
    }
}

function closeOpenPopups() {
    let windows = document.querySelectorAll('.bdpopup');
    for (var i = 0; i < windows.length; i++) {
        windows[i].classList.add('cfhide');
    }
}
