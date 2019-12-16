function confirmDeleteAccount() {
  var richiesta = window.confirm("Sei sicuro di voler eliminare l'account?");
  if(richiesta)
  {
    document.getElementById("form_close_account").submit();
  }
}
