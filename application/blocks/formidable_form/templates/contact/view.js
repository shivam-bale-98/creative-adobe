(function ($) {

    if ($.fn.Dropzone) {
        Dropzone.autoDiscover = false;
    }

    "use strict";

    $.fn.tagName = function() {
        return this.prop('tagName').toLowerCase();
    };

    $.fn.formidable = function (settings) {

        var timeout = '';
        var formidable = this;
        var dropzone = 0;
        var debug = false;

        $.extend(formidable, {

            settings: $.extend({

                // Do callback on load
                // Vars: method|function
                onloadCallback: function() {},

                // Set locale
                // Vars: string --> 'en', 'fr', 'nl', ...
                locale: 'en',

                // Show errors on top of form or underneath each element
                // Vars: string -> 'form', 'element'
                errors: 'element',

                // Add additional classes to each error message
                // Vars: string
                errorsAdditionalClass: '',

                // Do callback on errors
                // Vars: method|function
                errorsCallback: function() {},

                // Hide form on successful submission
                // Vars: bolean
                successHideForm: false,

                // Add additional classes to each error message
                // Vars: string
                successAdditionalClass: '',

                // Scroll to top on success
                // Vars: bolean
                scrollToTop: true,

                // Scroll animation time
                // Vars: int
                scrollTime: 200,

                // Scroll offset from top
                // Vars: int
                scrollOffset: 0,

                // Do callback on success
                // Vars: method|function
                successCallback: function() {},

                // Dependencies for the form.
                // Should be loaded through the controller
                // Vars: json string
                dependencies: '',

            }, settings),

            init: function()
            {
                // initialize formObj
                this.formObj = $('form', this);

                // set locale
                this.setLocale();

                // add honeypot
                this.addHoneypot();

                // add resolution
                this.setResolution();

                // enable masking
                this.enableMasking();

                // enable counter
                this.enableCounter();

                // enable ranges
                this.enableRanges();

                // enable others for options
                this.enableOthers();

                // load dependencies
                this.loadDependencies();

                // do callback
                this.doCallbackOnload();

                // on submit!
                this.formObj.off('submit').on('submit', this.captchaSubmit.bind(this));
            },

            formSubmit: function()
            {
                // stop form from submitting
                // e.preventDefault();

                // capture data
                var formData = this.captureData();

                // disable fields
                this.startProcess();

                $.ajax({
                    url: this.formObj.attr('action'),
                    data: formData,
                    method: 'post',
                    dataType: 'json',

                    // when using formdata
                    contentType: false,
                    processData: false,
                    cache: false,

                    beforeSend: this.beforeFormSend.bind(this),
                    error: this.formSubmitError.bind(this),
                    success: this.formSubmitSuccess.bind(this),
                    complete: this.formSubmitComplete.bind(this)
                });
            },

            beforeFormSend: function()
            {
                formidable.removeClass('was-validated');
                formidable.find(':input').removeClass('is-invalid is-valid');
                formidable.find('.invalid-feedback, .valid-feedback').remove();
            },

            formSubmitError: function(response)
            {
                var data = response.responseJSON;

                if (data.message) {
                    this.formMessage(data.message);
                }

                if (data.errors) {
                    $(this.formObj)
                        .find('input[type="tel"]')
                        .each((index, item) => {
                            let number = $(item)
                                .parents(".form-group")
                                .find("#" + $(item)[0].id + "-number-original")
                                .val();
                            if (number.length) {
                                $(item).val(number);
                            }
                        });

                    // set all errors
                    if (this.settings.errors == 'form') {
                        var errors = [];
                        $.each(data.errors, function(handle, err) {
                            if ($.isArray(err)) {
                                err = err.join('<br />');
                            }
                            errors.push(err);
                        });
                        this.formMessage({type: 'error', message: errors});
                    }
                    else {
                        if (data.errors.form) {
                            this.formMessage({type: 'error', message: data.errors.form});
                        }
                    }
                    $.each(data.errors, this.elementMessage.bind(this));
                }
            },

            formSubmitSuccess: function(data)
            {
                if (data.redirect) {
                    window.location.href = data.redirect;
                    return;
                }

                if (data.message) {
                    this.formMessage({type: 'success', message: data.message});
                }
            },

            formSubmitComplete: function(response, status)
            {
                var data = response.responseJSON;

                // set as validated
                this.setFormValidated();

                // reload ccm_token
                this.resetToken(data.ccm_token);

                // refresh recaptcha
                if (typeof refresh_recaptcha !== "undefined") {
                    refresh_recaptcha();
                }

                // enable fields
                this.stopProcess();

                // scroll to top of form
                this.scrollToTop();

                // reload dependencies
                this.reloadDependencies();

                // do callback
                if (status == 'error') {
                    this.doCallbackError(response);
                }
                if (status == 'success') {

                    // clear form
                    this.resetForm();

                    // hide form
                    this.hideForm();

                    this.doCallbackSuccess(response);
                }
            },

            captureData: function()
            {
                // only works for data, no files
                //return this.formObj.serialize();

                $(this.formObj)
                    .find('input[type="tel"]')
                    .each((index, item) => {
                        let number = $(item)
                            .parents(".form-group")
                            .find("#" + $(item)[0].id + "-number-original")
                            .val();
                        let dialCode = $(item)
                            .parents(".form-group")
                            .find("#" + $(item)[0].id + "-dial-code")
                            .val();
                        if (number.length) {
                            $(item).val(dialCode + number);
                        }
                    });

                var formData = new FormData(this.formObj[0]);
                return formData;
            },

            setLocale: function()
            {
                // set locale for moment.js
                if (window.moment) {
                    moment.locale(this.settings.locale.substr(0, 2));
                }
            },

            setResolution: function()
            {
                var resolution = $('<input>').attr({
                    'type': 'hidden',
                    'name': 'resolution',
                    'id': 'resolution',
                    'value': screen.width + 'x' + screen.height
                });
                this.formObj.prepend(resolution);
            },

            addHoneypot: function()
            {
                var honeypot = $('<input>').attr({
                    'type': 'hidden',
                    'name': 'emailaddress',
                    'id': 'emailaddress',
                    'value': ''
                });
                this.formObj.prepend(honeypot);
            },

            enableMasking: function()
            {
                // mask for "normal" fields
                if ($.fn.mask) {
                    $('[data-mask]').each(function(i, f)  {
                        $(f).mask($(f).attr('data-mask'));
                    });
                }
                // mask / placeholder for selectboxes
                $('[data-placeholder]').each(function(i, f) {
                    var attr = {disabled: true, hidden: true, selected: true};
                    if ($(f).attr('multiple')) {
                        attr = {disabled: true, selected: false};
                    }
                    $(f).find('option:first').prop(attr);
                })
            },

            enableCounter: function()
            {
                $('[data-range-type]', formidable).each(function(i, f)  {
                    var typ = $(f).attr('data-range-type');
                    var min = parseFloat($(f).attr('data-range-min'));
                    var max = parseFloat($(f).attr('data-range-max'));
                    var range = '<div data-range><span data-count></span>/<span data-max>'+max+'</span></div>';
                    $(f).wrapAll('<div data-formidable-countable>').after(range);
                    var tag = $(f).tagName().toLowerCase();
                    // for select, checkbox and radio...
                    if ($.inArray(tag, ['select']) > -1 || $.inArray($(f).attr('type'), ['radio', 'checkbox']) > -1) {
                        $(f).on('change', function() {
                            var range = $(f).closest('[data-formidable-countable]').find('[data-count]');
                            var checked = parseFloat($(":selected", $(f)).length);
                            range.text(checked);
                            $(f).find(':not(:selected)').prop('disabled', false);
                            if (checked >= max) {
                                $(f).find(':not(:selected)').prop('disabled', true);
                            }
                        }).trigger('change');
                    }
                    // for number field
                    else if ($.inArray($(f).attr('type'), ['number']) > -1) {
                        var range = $(f).closest('[data-formidable-countable]').find('[data-count]');
                        range.text(min);
                    }
                    // for file field
                    else if ($.inArray(typ, ['files']) > -1) {
                        $('[data-additional-file]', $(f)).on('click', function() {
                            $('.file.hide:first', $(f)).removeClass('hide');
                            if ($('.file.hide', $(f)).length <= 0) {
                                $('[data-additional-file]', $(f)).prop('disabled', true);
                            }
                        });
                    }
                    // for others
                    else {
                        if ($.fn.simplyCountable) {
                            $(f).simplyCountable({counter: $('[data-count]', $(f).closest('[data-formidable-countable]')), countType: typ, maxCount: max});
                        }
                    }
                });
            },

            enableRanges: function()
            {
                $('input[type="range"]', formidable).each(function(i, f) {
                    var span = $('[data-range-value="'+$(this).attr('id')+'"]');
                    span.html($(f).val());

                    $(f).on('input', function() {
                        span.html($(f).val());
                    });
                });
            },

            enableOthers: function()
            {
                $('[data-option-other]', formidable).each(function(i, f) {
                    var element = $(f).find('[type="checkbox"], [type="radio"], select');
                    element.on('change', function() {
                        var value = element.val();
                        if ($.inArray(element.attr('type'), ['radio', 'checkbox']) > -1) {
                            value = [];
                            $(f).find(':is(:checked)').each(function(i, e) {
                                value.push($(e).val());
                            });
                        }
                        var other = $(f).find('div.option-other');
                        if ($.inArray('other_value', $.isArray(value)?value:[value]) > -1) {
                            other.addClass('enabled').find(':input').prop({disabled: false});
                        }
                        else {
                            other.removeClass('enabled').find(':input').val('').prop({disabled: true});
                        }
                    }).trigger('change');
                })
            },

            // run dependencies
            loadDependencies: function()
            {
                $.each(this.settings.dependencies.selectors, function(d, dependency) {
                    $(dependency.selector, formidable).on('change keyup', function() {
                        clearTimeout(timeout);
                        timeout = setTimeout(formidable.checkDependencyRule(dependency.id), 200);
                    }).trigger('change');
                });
            },

            // reload dependencies
            reloadDependencies: function()
            {
                this.settings.dependencies.reload = true;
                $.each(this.settings.dependencies.selectors, function(d, dependency) {
                    formidable.checkDependencyRule(dependency.id);
                });
                this.settings.dependencies.reload = false;
            },

            checkDependencyRule: function(id)
            {
                for (let rs = 0; rs < this.settings.dependencies.rules.length; rs++) {

                    var rules = this.settings.dependencies.rules[rs];

                    // skip rules...
                    if ($.inArray(rules.id, id) <= -1) {
                        continue;
                    }

                    var selector = $(rules.selector, formidable);

                    var inverse = false;

                    // first go back to basics to it's "default" state.
                    // example: if show is action, default is hidden.
                    for (let r = 0; r < rules.rules.length; r++) {
                        var rule = rules.rules[r];
                        for (let a = 0; a < rule.actions.length; a++) {
                            var action = rule.actions[a];
                            if (this.settings.dependencies.reload && $.inArray(action[0], ['disable']) <= -1) {
                                continue;
                            }
                            switch (action[0]) {
                                case 'show':
                                    selector.prop('disabled', true).closest('[data-formidable-handle]').hide();
                                    break;
                                case 'hide':
                                    selector.prop('disabled', false).closest('[data-formidable-handle]').show();
                                    inverse = true;
                                    break;
                                case 'disable':
                                    selector.prop('disabled', false);
                                    break;
                                case 'enable':
                                    selector.prop('disabled', true);
                                    inverse = true;
                                    break;
                                case 'class':
                                    formidable.removeElementClass(selector, action[1]);
                                    break;
                                case 'value':
                                    formidable.setElementValue(selector, '');
                                    break;
                            }
                        }
                    }

                    for (let r = 0; r < rules.rules.length; r++) {

                        var rule = rules.rules[r];

                        if (debug) console.log('---- RuleNo: '+r+' ----');
                        if (debug) console.log('Selector: '+rules.selector);

                        var match = true;

                        for (let s = 0; s < rule.selectors.length; s++) {

                            var line = rule.selectors[s];

                            var input = $(line.handle, formidable);
                            if (!input.length) {
                                continue;
                            }

                            var type = input.tagName();
                            if (type == 'input') {
                                type = input.attr('type');
                            }

                            var value = [];
                            switch (type) {
                                case 'select':
                                    value[0] = input.val().toLowerCase();
                                    break;
                                case 'checkbox':
                                case 'radio':
                                    $.each($(line.handle+':is(:checked)', formidable), function(c, check) {
                                        value[c] = $(check).val().toLowerCase();
                                    });
                                    break;
                                default:
                                    value[0] = input.val().toLowerCase();
                                    break;
                            }

                            if (debug) console.log('Rule selector: '+line.handle);
                            if (debug) console.log('Value: '+(value.length?value:'(empty)'));

                            for (let c = 0; c < line.conditions.length; c++) {

                                var cond = line.conditions[c];
                                var compare_value = cond.value?cond.value.toLowerCase():'';

                                switch (cond.comparison) {
                                    case 'empty':
                                        // not empty is false
                                        if (value.length > 0) match = false;
                                        //if (debug) console.log('Check: '+value.length);
                                        break;
                                    case 'not_empty':
                                        // empty is false
                                        if (value.length <= 0) match = false;
                                        break;
                                    case 'equals':
                                        // not equals is false
                                        if ($.inArray(compare_value, value) <= -1) match = false;
                                        break;
                                    case 'not_equals':
                                        // equals is false
                                        if ($.inArray(compare_value, value) > -1) match = false;
                                        break;
                                    case 'contains':
                                        // not contains is false
                                        for (let i = 0; i < value.length; i++) {
                                            if (value[i].search(compare_value) <= -1) {
                                                match = false;
                                                break;
                                            }
                                        }
                                        break;
                                    case 'not_contains':
                                        // contains is false
                                        for (let i = 0; i < value.length; i++) {
                                            if (value[i].search(compare_value) >= -1) {
                                                match = false;
                                                break;
                                            }
                                        }
                                        break;
                                }
                            }
                        }

                        if (debug) console.log('Match: '+(match?'yes':'no'));

                        if (inverse) {
                            if (debug) console.log('Inversed?: '+(inverse?'yes':'no'));
                            match = !match;
                        }

                        if (!match) {
                            continue;
                        }

                        for (let a = 0; a < rule.actions.length; a++) {
                            var action = rule.actions[a];
                            if (this.settings.dependencies.reload && $.inArray(action[0], ['disable']) <= -1) {
                                continue;
                            }
                            if (debug) console.log('Action '+action[0]);
                            switch (action[0]) {
                                case 'show':
                                    selector.prop('disabled', false).closest('[data-formidable-handle]').show();
                                    break;
                                case 'hide':
                                    selector.prop('disabled', true).closest('[data-formidable-handle]').hide();
                                    break;
                                case 'disable':
                                    selector.prop('disabled', true);
                                    break;
                                case 'enable':
                                    selector.prop('disabled', false);
                                    break;
                                case 'class':
                                    formidable.addElementClass(selector, action[1]);
                                    if (debug) console.log('Class: '+action[1]);
                                    break;
                                case 'value':
                                    formidable.setElementValue(selector, action[1]);
                                    if (debug) console.log('Value: '+action[1]);
                                    break;
                                case 'clear':
                                    formidable.setElementValue(selector);
                                    break;
                            }
                        }
                        if (debug) console.log('------------------');
                    }
                }
            },

            // set field value
            setElementValue: function(selector, value)
            {
                var type = selector.tagName();
                if (type == 'input') {
                    type = selector.attr('type');
                }
                switch (type) {
                    case 'select':
                        selector.prop('selectedIndex', 0);
                        if (value.length) {
                            selector.val(value);
                        }
                        break;
                    case 'checkbox':
                    case 'radio':
                        selector.prop('checked', false);
                        if (value.length) {
                            for (let i = 0; i < selector.length; i++) {
                                var s = selector[i];
                                $(s).prop('checked', false);
                                if ($(s).val() == value) $(s).prop('checked', true);
                            };
                        }
                        break;
                    default:
                        selector.val(value);
                        break;
                }
                selector.trigger('change');
            },

            // add field class
            addElementClass: function(selector, className, todo)
            {
                selector.addClass(className);
            },

            // remove field class
            removeElementClass: function(selector, className, todo)
            {
                selector.removeClass(className);
            },

            // if form is validated
            setFormValidated: function()
            {
                // Disabled for now because i don't want to use :valid or :invalid
                // formidable.addClass('was-validated');
                $('[data-formidable-type]', formidable).find(':input:not(.is-invalid):not(.no-valid)').addClass('is-valid');
            },

            // start 'busy' process
            startProcess: function()
            {
                formidable.addClass('processing').find(':input').attr('disabled', true);
                formidable.find("button[type='submit']").addClass("loading");
                formidable.find(".loader-spinner ").addClass('active');
            },

            // stop 'busy' process
            stopProcess: function()
            {
                formidable.removeClass('processing').find(':input').attr('disabled', false);
                formidable.find("button[type='submit']").removeClass("loading");
                formidable.find(".loader-spinner ").removeClass('active');
            },

            setSendable: function(sendable)
            {
                this.sendable = sendable;
            },

            getSendable: function()
            {
                return this.sendable;
            },

            // hide form
            hideForm: function()
            {
                if (!!this.settings.successHideForm) {
                    this.formObj.remove();

                    // this.formObj.find('button[type="submit"]').addClass('in-active');
                    // $('.loader-spinner').addClass('active');
                }
            },

            resetForm: function()
            {
                $('[data-formidable-type]', formidable).find(':input').removeClass('is-valid no-valid is-invalid is-invalid');
                $('div.invalid-feedback, div.valid-feedback', formidable).remove();
                if (typeof refresh_recaptcha !== "undefined") {
                    refresh_recaptcha();
                }
                this.formObj.trigger('reset');
            },

            // callback called onload of the form
            doCallbackOnload: function()
            {
                if (!!this.settings.onloadCallback && $.isFunction(this.settings.onloadCallback)) {
                    this.settings.onloadCallback.call(this, formidable);
                }
            },

            // callback called when errors occure on submit
            doCallbackError: function(response)
            {
                if (this.settings.errorsCallback && $.isFunction(this.settings.errorsCallback)) {
                    this.settings.errorsCallback.call(this, this, response);
                }
            },

            // callback called when form is successfully submitted
            doCallbackSuccess: function(response)
            {
                if (this.settings.successCallback && $.isFunction(this.settings.successCallback)) {
                    this.settings.successCallback.call(this, this, response);
                }
            },

            // reload token data in form
            resetToken: function(ccm_token)
            {
                $('[id="ccm_token"]', formidable).val(ccm_token);
            },

            // move to top
            scrollToTop: function()
            {
                if (!!this.settings.scrollToTop) {
                    $('html, body').animate({
                        scrollTop: formidable.offset().top + this.settings.scrollOffset
                    }, this.settings.scrollTime);
                }
            },

            // show message (notifications and errors) on top of form
            formMessage: function(message)
            {
                var type = 'error';
                if (!!message.type) {
                    type = message.type;
                }
                if (!!message.message) {
                    message = message.message;
                }
                if ($.isArray(message)) {
                    message = message.join('<br />');
                }

                var holder = $('[data-formidable-message]', formidable);
                holder.attr('class', '').html(message);
                if (type == 'error') {
                    holder.addClass('alert alert-danger');
                    if (this.settings.errorsAdditionalClass) {
                        holder.addClass(this.settings.errorsAdditionalClass);
                    }
                }
                if (type == 'info') {
                    holder.addClass('alert alert-info');
                }
                if (type == 'success') {
                    holder.addClass('alert alert-success');
                    if (this.settings.successAdditionalClass) {
                        holder.addClass(this.settings.successAdditionalClass);
                    }
                }
                holder.show();
            },

            // show message (erros) after the specific element
            elementMessage: function(handle, message)
            {
                var type = 'error';
                if (!!message.type) {
                    type = message.type;
                }
                if (!!message.message) {
                    message = message.message;
                }
                if ($.isArray(message)) {
                    message = message.join('<br />');
                }

                var holder = $('[data-formidable-handle="'+handle+'"]', formidable);
                if (typeof handle === 'object') {
                    holder = handle;
                }

                var elements = holder.find(':input:not(.no-valid)');
                if (type == 'error') {
                    elements.addClass('is-invalid');
                    if (this.settings.errorsAdditionalClass) {
                        elements.addClass(this.settings.errorsAdditionalClass);
                    }
                }
                else {
                    elements.addClass('is-valid');
                    if (this.settings.successAdditionalClass) {
                        elements.addClass(this.settings.successAdditionalClass);
                    }
                }

                // show message with element
                if (this.settings.errors == 'element') {
                    if (message.length > 0) {
                        var msg = $('<div>');
                        msg.html(message);
                        if (type == 'error') {
                            msg.addClass('invalid-feedback');
                            if (this.settings.errorsAdditionalClass) {
                                msg.addClass(this.settings.errorsAdditionalClass);
                            }
                        }
                        else {
                            msg.addClass('valid-feedback');
                            if (this.settings.successAdditionalClass) {
                                msg.addClass(this.settings.successAdditionalClass);
                            }
                        }
                        holder.append(msg);
                    }
                }
            },

            getFormObject: function()
            {
                return this.formObj;
            },

            addDropzoneUpload: function()
            {
                dropzone++;
                this.startProcess();
            },

            removeDropzoneUpload: function()
            {
                dropzone--;
                if (dropzone <= 0) {
                    this.stopProcess();
                }
            },

            captchaSubmit: function (e) {
                e.preventDefault();
                let formObj = this;

                grecaptcha.ready(function() {
                    grecaptcha.execute(formidable_captcha_site_key, {action: 'submit'}).then(function(token) {
                        // Add your logic to submit to your backend server here.
                        $('[name="g-recaptcha-response"]', formObj).val(token);
                        formObj.formSubmit();
                    });
                });
            },

        });

        formidable.init();

        return formidable;
    };

    $.fn.formidableDropzone = function (formidable, files) {

        var dropzone = this;

        $.extend(dropzone, {

            settings: $.extend({

                // set max uploaded files
                maxFiles: 10,

                // set accepted extensions
                acceptedFiles: '.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx,.pdf',

                // set max uploaded filesize
                maxFilesize: 5 // MB

            }),

            formidable: formidable,
            files: files,
            dropzoneObj: null,

            init: function()
            {
                this.dropzoneDiv = $(this);
                this.formObj = this.formidable.getFormObject();

                this.settings.action = this.formObj.attr('action');;

                if (this.dropzoneDiv.data('range-max')) {
                    this.settings.maxFiles = this.dropzoneDiv.data('range-max');
                }
                if (this.dropzoneDiv.data('extensions')) {
                    this.settings.acceptedFiles = this.dropzoneDiv.data('extensions');
                }
                if (this.dropzoneDiv.data('filesize')) {
                    this.settings.maxFilesize = this.dropzoneDiv.data('filesize');
                }

                // do callback
                this.doDropzone();
            },

            doDropzone: function()
            {
                this.dropzoneDiv.dropzone({
                    url: this.settings.action,
                    maxFiles: this.settings.maxFiles,
                    acceptedFiles: this.settings.acceptedFiles,
                    maxFilesize: this.settings.maxFilesize,
                    createImageThumbnails: true,
                    autoProcessQueue: true,
                    uploadMultiple: false,
                    addRemoveLinks: false,
                    previewTemplate: this.dropzoneDiv.next('[id="dropzoneTemplate"]').html(),
                    init: this.initialize.bind(this),
                    sending: this.sending.bind(this),
                    addedfiles: this.add.bind(this),
                    removedfile: this.removeFile.bind(this),
                    processing: this.processing.bind(this),
                    error: this.error.bind(this),
                    success: this.success.bind(this),
                    complete: this.complete.bind(this)
                });
                this.dropzoneObj = this.dropzoneDiv.get(0).dropzone;

                if (files.length > 0) {
                    this.mocks();
                }
            },

            initialize: function()
            {

            },

            sending: function(file, json, formData)
            {
                formData.append('formID', $('[id="formID"]', this.formidable.getFormObject()).val());
                formData.append('bID', $('[id="bID"]', this.formidable.getFormObject()).val());
                formData.append('ccm_token', $('[id="ccm_token"]', this.formidable.getFormObject()).val());
                formData.append('handle', this.dropzoneDiv.attr('id'));
                formData.append('action', 'upload_file');
            },

            add: function(file)
            {
                this.removeErrors();
            },

            processing: function(file)
            {
                this.formidable.addDropzoneUpload();
                this.removeErrors();
            },

            error:  function(file, message)
            {
                this.formidable.stopProcess();
                this.dropzoneObj.removeFile(file);
                this.formidable.elementMessage(this.dropzoneDiv.parent(), message);
            },

            removeFile: function(file)
            {
                var response = false;

                if (file.xhr && file.xhr.responseText.length > 0) {
                    response = $.parseJSON(file.xhr.responseText);
                }

                if (!!response) {

                    this.doRemoveFile(response.file);
                }

                // do native dropzone remove
                file.previewElement.remove();

                if ($('.dropzone-item', this.dropzoneDiv).length <= 0) {
                    this.dropzoneDiv.removeClass('dz-started');
                }
            },

            doRemoveFile: function(file)
            {
                var formData = new FormData();

                formData.append('formID', $('[id="formID"]', this.formidable.getFormObject()).val());
                formData.append('bID', $('[id="bID"]', this.formidable.getFormObject()).val());
                formData.append('ccm_token', $('[id="ccm_token"]', this.formidable.getFormObject()).val());
                formData.append('handle', this.dropzoneDiv.attr('id'));
                formData.append('file', file);
                formData.append('action', 'delete_file');

                $.ajax({
                    url: this.settings.action,
                    data: formData,
                    method: 'post',
                    dataType: 'json',

                    // when using formdata
                    contentType: false,
                    processData: false,
                    cache: false,

                    error: this.removeFileError.bind(this),
                    success: this.removeFileComplete.bind(this)
                });
            },

            removeFileComplete: function(response)
            {
                $('[type="hidden"][value="'+response.file+'"]', this.dropzoneDiv).remove();
            },

            removeFileError: function(response)
            {
                var data = response.responseJSON;

                if (data.errors) {
                    // set all errors
                    if (this.settings.errors == 'form') {
                        var errors = [];
                        $.each(data.errors, function(handle, err) {
                            if ($.isArray(err)) {
                                err = err.join('<br />');
                            }
                            errors.push(err);
                        });
                        this.formidable.formMessage({type: 'error', message: errors});
                    }
                    else {
                        $.each(data.errors, function(handle, message) {
                            this.formidable.elementMessage(this.dropzoneDiv.parent(), message);
                        }.bind(this));
                    }
                }
            },

            removeErrors: function()
            {
                $('.invalid-feedback:not(:last-child)', this.dropzoneDiv.parent()).remove();
            },

            removeAllErrors: function()
            {
                $('.invalid-feedback', this.dropzoneDiv.parent()).remove();
            },

            removeNonExistingThumbnail: function()
            {
                $('[data-dz-thumbnail]', this.dropzoneDiv).each(function(i, row) {
                    if (!$(row).attr('href') || $(row).attr('href').length <= 0) {
                        $(row).parent().remove();
                    }
                });
            },

            success: function(file, json)
            {
                this.removeNonExistingThumbnail();

                /* append to object */
                var input = $('<input>').attr({
                    'type': 'hidden',
                    'name': this.dropzoneDiv.attr('id')+'[]',
                    'value': json.file
                });
                $('.dropzone-item:last-child', this.dropzoneDiv).prepend(input);
            },

            complete: function(file)
            {
                this.formidable.removeDropzoneUpload();
            },

            mocks: function()
            {
                $.each(this.files, function(i, file) {

                    var mock = {
                        name: file.name,
                        size: file.size,
                        //url: file.url,
                        accepted: true,
                        status: Dropzone.ADDED,
                    }
                    this.dropzoneObj.files.push(mock);
                    this.dropzoneObj.displayExistingFile(mock);

                    this.removeNonExistingThumbnail();

                    var input = $('<input>').attr({
                        'type': 'hidden',
                        'name': this.dropzoneDiv.attr('id')+'[]',
                        'value': mock.name
                    });

                    $('.dropzone-item:last-child', this.dropzoneDiv).prepend(input);
                    $('.dropzone-item:last-child', this.dropzoneDiv).find('[data-dz-remove]').on('click', function() {
                        /*
                        // add hidden input to remove files from controller
                        // this will need a new "form submit" or else it won't work
                        // therefore we remove the file directly
                        var input = $('<input>').attr({
                            'type': 'hidden',
                            'name': this.dropzoneDiv.attr('id')+'_delete[]',
                            'value': mock.name
                        });
                        this.dropzoneDiv.prepend(input);
                        */
                        this.doRemoveFile(mock.name);

                    }.bind(this));

                }.bind(this));
            }

        });

        dropzone.init();

        return dropzone;
    };

}(jQuery));