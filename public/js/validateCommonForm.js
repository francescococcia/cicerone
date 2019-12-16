
$(document).ready(function() {

    /**
     * Validate the form
     */
    $('#commonFields-form').validate({
        rules: {
            name: 'required',
            surname: 'required',
            email: {
                required: true,
                email: true,
                remote: '/account/validate-email'
            },
            password: {
                required: true,
                minlength: 6,
                validPassword: true
            },
            password_confirmation: {
                required: true,
                equalTo: '#password'
            },
            self_phone:{
                required: true,
                phoneNumber: true
            },
            sex: 'required',
            birthday:
            {
               required: true,
               date: true,
               maxDate: true,
               minDate: true
            },
        },
        messages: {
            name: "Inserisci il tuo nome",
            surname: "Inserisci il tuo cognome",
            self_phone:{
                    required: "Inserisci il tuo numero di telefono",
                    matches: "Inserisci un numero di telefono valido"
            },
            sex: "Inserisci il tuo sesso",
            email: {
                    remote: "Email già in utilizzo",
                    email: "Inserisci una email valida",
                    required: "Inserisci una email"
            },
            password:{
                required:"Inserisci almeno 6 caratteri",
                minlength: "Inserisci almeno 6 caratteri"
            },
            password_confirmation: {
                required: "Ripeti la stessa password",
                equalTo: "I campi della password devono essere uguali",
            },
            birthday:{
              required: "Inserisci la data di nascita",
              date: "Inserisci una data valida",
            },
        }
    });
});
