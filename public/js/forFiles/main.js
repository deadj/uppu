Dropzone.options.myDropzone = {
    url: "/",
    autoProcessQueue: false,
    maxFilesize: 2,
    maxFiles: 1,

    init: function () {

        var myDropzone = this;

        $("#uploadButton").click(function (e) {
            var name = document.getElementById('name').value;

            if (!name.trim() == "") {
                e.preventDefault();
                myDropzone.processQueue();
            }
        });

        this.on('maxfilesreached', function() {
            myDropzone.removeEventListeners();
        });

        this.on("addedfile", function (file) {
            var deleteButton = document.getElementById("deleteButton");
            deleteButton.style.display = "block";

            deleteButton.addEventListener("click", function (e) {
                e.preventDefault();
                e.stopPropagation();

                myDropzone.removeFile(file);
                myDropzone.setupEventListeners();
            });
        });

        this.on("error", function (file) {
            myDropzone.removeEventListeners();
        }); 

        this.on('sending', function(file, xhr, formData) {
            var data = $('#myForm').serializeArray();
            $.each(data, function(key, el) {
                formData.append(el.name, el.value);
            });
        });
    },

    success: function(file, response) {
        document.location.href = "/" + response;
    }
}
