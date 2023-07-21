let ajaxUrl = '';
let ajaxToken = '';
let ajaxUpdateUrl = '';
let ajaxInsertUrl = '';
let ajaxDeleteUrl = '';
function handleScreen() {
    let screen = $(this).width();
    if(screen >= 1200) {
        if($("aside#sidebar").hasClass('show-sidebar')) {
            $("aside#sidebar").removeClass('show-sidebar');
        }
    }
    else {
        if($("aside#sidebar").hasClass('show-sidebar')) {
            $("aside#sidebar").removeClass('show-sidebar');
        }
    }
}
$(document).ready(function() {
    handleScreen();
    $(window).resize(function(){
        handleScreen();
    });
    $('.select2').each(function() {
        let $this = $(this);
        let attributes = {};
        if($this.hasClass('multiple')) {
            attributes.multiple = true;
        }
        if($this.data('placeholder')) {
            attributes.placeholder = $this.data('placeholder');
        }
        $(this).select2(attributes);
    });
    $(document).on('click','.popper-button', function(e) {
        e.preventDefault();
        let popper = $(this).data('popper');
        popper = $('.popper[data-popper="'+popper+'"]');
        if(popper.length && popper.hasClass('show-popper')) {
            $('.popper').removeClass('show-popper');
        }
        else {
            $('.popper').removeClass('show-popper');
            popper.addClass('show-popper');
        }
    });
    $(document).on('click','.popper-fixed-close',function(e) {
        e.preventDefault();
        $('.popper').removeClass('show-popper');
    });
    $(document).on("click", function(event){
        let a = $(event.target).parents(".popper-button");
        if (!$(event.target).is(".popper,.popper-button") && !$(event.target).parents(".popper-button").length && !$(event.target).parents(".popper").length) {
            $('.popper').removeClass('show-popper');
        }
    });
    $(document).on("click", "#menu-toggle", function() {
        $("aside#sidebar").toggleClass('show-sidebar');
    })
    $(document).on("click",".menu-item.has-children > a", function(e) {
        e.preventDefault();
        let $this = $(this);
        if($this.hasClass('show')) {
            $this.removeClass('show');
            $this.siblings(".menu-children").slideUp();
        }
        else {
            $(".menu-item.has-children > a").removeClass("show");
            $(".menu-item.has-children .menu-children").slideUp();
            $this.addClass('show');
            $this.siblings(".menu-children").slideDown();
        }
    });
    $('#set-show-items-number').on('change',function(e) {
        if(ajaxUrl) {
            e.preventDefault();
            const $this = $(this);
            $number = $this.val();
            $this.prop('disabled',true);
            $.ajax({
                url: ajaxUrl,
                data: {
                    action: 'setShowItemsNumber',
                    number: $number,
                },
                success: function(data) {
                    location.reload();
                },
                error: function(response) {
                    $this.prop('disabled',false);
                    if(response.responseJSON.message) {
                        alert(response.responseJSON.message);
                    }
                    else {
                        alert('Lỗi! Vui lòng thử lại sau!');
                    }
                }
            });
        }
    })
    $('.check-list-all').on('change',function(e){
        e.preventDefault();
        let checked = false;
        if($(this).is(':checked')) {
            checked = true;
        }
        $('.check-list-all').prop('checked',checked);
        $('.check-list-item').prop('checked',checked);
    });
    $('.check-list-item').on('change',function(e){
        e.preventDefault();
        let checked = false;
        if(!$('.check-list-item:not(:checked)').length) {
            checked = true;
        }
        $('.check-list-all').prop('checked',checked);
    });
    $('.data-sorting').on('click',function() {
        let Form = $('#data-form');
        if(Form.length) {
            let orderBy = $(this).data('order-by');
            if(orderBy) {
                Form.find('input[name="orderBy"').val(orderBy);
                let orderType = $(this).data('order-type');
                orderType = orderType=='desc'?'desc':'asc';
                Form.find('input[name="orderType"').val(orderType);
                $('#data-form').submit();
            }
        }
    });
    $(document).on('click','.table-data .btn.update-data',function (e){
        if(ajaxUpdateUrl) {
            e.preventDefault();
            const id = $(this).parents('tr').find('input[name="id"]').val();
            const key = $(this).data('name');
            const value = $(this).data('update');
            $.ajax({
                url: ajaxUpdateUrl,
                type: 'POST',
                data: {
                    id: id,
                    [key]: value,
                    _token: ajaxToken,
                },
                success: function(data) {
                    location.reload();
                },
                error: function(response, status, error) {
                    $(this).prop('disabled',false);
                    if(response.responseJSON.message) {
                        alert(response.responseJSON.message);
                    }
                    else {
                        alert('Lỗi! Vui lòng thử lại sau!');
                    }
                }
            });
        }
    });
    $('.btn-handle-checkbox').on('click',function(e) {
        e.preventDefault();
        const $this = $(this);
        const action = $this.siblings('.list-handle-checkbox').val();
        const actionUrl = action == 'delete' ? ajaxDeleteUrl : ajaxUpdateUrl;
        const actionMethod = action == 'delete' ? 'DELETE' : 'POST';
        if(actionUrl) {
            const list = $('.check-list-item:checked');
            if(list.length > 0) {
                if(action) {
                    let ids = [];
                    list.map((i, item)=> {
                        ids.push(item.value);
                    });
                    $this.prop('disabled',true);
                    $.ajax({
                        url: actionUrl,
                        type: actionMethod,
                        data: {
                            ids: ids,
                            action: action,
                            _token: ajaxToken,
                        },
                        success: function(data) {
                            $this.prop('disabled',false);
                            alert('Thành công!');
                            location.reload();
                        },
                        error: function(response, status, error) {
                            $this.prop('disabled',false);
                            if(response.responseJSON.message) {
                                alert(response.responseJSON.message);
                            }
                            else {
                                alert('Lỗi! Vui lòng thử lại sau!');
                            }
                        }
                    });
                }
                else {
                    alert('Vui lòng chọn hành động');
                }
            }
            else {
                alert('Vui lòng chọn thành phần cần thực hiện');
            }
        }
        else {
            alert('Lỗi! Vui lòng thử lại sau!');
        }
    });
    $('.btn-delete-data').on('click',function(e) {
        e.preventDefault();
        const $this = $(this);
        const id = $(this).parents('tr').find('input[name="id"]').val();
        if(ajaxDeleteUrl) {
            $this.prop('disabled',true);
            $.ajax({
                url: ajaxDeleteUrl,
                type: 'DELETE',
                data: {
                    id: id,
                    _token: ajaxToken,
                },
                success: function(data) {
                    $this.prop('disabled',false);
                    alert('Thành công!');
                    location.reload();
                },
                error: function(response, status, error) {
                    $this.prop('disabled',false);
                    if(response.responseJSON.message) {
                        alert(response.responseJSON.message);
                    }
                    else {
                        alert('Lỗi! Vui lòng thử lại sau!');
                    }
                }
            });
        }
        else {
            alert('Lỗi! Vui lòng thử lại sau!');
        }
    });
    $(document).on('click','.form-yes-no-wrapper input.yes-option',function() {
        $(this).siblings('input.no-option').click();
    });
    $('.form-check-list .select-all input').on('change',function(e){
        e.preventDefault();
        let checked = false;
        if($(this).is(':checked')) {
            checked = true;
        }
        const list = $(this).parents('.form-check-list').find('input');
        list.prop('checked',checked);
        list.prop('checked',checked);
    });
    $('.form-check-list label:not(.select-all) input').on('change',function(e){
        e.preventDefault();
        let checked = false;
        console.log();
        if(!$(this).parents('.form-check-list').find('label:not(.select-all) input:not(:checked)').length) {
            checked = true;
        }
        $(this).parents('.form-check-list').find('.select-all input').prop('checked',checked);
    });
    $('.one-value input').on('change',function(e){
        e.preventDefault();
        let checked = false;
        $(this).parent('label').siblings('label').find('input').prop('checked',false);
    });
    $(document).on('submit','#add-new-item-form',function(e) {
        e.preventDefault();
        const $this = $(this);
        let formData = new FormData($this[0]);
        formData.append('_token',ajaxToken);
        if(ajaxInsertUrl) {
            $this.prop('disabled',true);
            $.ajax({
                url: ajaxInsertUrl,
                type: 'POST',
                enctype: 'multipart/form-data',
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    $this.prop('disabled',false);
                    $this.find(".form-field-error").remove();
                    alert('Thành công!');
                    location.reload();
                },
                error: function(response, status, error) {
                    $this.find(".form-field-error").remove();
                    $this.prop('disabled',false);
                    if(response.responseJSON.errors) {
                        Object.keys(response.responseJSON.errors).forEach((key,i) => {
                            let item = response.responseJSON.errors[key];
                            let input = $this.find('*[name="'+key+'"]').parent();
                            $('<span class="form-field-error">'+item[0]+'</span>').insertAfter(input);
                            
                        });
                        alert('Vui lòng kiểm tra lại dữ liệu');
                    }
                    else if(response.responseJSON.message) {
                        alert(response.responseJSON.message);
                    }
                    else {
                        alert('Lỗi! Vui lòng thử lại sau!');
                    }
                }
            });
        }
        else {
            alert('Lỗi! Vui lòng thử lại sau!');
        }
    });

    $(document).on('click','.edit-item-ajax',function(e) {
        e.preventDefault();
        const $this = $(this);
        const url = $this.attr('href');
        if(ajaxDeleteUrl) {
            $this.prop('disabled',true);
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: ajaxToken,
                    view: 1,
                },
                success: function(data) {
                    $this.prop('disabled',false);
                    $('.popper[data-popper="edit-item-ajax"] .popper-fixed-content-body').html(data);
                    $('.popper[data-popper="edit-item-ajax"]').addClass('show-popper');
                },
                error: function(response, status, error) {
                    $this.prop('disabled',false);
                    if(response.responseJSON.message) {
                        alert(response.responseJSON.message);
                    }
                    else {
                        alert('Lỗi! Vui lòng thử lại sau!');
                    }
                }
            });
        }
        else {
            alert('Lỗi! Vui lòng thử lại sau!');
        }
    });

    $(document).on('submit','#edit-item-ajax-form',function(e) {
        e.preventDefault();
        const $this = $(this);
        let formData = new FormData($this[0]);
        formData.append('_token',ajaxToken);
        const url = $this.attr('action');
        if(url) {
            $this.prop('disabled',true);
            $.ajax({
                url: url,
                type: 'POST',
                enctype: 'multipart/form-data',
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    $this.prop('disabled',false);
                    $this.find(".form-field-error").remove();
                    alert('Thành công!');
                    location.reload();
                },
                error: function(response, status, error) {
                    $this.find(".form-field-error").remove();
                    $this.prop('disabled',false);
                    if(response.responseJSON.errors) {
                        Object.keys(response.responseJSON.errors).forEach((key,i) => {
                            let item = response.responseJSON.errors[key];
                            let input = $this.find('*[name="'+key+'"]').parent();
                            $('<span class="form-field-error">'+item[0]+'</span>').insertAfter(input);
                            
                        });
                        alert('Vui lòng kiểm tra lại dữ liệu');
                    }
                    else if(response.responseJSON.message) {
                        alert(response.responseJSON.message);
                    }
                    else {
                        alert('Lỗi! Vui lòng thử lại sau!');
                    }
                }
            });
        }
        else {
            alert('Lỗi! Vui lòng thử lại sau!');
        }
    });
});