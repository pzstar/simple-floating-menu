/**
 * The menu list.
 *
 * Two fixes to where WordPress puts things on this screen: the add panel is
 * printed through admin_notices, which lands it above the page heading, and
 * the Add New button beside that heading goes to the post editor, which is
 * not where one of these menus is made.
 */
(function () {
    'use strict';

    function ready(fn) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', fn);
        } else {
            fn();
        }
    }

    ready(function () {
        var panel = document.querySelector('.sfm-add');

        if (!panel) {
            return;
        }

        /* Move it under the heading, where WordPress moves its own notices,
           rather than leaving it stranded above the page title. */
        var anchor = document.querySelector('.wrap .wp-header-end');

        if (anchor && anchor.parentNode) {
            anchor.parentNode.insertBefore(panel, anchor.nextSibling);
        }

        var name = panel.querySelector('#sfm-new-name');

        function openPanel() {
            panel.open = true;
            panel.scrollIntoView({ block: 'nearest' });

            if (name) {
                name.focus();
            }
        }

        /* Add New belongs to the post editor, which this type does not use.
           Pointed at the panel that is already on the screen instead. */
        var addNew = document.querySelector('.wrap .page-title-action');

        if (addNew) {
            addNew.setAttribute('href', '#sfm-add-panel');

            addNew.addEventListener('click', function (event) {
                event.preventDefault();
                openPanel();
            });
        }

        /* Arriving with the panel asked for, from anywhere that still links
           to the old address. */
        if (window.location.search.indexOf('sfm_add=1') !== -1) {
            openPanel();
        }

        dropZone(panel);
    });

    /* The import drop zone.
     *
     * Dropping and browsing both work without any of this: the file input
     * covers the whole box, so the browser handles them on its own. What this
     * adds is reading the file before it is sent, so an export can be named and
     * counted on the screen, and a file that is not one can be turned away here
     * rather than after a round trip. */
    function dropZone(panel) {
        var strings = window.sfmList || {};
        var drop = panel.querySelector('.sfm-drop');

        if (!drop) {
            return;
        }

        var input = panel.querySelector('.sfm-drop-input');
        var idle = panel.querySelector('.sfm-drop-idle');
        var file = panel.querySelector('.sfm-drop-file');
        var error = panel.querySelector('.sfm-drop-error');
        var submit = panel.querySelector('.sfm-import-submit');

        /* Only disabled once the script is running, so with no script the form
           still submits and the server still decides. */
        submit.disabled = true;

        function clear() {
            idle.hidden = false;
            file.hidden = true;
            error.hidden = true;
            error.textContent = '';
            drop.classList.remove('is-loaded', 'is-invalid');
            submit.disabled = true;
        }

        function reject(message) {
            clear();
            drop.classList.add('is-invalid');
            error.hidden = false;
            error.textContent = message;
        }

        /* A list can arrive as an object rather than an array, because that is
           what PHP encodes a gappy array as. */
        function entries(value) {
            if (Array.isArray(value)) {
                return value;
            }

            if (value && typeof value === 'object') {
                return Object.keys(value).map(function (key) { return value[key]; });
            }

            return null;
        }

        function describe(data) {
            if (!data || typeof data !== 'object' || Array.isArray(data)) {
                return null;
            }

            if (data.format !== strings.format) {
                return null;
            }

            var buttons = entries(data.buttons);

            if (!buttons) {
                return null;
            }

            return {
                title: data.title || strings.untitled,
                buttons: buttons.length
            };
        }

        function countLabel(n) {
            if (n === 0) {
                return strings.noButtons;
            }

            if (n === 1) {
                return strings.buttonOne;
            }

            return String(strings.buttonMany).replace('%d', n);
        }

        function show(info) {
            error.hidden = true;
            error.textContent = '';
            idle.hidden = true;
            file.hidden = false;
            drop.classList.remove('is-invalid');
            drop.classList.add('is-loaded');

            /* Text, never markup: the title comes out of an uploaded file. */
            file.querySelector('.sfm-drop-file-title').textContent = info.title;
            file.querySelector('.sfm-drop-file-meta').textContent = countLabel(info.buttons);

            submit.disabled = false;
        }

        input.addEventListener('change', function () {
            var chosen = this.files && this.files[0];

            if (!chosen) {
                clear();
                return;
            }

            /* wp_localize_script stringifies everything it is given, so the
               limit is read back as a number rather than compared as one. */
            if (chosen.size > parseInt(strings.maxBytes, 10)) {
                reject(String(strings.tooBig).replace('%s', strings.sizeLimit));
                return;
            }

            var reader = new FileReader();

            reader.onerror = function () {
                reject(strings.unreadable);
            };

            reader.onload = function (event) {
                var data;

                try {
                    data = JSON.parse(event.target.result);
                } catch (e) {
                    reject(strings.notJson);
                    return;
                }

                var info = describe(data);

                if (!info) {
                    reject(strings.notRecognised);
                    return;
                }

                show(info);
            };

            reader.readAsText(chosen);
        });

        panel.addEventListener('click', function (event) {
            if (!event.target.closest('.sfm-drop-clear')) {
                return;
            }

            event.preventDefault();

            /* Resetting through a throwaway form is the one way to empty a file
               input that every browser agrees on. */
            var holder = document.createElement('form');
            input.parentNode.insertBefore(holder, input);
            holder.appendChild(input);
            holder.reset();
            holder.parentNode.insertBefore(input, holder);
            holder.parentNode.removeChild(holder);

            clear();
        });

        ['dragenter', 'dragover'].forEach(function (name) {
            drop.addEventListener(name, function (event) {
                event.preventDefault();
                drop.classList.add('is-dragging');
            });
        });

        ['dragleave', 'drop'].forEach(function (name) {
            drop.addEventListener(name, function () {
                drop.classList.remove('is-dragging');
            });
        });
    }
}());
