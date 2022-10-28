<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
<script>
    var content_config = {
        placeholder: 'Description',
        tabsize: 2,
        height: 150,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'clear']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture']], //, 'video'
            ['view', ['fullscreen', 'codeview', 'help']]
        ]
    };
    $('#description').summernote(content_config);
    // Setup currency
    var currency = {!! json_encode(get_application_currency()) !!};
    window.sharedData.company_currency = currency;
    setupPriceInput(window.sharedData.company_currency);

    $(".save_form_button").click(function () {
        var form = $(this).closest('form');

        // Remove price mask from values
        var price_inputs = form.find('.price_input');
        price_inputs.each(function (index, elem) {
            var price_input = $(elem);
            price_input.val(price_input.unmask());
        });

        // Submit form
        form.submit();
    });

    $("#savePlan").click(function(){
        $("#planForm").submit();
    })

    $(document).on("click", "#feature-add", function () {
        let newFeatureNo = parseInt($('#total_feature').val()) + 1;
        $('#feature-data #feature-list').append(GenerateTextbox(newFeatureNo));
        $('#total_feature').val(newFeatureNo);
    });

    $("body").on("click", ".f-remove", function () {
        $(this).closest("div.dynamic__feature").remove();
    });

    function GenerateTextbox(newFeatureNo) {
        return `<div class="row dynamic__feature">
            <div class="col">
            <div class="form-group required">
            <input class="form-control mb-1" name="features[${newFeatureNo}][label]" type="text" placeholder="Feature Label"/>
            </div>
            </div>
            <div class="col">
            <div class="form-group required">
            <input class="form-control" name="features[${newFeatureNo}][value]" placeholder="Feature Value">
            <small class="form-text text-muted">Set -1 to make this feature unlimited.
            </small>
            </div>
            </div>
            <div class="col">
            <div class="form-group required">
            <select name="features[${newFeatureNo}][is_displayed]" type="text"
            class="form-control">
            <option value="1">@php echo __('messages.yes') @endphp</option>
            <option value="0">@php echo __('messages.no') @endphp</option>
            </select>
            </div>
            </div>
            <div class="col">
            <div class="form-group required">
            <input type="number" class="form-control" name="features[${newFeatureNo}][order]" value="${newFeatureNo + 1}">
            </div>
            </div>
            <div class="col-1">
            <div class="form-group">
            <button type="button" class="btn btn-danger f-remove"><i class="fa fa-trash"></i></button>
            </div>
            </div>
            </div>`;
    }
</script>