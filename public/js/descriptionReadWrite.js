function onlyReadDescription(){
  $("#description_pass").prop("readonly",true);
}

function writedDescription(){
  $("#description_pass").prop("readonly",false);
}

function getDescription(){
  var description_pass = document.getElementById('description_pass').value;
  var description = document.getElementById('description');
  description.value = description_pass;
}
