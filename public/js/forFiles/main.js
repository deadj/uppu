Dropzone.options.myDropzone = {
    url: "/",
    autoProcessQueue: false,
    maxFilesize: 100,
    maxFiles: 1,
    dictDefaultMessage: "Кликните для выбора или перетащите файл в область",
    dictFallbackMessage: "",

    init: function () {
        var myDropzone = this;

        $("#uploadButton").click(function (e) {
            var name = document.getElementById('name').value;

            if (!name.trim() == "" && myDropzone.files.length != 0) {
                var uploadButton = document.getElementById('uploadButton');
                uploadButton.disabled = true;
                var uploadImg = document.createElement('img');
                uploadImg.src = "/img/ajax-loader.gif";
                uploadButton.innerHTML = "";
                uploadButton.appendChild(uploadImg);

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
            var uploadButton = document.getElementById("uploadButton");
            uploadButton.style.display = "block";

            deleteButton.addEventListener("click", function (e) {
                e.preventDefault();
                e.stopPropagation();

                myDropzone.removeFile(file);
                myDropzone.setupEventListeners();

                deleteButton.style.display = "none";
                var uploadButton = document.getElementById("uploadButton");
                uploadButton.style.display = "none";
                uploadButton.innerHTML = "Загрузить";
                uploadButton.disabled = false;
            });
        });

        this.on("error", function (file) {
            myDropzone.removeEventListeners();

            var uploadButton = document.getElementById('uploadButton');
            uploadButton.disabled = false;
        }); 

        this.on('sending', function(file, xhr, formData) {
            var data = $('#myForm').serializeArray();
            $.each(data, function(key, el) {
                formData.append(el.name, el.value);
            });
        });
    },

    success: function(file, response) {
        document.location.href = "/file/" + response;
    }
}