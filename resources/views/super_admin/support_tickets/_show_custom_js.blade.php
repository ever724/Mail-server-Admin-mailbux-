<script>
    $("#closeTicketButton").click(function () {
        $("#closeTicketForm").submit();
    });

    let tinymceWrapper = tinymce.init({
        selector: "textarea#replyBody",
        plugins: "searchreplace autolink directionality visualblocks visualchars image link media codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount charmap emoticons autosave",
        toolbar: "undo redo | fontsize | bold italic underline forecolor backcolor | link image addcomment showcomments  | alignleft aligncenter alignright alignjustify lineheight | checklist bullist numlist indent outdent | removeformat",
        height: '400px',
        width: '100%',
        menubar: false,
        branding: false,
        resize: false
    });

    let fileCount = 0;

    $("#addFile").click(function () {
        $(this).parent().append(`<div class="row mt-2"> <input type="file" class="form-control col-10" name="files[${fileCount}]"> <a class="col-2 btn btn-md btn-danger remove-file"><i class="fa fa-minus"></i></a><div>`);

        $('.btn.remove-file').click(function(){
            $(this).parent().remove();
        });

        fileCount++;
    });




    $("#submitReply").submit(function(e){
        e.preventDefault();

        $('.reply-error').remove();

        $('.is-invalid').removeClass('is-invalid');

        let data = new FormData();

        $(this).find('input, textarea').each(function () {
            let element = $(this);
            let name = element.attr('name');
            let value = null;

            if(element.is('input') && element.attr('type') === 'file'){
                value = element[0].files[0];
            } else if(element.is('input, textarea')){
                value = element.val();
            }

            if(value !== undefined){
                data.append(name, value);
            }
        });

        if(data.get('body') === ''){
            data.set('body', tinymce.get('replyBody').getContent());
        }

        $.ajax({
            type: 'POST',
            url: "{{route('super_admin.support_tickets.reply', [$support_ticket])}}",
            contentType: false,
            processData: false,
            data : data,
            success: function(result){
                location.reload();
            },
            error: function(err){

                let errors = err.responseJSON.errors;

                for(let key in errors){

                    let errorMessage = errors[key][0];
                    let arrayKey = key.split('.');
                    if(arrayKey.length === 2){
                        key = `${arrayKey[0]}[${arrayKey[1]}]`
                    }

                    $(`[name="${key}"]`).addClass('is-invalid')
                        .parent()
                        .append(`<span class="invalid-feedback reply-error" role="alert"><strong>${errorMessage}</strong></span>`);
                }
            }
        })

    })

</script>