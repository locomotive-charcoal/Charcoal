/**
 * Charcoal Tabulator Handler
 */

var Tabulator = Tabulator || {};

;(function (Charcoal, $, document, Tabulator) {
    'use strict';

    /**
     * Tabulator Input Property
     *
     * charcoal/admin/property/input/tabulator
     *
     * Require:
     * - tabulator
     *
     * @param {Object} opts - Options for input property.
     */
    Charcoal.Admin.Property_Input_Tabulator = function (opts) {
        this.input_type = 'charcoal/admin/property/input/tabulator';

        Charcoal.Admin.Property.call(this, opts);

        this.input_id = null;
        this.tabulator_selector = null;
        this.tabulator_element = null;
        this.tabulator_options = null;
        this.tabulator_instance = null;
        this.min_rows = 0;
        this.max_rows = null;

        this.set_properties(opts).create_tabulator().set_events()
    };
    Charcoal.Admin.Property_Input_Tabulator.prototype = Object.create(Charcoal.Admin.Property.prototype);
    Charcoal.Admin.Property_Input_Tabulator.prototype.constructor = Charcoal.Admin.Property_Input_Tabulator;
    Charcoal.Admin.Property_Input_Tabulator.prototype.parent = Charcoal.Admin.Property.prototype;

    // Create the Tabulator instance
    Charcoal.Admin.Property_Input_Tabulator.prototype.create_tabulator = function () {
        this.tabulator_instance = new Tabulator(this.tabulator_selector + '-tabulator', this.tabulator_options);
        return this;
    };

    // Set the tabulator properties.
    Charcoal.Admin.Property_Input_Tabulator.prototype.set_properties = function (opts) {
        this.input_id = opts.id || this.input_id;
        this.tabulator_selector   = opts.data.tabulator_selector || this.tabulator_selector;
        this.tabulator_element    = opts.data.tabulator_element || this.tabulator_element;
        this.tabulator_options    = opts.data.tabulator_options || this.tabulator_options;
        this.tabulator_columns = opts.data.tabulator_columns || this.tabulator_columns;
        this.min_rows = opts.data.multiple_options.min || this.min_rows;
        this.max_rows = opts.data.multiple_options.max || this.max_rows;
        this.feedback_selector = '#' + this.input_id + '-tabulator-feedback';

        if (!this.tabulator_element && this.tabulator_selector) {
            this.tabulator_element = document.querySelector(this.tabulator_selector);
        }

        if (!this.tabulator_element) {
            return;
        }

        var that = this;

        // Setup the columns
        var columns = this.tabulator_columns.map(function (column) {
            // Remove the settings property because it is not recognised by Tabulator
            var cleanColumn = {}
            for (var key in column) {
                if (key !== 'options') {
                    cleanColumn[key] = column[key]
                }
            }
            return cleanColumn
        });

        // Add re-order handle
        if (this.tabulator_options.allow_reorder || this.tabulator_options.show_row_number) {
            var formatter = 'handle'
            var cssClasses = []

            if (this.tabulator_options.show_row_number) {
                formatter = 'rownum'
                cssClasses.push('row-num');
            }

            if (this.tabulator_options.allow_reorder) {
                cssClasses.push('reorder');
            }

            this.tabulator_options.movableRows = this.tabulator_options.allow_reorder;
            columns.unshift({
                formatter: formatter,
                headerSort: false,
                frozen: true,
                width: 40,
                minWidth: 30,
                rowHandle: true,
                resizable: false,
                hozAlign: 'center',
                cssClass: cssClasses.join(' '),
            })
        }

        // Row Actions
        var actionsColumns = {
            frozen: false,
            visible: true,
            width: 20,
            minWidth: 20,
            headerSort: false,
            rowHandle: false,
            resizable: false,
            hozAlign: 'center',
            cssClass: 'row-actions',
            columns: [],
        };

        if (this.tabulator_options.allow_add) {
            columns.push({
                cssClass: 'row-action add-row',
                visible: true,
                width: 10,
                headerSort: false,
                rowHandle: false,
                resizable: false,
                hozAlign: 'center',
                formatter: function () {
                    return '<div class="tabulator-action-button add-row"><i class="fa fa-plus-circle"></i></div>';
                },
                cellClick: function (e, cell) {
                    that.add_row(true, cell.getRow())
                }
            })
        }

        if (this.tabulator_options.allow_remove) {
            columns.push({
                cssClass: 'row-action remove-row',
                visible: !this.tabulator_options.allow_add,
                width: 10,
                headerSort: false,
                rowHandle: false,
                resizable: false,
                hozAlign: 'center',
                formatter: function () {
                    return '<div class="tabulator-action-button"><i class="fa fa-minus-circle"></i></div>';
                },
                cellClick: function (e, cell) {
                    that.remove_row(cell.getRow())
                }
            })
        }

        if (actionsColumns.columns.length > 0) {
            columns.push(actionsColumns)
        }

        var default_opts = {
            columns: columns,
            data: that.get_data(),
            tabEndNewRow: this.new_row_data(),
            footerElement: '#' + this.input_id + '-tabulator-footer',
            placeholder: this.tabulator_options.empty_table_message,
        };

        this.tabulator_options = $.extend({}, default_opts, this.tabulator_options);

        return this;
    };

    // Events
    // =========================================================================

    Charcoal.Admin.Property_Input_Tabulator.prototype.set_events = function () {
        var that = this;

        // Handle add row event.
        $('.js-' + this.input_id + '-add').on('click', function () {
            that.add_row();
        });

        // After the table is built, redraw it instantly to hide the columns that are related to a specific language.
        this.tabulator_instance.on('tableBuilt', function () {
            that.switch_language();
            that.tabulator_instance.validate();

            // Make sure that the table has the minimum amount of rows.
            while (that.row_count() < that.min_rows) {
                that.add_row()
            }
        })

        // Prepare the data to be saved by Charcoal anytime a cell changes.
        this.tabulator_instance.on('dataChanged', function (){
            that.save_data();
            that.clear_feedback();
        });

        // Resave the data after it has been sorted
        this.tabulator_instance.on('rowMoved', function (){
            that.save_data();
            that.clear_feedback();
        });

        // Handle moving rows between tables.
        this.tabulator_instance.on('movableRowsSent', function (fromRow){
            that.remove_row(fromRow)
            that.save_data();
            that.clear_feedback();
        });

        // Handle validation errors
        this.tabulator_instance.on('validationFailed', function (cell, value, validators){
            validators.forEach(function (validator) {
                switch (validator.type) {
                    case 'required':
                        that.show_warning('This field cannot be empty');
                        break;

                    case 'unique':
                        that.show_warning('Value must be unique');
                        break;

                    case 'maxLength':
                        var limit = validator.parameters
                        that.show_warning('This field cannot contain more than ' + limit + ' characters');
                        break

                    case 'validUrl':
                        that.show_warning('This seems to be an invalid URL');
                        break

                    default:
                        console.warn('Unknown validation error', value, validators, cell)
                        break;
                }
            })
        });

        // Update the columns to display when the language switcher is toggled.
        $(document).on('switch_language.charcoal', function () {
            that.switch_language();
        });

        return this
    };

    // Actions
    // =========================================================================

    Charcoal.Admin.Property_Input_Tabulator.prototype.switch_language = function () {
        var that = this
        var currentLanguage = Charcoal.Admin.lang();
        var columns = that.tabulator_columns;

        columns.forEach(function (column) {
            var fieldName = column.field;

            if (column.options !== undefined && column.options.language) {
                if (column.options.language === currentLanguage) {
                    that.tabulator_instance.showColumn(fieldName);
                } else {
                    that.tabulator_instance.hideColumn(fieldName);
                }
            } else {
                that.tabulator_instance.showColumn(fieldName);
            }
        })

        this.tabulator_instance.redraw();

        return this;
    };

    Charcoal.Admin.Property_Input_Tabulator.prototype.add_row = function (top, index) {
        if (!this.max_rows || this.row_count() < this.max_rows) {
            this.tabulator_instance.addRow(this.new_row_data(), top, index);
            this.save_data();
        } else {
            this.show_error('You cannot add another row')
        }
    };

    Charcoal.Admin.Property_Input_Tabulator.prototype.remove_row = function (index) {
        if (this.row_count() > this.min_rows) {
            this.tabulator_instance.deleteRow(index);
            this.save_data();
        } else {
            this.show_error('The table cannot have less than ' + this.min_rows + ' row(s)')
        }
    };

    // Utils
    // =========================================================================

    Charcoal.Admin.Property_Input_Tabulator.prototype.row_count = function () {
        return JSON.parse(this.get_data()).length;
    }

    Charcoal.Admin.Property_Input_Tabulator.prototype.new_row_data = function () {
        var row = {}

        this.tabulator_columns.forEach(function (column) {
            if (column.field && column.options.default_value) {
                row[column.field] = column.options.default_value
            }
        })

        return row
    };

    Charcoal.Admin.Property_Input_Tabulator.prototype.save_data = function () {
        var data = JSON.stringify(this.tabulator_instance.getData());
        $(this.tabulator_selector).val(data);
    }

    Charcoal.Admin.Property_Input_Tabulator.prototype.get_data = function () {
        return $(this.tabulator_selector).val() || '[' + JSON.stringify(this.new_row_data()) + ']';
    }

    // Feedback
    // =========================================================================

    Charcoal.Admin.Property_Input_Tabulator.prototype.show_warning = function (message) {
        this.update_feedback(message, 'warning')
    }

    Charcoal.Admin.Property_Input_Tabulator.prototype.show_error = function (message) {
        this.update_feedback(message, 'error')
    }

    Charcoal.Admin.Property_Input_Tabulator.prototype.clear_feedback = function () {
        var $feedback = $(this.feedback_selector);
        $feedback.text('')
        $feedback.removeClass('alert-danger');
        $feedback.removeClass('alert-warning');
        $feedback.removeClass('alert-info');
    }

    Charcoal.Admin.Property_Input_Tabulator.prototype.update_feedback = function (message, type) {
        this.clear_feedback()
        var $feedback = $(this.feedback_selector);

        switch (type) {
            case 'error':
                $feedback.addClass('alert-danger')
                break;
            case 'warning':
                $feedback.addClass('alert-warning')
                break
            default:
                $feedback.addClass('alert-info')
                break;
        }

        $feedback.text(message);
    }

    // Custom Formatter
    // =========================================================================

    Tabulator.extendModule('format', 'formatters', {
        // Formatter : svg
        svg: function (cell, formatterParams){
            var values = cell.getValue();
            var srcFile = formatterParams.source;

            var _w = formatterParams.width || 25;
            var _h = formatterParams.height || 25;

            if (!values) {
                return '';
            }

            if (!Array.isArray(values)) {
                values = [ values ];
            }

            var result = '';
            values.forEach(function (value) {
                result += '<svg role="img" width="' + _w + '" height="' + _h + '"><use xlink:href="' + srcFile + '#' + value + '"></use></svg>'
            })

            return result
        },

        // Reorder handle
        handle: function () {
            return '<div class="tabulator-reorder-handle text-center"><i class="fa fa-arrows-v"></i></div>';
        },

        // Chip
        chip: function (cell) {
            var span = document.createElement('span');
            span.classList.add('chip')
            if (cell.getValue()) {
                span.classList.add('chip-success')
            }

            return span.outerHTML;
        }
    });

    // Custom Validators
    // =========================================================================

    // Validator : validUrl
    Tabulator.extendModule('validate', 'validators', {
        validUrl: function (cell, value) {
            if (!value) {
                return true
            }

            var pattern = /^(ftp|http|https):\/\/[^ "]+$/;
            return pattern.test(value);
        }
    })

}(Charcoal, jQuery, document, Tabulator));
