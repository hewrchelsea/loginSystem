$(document).ready(() => {
    const BURGER_BTN = document.querySelector('.burger')
    const BURGER_DROPDOWN = document.querySelector('.burgerDropdown')
    const CLOSE_BURGER = document.querySelector('.closeBurger')
    BURGER_BTN.onclick = () => {
        if (BURGER_DROPDOWN.style.display == 'none' || BURGER_DROPDOWN.style.display == '') {
            BURGER_DROPDOWN.style.display = 'block'
            BURGER_BTN.style.display = 'none';
            CLOSE_BURGER.style.display = 'flex'
            document.querySelector('html').style.overflowY = 'hidden'
        }
    }
    CLOSE_BURGER.onclick = () => {
        if (BURGER_DROPDOWN.style.display == 'block') {
            BURGER_DROPDOWN.style.display = 'none'
            BURGER_BTN.style.display = '';
            CLOSE_BURGER.style.display = 'none'
            document.querySelector('html').style.overflowY = 'auto'
        }
    }


    BURGER_DROPDOWN.onclick = event => {
        if (event.target.classList.contains('burgerDropdown')) {
            if (BURGER_DROPDOWN.style.display == 'block') {
                BURGER_DROPDOWN.style.display = 'none'
                BURGER_BTN.style.display = '';
                CLOSE_BURGER.style.display = 'none'
                document.querySelector('html').style.overflowY = 'auto'
            }
        }
    }

})