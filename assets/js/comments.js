(function ($) {
    // Comments plugin
    $.comments = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.comments');
            return false;
        }
    };

    // Default settings
    var defaults = {
        listSelector: '[data-comment="list"]',
        parentSelector: '[data-comment="parent"]',
        appendSelector: '[data-comment="append"]',
        formSelector: '[data-comment="form"]',
        contentSelector: '[data-comment="content"]',
        toolsSelector: '[data-comment="tools"]',
        formGroupSelector: '[data-comment="form-group"]',
        errorSummarySelector: '[data-comment="form-summary"]',
        errorSummaryToggleClass: 'hidden',
        errorClass: 'has-error',
        offset: 0
    };

    // Edit the comment
    $(document).on('click', '[data-comment="update"]', function (evt) {
        evt.preventDefault();

        $.comments('createForm');

        var data = $.data(document, 'comments'),
            $this = $(this),
            $form = data.clone,
            $append = $this.parents(data.appendSelector),
            content = $append.find(data.contentSelector).text();

        $form.attr('action', $this.data('comment-url'));
        $form.attr('data-comment-action', 'update');
        $form.attr('data-comment-id', $this.data('comment-id'));
        $form.find('textarea').text(content);


        $append.append($form);
    });

    // Reply to comment
    $(document).on('click', '[data-comment="reply"]', function (evt) {
        evt.preventDefault();

        $.comments('createForm');

        var data = $.data(document, 'comments'),
            $this = $(this),
            $form = data.clone,
            $append = $this.parents(data.appendSelector);

        $form.attr('action', $this.data('comment-url'));
        $form.attr('data-comment-action', 'reply');
        $form.find('[data-comment="parent-id"]').val($this.data('comment-id'));

        $append.append($form);
    });

    // Delete comment
    $(document).on('click', '[data-comment="delete"]', function (evt) {
        evt.preventDefault();

        var data = $.data(document, 'comments'),
            $this = $(this);

        if (confirm($this.data('comment-confirm'))) {
            $.ajax({
                url: $this.data('comment-url'),
                type: 'DELETE',
                error: function (xhr, status, error) {
                    alert('error');
                },
                success: function (result, status, xhr) {
                    console.log(result);
                    console.log($this.parents('[data-comment="parent"][data-comment-id="' + $this.data('comment-id') + '"]'));
                    $this.parents('[data-comment="parent"][data-comment-id="' + $this.data('comment-id') + '"]').find(data.contentSelector).text(result);
                    $this.parents(data.toolsSelector).remove();
                }
            });
        }
    });

    // Scroll to parent comment
    $(document).on('click', '[data-comment="ancor"]', function (evt) {
        evt.preventDefault();
        $.comments('scrollTo', $(this).data('comment-parent'));
    });

    // AJAX updating form submit
    $(document).on('submit', '[data-comment-action="update"]', function (evt) {
        evt.preventDefault();

        var data = $.data(document, 'comments'),
            $this = $(this);

        $.ajax({
            url: $(this).attr('action'),
            type: 'PUT',
            data: $(this).serialize(),
            beforeSend: function (xhr, settings) {
                $this.find('[type="submit"]').attr('disabled', true);
            },
            complete: function (xhr, status) {
                $this.find('[type="submit"]').attr('disabled', false);
            },
            error: function (xhr, status, error) {
                if (xhr.status === 400) {
                    $.comments('updateErrors', $this, xhr.responseJSON);
                } else {
                    alert(error);
                }
            },
            success: function (response, status, xhr) {
                $this.parents('[data-comment="parent"][data-comment-id="' + $this.data('comment-id') + '"]').find(data.contentSelector).text(response);
                $.comments('removeForm');
            }
        });
    });

    // AJAX reply form submit
    $(document).on('submit', '[data-comment-action="reply"]', function (evt) {
        evt.preventDefault();

        var data = $.data(document, 'comments'),
            $this = $(this);

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            beforeSend: function (xhr, settings) {
                $this.find('[type="submit"]').attr('disabled', true);
            },
            complete: function (xhr, status) {
                $this.find('[type="submit"]').attr('disabled', false);
            },
            error: function (xhr, status, error) {
                if (xhr.status === 400) {
                    $.comments('updateErrors', $this, xhr.responseJSON);
                } else {
                    alert(error);
                }
            },
            success: function (response, status, xhr) {
                $(data.listSelector).html(response);
                $.comments('removeForm');
            }
        });
    });

    // AJAX create form submit
    $(document).on('submit', '[data-comment-action="create"]', function (evt) {
        evt.preventDefault();

        var data = $.data(document, 'comments'),
            $this = $(this);

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            beforeSend: function (xhr, settings) {
                $this.find('[type="submit"]').attr('disabled', true);
            },
            complete: function (xhr, status) {
                $this.find('[type="submit"]').attr('disabled', false);
            },
            error: function (xhr, status, error) {
                if (xhr.status === 400) {
                    $.comments('updateErrors', $this, xhr.responseJSON);
                } else {
                    alert(error);
                }
            },
            success: function (response, status, xhr) {
                $(data.listSelector).html(response);
                $.comments('clearErrors', $this);
                $this.trigger('reset');
            }
        });
    });

    // Methods
    var methods = {
        init: function (options) {
            if ($.data(document, 'comments') !== undefined) {
                return;
            }

            // Set plugin data
            $.data(document, 'comments', $.extend({}, defaults, options || {}));

            return this;
        },
        destroy: function () {
            $(document).unbind('.comments');
            $(document).removeData('comments');
        },
        data: function () {
            return $.data(document, 'comments');
        },
        createForm: function () {
            var data = $.data(document, 'comments'),
                $form = $(data.formSelector),
                $clone = $form.clone();

            methods.removeForm();

            $clone.removeAttr('id');
            $clone.attr('data-comment', 'js-form');

            data.clone = $clone;
        },
        removeForm: function () {
            var data = $.data(document, 'comments');

            if (data.clone !== undefined) {
                $('[data-comment="js-form"]').remove();
                data.clone = undefined;
            }
        },
        scrollTo: function (id) {
            var data = $.data(document, 'comments'),
                topScroll = $('[data-comment="parent"][data-comment-id="' + id + '"]').offset().top;
            $('body, html').animate({
                scrollTop: topScroll - data.offset
            }, 500);
        },
        updateErrors: function ($form, response) {
            var data = $.data(document, 'comments'),
                message = '';

            $.each(response, function (id, msg) {
                $('#' + id).closest(data.formGroupSelector).addClass(data.errorClass);
                message += msg;
            });

            $form.find(data.errorSummarySelector).toggleClass(data.errorSummaryToggleClass).text(message);
        },
        clearErrors: function ($form) {
            var data = $.data(document, 'comments');

            $form.find('.' + data.errorClass).removeClass(data.errorClass);
            $form.find(data.errorSummarySelector).toggleClass(data.errorSummaryToggleClass).text('');
        }
    };
})(window.jQuery);