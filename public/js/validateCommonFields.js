$.validator.addMethod("minDate", function(value, element) {
    var curDate = new Date();
    var minDate = new Date();
    minDate.setFullYear(minDate.getFullYear() - 119);
    var inputDate = new Date(value);
    if (inputDate > curDate || inputDate < minDate)
    {
      return false;
    }
    return true;
}, "Sei sicuro? Dovrebbe esserci un errore");

$.validator.addMethod("maxDate", function(value, element) {
    var maxDate = new Date();
    var curDate = new Date();
    maxDate.setFullYear(maxDate.getFullYear() - 18);
    var inputDate = new Date(value);
    if (inputDate > maxDate && inputDate < curDate)
        return false;
    return true;
}, "Devi essere maggiore di 18 anni");

$.validator.addMethod('validPassword',
    function(value, element, param) {
        if (value != '') {
            if (value.match(/.*[a-z]+.*/i) == null) {
                return false;
            }
            if (value.match(/.*\d+.*/) == null) {
                return false;
            }
        }

        return true;
    },
    'Deve contenere almeno una lettera ed un numero'
);

$.validator.addMethod("phoneNumber", function (phone_number, element) {
    phone_number = phone_number.replace(/\s+/g, "");
    return this.optional(element) || phone_number.length > 9 &&
    phone_number.match(/^[0-9]{10}$/);
}, "Inserisci un numero di telefono valido");
