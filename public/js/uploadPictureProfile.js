$(document).ready(function() {
    var readURL = function(input) {
        if (input.files && input.files[0])
        {
          var file = input.files[0];
          var fileType = file["type"];
          var validImageTypes = ["image/gif", "image/jpeg", "image/png"];
          if ($.inArray(fileType, validImageTypes) < 0)
          {
              alert("Il file inserito non è un' immagine");
          }
          else
          {
            if((file.size / 1024 / 1024) < 2)
            {
              var reader = new FileReader();

              reader.onload = function (e) {
                  $('.avatar').attr('src', e.target.result);
              }

              reader.readAsDataURL(input.files[0]);

              document.getElementById("pictureform").submit();

            }
            else {
              alert("L'immagine deve essere massimo 2MB");
            }
        }
      }
    }


    $(".file-upload").on('change', function(){
        readURL(this);
    });
});
