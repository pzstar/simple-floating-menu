/**
 * The menu builder.
 *
 * The list on the left and the editors on the right are two views of the same
 * buttons: selecting a row shows that button's editor, and typing in an editor
 * updates the row it belongs to.
 */
(function ($) {
    'use strict';

    $(function () {
        var $form = $('#sfm-builder-form');

        if (!$form.length) {
            return;
        }

        var $list = $('#sfm-list');
        var $detail = $('#sfm-detail');
        var counter = $list.children('.sfm-row').length;
        var dirty = false;

        function markDirty() {
            dirty = true;
        }

        function editorFor($row) {
            return $detail.children('.sfm-editor[data-index="' + $row.attr('data-index') + '"]');
        }

        /** Shows one button's settings and marks its row as the chosen one. */
        function select($row) {
            if (!$row || !$row.length) {
                $detail.children('.sfm-editor').attr('hidden', true);
                $detail.children('.sfm-detail-empty').show();
                return;
            }

            $list.children('.sfm-row').removeClass('is-selected');
            $row.addClass('is-selected');

            $detail.children('.sfm-editor').attr('hidden', true);
            $detail.children('.sfm-detail-empty').hide();
            editorFor($row).removeAttr('hidden');
        }

        /** Keeps a row's label, address and icon in step with its editor. */
        function sync($editor) {
            var index = $editor.attr('data-index');
            var $row = $list.children('.sfm-row[data-index="' + index + '"]');

            if (!$row.length) {
                return;
            }

            var label = $editor.find('.sfm-input-label').val();
            var tooltip = $editor.find('.sfm-input-tooltip').val();
            var url = $editor.find('.sfm-input-url').val();
            var icon = $editor.find('.sfm-item-icon-value').val();

            /* The label names it; the tooltip stands in while it is empty. */
            var name = label || tooltip || sfmBuilder.untitled;

            $row.find('.sfm-row-label').text(name);
            $row.find('.sfm-row-url').text(url || '');
            /* The glyph is the <i> inside each holder, not the holder. */
            $row.find('.sfm-row-icon i').attr('class', icon || '');
            $editor.find('.sfm-editor-icon i').attr('class', icon || '');
            $editor.find('.sfm-item-icon-preview i').attr('class', icon || '');

            $editor.find('.sfm-editor-title').text(name);
        }

        function colorPickers($scope) {
            if (!$.fn.wpColorPicker) {
                return;
            }

            $scope.find('.sfm-color').not('.wp-color-picker').wpColorPicker({
                change: function () {
                    markDirty();
                },
                clear: markDirty
            });
        }

        /* ---- adding and removing ------------------------------------- */

        function addButton(label, url) {
            var index = counter++;
            var row = $('#tmpl-sfm-row').html().split('__i__').join(index);
            var editor = $('#tmpl-sfm-editor').html().split('__i__').join(index);

            var $row = $(row).appendTo($list);
            var $editor = $(editor).appendTo($detail);

            /* Every button needs an id of its own: the id is what its colours
               are keyed to in the generated CSS. */
            $row.find('.sfm-row-id').val('sfm-' + Date.now().toString(36) + index);

            if (label) {
                $editor.find('.sfm-input-label').val(label);
            }

            if (url) {
                $editor.find('.sfm-input-url').val(url);
            }

            $list.siblings('.sfm-list-empty').attr('hidden', true);
            applyAction($editor);
            colorPickers($editor);
            select($row);
            sync($editor);
            markDirty();

            return $editor;
        }

        $form.on('click', '.sfm-add-button', function () {
            addButton('', '').find('.sfm-input-label').trigger('focus');
        });

        /* Picking a page fills a new button in with its title and address, so
           the common case takes one choice instead of two fields. */
        $form.on('change', '.sfm-picker', function () {
            var $picker = $(this);
            var url = $picker.val();

            if (!url) {
                return;
            }

            addButton($picker.find('option:selected').text(), url);

            $picker.val('');

            if ($picker.data('chosen')) {
                $picker.trigger('chosen:updated');
            }
        });

        $form.on('click', '.sfm-row-remove', function (e) {
            e.stopPropagation();

            if (!window.confirm(sfmBuilder.confirmRemove)) {
                return;
            }

            var $row = $(this).closest('.sfm-row');
            var wasSelected = $row.hasClass('is-selected');

            editorFor($row).remove();
            $row.remove();

            if (!$list.children('.sfm-row').length) {
                $list.siblings('.sfm-list-empty').removeAttr('hidden');
            }

            if (wasSelected) {
                select($list.children('.sfm-row').first());
            }

            markDirty();
        });

        /* ---- selecting and editing ----------------------------------- */

        $form.on('click', '.sfm-row-open', function () {
            select($(this).closest('.sfm-row'));
        });

        $form.on('input change', '.sfm-editor input, .sfm-editor select', function () {
            /* The icon picker lives inside the editor while it is open, but
               searching it is browsing, not editing. */
            if ($(this).closest('.sfm-icon-picker').length) {
                return;
            }

            sync($(this).closest('.sfm-editor'));
            markDirty();
        });

        /* ---- what the placement makes possible -------------------------- */

        /* An offset only means something on the edge it is measured from, and
           only a corner has room to run either way. */
        function applyPosition() {
            var position = $('#sfm-position').val();

            $('[data-when-position]').each(function () {
                var allowed = $(this).attr('data-when-position').split(',');

                $(this).prop('hidden', $.inArray(position, allowed) === -1);
            });
        }

        $form.on('change', '#sfm-position', applyPosition);

        /* One size control stands for both axes, as it does in premium, so the
           width follows the moment somebody sets a size. */
        $form.on('input', '#sfm-button_height', function () {
            $('#sfm-button_width').val($(this).val());
        });

        /* ---- what a button does --------------------------------------- */

        /** Shows only the fields the chosen action actually uses. */
        function applyAction($editor) {
            var action = $editor.find('.sfm-input-action').val() || 'default';

            $editor.find('[data-when-action]').each(function () {
                $(this).prop('hidden', $(this).attr('data-when-action') !== action);
            });
        }

        $form.on('change', '.sfm-input-action', function () {
            applyAction($(this).closest('.sfm-editor'));
            markDirty();
        });

        /* ---- the icon chooser ---------------------------------------- */

        /* One picker for the whole screen, built the first time it is asked
           for and then moved into whichever field wants it. Building the five
           libraries costs enough that it is not worth doing per button, and
           never at all for somebody who does not open it. */

        var $picker = null;
        var $pickerField = null;

        function buildPicker() {
            var libraries = sfmBuilder.libraries || {};
            var html = '<div class="sfm-icon-picker">';

            html += '<select class="sfm-icon-library">';

            $.each(libraries, function (slug, library) {
                html += '<option value="' + slug + '">' + library.name + '</option>';
            });

            html += '</select>';
            html += '<input type="search" class="sfm-icon-search" placeholder="' + sfmBuilder.searchIcons + '"/>';

            $.each(libraries, function (slug, library) {
                html += '<ul class="sfm-icon-list" data-library="' + slug + '">';

                $.each(library.icons, function (index, icon) {
                    html += '<li><i class="' + icon + '" data-icon="' + icon + '" title="' + icon + '"></i></li>';
                });

                html += '</ul>';
            });

            html += '<p class="sfm-icon-empty" hidden>' + sfmBuilder.noIcons + '</p>';

            return $(html + '</div>');
        }

        function closePicker() {
            if ($picker) {
                $picker.hide();
            }

            $pickerField = null;
        }

        function markCurrent() {
            if (!$picker || !$pickerField) {
                return;
            }

            var current = $pickerField.find('.sfm-item-icon-value').val();

            $picker.find('.sfm-icon-list i')
                .removeClass('is-current')
                .filter('[data-icon="' + current + '"]')
                .addClass('is-current');
        }

        $form.on('click', '.sfm-item-icon-choose', function (event) {
            event.stopPropagation();

            var $field = $(this).closest('.sfm-item-icon');

            if (!$picker) {
                $picker = buildPicker();
                $picker.find('.sfm-icon-list').hide().first().show();
            }

            /* Clicking the same button again closes it. */
            if ($pickerField && $pickerField.is($field)) {
                closePicker();
                return;
            }

            $pickerField = $field;
            $field.append($picker);
            $picker.show();

            markCurrent();

            $picker.find('.sfm-icon-search').val('').trigger('input');
            $picker.find('.sfm-icon-search').trigger('focus');
        });

        /* Clicks inside the picker are browsing, not dismissing. */
        $form.on('click', '.sfm-icon-picker', function (event) {
            event.stopPropagation();
        });

        $(document).on('click', closePicker);

        $form.on('change', '.sfm-icon-library', function () {
            var slug = $(this).val();

            $picker.find('.sfm-icon-list').hide().filter('[data-library="' + slug + '"]').show();
            $picker.find('.sfm-icon-search').val('').trigger('input');
        });

        $form.on('input', '.sfm-icon-search', function () {
            var term = ($(this).val() || '').toLowerCase();
            var shown = 0;

            $picker.find('.sfm-icon-list:visible li').each(function () {
                var icon = $(this).children('i').attr('data-icon') || '';
                var hit = icon.toLowerCase().indexOf(term) !== -1;

                $(this).toggle(hit);

                if (hit) {
                    shown++;
                }
            });

            /* Said out loud rather than by showing an empty box. */
            $picker.find('.sfm-icon-empty').prop('hidden', shown !== 0);
        });

        /* Enter in the icon search would otherwise submit the whole menu. */
        $form.on('keydown', '.sfm-icon-search', function (event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                return;
            }

            if (event.key === 'Escape') {
                closePicker();
            }
        });

        $form.on('click', '.sfm-icon-list i', function () {
            var icon = $(this).attr('data-icon');

            if ($pickerField) {
                /* Read before the picker moves, which detaches what was clicked. */
                var $editor = $pickerField.closest('.sfm-editor');

                $pickerField.find('.sfm-item-icon-value').val(icon);
                sync($editor);
                markDirty();
            }

            closePicker();
        });

        /* ---- reordering ---------------------------------------------- */

        if ($.fn.sortable) {
            $list.sortable({
                handle: '.sfm-row-grip',
                axis: 'y',
                update: markDirty
            });
        }

        /* ---- leaving ------------------------------------------------- */

        $form.on('submit', function () {
            dirty = false;
        });

        $form.on('click', '.sfm-trash', function () {
            dirty = false;
        });

        $(window).on('beforeunload', function () {
            if (dirty) {
                return sfmBuilder.confirmLeave;
            }
        });

        /* ---- design modal --------------------------------------------- */

        var $modal = $('#sfm-mega-menu');
        var $trigger = $('#sfm-design-trigger');
        var lastFocus = null;

        function openModal() {
            lastFocus = document.activeElement;
            $modal.prop('hidden', false);
            $modal.find('.sfm-tab.active').trigger('focus');
        }

        function closeModal() {
            $modal.prop('hidden', true);

            if (lastFocus) {
                $(lastFocus).trigger('focus');
            }
        }

        $trigger.on('click', openModal);

        /* The saved notice: slides in, then takes itself away. */
        var alertTimer = null;

        function showAlert(message, kind) {
            var $alert = $('.sfm-alert');

            if (!$alert.length) {
                return;
            }

            window.clearTimeout(alertTimer);

            $alert.find('.sfm-alert-message').text(message);
            $alert.removeClass('sfm-alert-success sfm-alert-warning')
                  .addClass('sfm-alert-' + kind)
                  .addClass('sfm-alert-active');

            alertTimer = window.setTimeout(function () {
                $alert.removeClass('sfm-alert-active sfm-alert-success sfm-alert-warning');
            }, 3500);
        }

        $(document).on('click', '.sfm-alert-close', function () {
            window.clearTimeout(alertTimer);
            $('.sfm-alert').removeClass('sfm-alert-active sfm-alert-success sfm-alert-warning');
        });

        /* Premium saves the design without leaving the page, so this sends the
           panels' own fields and leaves the rest of the builder alone. */
        $form.on('click', '#sfm-design-save', function () {
            var $button = $(this);
            var $spinner = $modal.find('.sfm-toolbar .spinner');

            if ($button.prop('disabled')) {
                return;
            }

            $button.prop('disabled', true);
            $spinner.addClass('is-active');

            $.post(window.ajaxurl, {
                action: 'sfm_standalone_save_design',
                nonce: sfmBuilder.designNonce,
                menu: sfmBuilder.menu,
                data: $modal.find('.sfm-content :input').serialize()
            }).done(function (res) {
                if (!res || !res.success) {
                    showAlert(sfmBuilder.designFailed, 'warning');
                    return;
                }

                /* The card outside the modal summarises what was just saved. */
                var $facts = $('.sfm-design-facts');

                if ($facts.length && res.data && res.data.facts) {
                    $facts.empty();

                    $.each(res.data.facts, function (i, fact) {
                        $facts.append($('<span/>', { 'class': 'sfm-design-fact', text: fact }));
                    });
                }

                /* The panels stay open, the way premium leaves them, so more
                   than one can be worked through without reopening. */
                showAlert(sfmBuilder.designSaved, 'success');
            }).fail(function () {
                showAlert(sfmBuilder.designFailed, 'warning');
            }).always(function () {
                $button.prop('disabled', false);
                $spinner.removeClass('is-active');
            });
        });
        $('#sfm-design-close, #sfm-design-done').on('click', closeModal);

        $modal.on('click', '.sfm-modal-backdrop', closeModal);

        $(document).on('keydown', function (event) {
            if (event.key === 'Escape' && !$modal.prop('hidden')) {
                closeModal();
            }
        });

        /* The design fields live inside the form, so they save with everything
           else; the card only summarises what they are set to. */
        $modal.on('change', 'select, input', function () {
            markDirty();
        });

        /* ---- settings tabs -------------------------------------------- */

        var $tabs = $('.sfm-menu .sfm-tab');

        $tabs.on('click', function (event) {
            event.preventDefault();

            var name = $(this).data('tab');

            $tabs.removeClass('active');
            $(this).addClass('active');

            /* The bar names the panel that is open, as premium's does. */
            $('#sfm-modal-title').text($(this).attr('data-title'));

            $('.sfm-panel').each(function () {
                var match = $(this).data('panel') === name;

                $(this).toggleClass('active', match).prop('hidden', !match);
            });
        });

        /* ---- start --------------------------------------------------- */

        /* Without Chosen the picker is still a working select, so this only
           ever improves it. */
        if ($.fn.chosen) {
            /* Named apart from the icon picker: both live in this one scope,
               and a var declaration is hoisted to the top of it. */
            var $contentPicker = $form.find('.sfm-picker');

            /* The first option carries a readable label for the case where this
               script never runs. Chosen shows its placeholder only while
               nothing is chosen, and a first option with text counts as a
               choice, so the text comes off before it is handed over. */
            $contentPicker.find('option[value=""]').first().text('');

            $contentPicker.chosen({
                width: '100%',
                placeholder_text_single: sfmBuilder.searchContent,
                search_contains: true
            });
        }

        colorPickers($detail);
        colorPickers($modal);

        /* The typography controls, set up the way premium sets them up: a
           search box on each select, and a slider beside each number. */
        if ($.fn.chosen) {
            $form.find('.sfm-typography-input-field > select').chosen({ width: '100%' });
        }

        if ($.fn.slider) {
            $form.find('.sfm-range-input-selector').each(function () {
                var $input = $(this);

                $input.prev('.sfm-range-slider').slider({
                    value: parseFloat($input.val()) || 0,
                    min: parseFloat($input.attr('min')),
                    max: parseFloat($input.attr('max')),
                    step: parseFloat($input.attr('step')),
                    range: 'min',
                    slide: function (event, ui) {
                        $(this).next().val(ui.value).trigger('change');
                    }
                });
            });

            /* Typing a number straight in moves the slider to match, kept
               inside the range it allows. */
            $form.on('blur', '.sfm-range-input-selector', function () {
                var $input = $(this);
                var value = parseFloat($input.val());
                var min = parseFloat($input.attr('min'));
                var max = parseFloat($input.attr('max'));

                if (isNaN(value)) {
                    return;
                }

                value = Math.min(Math.max(value, min), max);

                $input.val(value);
                $input.prev('.sfm-range-slider').slider('value', value);
            });
        }

        $detail.children('.sfm-editor').each(function () {
            applyAction($(this));
        });

        applyPosition();
        select($list.children('.sfm-row').first());

        /* Setting the fields up counts as a change to the browser, so the
           unsaved warning only starts once the page has settled. */
        dirty = false;
    });
}(jQuery));
