var app = {

    init: function () {

        /**
        * *****************************
        * L I S T E N E R S
        * *****************************
        */
        $('.sidenav').sidenav();
        $('.search-input').on('keyup', app.searchContact);

    }, 

    searchContact: function (evt) {
        evt.preventDefault();
        let userInput = $('.search-input').val();

        $('.custom-row').hide();
        $('.custom-row:contains("' + userInput + '")').show();

        $(window).keydown((event) => {
            if (event.keyCode == 13) {
                event.preventDefault();
                return false;
            }
        })
    }
}

document.addEventListener('DOMContentLoaded', app.init)
