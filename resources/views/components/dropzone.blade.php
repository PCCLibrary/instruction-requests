<!-- resources/views/components/dropzone.blade.php -->

<div id="dropzone" class="dropzone"></div>

<script>
    Dropzone.autoDiscover = false;

    document.addEventListener('DOMContentLoaded', function () {
        new Dropzone("#dropzone", {
            url: "{{ route('file.upload') }}", // Update with your file upload route
            acceptedFiles: 'application/pdf,.doc,.docx,.txt',
            maxFilesize: 5, // Set max file size (in MB)
            addRemoveLinks: true,
            init: function () {
                this.on('success', function (file, response) {
                    console.log(response); // Handle the response after successful upload
                });
            }
        });
    });
</script>
